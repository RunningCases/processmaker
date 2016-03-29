<?php

namespace ProcessMaker\BusinessModel\Migrator;


class SupervisorsMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * SupervisorsMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Supervisor';
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
                $this->processes->createProcessUser($data['processUser']);
                $this->processes->addNewGroupRow($data['groupwfs']);
            } else {
                $this->processes->addNewProcessUser($data['processUser']);
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
            $oData = new \StdClass();
            $oData->processUser = $this->processes->getProcessUser($prj_uid);
            //groups - supervisor
            $oData->groupwfs = $this->processes->getGroupwfRows($oData->processUser);
            

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