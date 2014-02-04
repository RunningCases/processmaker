<?php
namespace BusinessModel;

use \G;
use \AdditionalTables;
use \Fields;

class ReportTable
{
    /**
     * List of ReportTables in process
     * @var string $sProcessUid. Uid for Process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getReportTables($sProcessUid)
    {
        $oDBSource = new DbSource();
        $oCriteria = $oDBSource->getCriteriaDBSList($sProcessUid);
        
        $rs = \DbSourcePeer::doSelectRS($oCriteria);
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $rs->next();

        $dbConnecions = array();
        while ($row = $rs->getRow()) {
            $row = array_change_key_case($row, CASE_LOWER);
            $dataDb = $this->getReportTable($sProcessUid, $row['dbs_uid']);
            $dbConnecions[] = array_change_key_case($dataDb, CASE_LOWER);
            $rs->next();
        }
        return $dbConnecions;
    }

    /**
     * Get data for ReportTable
     * @var string $sProcessUid. Uid for Process
     * @var string $dbConnecionUid. Uid for Data Base Connection
     *
     * return object
     */
    public function getReportTable($sProcessUid, $dbConnecionUid)
    {
        try {
            G::LoadClass( 'dbConnections' );
            $dbs = new dbConnections($sProcessUid);
            $oDBConnection = new DbSource();
            $aFields = $oDBConnection->load($dbConnecionUid, $sProcessUid);
            if ($aFields['DBS_PORT'] == '0') {
                $aFields['DBS_PORT'] = '';
            }
            $aFields['DBS_PASSWORD'] = $dbs->getPassWithoutEncrypt($aFields);

            $response = array_change_key_case($aFields, CASE_LOWER);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save Data for ReportTable
     * @var string $processUid. Uid for Process
     * @var string $dataReportTable. Data for ReportTable
     * @var string $create. Create o Update ReportTable
     * @var string $sReportTableUid. Uid for ReportTable
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function createReportTable($processUid, $dataReportTable)
    {
        $dataValidate =  array();
        $oAdditionalTables = new AdditionalTables();
        $oFields = new Fields();

        $dataValidate = array_change_key_case($dataReportTable, CASE_UPPER);

        $dataValidate['PRO_UID'] = trim($processUid);        
        $this->validateProUid($dataValidate['PRO_UID']);

        $repTabClassName = $oAdditionalTables->getPHPName($dataValidate['REP_TAB_NAME']);
        $columns = $dataValidate['COLUMNS'];


        // Reserved Words Table, Field, Sql
        $reservedWords = array ('ALTER','CLOSE','COMMIT','CREATE','DECLARE','DELETE',
            'DROP','FETCH','FUNCTION','GRANT','INDEX','INSERT','OPEN','REVOKE','ROLLBACK',
            'SELECT','SYNONYM','TABLE','UPDATE','VIEW','APP_UID','ROW','PMTABLE');
        $reservedWordsPhp = array ('case','catch','cfunction','class','clone','const','continue',
            'declare','default','do','else','elseif','enddeclare','endfor','endforeach','endif',
            'endswitch','endwhile','extends','final','for','foreach','function','global','goto',
            'if','implements','interface','instanceof','private','namespace','new','old_function',
            'or','throw','protected','public','static','switch','xor','try','use','var','while');
        $reservedWordsSql = G::reservedWordsSql();

        $defaultColumns = $this->getReportTableDefaultColumns($data['REP_TAB_TYPE']);
        $columns = array_merge( $defaultColumns, $columns );

        // validations
        if (is_array( $oAdditionalTables->loadByName( $data['REP_TAB_NAME'] ) )) {
            throw new \Exception(G::loadTranslation('ID_PMTABLE_ALREADY_EXISTS', array($data['REP_TAB_NAME'])));
        }

        if (in_array( strtoupper( $data["REP_TAB_NAME"] ), $reservedWords ) || 
            in_array( strtoupper( $data["REP_TAB_NAME"] ), $reservedWordsSql )) {
            throw (new \Exception(G::LoadTranslation("ID_PMTABLE_INVALID_NAME", array($data["REP_TAB_NAME"]))));
        }


        //backward compatility
        $columnsStd = array();
        foreach ($columns as $i => $column) {
            if (in_array(strtoupper($columns[$i]['field_name']), $reservedWordsSql) || 
                in_array( strtolower( $columns[$i]['field_name']), $reservedWordsPhp )) {
                throw (new \Exception(G::LoadTranslation("ID_PMTABLE_INVALID_FIELD_NAME", array($columns[$i]['field_name']))));
            }

            switch ($column['field_type']) {
                case 'INT':
                    $columns[$i]['field_type'] = 'INTEGER';
                    break;
                case 'TEXT':
                    $columns[$i]['field_type'] = 'LONGVARCHAR';
                    break;
                // propel DATETIME equivalent is TIMESTAMP
                case 'DATETIME':
                    $columns[$i]['field_type'] = 'TIMESTAMP';
                    break;
            }

            // VALIDATIONS
            if ($columns[$i]['field_autoincrement']) {
                $typeCol = $columns[$i]['field_type'];
                if (! ($typeCol === 'INTEGER' || $typeCol === 'TINYINT' || $typeCol === 'SMALLINT' || $typeCol === 'BIGINT')) {
                    $columns[$i]['field_autoincrement'] = false;
                }
            }


            $temp = new \stdClass();
            foreach ($column as $key => $valCol) {
                eval('$temp->' . $key . " = '" . $valCol . "';");
            }
            $temp->uid = (isset($temp->uid)) ? $temp->uid : '';
            $temp->_index = (isset($temp->_index)) ? $temp->_index : '';
            $temp->field_uid = (isset($temp->field_uid)) ? $temp->field_uid : '';
            $temp->field_dyn = (isset($temp->field_dyn)) ? $temp->field_dyn : '';
            $temp->field_filter = (isset($temp->field_filter)) ? $temp->field_filter : '';
            $temp->field_autoincrement = (isset($temp->field_autoincrement)) ? $temp->field_autoincrement : '';
            $columnsStd[$i] = $temp;
        }

        G::LoadClass("pmTable");
        $pmTable = new \pmTable($dataValidate['REP_TAB_NAME']);
        $pmTable->setDataSource($dataValidate['REP_TAB_CONNECTION']);
        $pmTable->setColumns($columnsStd);
        $pmTable->setAlterTable(true);
        $pmTable->build();
        $buildResult = ob_get_contents();
        ob_end_clean();

        // Updating additional table struture information
        $addTabData = array(
            'ADD_TAB_UID' => $dataValidate['REP_TAB_UID'],
            'ADD_TAB_NAME' => $dataValidate['REP_TAB_NAME'],
            'ADD_TAB_CLASS_NAME' => $repTabClassName,
            'ADD_TAB_DESCRIPTION' => $dataValidate['REP_TAB_DSC'],
            'ADD_TAB_PLG_UID' => '',
            'DBS_UID' => ($dataValidate['REP_TAB_CONNECTION'] ? $dataValidate['REP_TAB_CONNECTION'] : 'workflow'),
            'PRO_UID' => $dataValidate['PRO_UID'],
            'ADD_TAB_TYPE' => $dataValidate['REP_TAB_TYPE'],
            'ADD_TAB_GRID' => $dataValidate['REP_TAB_GRID']
        );
        //new report table
        //create record
        $addTabUid = $oAdditionalTables->create( $addTabData );

        // Updating pmtable fields
        foreach ($columnsStd as $i => $column) {
            $field = array (
                'FLD_UID' => $column['uid'],
                'FLD_INDEX' => $i,
                'ADD_TAB_UID' => $addTabUid,
                'FLD_NAME' => $column['field_name'],
                'FLD_DESCRIPTION' => $column['field_label'],
                'FLD_TYPE' => $column['field_type'],
                'FLD_SIZE' => $column['field_size'] == '' ? null : $column['field_size'],
                'FLD_NULL' => $column['field_null'] ? 1 : 0,
                'FLD_AUTO_INCREMENT' => $column['field_autoincrement'] ? 1 : 0,
                'FLD_KEY' => $column['field_key'] ? 1 : 0,
                'FLD_FOREIGN_KEY' => 0,
                'FLD_FOREIGN_KEY_TABLE' => '',
                'FLD_DYN_NAME' => $column['field_dyn'],
                'FLD_DYN_UID' => $column['field_uid'],
                'FLD_FILTER' => (isset($column['field_filter']) && $column['field_filter']) ? 1 : 0
            );
            $oFields->create( $field );
        }

        try {
            $oAdditionalTables->populateReportTable(
                $data['REP_TAB_NAME'],
                $pmTable->getDataSource(),
                $data['REP_TAB_TYPE'],
                $data['PRO_UID'],
                $data['REP_TAB_GRID'],
                $addTabUid
            );
        } catch (\Exception $e) {
            $result->message = $result->msg = $e->getMessage();
        }
        die('funciona cochalo');
    }

    /**
     * Delete ReportTable
     * @var string $sReportTableUID. Uid for ReportTable
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteReportTable($sProcessUid, $reportTableUid)
    {
        $at = new AdditionalTables();
        $table = $at->load( $reportTableUid );

        if (! isset( $table )) {
            require_once 'classes/model/ReportTable.php';
            $rtOld = new ReportTable();
            $existReportTableOld = $rtOld->load( $reportTableUid );
            if (count($existReportTableOld) == 0) {
                throw new Exception( G::LoadTranslation('ID_TABLE_NOT_EXIST_SKIPPED') );
            }
        }
        $at->deleteAll( $reportTableUid );
    }


    public function testConnection($dataCon) 
    {
        $resp = array();
        $resp['resp'] = false;

        G::LoadClass( 'net' );
        $Server = new \NET($dataCon['DBS_SERVER']);

        // STEP 1 : Resolving Host Name
        if ($Server->getErrno() != 0) {
            $resp['message'] = "Error Testting Connection: Resolving Host Name FAILED : " . $Server->error;
            return $resp;
        }

        // STEP 2 : Checking port
        $Server->scannPort($dataCon['DBS_PORT']);
        if ($Server->getErrno() != 0) {
            $resp['message'] = "Error Testting Connection: Checking port FAILED : " . $Server->error;
            return $resp;
        }

        // STEP 3 : Trying to connect to host
        $Server->loginDbServer($dataCon['DBS_USERNAME'], $dataCon['DBS_PASSWORD']);
        $Server->setDataBase($dataCon['DBS_DATABASE_NAME'], $dataCon['DBS_PORT']);
        if ($Server->errno == 0) {
            $response = $Server->tryConnectServer($dataCon['DBS_TYPE']);
            if ($response->status != 'SUCCESS') {
                $resp['message'] = "Error Testting Connection: Trying to connect to host FAILED : " . $Server->error;
                return $resp;
            }
        } else {
            $resp['message'] = "Error Testting Connection: Trying to connect to host FAILED : " . $Server->error;
            return $resp;
        }
                
        // STEP 4 : Trying to open database
        $Server->loginDbServer($dataCon['DBS_USERNAME'], $dataCon['DBS_PASSWORD']);
        $Server->setDataBase($dataCon['DBS_DATABASE_NAME'], $dataCon['DBS_PORT']);
        if ($Server->errno == 0) {
            $response = $Server->tryConnectServer($dataCon['DBS_TYPE']);
            if ($response->status == 'SUCCESS') {
                $response = $Server->tryOpenDataBase($dataCon['DBS_TYPE']);
                if ($response->status != 'SUCCESS') {
                    $resp['message'] = "Error Testting Connection: Trying to open database FAILED : " . $Server->error;
                    return $resp;
                }
            } else {
                $resp['message'] = "Error Testting Connection: Trying to open database FAILED : " . $Server->error;
                return $resp;
            }
        } else {
            $resp['message'] = "Error Testting Connection: Trying to open database FAILED : " . $Server->error;
            return $resp;
        }

        // CORRECT CONNECTION
        $resp['resp'] = true;
        return $resp;
    }


    protected function getReportTableDefaultColumns ($type = 'NORMAL')
    {
        $defaultColumns = array ();
        $application = array(
            'uid' => '',
            'field_dyn' => '',
            'field_uid' => '',
            'field_name' => 'APP_UID',
            'field_label' => 'APP_UID',
            'field_type' => 'VARCHAR',
            'field_size' => 32,
            'field_dyn' => '',
            'field_key' => 1,
            'field_null' => 0,
            'field_filter' => false,
            'field_autoincrement' => false
        ); //APPLICATION KEY
        
        array_push( $defaultColumns, $application );

        $application = array(
            'uid' => '',
            'field_dyn' => '',
            'field_uid' => '',
            'field_name' => 'APP_NUMBER',
            'field_label' => 'APP_NUMBER',
            'field_type' => 'INTEGER',
            'field_size' => 11,
            'field_dyn' => '',
            'field_key' => 0,
            'field_null' => 0,
            'field_filter' => false,
            'field_autoincrement' => false
        ); //APP_NUMBER

        array_push( $defaultColumns, $application );

        $application = array(
            'uid' => '',
            'field_dyn' => '',
            'field_uid' => '',
            'field_name' => 'APP_STATUS',
            'field_label' => 'APP_STATUS',
            'field_type' => 'VARCHAR',
            'field_size' => 10,
            'field_dyn' => '',
            'field_key' => 0,
            'field_null' => 0,
            'field_filter' => false,
            'field_autoincrement' => false
        ); //APP_STATUS
        
        array_push( $defaultColumns, $application );

        //if it is a grid report table
        if ($type == 'GRID') {
            //GRID INDEX
            $gridIndex = array(
                'uid' => '',
                'field_dyn' => '',
                'field_uid' => '',
                'field_name' => 'ROW',
                'field_label' => 'ROW',
                'field_type' => 'INTEGER',
                'field_size' => '11',
                'field_dyn' => '',
                'field_key' => 1,
                'field_null' => 0,
                'field_filter' => false,
                'field_autoincrement' => false
            );
            array_push( $defaultColumns, $gridIndex );
        }

        return $defaultColumns;
    }

    public function validateProUid ($proUid) {
        $proUid = trim($proUid);
        if ($proUid == '') {
            throw (new \Exception('This process doesn\'t exist!'));
        }

        $oProcess = new \Process();
        if (!($oProcess->processExists($proUid))) {
            throw (new \Exception('This process doesn\'t exist!'));
        }

        return $proUid;
    }

    public function arrayToObject($d) {
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return (object) array_map(__FUNCTION__, $d);
        }
        else {
            // Return object
            return $d;
        }
    }
}

