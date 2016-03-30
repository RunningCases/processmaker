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
            $aReportTablesVars = array();
            if ($replace) {
                //Todo Create
            } else {
                //Todo addOnlyNew
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
            $oDataReportTables = $this->processes->getReportTables($prj_uid);
            $oData->reportContent[0] = $this->getData($oDataReportTables);

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

    /**
     * @param $oDataReportTables
     * @return array
     * @throws ExportException
     */
    public function getData($oDataReportTables)
    {
        $oData = array();
        $at = new \AdditionalTables();
        try {
            \G::LoadCLass('net');
            $net = new \NET(\G::getIpAddress());
            \G::LoadClass("system");
            $META = " \n-----== ProcessMaker Open Source Private Tables ==-----\n" . " @Ver: 1.0 Oct-2009\n" . " @Processmaker version: " . \System::getVersion() . "\n" . " -------------------------------------------------------\n" . " @Export Date: " . date("l jS \of F Y h:i:s A") . "\n" . " @Server address: " . getenv('SERVER_NAME') . " (" . getenv('SERVER_ADDR') . ")\n" . " @Client address: " . $net->hostname . "\n" . " @Workspace: " . SYS_SYS . "\n" . " @Export trace back:\n\n";
            $EXPORT_TRACEBACK = Array();
            foreach ($oDataReportTables as $table) {
                $tableRecord = $at->load($table['ADD_TAB_UID']);
                $tableData = $at->getAllData($table['ADD_TAB_UID'], null, null, false);
                $table['ADD_TAB_NAME'] = $tableRecord['ADD_TAB_NAME'];
                $rows = $tableData['rows'];
                $count = $tableData['count'];
                array_push($EXPORT_TRACEBACK, Array('uid' => $table['ADD_TAB_UID'], 'name' => $table['ADD_TAB_NAME'],
                    'num_regs' => $tableData['count'], 'schema' => 'yes', 'data' => 'no'));
            }
            $sTrace = "TABLE UID                        TABLE NAME\tREGS\tSCHEMA\tDATA\n";
            foreach ($EXPORT_TRACEBACK as $row) {
                $sTrace .= "{$row['uid']}\t{$row['name']}\t\t{$row['num_regs']}\t{$row['schema']}\t{$row['data']}\n";
            }

            $META .= $sTrace;
            $PUBLIC_ROOT_PATH = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP . 'public' . PATH_SEP;
            $filenameOnly = strtolower('SYS-' . SYS_SYS . "_" . date("Y-m-d") . '_' . date("Hi") . ".pmt");
            $filename = $PUBLIC_ROOT_PATH . $filenameOnly;
            $fp = fopen($filename, "wb");

            $bytesSaved = 0;
            $bufferType = '@META';
            $fsData = sprintf("%09d", strlen($META));
            $fsbufferType = sprintf("%09d", strlen($bufferType));
            $bytesSaved += fwrite($fp, $fsbufferType);
            $bytesSaved += fwrite($fp, $bufferType);
            $bytesSaved += fwrite($fp, $fsData);
            $bytesSaved += fwrite($fp, $META);

            foreach ($oDataReportTables as $table) {
                $oAdditionalTables = new \AdditionalTables();
                $aData = $oAdditionalTables->load($table['ADD_TAB_UID'], true);
                $bufferType = '@SCHEMA';
                $SDATA = serialize($aData);
                $fsUid = sprintf("%09d", strlen($table['ADD_TAB_UID']));
                $fsData = sprintf("%09d", strlen($SDATA));
                $fsbufferType = sprintf("%09d", strlen($bufferType));
                $bytesSaved += fwrite($fp, $fsbufferType);
                $bytesSaved += fwrite($fp, $bufferType);
                $bytesSaved += fwrite($fp, $fsUid);
                $bytesSaved += fwrite($fp, $table['ADD_TAB_UID']);
                $bytesSaved += fwrite($fp, $fsData);
                $bytesSaved += fwrite($fp, $SDATA);
            }
            $oData['REPORTDATA'] = file_get_contents($filename);
            fclose($fp);
            return $oData;

        } catch (\Exception $e) {
            $exception = new ExportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

}