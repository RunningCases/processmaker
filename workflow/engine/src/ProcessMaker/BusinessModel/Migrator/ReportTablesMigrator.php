<?php

namespace ProcessMaker\BusinessModel\Migrator;

class ReportTablesMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * ReportTablesMigrator constructor.
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
            $aReportTablesVars = array();
            if ($replace) {
                $this->processes->createReportTables($data, $aReportTablesVars);
            } else {
                $this->processes->updateReportTables($data, $aReportTablesVars);
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
            $oData->reportTables = $this->processes->getReportTables($prj_uid);
            $oData->reportTablesVars = $this->processes->getReportTablesVar($prj_uid);

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