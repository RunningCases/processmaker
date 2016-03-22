<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:28 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use Symfony\Component\Config\Definition\Exception\Exception;

class TemplatesMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * TemplatesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data)
    {
        try {
            //TABLE
            $aTable = $data['TABLE'];
            foreach ($aTable as $value) {
                if($value['PRF_EDITABLE'] === 1){
                    $this->processes->createFilesManager($value['PRO_UID'],array($value));
                }
            }
            $aPath = $data['PATH'];
            foreach ($aPath as $target => $files) {
                $basePath = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'mailTemplates' . PATH_SEP;
                if(strtoupper($target) === 'TEMPLATES'){
                    foreach ($files as $file) {
                        $filename = $basePath . ((isset($file["file_path"]))? $file["file_path"] : $file["filepath"]);
                        $path = dirname($filename);

                        if (!is_dir($path)) {
                            Util\Common::mk_dir($path, 0775);
                        }

                        file_put_contents($filename, $file["file_content"]);
                        chmod($filename, 0775);
                    }
                }
            }
        } catch (\Exception $e) {
            \Logger::log($e->getMessage());
            throw new ImportException($e->getMessage());
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
     */
    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $arrayExcludeFile = array();
            $oData->filesManager = $this->processes->getFilesManager($prj_uid, 'TEMPLATES');

            $fileHandler = new FileHandler();
            $workflowFile = $fileHandler->getTemplatesOrPublicFiles($prj_uid, $arrayExcludeFile, 'TEMPLATES');

            $result = array(
                'workflow-definition' => (array)$oData,
                'workflow-files' => $workflowFile
            );

            return $result;

        } catch (\Exception $e) {
            \Logger::log($e->getMessage());
            throw new ExportException($e->getMessage());
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }
}