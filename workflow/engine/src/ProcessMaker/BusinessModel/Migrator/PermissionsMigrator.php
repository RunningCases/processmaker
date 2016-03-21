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
            $oData->process = $this->processes->getProcessRow($prj_uid, false);
            $oData->tasks = $this->processes->getTaskRows($prj_uid);
            $oData->routes = $this->processes->getRouteRows($prj_uid);
            $oData->lanes = $this->processes->getLaneRows($prj_uid);
            $oData->gateways = $this->processes->getGatewayRows($prj_uid);
            $oData->steps = $this->processes->getStepRows($prj_uid);
            $oData->taskusers = $this->processes->getTaskUserRows($oData->tasks);
            $oData->groupwfs = $this->processes->getGroupwfRows($oData->taskusers);
            $oData->steptriggers = $this->processes->getStepTriggerRows($oData->tasks);
            $oData->reportTablesVars = $this->processes->getReportTablesVarsRows($prj_uid);
            $oData->objectPermissions = $this->processes->getObjectPermissionRows($prj_uid, $oData);

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