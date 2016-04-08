<?php

namespace ProcessMaker\BusinessModel\Migrator;

/**
 * Class VariablesMigrator
 * @package ProcessMaker\BusinessModel\Migrator
 */

class VariablesMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * VariablesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Variables';
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
     * @param $data
     * @param $replace
     * @throws ImportException
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
            $exception = new ImportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
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