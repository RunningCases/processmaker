<?php

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Util;


class TemplatesMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * TemplatesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Templates';
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
                if ($value['PRF_EDITABLE'] === '1') {
                    if ($replace) {
                        $this->processes->createFilesManager($value['PRO_UID'], array($value));
                    } else {
                        $this->processes->addNewFilesManager($value['PRO_UID'], array($value));
                    }
                }
            }
            $aPath = $data['PATH'];
            foreach ($aPath as $target => $files) {
                $basePath = PATH_DATA . 'sites' . PATH_SEP . config("system.workspace") . PATH_SEP . 'mailTemplates' . PATH_SEP;
                if (strtoupper($target) === 'TEMPLATE') {
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
            $arrayExcludeFile = array();
            $oData->filesManager = $this->processes->getFilesManager($prj_uid, 'template');

            $fileHandler = new FileHandler();
            $workflowFile = $fileHandler->getTemplatesOrPublicFiles($prj_uid, $arrayExcludeFile, 'template');

            $result = array(
                'workflow-definition' => (array)$oData,
                'workflow-files' => $workflowFile
            );

            return $result;

        } catch (\Exception $e) {
            throw new ExportException($e->getMessage());
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }
}