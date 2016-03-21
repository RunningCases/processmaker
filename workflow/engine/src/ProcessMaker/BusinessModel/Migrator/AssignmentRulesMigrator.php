<?php

namespace ProcessMaker\BusinessModel\Migrator;

/**
 * The assignment rules migrator class.
 * The container class that stores the import and export rules for assignment rules.
 *
 * Class AssignmentRulesMigrator
 * @package ProcessMaker\BusinessModel\Migrator
 */

class AssignmentRulesMigrator implements Importable
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
           Logger::log($e);
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }

}