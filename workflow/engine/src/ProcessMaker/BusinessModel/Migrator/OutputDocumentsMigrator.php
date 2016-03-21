<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:30 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use Symfony\Component\Config\Definition\Exception\Exception;

class OutputDocumentsMigrator implements Importable, Exportable
{
     protected $processes;

    /**
     * OutputDocumentsMigrator constructor.
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
            $this->processes->createOutputRows($data);
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

    /**
     * @param $prj_uid
     * @return array
     */
    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $oData->outputs = $this->processes->getOutputRows($prj_uid);

            $result = array(
                'workflow-definition' => (array)$oData->outputs
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