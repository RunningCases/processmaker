<?php
namespace ProcessMaker\Exporter;

use ProcessMaker\Project;
use ProcessMaker\Util;

abstract class Exporter
{
    /**
     * @var string The Project UID
     */
    protected $prjUid;

    /**
     * Exporter version
     */
    const VERSION = "3.0";

    /**
     * @var \ProcessMaker\Project\Adapter\BpmnWorkflow
     */
    protected $bpmnProject;

    protected $projectData;

    public function __construct($prjUid)
    {
        $this->prjUid = $prjUid;

        $this->bpmnProject = Project\Bpmn::load($prjUid);
        $this->projectData = $this->bpmnProject->getProject();
    }

    /**
     * Builds Output content of exported project
     *
     * @return string xml output of exported project
     */
    public abstract function export();

    /**
     * Builds Output content of exported project and save it into a given file path
     *
     * @param string $outputFile path of output file
     * @return mixed
     */
    public abstract function saveExport($outputFile);

    /**
     * Builds exported content of a Project
     *
     * @return mixed
     */
    public abstract function build();

    public function getProjectName()
    {
        return $this->projectData["PRJ_NAME"];
    }

    public function getProjectUid()
    {
        return $this->projectData["PRJ_UID"];
    }

    /**
     * Builds Project Data Structure
     *
     * @return array
     */
    protected function buildData()
    {
        $data = array();

        $data["metadata"] = $this->getMetadata();
        $data["metadata"]["name"] = $this->getProjectName();
        $data["metadata"]["uid"] = $this->getProjectUid();

        $bpmnStruct["ACTIVITY"] = \BpmnActivity::getAll($this->prjUid);
        $bpmnStruct["BOUND"] = \BpmnBound::getAll($this->prjUid);
        $bpmnStruct["DATA"] = array();
        $bpmnStruct["DIAGRAM"] = \BpmnDiagram::getAll($this->prjUid);
        $bpmnStruct["DOCUMENTATION"] = array();
        $bpmnStruct["EVENT"] = \BpmnEvent::getAll($this->prjUid);
        $bpmnStruct["EXTENSION"] = array();
        $bpmnStruct["FLOW"] = \BpmnFlow::getAll($this->prjUid, null, null, "", CASE_UPPER, false);
        $bpmnStruct["GATEWAY"] = \BpmnGateway::getAll($this->prjUid);
        $bpmnStruct["LANE"] = array();
        $bpmnStruct["LANESET"] = array();
        $bpmnStruct["PARTICIPANT"] = array();
        $bpmnStruct["PROCESS"] = \BpmnProcess::getAll($this->prjUid);
        $bpmnStruct["PROJECT"] = array(\BpmnProjectPeer::retrieveByPK($this->prjUid)->toArray());

        $oProcess = new \Processes();
        $workflowData = (array) $oProcess->getWorkflowData($this->prjUid);
        $workflowData["process"]['PRO_DYNAFORMS'] = empty($workflowData["process"]['PRO_DYNAFORMS'])
            ? "" : serialize($workflowData["process"]['PRO_DYNAFORMS']);

        $workflowData["process"] = array($workflowData["process"]);
        $workflowData["processCategory"] = empty($workflowData["processCategory"]) ? array() : $workflowData["processCategory"];


        $data["bpmn-definition"] = $bpmnStruct;
        $data["workflow-definition"] = $workflowData;
        $data["workflow-files"] = array();

        // getting dynaforms
        foreach ($workflowData["dynaforms"] as $dynaform) {
            $dynFile = PATH_DYNAFORM . $dynaform['DYN_FILENAME'] . '.xml';
            $data["workflow-files"]["DYNAFORMS"][] = array(
                "filename" => $dynaform['DYN_TITLE'],
                "filepath" => $dynaform['DYN_FILENAME'] . '.xml',
                "file_content" => file_get_contents($dynFile)
            );

            $htmlFile = PATH_DYNAFORM . $dynaform['DYN_FILENAME'] . '.html';

            if (file_exists($htmlFile)) {
                $data["workflow-files"]["DYNAFORMS"][] = array(
                    "filename" => $dynaform['DYN_FILENAME'] . '.html',
                    "filepath" => $dynaform['DYN_FILENAME'] . '.html',
                    "file_content" => file_get_contents($htmlFile)
                );
            }
        }

        // getting templates files
        $workspaceTargetDirs = array("TEMPLATES" => "mailTemplates", "PUBLIC" => "public");
        $workspaceDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP;

        foreach ($workspaceTargetDirs as $target => $workspaceTargetDir) {
            $templatesDir = $workspaceDir . $workspaceTargetDir . PATH_SEP . $this->prjUid;
            $templatesFiles = Util\Common::rglob("$templatesDir/*", 0, true);

            foreach ($templatesFiles as $templatesFile) {
                if (is_dir($templatesFile)) continue;
                $filename = basename($templatesFile);
                $data["workflow-files"][$target][] = array(
                    "filename" => $filename,
                    "filepath" => $this->prjUid . PATH_SEP . $filename,
                    "file_content" => file_get_contents($templatesFile)
                );
            }
        }

        return $data;
    }

    /**
     * Returns the container name of project data structure
     *
     * @return string
     */
    public static function getContainerName()
    {
        return "ProcessMaker-Project";
    }

    /**
     * Returns the exporter version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Returns all metadata to include on export content
     *
     * @return array
     */
    public function getMetadata()
    {
        return array(
            "vendor_version" => \System::getVersion(),
            "vendor_version_code" => "Michelangelo",
            "export_timestamp" => date("U"),
            "export_datetime" => date("Y-m-d\TH:i:sP)"),
            "export_server_addr" => isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"].":".$_SERVER["SERVER_PORT"] : "Unknown",
            "export_server_os" => PHP_OS ,
            "export_server_php_version" => PHP_VERSION_ID,
            "workspace" => defined("SYS_SYS") ? SYS_SYS : "Unknown",
        );
    }
}