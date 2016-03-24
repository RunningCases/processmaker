<?php

namespace ProcessMaker\BusinessModel\Migrator;


class SupervisorsObjectsMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * SupervisorsObjectsMigrator constructor.
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
     */
    public function import($data, $replace)
    {
        try {
            if ($replace) {
                $this->processes->createStepSupervisorRows($data);
            } else {
                $this->processes->updateStepSupervisorRows($data);
            }
        } catch (\Exception $e) {
            \Logger::log($e->getMessage());
            throwException(new ImportException($e->getMessage()));
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
     */
    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $oData->stepSupervisor = $this->processes->getStepSupervisorRows($prj_uid);

            $result = array(
                'workflow-definition' => (array)$oData
            );

            return $result;

        } catch (\Exception $e) {
            \Logger::log($e->getMessage());
            throwException(new ExportException($e->getMessage()));
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}