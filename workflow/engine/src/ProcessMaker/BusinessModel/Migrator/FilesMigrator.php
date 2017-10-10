<?php

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Util;
use \ProcessMaker\BusinessModel\Migrator\FileHandler;

class FilesMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * FilesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Public Files';
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    /**
     * @param $data
     * @param $replace
     * @throws ImportException
     */
    public function import($data, $replace)
    {
        try {
            $aTable = $data['TABLE'];
            foreach ($aTable as $value) {
                if ($value['PRF_EDITABLE'] === '0') {
                    if ($replace) {
                        $this->processes->createFilesManager($value['PRO_UID'], array($value));
                    } else {
                        $this->processes->addNewFilesManager($value['PRO_UID'], array($value));
                    }
                }
            }
            $aPath = $data['PATH'];
            foreach ($aPath as $target => $files) {
                $basePath = PATH_DATA . 'sites' . PATH_SEP . config("system.workspace") . PATH_SEP . 'public' . PATH_SEP;
                if (strtoupper($target) === 'PUBLIC') {
                    foreach ($files as $file) {
                        $filename = $basePath . ((isset($file["file_path"])) ? $file["file_path"] : $file["filepath"]);
                        $path = dirname($filename);
                        if (!is_dir($path)) {
                            Util\Common::mk_dir($path, 0775);
                        }
                        if (file_exists($filename)) {
                            if ($replace) {
                                file_put_contents($filename, $file["file_content"]);
                            }
                        } else {
                            file_put_contents($filename, $file["file_content"]);
                        }


                        @chmod($filename, 0775);
                    }
                }
            }
        } catch (\Exception $e) {
            $exception = new ImportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }

    public function beforeExport()
    {
        // TODO: Implement beforeExport() method.
    }

    /**
     * @param $prj_uid
     * @return array
     * @throws ExportException
     */
    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $oData->filesManager = $this->processes->getFilesManager($prj_uid, 'public');

            $fileHandler = new FileHandler();
            $arrayPublicFileToExclude = $fileHandler->getFilesToExclude($prj_uid);
            $workflowFile = $fileHandler->getTemplatesOrPublicFiles($prj_uid, $arrayPublicFileToExclude, 'PUBLIC');

            $result = array(
                'workflow-definition' => (array)$oData,
                'workflow-files' => $workflowFile
            );

            return $result;

        } catch (\Exception $e) {
            $exception = new ExportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }
}