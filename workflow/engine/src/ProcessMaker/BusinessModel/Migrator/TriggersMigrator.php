<?php

namespace ProcessMaker\BusinessModel\Migrator;

use Symfony\Component\Config\Definition\Exception\Exception;

class TriggersMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * TriggersMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Triggers';
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
                $this->processes->createTriggerRows($data);
            } else {
                $this->processes->addNewTriggerRows($data);
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
            $oData = new \StdClass();
            $tasks = $this->processes->getTaskRows($prj_uid);
            $oData->steptriggers = $this->processes->getStepTriggerRows($tasks);
            $oData->triggers = $this->processes->getTriggerRows($prj_uid);

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