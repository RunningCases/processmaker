<?php

namespace ProcessMaker\BusinessModel\Migrator;


class PermissionsMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * PermissionsMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Permissions';
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
            if ($replace) {
                $this->processes->createObjectPermissionsRows($data['objectPermissions']);
                $this->processes->addNewGroupRow($data['groupwfs']);
            } else {
                $this->processes->addNewObjectPermissionRows($data['objectPermissions']);
                $this->processes->addNewGroupRow($data['groupwfs']);
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
            //groups - permissions
            $oData->groupwfs = $this->processes->getGroupwfRows($oData->objectPermissions);


            $result = array(
                'workflow-definition' => (array)$oData
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