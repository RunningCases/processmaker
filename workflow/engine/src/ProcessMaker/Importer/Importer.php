<?php
namespace ProcessMaker\Importer;

use ProcessMaker\Util;
use ProcessMaker\Project\Adapter;

abstract class Importer
{
    protected $data = array();
    protected $importData = array();
    protected $filename = "";
    protected $saveDir = "";
    protected $metadata = array();

    const IMPORT_OPTION_OVERWRITE = "project.import.override";
    const IMPORT_OPTION_DISABLE_AND_CREATE_NEW = "project.import.disable_and_create_new";
    const IMPORT_OPTION_CREATE_NEW = "project.import.create_new";

    /**
     * Success, Project imported successfully.
     */
    const IMPORT_STAT_SUCCESS = 100;
    /**
     * Error, Target Project already exists.
     */
    const IMPORT_STAT_TARGET_ALREADY_EXISTS = 101;
    /**
     * Error, Invalid file type or the file have corrupt data.
     */
    const IMPORT_STAT_INVALID_SOURCE_FILE = 102;

    public abstract function load();

    public function import($option = self::IMPORT_OPTION_CREATE_NEW)
    {
        $this->prepare();

        switch ($option) {
            case self::IMPORT_OPTION_CREATE_NEW:
                if ($this->targetExists()) {
                    throw new \Exception(sprintf(
                        "Project already exists, you need set an action to continue. " .
                        "Available actions: [%s|%s|%s].", self::IMPORT_OPTION_CREATE_NEW,
                        self::IMPORT_OPTION_OVERWRITE, self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW
                    ), self::IMPORT_STAT_TARGET_ALREADY_EXISTS);
                }
                $generateUid = false;
                $result = $this->doImport($generateUid);
                break;
            case self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW:
                $this->disableProject();
                // this option should generate new uid for all objects
                $generateUid = true;
                $result = $this->doImport($generateUid);
                break;
            case self::IMPORT_OPTION_OVERWRITE:
                // this option shouldn't generate new uid for all objects
                $generateUid = false;
                $this->removeProject();
                $result = $this->doImport($generateUid);
                break;
        }

        return $result;
    }

    /**
     * Prepare for import, it makes all validations needed
     * @return int
     * @throws \Exception
     */
    public function prepare()
    {
        if ($this->validateSource() === false) {
            throw new \Exception(
                "Error, Invalid file type or the file have corrupt data",
                self::IMPORT_STAT_INVALID_SOURCE_FILE
            );
        }

        $this->importData = $this->load();

        $this->validateImportData();
    }

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Validates the source file
     * @return mixed
     */
    public function validateSource()
    {
        return true;
    }

    public function validateImportData()
    {
        if (! isset($this->importData["tables"]["bpmn"])) {
            throw new \Exception("BPMN Definition is missing.");
        }
        if (! isset($this->importData["tables"]["bpmn"]["project"]) || count($this->importData["tables"]["bpmn"]["project"]) !== 1) {
            throw new \Exception("BPMN table: \"Project\", definition is missing or has multiple definition.");
        }

        return true;
    }

    /**
     * Verify if the project already exists
     * @return mixed
     */
    public function targetExists()
    {
        $prjUid = $this->importData["tables"]["bpmn"]["project"][0]["prj_uid"];

        $bpmnProject = \BpmnProjectPeer::retrieveByPK($prjUid);

        return is_object($bpmnProject);
    }

    public function updateProject()
    {

    }

    public function disableProject()
    {
        $project = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($this->metadata["uid"]);
        $project->setDisabled();
    }

    public function removeProject()
    {
        $project = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($this->metadata["uid"]);
        $force = true;
        $project->remove($force);
    }

    /**
     * Sets the temporal file save directory
     * @param $dirName
     */
    public function setSaveDir($dirName)
    {
        $this->saveDir = rtrim($dirName, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Gets the temporal file save directory
     * @return string
     */
    public function getSaveDir()
    {
        if (empty($this->saveDir)) {
            $this->saveDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        }

        return $this->saveDir;
    }

    /**
     * Sets the temporal source file
     * @param $filename
     */
    public function setSourceFile($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Set source from Global Http Request resource
     * @param $varName
     * @throws \Exception
     */
    public function setSourceFromGlobals($varName)
    {
        if (! array_key_exists($varName, $_FILES)) {
            throw new \Exception("Couldn't find specified source \"$varName\" in PHP Globals");
        }

        $data = $_FILES[$varName];

        if ($data["error"] != 0){
            throw new \Exception("Error while uploading file. Error code: {$data["error"]}");
        }

        $this->filename = $this->getSaveDir() . $data["name"];

        $oldUmask = umask(0);
        move_uploaded_file($data["tmp_name"], $this->filename);
        @chmod($this->filename, 0755);
        umask($oldUmask);
    }

    protected function importBpmnTables(array $tables, $generateUid = false)
    {
        // Build BPMN project struct
        $project = $tables["project"][0];
        $diagram = $tables["diagram"][0];
        $diagram["activities"] = $tables["activity"];
        $diagram["artifacts"] = array();
        $diagram["events"] = $tables["event"];
        $diagram["flows"] = $tables["flow"];
        $diagram["gateways"] = $tables["gateway"];
        $diagram["lanes"] = array();
        $diagram["laneset"] = array();
        $project["diagrams"] = array($diagram);
        $project["prj_author"] = isset($this->data["usr_uid"])? $this->data["usr_uid"]: "00000000000000000000000000000001";
        $project["process"] = $tables["process"][0];

        return Adapter\BpmnWorkflow::createFromStruct($project, $generateUid);
    }

    protected function importWfTables(array $tables)
    {
        $tables = (object) $tables;

        $processes = new \Processes();
        $processes->createProcessPropertiesFromData($tables);
    }

    protected function importWfFiles(array $workflowFiles)
    {
        foreach ($workflowFiles as $target => $files) {
            switch ($target) {
                case "dynaforms":
                    $basePath = PATH_DYNAFORM;
                    break;
                case "public":
                    $basePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP;
                    break;
                case "templates":
                    $basePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "mailTemplates" . PATH_SEP;
                    break;
                default:
                    $basePath = "";
            }

            if (empty($basePath)) continue;

            foreach ($files as $file) {
                $filename = $basePath . $file["file_path"];
                $path = dirname($filename);

                if (! is_dir($path)) {
                    Util\Common::mk_dir($path, 0775);
                }

                file_put_contents($filename, $file["file_content"]);
                chmod($filename, 0775);
            }
        }
    }

    public function doImport($generateUid = true)
    {
        $tables = $this->importData["tables"];
        $files = $this->importData["files"];

        $result = $this->importBpmnTables($tables["bpmn"], $generateUid);
        $this->importWfTables($tables["workflow"]);
        $this->importWfFiles($files["workflow"]);

        if ($generateUid) {
            return $result[0]["new_uid"];
        } else {
            return $result;
        }
    }
}