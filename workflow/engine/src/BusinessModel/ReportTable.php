<?php
namespace BusinessModel;

use \G;
use \AdditionalTables;
use \Fields;

class ReportTable
{
    /**
     * List of ReportTables in process
     * @var string $pro_uid. Uid for Process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getReportTables($pro_uid)
    {
        $reportTables = array();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\AdditionalTablesPeer::ADD_TAB_UID);
        $oCriteria->add(\AdditionalTablesPeer::PRO_UID, $pro_uid, \Criteria::EQUAL);
        $oDataset = \AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $reportTables[] = $this->getReportTable($pro_uid, $row['ADD_TAB_UID'], false);
        }

        return $reportTables;
    }

    /**
     * Get data for ReportTable
     * @var string $pro_uid. Uid for Process
     * @var string $rep_uid. Uid for Report Table
     * @var string $validate. Flag for validate
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getReportTable($pro_uid, $rep_uid, $validate = true)
    {
        //VALIDATION
        if ($validate) {
            $pro_uid = $this->validateProUid($pro_uid);
            $rep_uid = $this->validateRepUid($rep_uid);
            $repData['PRO_UID'] = $pro_uid;
        }

        $repData = array();
        $additionalTables = new AdditionalTables();

        // REPORT TABLE PROPERTIES
        $table = $additionalTables->load( $rep_uid, true );
        $table['DBS_UID'] = $table['DBS_UID'] == null || $table['DBS_UID'] == '' ? 'workflow' : $table['DBS_UID'];
        $repData['REP_UID']             = $rep_uid;
        $repData['REP_TAB_NAME']        = $table['ADD_TAB_NAME'];
        $repData['REP_TAB_DESCRIPTION'] = $table['ADD_TAB_DESCRIPTION'];
        $repData['REP_TAB_CLASS_NAME']  = $table['ADD_TAB_CLASS_NAME'];
        $repData['REP_TAB_CONNECTION']  = $table['DBS_UID'];
        $repData['REP_TAB_TYPE']        = $table['ADD_TAB_TYPE'];
        $repData['REP_TAB_GRID']        = $table['ADD_TAB_GRID'];

        // REPORT TABLE NUM ROWS DATA
        $tableData = $additionalTables->getAllData( $rep_uid, 0, 2 );
        $repData['REP_NUM_ROWS'] = $tableData['count'];

        // REPORT TABLE FIELDS
        foreach ($table['FIELDS'] as $valField) {
            $fieldTemp = array();
            $fieldTemp = array_change_key_case($valField, CASE_LOWER);
            $repData['FIELDS'][] = $fieldTemp;
        }

        $repData = array_change_key_case($repData, CASE_LOWER);
        return $repData;
    }

    /**
     * Get data for ReportTable
     * @var string $pro_uid. Uid for Process
     * @var string $rep_uid. Uid for Report Table
     * @var string $validate. Flag for validate
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getDataReportTableData($pro_uid, $rep_uid)
    {
        //VALIDATION
        if ($validate) {
            $pro_uid = $this->validateProUid($pro_uid);
            $rep_uid = $this->validateRepUid($rep_uid);
            $repData['PRO_UID'] = $pro_uid;
        }

        $additionalTables = new AdditionalTables();
        $table  = $additionalTables->load($rep_uid, true);
        $result = $additionalTables->getAllData($rep_uid);
        $primaryKeys = $additionalTables->getPrimaryKeys();
        if (is_array($result['rows'])) {
            foreach ($result['rows'] as $i => $row) {
                $result['rows'][$i] = array_change_key_case($result['rows'][$i], CASE_LOWER);
                $primaryKeysValues = array ();
                foreach ($primaryKeys as $key) {
                    $primaryKeysValues[] = isset( $row[$key['FLD_NAME']] ) ? $row[$key['FLD_NAME']] : '';
                }

                $result['rows'][$i]['__index__'] = G::encrypt( implode( ',', $primaryKeysValues ), 'pmtable' );
            }
        } else {
            $result['rows'] = array();
        }
        return $result;
    }

    /**
     * Save Data for Report Table
     * @var string $pro_uid. Uid for Process
     * @var string $rep_data. Data for Report Table
     * @var string $createRep. Flag for create Report Table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function saveReportTable($pro_uid, $rep_data, $createRep = true)
    {
        // CHANGE CASE UPPER REPORT TABLE
        $dataValidate =  array();
        $dataValidate = array_change_key_case($rep_data, CASE_UPPER);

        // VALIDATION REPORT TABLE DATA
        $pro_uid = $this->validateProUid($pro_uid);
        $dataValidate['PRO_UID']            = $pro_uid;
        $dataValidate['REP_TAB_NAME']       = $this->validateRepName($dataValidate['REP_TAB_NAME']);
        $tempRepTabName                     = $dataValidate['REP_TAB_CONNECTION'];
        $dataValidate['REP_TAB_CONNECTION'] = $this->validateRepConnection($tempRepTabName, $pro_uid);
        if ($dataValidate['REP_TAB_TYPE'] == 'GRID') {
            $dataValidate['REP_TAB_GRID']   = $this->validateRepGrid($dataValidate['REP_TAB_GRID'], $pro_uid);
        }

        // VERIFY COLUMNS REPORT TABLE
        $oAdditionalTables = new AdditionalTables();
        $oFields = new Fields();

        $repTabClassName = $oAdditionalTables->getPHPName($dataValidate['REP_TAB_NAME']);
        $columns = $dataValidate['FIELDS'];


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
        $fieldsValidate = $this->getDynafields($pro_uid, $dataValidate['REP_TAB_TYPE'], $dataValidate['REP_TAB_GRID']);
        foreach ($columns as $i => $column) {
            if (isset($columns[$i]['fld_dyn'])) {
                $columns[$i]['field_dyn'] = $columns[$i]['fld_dyn'];
                unset($columns[$i]['fld_dyn']);
            }
            if (isset($columns[$i]['fld_name'])) {
                $columns[$i]['field_name'] = $columns[$i]['fld_name'];
            }
            if (isset($columns[$i]['fld_label'])) {
                $columns[$i]['field_label'] = $columns[$i]['fld_label'];
                unset($columns[$i]['fld_label']);
            }
            if (isset($columns[$i]['fld_type'])) {
                $columns[$i]['field_type'] = $columns[$i]['fld_type'];
                unset($columns[$i]['fld_type']);
            }
            if (isset($columns[$i]['fld_size'])) {
                $columns[$i]['field_size'] = $columns[$i]['fld_size'];
                unset($columns[$i]['fld_size']);
            }

            if (in_array(strtoupper($columns[$i]['field_name']), $reservedWordsSql) || 
                in_array( strtolower( $columns[$i]['field_name']), $reservedWordsPhp )) {
                throw (new \Exception(G::LoadTranslation("ID_PMTABLE_INVALID_FIELD_NAME", array($columns[$i]['field_name']))));
            }

            // VALIDATIONS
            $columns[$i]['field_type'] = $this->validateFldType($columns[$i]['field_type']);
            if ($columns[$i]['field_autoincrement']) {
                $typeCol = $columns[$i]['field_type'];
                if (! ($typeCol === 'INTEGER' || $typeCol === 'TINYINT' || $typeCol === 'SMALLINT' || $typeCol === 'BIGINT')) {
                    $columns[$i]['field_autoincrement'] = false;
                }
            }

            if (isset($columns[$i]['fld_name'])) {
                if ($columns[$i]['field_dyn'] != '') {
                    $res = array_search($columns[$i]['field_dyn'], $fieldsValidate['NAMES']);
                    if ($res === false) {
                        throw (new \Exception("The property 'fields' in key '$i' in property fld_dyn: '".$columns[$i]['field_dyn']."', is incorrect."));
                    } else {
                        $columns[$i]['_index']    = $fieldsValidate['INDEXS'][$res];
                        $columns[$i]['field_uid'] = $fieldsValidate['UIDS'][$res];
                    }
                }
                unset($columns[$i]['fld_name']);
            }

            $temp = new \stdClass();
            foreach ($columns[$i] as $key => $valCol) {
                eval('$temp->' . str_replace('fld', 'field', $key) . " = '" . $valCol . "';");
            }
            $temp->uid = (isset($temp->uid)) ? $temp->uid : '';
            $temp->_index = (isset($temp->_index)) ? $temp->_index : '';
            $temp->field_uid = (isset($temp->field_uid)) ? $temp->field_uid : '';
            $temp->field_dyn = (isset($temp->field_dyn)) ? $temp->field_dyn : '';

            $temp->field_key = (isset($temp->field_key)) ? $temp->field_key : 0;
            $temp->field_null = (isset($temp->field_null)) ? $temp->field_null : 1;
            $temp->field_dyn = (isset($temp->field_dyn)) ? $temp->field_dyn : '';
            $temp->field_filter = (isset($temp->field_filter)) ? $temp->field_filter : 0;
            $temp->field_autoincrement = (isset($temp->field_autoincrement)) ? $temp->field_autoincrement : 0;
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
        if ($createRep) {
            //new report table
            //create record
            $addTabUid = $oAdditionalTables->create( $addTabData );
        } else {
            //editing report table
            //updating record
            $addTabUid = $dataValidate['REP_TAB_UID'];
            $oAdditionalTables->update( $addTabData );

            //removing old data fields references
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->add( \FieldsPeer::ADD_TAB_UID, $dataValidate['REP_TAB_UID'] );
            \FieldsPeer::doDelete( $oCriteria );
        }

        $rep_uid   = $addTabUid;
        // Updating pmtable fields
        foreach ($columnsStd as $i => $column) {
            $column = (array)$column;
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
        $this->generateDataReport($pro_uid, $rep_uid, false);
        if ($createRep) {
            return $this->getReportTable($pro_uid, $rep_uid, false);
        }
    }

    /**
     * Update Data for ReportTable
     * @var string $pro_uid. Uid for Process
     * @var string $rep_data. Data for ReportTable
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function updateReportTable($pro_uid, $rep_data)
    {
        $pro_uid = $this->validateProUid($pro_uid);
        $rep_uid = $this->validateRepUid($rep_uid);

        $rep_uid      = trim($rep_data['rep_uid']);
        $dataValidate =  array();

        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(\AdditionalTablesPeer::ADD_TAB_UID, $rep_uid, \Criteria::EQUAL);
        $oDataset = \AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        if ($oDataset->next()) {
            $row = $oDataset->getRow();
            $dataValidate['rep_tab_uid']  = $rep_uid;
            $dataValidate['rep_tab_name'] = $row['ADD_TAB_NAME'];
            $dataValidate['rep_tab_dsc']  = $rep_data['rep_tab_dsc'];
            $dataValidate['rep_tab_connection'] = $row['DBS_UID'];
            $dataValidate['rep_tab_type'] = $row['ADD_TAB_TYPE'];
            $dataValidate['rep_tab_grid'] = $row['ADD_TAB_GRID'];
            $dataValidate['fields']       = $rep_data['fields'];
        } else {
            throw (new \Exception("The property rep_uid: '$rep_uid', is incorrect."));
        }
        $this->saveReportTable($pro_uid, $dataValidate, false);
    }

    /**
     * Delete ReportTable
     * @var string $pro_uid. Uid for Process
     * @var string $rep_uid. Uid for Report Table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteReportTable($pro_uid, $rep_uid)
    {
        $pro_uid = $this->validateProUid($pro_uid);
        $rep_uid = $this->validateRepUid($rep_uid);

        $at = new AdditionalTables();
        $table = $at->load( $rep_uid );

        if (! isset( $table )) {
            require_once 'classes/model/ReportTable.php';
            $rtOld = new ReportTable();
            $existReportTableOld = $rtOld->load($rep_uid);
            if (count($existReportTableOld) == 0) {
                throw new Exception(G::LoadTranslation('ID_TABLE_NOT_EXIST_SKIPPED'));
            }
        }
        $at->deleteAll($rep_uid);
    }

    /**
     * Generate Data for Report Table
     * @var string $pro_uid. Uid for Process
     * @var string $rep_uid. Uid for Report Table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function generateDataReport($pro_uid, $rep_uid, $validate = true) {
        if ($validate) {
            $pro_uid = $this->validateProUid($pro_uid);
            $rep_uid = $this->validateRepUid($rep_uid);
            G::loadClass('pmTable');
        }

        $additionalTables = new AdditionalTables();
        $table = $additionalTables->load($rep_uid);
        $additionalTables->populateReportTable(
            $table['ADD_TAB_NAME'],
            \pmTable::resolveDbSource( $table['DBS_UID'] ),
            $table['ADD_TAB_TYPE'],
            $table['PRO_UID'],
            $table['ADD_TAB_GRID'],
            $table['ADD_TAB_UID']
        );
    }

    /**
     * Get Fields of Dynaforms
     * @var string $pro_uid. Uid for Process
     * @var string $rep_tab_type. Type the Report Table
     * @var string $rep_tab_grid. Uid for Grid
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getDynafields ($pro_uid, $rep_tab_type, $rep_tab_grid = '')
    {
        G::LoadClass( 'reportTables' );

        $dynFields = array();
        $aFields   = array();
        $aFields['FIELDS']  = array();
        $aFields['PRO_UID'] = $pro_uid;

        if (isset( $rep_tab_type ) && $rep_tab_type == 'GRID') {
            $this->dynUid = $rep_tab_grid;
            $dynFields = $this->_getDynafields($pro_uid, 'grid', $rep_tab_grid);
        } else {
            $dynFields = $this->_getDynafields($pro_uid, 'xmlform');
        }

        $fieldReturn = array();
        foreach ($dynFields as $value) {
            $fieldReturn['NAMES'][]  = $value['FIELD_NAME'];
            $fieldReturn['UIDS'][]   = $value['FIELD_UID'];
            $fieldReturn['INDEXS'][] = $value['_index'];
        }
        return $fieldReturn;
    }

    /**
     * Get Fields of Dynaforms in xmlform
     * @var string $pro_uid. Uid for Process
     * @var string $type. Type the form
     * @var string $rep_tab_grid. Uid for Grid
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function _getDynafields ($pro_uid, $type = 'xmlform', $rep_tab_grid = '')
    {
        G::loadSystem( 'dynaformhandler' );

        $oCriteria = new \Criteria( 'workflow' );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_FILENAME );
        $oCriteria->add( \DynaformPeer::PRO_UID, $pro_uid );
        $oCriteria->add( \DynaformPeer::DYN_TYPE, $type );

        if ($rep_tab_grid != '') {
            $oCriteria->add( \DynaformPeer::DYN_UID, $this->dynUid );
        }

        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

        $fields      = array();
        $fieldsNames = array();
        $labelFieldsTypeList = array('dropdown','radiogroup');
        $excludeFieldsList   = array(
            'title',
            'subtitle',
            'link',
            'file',
            'button',
            'reset',
            'submit',
            'listbox',
            'checkgroup',
            'grid',
            'javascript',
            ''
        );

        $index = 0;
        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            if (file_exists( PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml' )) {
                $dynaformHandler = new \dynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
                $nodeFieldsList = $dynaformHandler->getFields();

                foreach ($nodeFieldsList as $node) {
                    $arrayNode = $dynaformHandler->getArray( $node );
                    $fieldName = $arrayNode['__nodeName__'];
                    $fieldType = isset($arrayNode['type']) ? $arrayNode['type']: '';
                    $fieldValidate = ( isset($arrayNode['validate'])) ? $arrayNode['validate'] : '';
                    if (! in_array( $fieldType, $excludeFieldsList ) && ! in_array( $fieldName, $fieldsNames ) ) {
                        $fields[] = array(
                            'FIELD_UID' => $fieldName . '-' . $fieldType,
                            'FIELD_NAME' => $fieldName,
                            '_index' => $index++
                        );
                        $fieldsNames[] = $fieldName;
                        if (in_array( $fieldType, $labelFieldsTypeList ) && ! in_array( $fieldName . '_label', $fieldsNames )) {
                            $fields[] = array(
                                'FIELD_UID' => $fieldName . '_label' . '-' . $fieldType,
                                'FIELD_NAME' => $fieldName . '_label',
                                '_index' => $index++
                            );
                            $fieldsNames[] = $fieldName;
                        }
                    }
                }
            }
        }
        sort($fields);
        return $fields;
    }

    /**
     * Get Default Columns of Report Table
     * @var string $type. Type of Report Table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getReportTableDefaultColumns ($type = 'NORMAL')
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

    /**
     * Validate Process Uid
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateProUid ($pro_uid) {
        $pro_uid = trim($pro_uid);
        if ($pro_uid == '') {
            throw (new \Exception("The project with prj_uid: '', does not exist."));
        }
        $oProcess = new \Process();
        if (!($oProcess->processExists($pro_uid))) {
            throw (new \Exception("The project with prj_uid: '$pro_uid', does not exist."));
        }
        return $pro_uid;
    }

    /**
     * Validate Report Table Uid
     * @var string $rep_uid. Uid for report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateRepUid ($rep_uid) {
        $rep_uid = trim($rep_uid);
        if ($rep_uid == '') {
            throw (new \Exception("The report table with rep_uid: '', does not exist."));
        }
        $oAdditionalTables = new \AdditionalTables();
        if (!($oAdditionalTables->exists($rep_uid))) {
            throw (new \Exception("The report table with rep_uid: '$rep_uid', does not exist."));
        }
        return $rep_uid;
    }

    /**
     * Validate Report Table Name
     * @var string $rep_tab_name. Name for report table
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateRepName ($rep_tab_name) {
        $rep_tab_name = trim($rep_tab_name);
        if ((strpos($rep_tab_name, ' ')) || (strlen($rep_tab_name) < 4)) {
            throw (new \Exception("The property rep_tab_name: '$rep_tab_name', is incorrect."));
        }
        $rep_tab_name = G::toUpper($rep_tab_name);
        if (substr($rep_tab_name, 0, 4) != 'PMT_') {
            $rep_tab_name = 'PMT_' . $rep_tab_name;
        }
        return $rep_tab_name;
    }

    /**
     * Validate Report Table Connection
     * @var string $rep_tab_connection. Connection for report table
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateRepConnection ($rep_tab_connection, $pro_uid) {
        $rep_tab_connection = trim($rep_tab_connection);
        if ($rep_tab_connection == '') {
            throw (new \Exception("The property rep_tab_connection: '$rep_tab_connection', is incorrect."));
        }

        $connections = array('workflow', 'rp');
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\DbSourcePeer::DBS_UID);
        $oCriteria->add(\DbSourcePeer::PRO_UID, $pro_uid, \Criteria::EQUAL);
        $oDataset = \AdditionalTablesPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        while ($oDataset->next()) {
            $row = $oDataset->getRow();
            $connections[] = $row['DBS_UID'];
        }

        if (!in_array($rep_tab_connection, $connections)) {
            throw (new \Exception("The property rep_tab_connection: '$rep_tab_connection', is incorrect."));
        }
        return $rep_tab_connection;
    }

    /**
     * Validate Report Table Grid
     * @var string $rep_tab_grid. Grid for report table
     * @var string $pro_uid. Uid for process
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateRepGrid ($rep_tab_grid, $pro_uid) {
        $rep_tab_grid = trim($rep_tab_grid);
        if ($rep_tab_grid == '') {
            throw (new \Exception("The property rep_tab_grid: '$rep_tab_grid', is incorrect."));
        }

        G::loadSystem('dynaformhandler');
        $grids = array();
        $aFieldsNames = array();

        $oCriteria = new \Criteria( 'workflow' );
        $oCriteria->addSelectColumn( \DynaformPeer::DYN_FILENAME );
        $oCriteria->add( \DynaformPeer::PRO_UID, $pro_uid );
        $oCriteria->add( \DynaformPeer::DYN_TYPE, 'xmlform' );
        $oDataset = \DynaformPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

        while ($oDataset->next()) {
            $aRow = $oDataset->getRow();
            $dynaformHandler = new \dynaformHandler( PATH_DYNAFORM . $aRow['DYN_FILENAME'] . '.xml' );
            $nodeFieldsList = $dynaformHandler->getFields();
            foreach ($nodeFieldsList as $node) {
                $arrayNode = $dynaformHandler->getArray( $node );
                $fieldName = $arrayNode['__nodeName__'];
                $fieldType = $arrayNode['type'];
                if ($fieldType == 'grid') {
                    if (! in_array( $fieldName, $aFieldsNames )) {
                        $grids[] = str_replace( $pro_uid . '/', '', $arrayNode['xmlgrid']);
                    }
                }
            }
        }

        if (!in_array($rep_tab_grid, $grids)) {
            throw (new \Exception("The property rep_tab_grid: '$rep_tab_grid', is incorrect."));
        }
        return $rep_tab_grid;
    }

    /**
     * Validate Field Type
     * @var string $fld_type. Type for field
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    public function validateFldType ($fld_type) {

        $fld_type = trim($fld_type);
        if ($fld_type == '') {
            throw (new \Exception("The property fld_type: '$fld_type', is incorrect."));
        }

        switch ($fld_type) {
            case 'INT':
                $fld_type = 'INTEGER';
                break;
            case 'TEXT':
                $fld_type = 'LONGVARCHAR';
                break;
            case 'DATETIME':
                $fld_type = 'TIMESTAMP';
                break;
        }

        G::loadClass('pmTable');

        $columnsTypes = \PmTable::getPropelSupportedColumnTypes();
        $res = array_search($fld_type, $columnsTypes);
        if ($res === false) {
            throw (new \Exception("The property fld_type: '$fld_type', is incorrect."));
        }
        return $fld_type;
    }
}

