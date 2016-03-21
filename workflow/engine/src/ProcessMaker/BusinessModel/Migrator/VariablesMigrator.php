<?php

namespace ProcessMaker\BusinessModel\Migrator;

/**
 * Class VariablesMigrator
 * @package ProcessMaker\BusinessModel\Migrator
 */

class VariablesMigrator implements Importable
{
    protected $processes;

    /**
     * VariablesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
    }

    /**
     * beforeImport hook
     * @param $data
     */
    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    /**
     * Imports the process variables
     * @param $data
     */
    public function import($data)
    {
        try {
            $this->processes->createProcessVariables($data);
        } catch (\Exception $e) {
           Logger::log($e);
        }
    }

    /**
     * Hook to launch after the import process has just finished
     * @param $data
     */
    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }


}