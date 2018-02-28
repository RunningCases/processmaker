<?php

namespace ProcessMaker\BusinessModel\Migrator;

class ReportTablesMigrator implements Importable, Exportable
{
    protected $processes;
    protected $className;

    /**
     * ReportTablesMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
        $this->className = 'ReportTables';
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
            $reportTable = new \ProcessMaker\BusinessModel\ReportTable();
            $arrayTableSchema = [];
            $arrayTablesToExclude = [];
            $processUid = '';

            foreach ($data['reportTablesDefinition'] as $value) {
                $arrayTable = $value;

                $processUid = $arrayTable['PRO_UID'];

                $arrayField = [];

                foreach ($data['reportTablesFields'] as $value2) {
                    if ($value2['ADD_TAB_UID'] == $arrayTable['ADD_TAB_UID']) {
                        unset($value2['ADD_TAB_UID']);

                        $arrayField[] = $value2;
                    }
                }

                if (!empty($arrayField)) {
                    $arrayTable['FIELDS'] = $arrayField;

                    $arrayTableSchema[] = $arrayTable;

                    //$replace: true  //Delete all tables and create it again
                    //$replace: false //Only create the tables that do not exist
                    if (!$replace) {
                        $additionalTable = new \AdditionalTables();

                        if ($additionalTable->loadByName($arrayTable['ADD_TAB_NAME'])) {
                            $arrayTablesToExclude[] = $arrayTable['ADD_TAB_NAME'];
                        }
                    }
                }
            }

            if (!empty($arrayTableSchema)) {
                $errors = $reportTable->createStructureOfTables(
                    $arrayTableSchema, [], $processUid, false, true, $arrayTablesToExclude
                );

                if ($errors != '') {
                    throw new \Exception($errors);
                }
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

            $oData->reportTablesDefinition = $this->processes->getReportTables($prj_uid);
            $oData->reportTablesFields = $this->processes->getReportTablesVar($prj_uid);

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