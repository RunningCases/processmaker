<?php

namespace ProcessMaker\BusinessModel\Migrator;

/**
 * Class VariablesMigrator
 * @package ProcessMaker\BusinessModel\Migrator
 */

class VariablesMigrator implements Importable, Exportable
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
    public function import($data, $replace)
    {
        try {
            if ($replace) {
                $this->processes->createProcessVariables($data);
            } else {
                $this->processes->updateProcessVariables($data);
            }
        } catch (\Exception $e) {
            \Logger::log($e->getMessage());
            throw new ImportException($e->getMessage());
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
            $oData = new \StdClass();
            $oData->processVariables = $this->processes->getProcessVariables($prj_uid);

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