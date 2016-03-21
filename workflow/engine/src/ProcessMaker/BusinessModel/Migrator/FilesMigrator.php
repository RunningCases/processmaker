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
            $this->processes->createFilesManager($data[0]['PRO_UID'],$data);
        } catch (\Exception $e) {
           Logger::log($e);
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
            \Logger::log($e);
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }
}