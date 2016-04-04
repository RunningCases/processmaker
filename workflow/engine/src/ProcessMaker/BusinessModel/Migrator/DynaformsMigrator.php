<?php

namespace ProcessMaker\BusinessModel\Migrator;

class DynaformsMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * DynaformsMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'Dynaforms';
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
                $this->processes->createDynaformRows($data);
            } else {
                $this->processes->addNewDynaformRows($data);
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
            $oData->steps = $this->processes->getStepRowsByElement($prj_uid,'DYNAFORM');
            $oData->dynaforms = $this->processes->getDynaformRows($prj_uid);
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