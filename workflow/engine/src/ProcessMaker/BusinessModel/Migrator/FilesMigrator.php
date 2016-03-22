<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:21 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\BusinessModel\Process;
use Symfony\Component\Config\Definition\Exception\Exception;
use \ProcessMaker\BusinessModel\Migrator\FileHandler;

class FilesMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * FilesMigrator constructor.
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
            $aTable = $data['TABLE'];
            foreach ($aTable as $value) {
                if($value['PRF_EDITABLE'] !== 1){
                    $this->processes->createFilesManager($value['PRO_UID'],array($value));
                }
            }
            $aPath = $data['PATH'];
            foreach ($aPath as $target => $files) {
                $basePath = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
                if(strtoupper($target) === 'PUBLIC'){
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

    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $oData->filesManager = $this->processes->getFilesManager($prj_uid, 'PUBLIC');

            $fileHandler = new FileHandler();
            $arrayPublicFileToExclude = $fileHandler->getFilesToExclude($prj_uid);
            $workflowFile = $fileHandler->getTemplatesOrPublicFiles($prj_uid, $arrayPublicFileToExclude, 'PUBLIC');

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