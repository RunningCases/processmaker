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

    /**
     * @param $data
     * @param $replace
     * @throws ImportException
     */
    public function import($data, $replace)
    {
        try {
            if ($replace) {
                $this->processes->createTaskRows($data);
            } else {
                $this->processes->updateTaskRows($data);
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
     * @throws ExportException
     */
    public function export($prj_uid)
    {
        try {
            $oAssignRules = new \StdClass();
            $oAssignRules->tasks = $this->processes->getTaskRows($prj_uid);
            $oAssignRules->taskusers = $this->processes->getTaskUserRows($oAssignRules->tasks);

            $result = array(
                'workflow-definition' => (array)$oAssignRules
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