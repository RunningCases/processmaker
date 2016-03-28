<?php

namespace ProcessMaker\BusinessModel\Migrator;

class InputDocumentsMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * InputDocumentsMigrator constructor.
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
                $this->processes->createInputRows($data);
            } else {
                $this->processes->addNewInputRows($data);
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
     * @throws ExportException
     */
    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $oData->inputs = $this->processes->getInputRows($prj_uid);

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