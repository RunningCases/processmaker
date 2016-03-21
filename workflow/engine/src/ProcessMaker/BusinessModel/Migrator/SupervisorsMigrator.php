<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:38 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;


class SupervisorsMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * SupervisorsMigrator constructor.
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
            $oData->processUser = $this->processes->getProcessUser($prj_uid);

            $result = array(
                'workflow-definition' => (array)$oData
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