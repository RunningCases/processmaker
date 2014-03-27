<?php
namespace ProcessMaker\Importer;

abstract class Importer
{
    protected $filename = "";
    protected $saveDir = "";

    const IMPORT_OPTION_OVERWRITE = "OVERWRITE_PROJECT";
    const IMPORT_OPTION_DISABLE_AND_CREATE_NEW = "DISABLE_AND_CREATE_NEW_PROJECT";
    const IMPORT_OPTION_CREATE_NEW = "CREATE_NEW_PROJECT";

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


    public function import($option = self::IMPORT_OPTION_CREATE_NEW)
    {
        switch ($option) {
            case self::IMPORT_OPTION_CREATE_NEW:
                $this->prepare();
                $this->createNewProject();
                break;
            case self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW:
                break;
            case self::IMPORT_OPTION_OVERWRITE:
                break;
        }
    }

    /**
     * Validates the source file
     * @return mixed
     */
    public function validateSource()
    {
        return true;
    }

    /**
     * Verify if the project already exists
     * @return mixed
     */
    public function targetExists()
    {
        return false;
    }

    public function createNewProject()
    {

    }

    public function updateProject()
    {

    }

    public function disableCurrentProject()
    {

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
        /*[PROCESS_FILENAME] => Array
        (
            [name] => sample29.pm
            [type] => application/pm
            [tmp_name] => /tmp/phpvHpCVO
            [error] => 0
            [size] => 1260881
        )*/

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

        if ($this->targetExists()) {
            throw new \Exception(sprintf(
                "Project already exists, you need set an action to continue. " .
                "Avaliable actions: [%s|%s|%s].", self::IMPORT_OPTION_CREATE_NEW,
                self::IMPORT_OPTION_OVERWRITE, self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW
            ), self::IMPORT_STAT_TARGET_ALREADY_EXISTS);
        }

        return self::IMPORT_STAT_SUCCESS;
    }
}