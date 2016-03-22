<?php

namespace ProcessMaker\BusinessModel\Migrator;

/**
 * The assignment rules migrator class.
 * The container class that stores the import and export rules for assignment rules.
 *
 * Class AssignmentRulesMigrator
 * @package ProcessMaker\BusinessModel\Migrator
 */

class AssignmentRulesMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * AssignmentRulesMigrator constructor.
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
            $this->processes->createTaskRows($data);
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
            $oData->process = $this->processes->getProcessRow($prj_uid, false);
            $oData->tasks = $this->processes->getTaskRows($prj_uid);
            $oData->routes = $this->processes->getRouteRows($prj_uid);
            $oData->lanes = $this->processes->getLaneRows($prj_uid);
            $oData->gateways = $this->processes->getGatewayRows($prj_uid);
            $oData->steps = $this->processes->getStepRows($prj_uid);
            $oData->triggers = $this->processes->getTriggerRows($prj_uid);
            $assignmentData = new \StdClass();
            $assignmentData->taskusers = $this->processes->getTaskUserRows($oData->tasks);

            $result = array(
                'workflow-definition' => (array)$assignmentData
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