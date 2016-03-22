<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:39 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;


class PermissionsMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * PermissionsMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data, $replace)
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
     * @throws ExportException
     */
    public function export($prj_uid)
    {
        try {
            $processData = new \StdClass();
            $processData->process = $this->processes->getProcessRow($prj_uid, false);
            $processData->tasks = $this->processes->getTaskRows($prj_uid);
            $processData->routes = $this->processes->getRouteRows($prj_uid);
            $processData->lanes = $this->processes->getLaneRows($prj_uid);
            $processData->gateways = $this->processes->getGatewayRows($prj_uid);
            $processData->steps = $this->processes->getStepRows($prj_uid);
            $processData->taskusers = $this->processes->getTaskUserRows($processData->tasks);
            $processData->groupwfs = $this->processes->getGroupwfRows($processData->taskusers);
            $processData->steptriggers = $this->processes->getStepTriggerRows($processData->tasks);
            $processData->reportTablesVars = $this->processes->getReportTablesVarsRows($prj_uid);
            $oData = new \StdClass();
            $oData->objectPermissions = $this->processes->getObjectPermissionRows($prj_uid, $processData);

            $result = array(
                'workflow-definition' => (array)$oData
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