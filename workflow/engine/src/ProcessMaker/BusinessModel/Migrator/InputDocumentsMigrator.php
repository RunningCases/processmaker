<?php

namespace ProcessMaker\BusinessModel\Migrator;

class InputDocumentsMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * InputDocumentsMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'InputDocument';
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
                $this->processes->createInputRows($data);
            } else {
                $this->processes->addNewInputRows($data);
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
            $oData->steps = $this->processes->getStepRowsByElement($prj_uid,'INPUT_DOCUMENT');
            $oData->inputs = $this->processes->getInputRows($prj_uid);

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