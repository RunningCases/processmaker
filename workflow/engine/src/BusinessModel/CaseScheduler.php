<?php
namespace BusinessModel;

use \G;

class CaseScheduler
{
    /**
     * Return case scheduler of a project
     * @param string $sProcessUID
     * @return array
     *
     * @access public
     */
    public function getCaseSchedulers($sProcessUID = '')
    {
        try {
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->clearSelectColumns();
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_PASS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::PRO_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::TAS_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_TIME_NEXT_RUN );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_RUN_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::USR_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_OPTION );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_EVERY_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_WEEK_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DAY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_MONTHS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_END_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_EVERY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_UNTIL );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::CASE_SH_PLUGIN_UID );
            $oCriteria->add( \CaseSchedulerPeer::PRO_UID, $sProcessUID );
            $oDataset = \CaseSchedulerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRows = array();
            while ($aRow = $oDataset->getRow()) {
                $aRow = array_change_key_case($aRow, CASE_LOWER);
                $aRows[] = $aRow;
                $oDataset->next();
            }
            return $aRows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return case scheduler of a project
     * @param string $sProcessUID
     * @param string $sCaseSchedulerUID
     * @return array
     *
     * @access public
     */
    public function getCaseScheduler($sProcessUID = '', $sCaseSchedulerUID = '')
    {
        try {
            $oCaseSchedulerTest = \CaseSchedulerPeer::retrieveByPK( $sCaseSchedulerUID );
            if (is_null($oCaseSchedulerTest)) {
                throw (new \Exception( 'This id: '. $sCaseSchedulerUID .' do not correspond to a registered case scheduler'));
            }
            $oCriteria = new \Criteria( 'workflow' );
            $oCriteria->clearSelectColumns();
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_NAME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_PASS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DEL_USER_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::PRO_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::TAS_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_TIME_NEXT_RUN );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_RUN_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_LAST_STATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::USR_UID );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_OPTION );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_TIME );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_DAYS_PERFORM_TASK );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_EVERY_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_WEEK_DAYS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_START_DAY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_MONTHS );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_END_DATE );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_EVERY );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_UNTIL );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::SCH_REPEAT_STOP_IF_RUNNING );
            $oCriteria->addSelectColumn( \CaseSchedulerPeer::CASE_SH_PLUGIN_UID );
            $oCriteria->add( \CaseSchedulerPeer::PRO_UID, $sProcessUID );
            $oCriteria->add( \CaseSchedulerPeer::SCH_UID, $sCaseSchedulerUID );
            $oDataset = \CaseSchedulerPeer::doSelectRS( $oCriteria );
            $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
            $oDataset->next();
            $aRows = array();
            while ($aRow = $oDataset->getRow()) {
                $aRow = array_change_key_case($aRow, CASE_LOWER);
                $aRows = $aRow;
                $oDataset->next();
            }
            return $aRows;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of unique ids of a Task (Unique id of Process)
     *
     * @param string $taskUid Unique id of Task
     *
     * return array
     */
    public function getTaskUid($taskUid)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\TaskPeer::TAS_UID);
            $criteria->add(\TaskPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $rsCriteria = \TaskPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the name exists in the case Scheduler
     *
     * @param string $processUid Unique id of Process
     * @param string $name       Name
     *
     * return bool Return true if the name exists, false otherwise
     */
    public function existsName($processUid, $name)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\CaseSchedulerPeer::TAS_UID);
            $criteria->add(\CaseSchedulerPeer::SCH_NAME, $name, \Criteria::EQUAL);
            $rsCriteria = \CaseSchedulerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the name exists in the case Scheduler
     *
     * @param string $processUid Unique id of Process
     * @param string $name       Name
     *
     * return bool Return true if the name exists, false otherwise
     */
    public function existsNameUpdate($schUid, $name)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\CaseSchedulerPeer::TAS_UID);
            $criteria->add(\CaseSchedulerPeer::SCH_NAME, $name, \Criteria::EQUAL);
            $criteria->add(\CaseSchedulerPeer::SCH_UID, $schUid, \Criteria::NOT_EQUAL);
            $rsCriteria = \CaseSchedulerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            return $rsCriteria->getRow();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the user exists
     *
     * @param string $userName  Name
     * @param string $userPass  Password
     * @param string $sProcessUID  Process
     *
     * return message
     */
    public function getUser($userName, $userPass, $sProcessUID, $sTaskUID)
    {
        try {
            $sPRO_UID = $sProcessUID;
            $sTASKS = $sTaskUID;
            $sWS_USER = trim( $userName );
            $sWS_PASS = trim( $userPass );
            if (\G::is_https()) {
                $http = 'https://';
            } else {
                $http = 'http://';
            }
            $endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
            @$client = new \SoapClient( $endpoint );
            $user = $sWS_USER;
            $pass = $sWS_PASS;
            $params = array ('userid' => $user,'password' => $pass);
            $result = $client->__SoapCall('login', array ($params));
            $fields['status_code'] = $result->status_code;
            $fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
            $fields['version'] = $result->version;
            $fields['time_stamp'] = $result->timestamp;
            $messageCode = 1;
            \G::LoadClass( 'Task' );
            \G::LoadClass( 'User' );
            \G::LoadClass( 'TaskUser' );
            \G::LoadClass( 'Groupwf' );
            if (! class_exists( 'GroupUser' )) {
                \G::LoadClass( 'GroupUser' );
            }
            if ($result->status_code == 0) {
                $oCriteria = new \Criteria( 'workflow' );
                $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                $oCriteria->addSelectColumn( \TaskUserPeer::USR_UID );
                $oCriteria->addSelectColumn( \TaskUserPeer::TAS_UID );
                $oCriteria->addSelectColumn( \UsersPeer::USR_USERNAME );
                $oCriteria->addSelectColumn( \UsersPeer::USR_FIRSTNAME );
                $oCriteria->addSelectColumn( \UsersPeer::USR_LASTNAME );
                $oCriteria->addJoin( \TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN );
                $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                $userIsAssigned = \TaskUserPeer::doCount( $oCriteria );
                if ($userIsAssigned < 1) {
                    $oCriteria = new \Criteria( 'workflow' );
                    $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                    $oCriteria->addJoin( \UsersPeer::USR_UID, \GroupUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                    $oCriteria->addJoin( \GroupUserPeer::GRP_UID, \TaskUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                    $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                    $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                    $userIsAssigned = \GroupUserPeer::doCount( $oCriteria );
                    if (! ($userIsAssigned >= 1)) {
                        throw (new \Exception( "The User `" . $sWS_USER . "` doesn't have the activity `" . $sTASKS . "` assigned"));
                    }
                }
                $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                while ($aRow = $oDataset->getRow()) {
                    $messageCode = $aRow['USR_UID'];
                    $oDataset->next();
                }
            } else {
                throw (new \Exception( $result->message));
            }
            return $messageCode;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new case scheduler of a project
     * @param string $sProcessUID
     * @param array  $aData
     * @param string $userUID
     * @return array
     *
     * @access public
     */
    public function addCaseScheduler($sProcessUID, $aData, $userUID)
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes". PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $aData['sch_repeat_stop_if_running'] = '0';
            $aData['case_sh_plugin_uid'] = null;
            $aData = array_change_key_case($aData, CASE_UPPER);
            $sOption = $aData['SCH_OPTION'];
            if (empty($aData)) {
                die( 'the information sended is empty!' );
            }
            $arrayTaskUid = $this->getTaskUid($aData['TAS_UID']);
            if (empty($arrayTaskUid)) {
                throw (new \Exception( 'task not found for id: '. $aData['TAS_UID']));
            }
            if ($aData['SCH_NAME']=='') {
                throw (new \Exception( '`sch_name` can`t be empty'));
            }
            if ($this->existsName($sProcessUID, $aData['SCH_NAME'])) {
                throw (new \Exception( 'duplicate Case Scheduler name'));
            }
            $mUser = $this->getUser($aData['SCH_DEL_USER_NAME'], $aData['SCH_DEL_USER_PASS'], $sProcessUID, $aData['TAS_UID']);
            $oUser = \UsersPeer::retrieveByPK( $mUser );
            if (is_null($oUser)) {
                throw (new \Exception($mUser));
            }
            $aData['SCH_DEL_USER_PASS'] = md5( $aData['SCH_DEL_USER_PASS']);
            if ($sOption != '5') {
                $pattern="/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/";
                if (!preg_match($pattern, $aData['SCH_START_TIME'])) {
                    throw (new \Exception( 'invalid value specified for `sch_start_time`. Expecting time in HH:MM format (The time can not be increased to 23:59)'));
                }
            }
            $patternDate="/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if (!preg_match($patternDate, $aData['SCH_START_DATE'])) {
                    throw (new \Exception( 'invalid value specified for `sch_start_date`. Expecting date in `YYYY-MM-DD` format, such as `2014-01-01`'));
                }
            }
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if (!preg_match($patternDate, $aData['SCH_END_DATE'])) {
                    throw (new \Exception( 'invalid value specified for `sch_end_date`. Expecting date in `YYYY-MM-DD` format, such as `2014-01-01`'));
                }
            }
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if ($aData['SCH_START_DATE'] == "") {
                    throw (new \Exception( '`sch_start_date` can`t be null'));
                }
            }
            if ($sOption == '2') {
                $aData['SCH_EVERY_DAYS'] = 1;
            } else {
                $aData['SCH_EVERY_DAYS'] = 0;
            }
            $oCaseScheduler = new \CaseScheduler();
            $aData['SCH_UID'] = \G::generateUniqueID();
            $aData['PRO_UID'] = $sProcessUID;
            $aData['SCH_STATE'] = 'ACTIVE';
            $aData['SCH_LAST_STATE'] = 'CREATED'; // 'ACTIVE';
            $aData['USR_UID'] = $userUID;
            $aData['SCH_DEL_USER_UID'] = $aData['USR_UID'];
            $sTimeTmp = $aData['SCH_START_TIME'];
            $nActualTime = $aData['SCH_START_TIME']; // time();
            $sValue = '';
            $sDaysPerformTask = '';
            $sWeeks = '';
            $sMonths = '';
            $sMonths = '';
            $sStartDay = '';
            $nSW = 0;
            $aData['SCH_DAYS_PERFORM_TASK'] = '';
            switch ($sOption) {
                case '1':
                    $aData['SCH_DAYS_PERFORM_TASK'] = '1';
                    $sValue = $aData['SCH_DAYS_PERFORM_TASK'];
                    switch ($sValue) {
                        case '1':
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'] . '|1';
                            $aData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                            $aData['SCH_WEEK_DAYS'] ='0|0|0|0|0|0|0';
                            break;
                        case '2':
                            $aData['SCH_OPTION'] = '2';
                            $aData['SCH_EVERY_DAYS'] = '1'; //check
                            $aData['SCH_WEEK_DAYS'] = '1|2|3|4|5|'; //check
                            break;
                        case '3': // Every [n] Days
                            $sDaysPerformTask = $aData['SCH_DAYS_PERFORM_TASK'];
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'];
                            break;
                    }
                    break;
                case '2': // If the option is zero, set by default 1
                    if ($aData['SCH_WEEK_DAYS'] == "") {
                        throw (new \Exception( '`sch_week_days` can`t be null'));
                    } else {
                        $weeks = $aData['SCH_WEEK_DAYS'];
                        $weeks = explode("|", $weeks);
                        foreach ($weeks as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7") {
                                $aData['SCH_WEEK_DAYS'] = $aData['SCH_WEEK_DAYS'];
                            } else {
                                throw (new \Exception( 'invalid value specified for `sch_week_days`'));
                            }
                        }
                    }
                    $aData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                    if (empty( $aData['SCH_EVERY_DAYS'] )) {
                        $nEveryDays = 1;
                    } else {
                        $nEveryDays = $aData['SCH_EVERY_DAYS'];
                    }
                    $aData['SCH_EVERY_DAYS'] = $nEveryDays;
                    $sWeeks = '';
                    if (! empty( $aData['SCH_WEEK_DAYS'] )) {
                        $aWeekDays = $aData['SCH_WEEK_DAYS'];
                    }
                    $sStartTime = $aData['SCH_START_TIME'];
                    $sWeeks = $aData['SCH_WEEK_DAYS'] . '|';
                    break;
                case '3':
                    $nStartDay = $aData['SCH_START_DAY'];
                    if ($nStartDay == "") {
                        throw (new \Exception( '`sch_start_day` can`t be null'));
                    }
                    if ($nStartDay == 1) {
                        if ($aData['SCH_START_DAY_OPT_1'] == "") {
                            throw (new \Exception( '`sch_start_day_opt_1` can`t be null'));
                        }
                        $temp = $aData['SCH_START_DAY_OPT_1'];
                        $temp = (int)$temp;
                        if ($temp >= 1 && $temp <= 31) {
                            $aData['SCH_START_DAY_OPT_1'] = $aData['SCH_START_DAY_OPT_1'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_1`. Must be between 1 and 31'));
                        }
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_1'];
                    } else {
                        if ($aData['SCH_START_DAY_OPT_2'] == "") {
                            throw (new \Exception( '`sch_start_day_opt_2` can`t be null'));
                        }
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_2'];
                        $optionTwo = $aData['SCH_START_DAY_OPT_2']{0};
                        if ($optionTwo == "1" || $optionTwo == "2" || $optionTwo == "3" || $optionTwo == "4" || $optionTwo == "5") {
                            $aData['SCH_START_DAY_OPT_2'] = $aData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_2`'));
                        }
                        $pipelineTwo = $aData['SCH_START_DAY_OPT_2']{1};
                        if ($pipelineTwo == "|") {
                            $aData['SCH_START_DAY_OPT_2'] = $aData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_2`'));
                        }
                        $dayTwo = $aData['SCH_START_DAY_OPT_2']{2};
                        if ($dayTwo == "1" || $dayTwo == "2" || $dayTwo == "3" || $dayTwo == "4" || $dayTwo == "5" || $dayTwo == "6" || $dayTwo == "7") {
                            $aData['SCH_START_DAY_OPT_2'] = $aData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_2`'));
                        }
                    }
                    if ($nStartDay == "") {
                        throw (new \Exception( '`sch_start_day` can`t be null'));
                    }
                    $sMonths = '';
                    if ($aData['SCH_MONTHS'] == "") {
                         throw (new \Exception( '`sch_months` can`t be null'));
                    }
                    if (! empty( $aData['SCH_MONTHS'] )) {
                        $aMonths = $aData['SCH_MONTHS'];
                        $aMonths = explode("|", $aMonths);
                        foreach ($aMonths as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7"|| $row == "8" || $row == "9" || $row == "10"|| $row == "11" || $row == "12") {
                                $aData['SCH_MONTHS'] = $aData['SCH_MONTHS'];
                            } else {
                                throw (new \Exception( 'invalid value specified for `sch_months`'));
                            }
                        }
                    }
                    $sMonths = $aData['SCH_MONTHS'];
                    $sStartDay = $aData['SCH_START_DAY'];
                    $sValue = $nStartDay;
                    break;
            }
            if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
                $sDateTmp = '';
                if ($sStartDay == '') {
                    $sStartDay = date('Y-m-d');
                } else {
                    $size = strlen($aData['SCH_START_DAY']);
                    if ($size > 4) {
                        $aaStartDay = explode( "|", $aData['SCH_START_DAY'] );
                        $aaStartDay[0] = $aaStartDay[0];
                        $aaStartDay[1] = $aaStartDay[1];
                        $aaStartDay[2]= ($aaStartDay[2] == 7 ? 1 : $aaStartDay[2]);
                        $sStartDay = $aaStartDay[0].'|'.$aaStartDay[1].'|'.$aaStartDay[2];
                    }
                }
                $dCurrentDay = date("d");
                $dCurrentMonth = date("m");
                $aStartDay = explode( "|", $aData['SCH_START_DAY'] );
                if ($sOption == '3' && $aStartDay[0] == '1') {
                    $monthsArray = explode( "|", $sMonths );
                    foreach ($monthsArray as $row) {
                        if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                            $startTime = $aData['SCH_START_TIME'] . ":00";
                            $aData['SCH_TIME_NEXT_RUN'] = date('Y') . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                            break;
                        } else {
                            $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                        }
                    }
                } else {
                    $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                }
            } else {
                if ($sOption == '4') {
                    $sDateTmp = date('Y-m-d');
                    $aData['SCH_START_TIME'] = date('Y-m-d', strtotime( $sDateTmp )) . ' ' . date('H:i:s', strtotime( $sTimeTmp ));
                    $aData['SCH_START_DATE'] = $aData['SCH_START_TIME'];
                    $aData['SCH_END_DATE'] = $aData['SCH_START_TIME'];
                }
                $aData['SCH_TIME_NEXT_RUN'] = $aData['SCH_START_TIME'];
                if ($sOption == '5') {
                    if ($aData['SCH_START_DATE'] != '') {
                        $sDateTmp = $aData['SCH_START_DATE'];
                    } else {
                        $sDateTmp = date('Y-m-d');
                        $aData['SCH_START_DATE'] = $sDateTmp;
                    }
                    $aData['SCH_END_DATE'] = date('Y-m-d', strtotime( $sDateTmp )) . ' ' . date('H:i:s', strtotime( $sTimeTmp ));
                    $aData['SCH_START_TIME'] = time();
                    $aData['SCH_START_DATE'] = $aData['SCH_START_TIME'];
                    if ($aData['SCH_REPEAT_EVERY'] == "") {
                        throw (new \Exception( '`sch_repeat_every` can`t be null'));
                    }
                    $patternHour="/^([0-1][0-9]|[2][0-3])[\.]([0-5][0-9])$/";
                    if (!preg_match($patternHour, $aData['SCH_REPEAT_EVERY'])) {
                        throw (new \Exception( 'invalid value specified for `sch_repeat_every`. Expecting time in HH.MM format (The time can not be increased to 23.59)'));
                    }
                    $nextRun = $aData['SCH_REPEAT_EVERY'] * 60 * 60;
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                    $date = $aData['SCH_START_TIME'];
                    $date += $nextRun;
                    $date = date("Y-m-d H:i", $date);
                    $aData['SCH_TIME_NEXT_RUN'] = $date;
                }
            }
            if (trim( $aData['SCH_END_DATE'] ) != '') {
                $aData['SCH_END_DATE'] = $aData['SCH_END_DATE'];
            }
            if (! empty( $aData['SCH_REPEAT_TASK_CHK'] )) {
                $nOptEvery = $aData['SCH_REPEAT_EVERY_OPT'];
                if ($nOptEvery == 2) {
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'] * 60;
                } else {
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                }
            }
            if ((isset( $aData['CASE_SH_PLUGIN_UID'] )) && ($aData['CASE_SH_PLUGIN_UID'] != "")) {
                $aData['CASE_SH_PLUGIN_UID'] = $aData['CASE_SH_PLUGIN_UID'];
            }
            // check this data
            $aData['SCH_REPEAT_UNTIL'] = '';
            $aData['SCH_REPEAT_STOP_IF_RUNNING'] = '0';
            $aData['CASE_SH_PLUGIN_UID'] = null;
            //
            $oCaseScheduler->create( $aData );
            $oCriteria = $this->getCaseScheduler($sProcessUID, $aData['SCH_UID']);
            return $oCriteria;
        } catch (Exception $oException) {
            die( $oException->getMessage() );
        }
    }

    /**
     * Update case scheduler for a project
     * @param string $sProcessUID
     * @param array  $aData
     * @param string $userUID
     * @param string $sSchUID
     *
     * @access public
     */
    public function updateCaseScheduler($sProcessUID, $aData, $userUID, $sSchUID = '')
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes". PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $aData = array_change_key_case($aData, CASE_UPPER);
            if (empty( $aData )) {
                die( 'The information sended is empty!' );
            }
            $oCaseScheduler = new \CaseScheduler();
            $aFields = $oCaseScheduler->Load($sSchUID);
            $sOption = $aFields['SCH_OPTION'];
            $aData['SCH_OPTION'] = $sOption;
            $aData['sch_repeat_stop_if_running'] = '0';
            $aData['case_sh_plugin_uid'] = null;
            $aData = array_change_key_case($aData, CASE_UPPER);
            if (empty($aData)) {
                die( 'the information sended is empty!' );
            }
            $arrayTaskUid = $this->getTaskUid($aData['TAS_UID']);
            if (empty($arrayTaskUid)) {
                throw (new \Exception( 'task not found for id: '. $aData['TAS_UID']));
            }
            if ($aData['SCH_NAME']=='') {
                throw (new \Exception( '`sch_name` can`t be empty'));
            }
            if ($this->existsNameUpdate($sSchUID, $aData['SCH_NAME'])) {
                throw (new \Exception( 'duplicate Case Scheduler name'));
            }
            $mUser = $this->getUser($aData['SCH_DEL_USER_NAME'], $aData['SCH_DEL_USER_PASS'], $sProcessUID, $aData['TAS_UID']);
            $oUser = \UsersPeer::retrieveByPK( $mUser );
            if (is_null($oUser)) {
                throw (new \Exception($mUser));
            }
            $aData['SCH_DEL_USER_PASS'] = md5( $aData['SCH_DEL_USER_PASS']);
            if ($sOption != '5') {
                $pattern="/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/";
                if (!preg_match($pattern, $aData['SCH_START_TIME'])) {
                    throw (new \Exception( 'invalid value specified for `sch_start_time`. Expecting time in HH:MM format (The time can not be increased to 23:59)'));
                }
            }
            $patternDate="/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/";
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if (!preg_match($patternDate, $aData['SCH_START_DATE'])) {
                    throw (new \Exception( 'invalid value specified for `sch_start_date`. Expecting date in `YYYY-MM-DD` format, such as `2014-01-01`'));
                }
            }
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if (!preg_match($patternDate, $aData['SCH_END_DATE'])) {
                    throw (new \Exception( 'invalid value specified for `sch_end_date`. Expecting date in `YYYY-MM-DD` format, such as `2014-01-01`'));
                }
            }
            if ($sOption == '1' || $sOption == '2' || $sOption == '3') {
                if ($aData['SCH_START_DATE'] == "") {
                    throw (new \Exception( '`sch_start_date` can`t be null'));
                }
            }
            if ($sOption == '2') {
                $aData['SCH_EVERY_DAYS'] = 1;
            } else {
                $aData['SCH_EVERY_DAYS'] = 0;
            }
            $oCaseScheduler = new \CaseScheduler();
            $aData['SCH_UID'] = $sSchUID;
            $aData['PRO_UID'] = $sProcessUID;
            $aData['SCH_STATE'] = 'ACTIVE';
            $aData['SCH_LAST_STATE'] = 'CREATED'; // 'ACTIVE';
            $aData['USR_UID'] = $userUID;
            $aData['SCH_DEL_USER_UID'] = $aData['USR_UID'];
            $sTimeTmp = $aData['SCH_START_TIME'];
            $nActualTime = $aData['SCH_START_TIME']; // time();
            $sValue = '';
            $sDaysPerformTask = '';
            $sWeeks = '';
            $sMonths = '';
            $sMonths = '';
            $sStartDay = '';
            $nSW = 0;
            $aData['SCH_DAYS_PERFORM_TASK'] = '';
            switch ($sOption) {
                case '1':
                    $aData['SCH_DAYS_PERFORM_TASK'] = '1';
                    $sValue = $aData['SCH_DAYS_PERFORM_TASK'];
                    switch ($sValue) {
                        case '1':
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'] . '|1';
                            $aData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                            $aData['SCH_WEEK_DAYS'] ='0|0|0|0|0|0|0';
                            break;
                        case '2':
                            $aData['SCH_OPTION'] = '2';
                            $aData['SCH_EVERY_DAYS'] = '1'; //check
                            $aData['SCH_WEEK_DAYS'] = '1|2|3|4|5|'; //check
                            break;
                        case '3': // Every [n] Days
                            $sDaysPerformTask = $aData['SCH_DAYS_PERFORM_TASK'];
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'];
                            break;
                    }
                    break;
                case '2': // If the option is zero, set by default 1
                    if ($aData['SCH_WEEK_DAYS'] == "") {
                        throw (new \Exception( '`sch_week_days` can`t be null'));
                    } else {
                        $weeks = $aData['SCH_WEEK_DAYS'];
                        $weeks = explode("|", $weeks);
                        foreach ($weeks as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7") {
                                $aData['SCH_WEEK_DAYS'] = $aData['SCH_WEEK_DAYS'];
                            } else {
                                throw (new \Exception( 'invalid value specified for `sch_week_days`'));
                            }
                        }
                    }
                    $aData['SCH_MONTHS'] ='0|0|0|0|0|0|0|0|0|0|0|0';
                    if (empty( $aData['SCH_EVERY_DAYS'] )) {
                        $nEveryDays = 1;
                    } else {
                        $nEveryDays = $aData['SCH_EVERY_DAYS'];
                    }
                    $aData['SCH_EVERY_DAYS'] = $nEveryDays;
                    $sWeeks = '';
                    if (! empty( $aData['SCH_WEEK_DAYS'] )) {
                        $aWeekDays = $aData['SCH_WEEK_DAYS'];
                    }
                    $sStartTime = $aData['SCH_START_TIME'];
                    $sWeeks = $aData['SCH_WEEK_DAYS'] . '|';
                    break;
                case '3':
                    $nStartDay = $aData['SCH_START_DAY'];
                    if ($nStartDay == "") {
                        throw (new \Exception( '`sch_start_day` can`t be null'));
                    }
                    if ($nStartDay == 1) {
                        if ($aData['SCH_START_DAY_OPT_1'] == "") {
                            throw (new \Exception( '`sch_start_day_opt_1` can`t be null'));
                        }
                        $temp = $aData['SCH_START_DAY_OPT_1'];
                        $temp = (int)$temp;
                        if ($temp >= 1 && $temp <= 31) {
                            $aData['SCH_START_DAY_OPT_1'] = $aData['SCH_START_DAY_OPT_1'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_1`. Must be between 1 and 31'));
                        }
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_1'];
                    } else {
                        if ($aData['SCH_START_DAY_OPT_2'] == "") {
                            throw (new \Exception( '`sch_start_day_opt_2` can`t be null'));
                        }
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_2'];
                            $optionTwo = $aData['SCH_START_DAY_OPT_2']{0};
                        if ($optionTwo == "1" || $optionTwo == "2" || $optionTwo == "3" || $optionTwo == "4" || $optionTwo == "5") {
                            $aData['SCH_START_DAY_OPT_2'] = $aData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_2`'));
                        }
                        $pipelineTwo = $aData['SCH_START_DAY_OPT_2']{1};
                        if ($pipelineTwo == "|") {
                            $aData['SCH_START_DAY_OPT_2'] = $aData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_2`'));
                        }
                        $dayTwo = $aData['SCH_START_DAY_OPT_2']{2};
                        if ($dayTwo == "1" || $dayTwo == "2" || $dayTwo == "3" || $dayTwo == "4" || $dayTwo == "5" || $dayTwo == "6" || $dayTwo == "7") {
                            $aData['SCH_START_DAY_OPT_2'] = $aData['SCH_START_DAY_OPT_2'];
                        } else {
                            throw (new \Exception( 'invalid value specified for `sch_start_day_opt_2`'));
                        }
                    }
                    if ($nStartDay == "") {
                        throw (new \Exception( '`sch_start_day` can`t be null'));
                    }
                    $sMonths = '';
                    if ($aData['SCH_MONTHS'] == "") {
                         throw (new \Exception( '`sch_months` can`t be null'));
                    }
                    if (! empty( $aData['SCH_MONTHS'] )) {
                        $aMonths = $aData['SCH_MONTHS'];
                        $aMonths = explode("|", $aMonths);
                        foreach ($aMonths as $row) {
                            if ($row == "1" || $row == "2" || $row == "3" || $row == "4" || $row == "5"|| $row == "6" || $row == "7"|| $row == "8" || $row == "9" || $row == "10"|| $row == "11" || $row == "12") {
                                $aData['SCH_MONTHS'] = $aData['SCH_MONTHS'];
                            } else {
                                throw (new \Exception( 'invalid value specified for `sch_months`'));
                            }
                        }
                    }
                    $sMonths = $aData['SCH_MONTHS'];
                    $sStartDay = $aData['SCH_START_DAY'];
                    $sValue = $nStartDay;
                    break;
            }
            if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
                if ($sStartDay == '') {
                    $sStartDay = date('Y-m-d');
                } else {
                    $size = strlen($aData['SCH_START_DAY']);
                    if ($size > 4) {
                        $aaStartDay = explode( "|", $aData['SCH_START_DAY'] );
                        $aaStartDay[0] = $aaStartDay[0];
                        $aaStartDay[1] = $aaStartDay[1];
                        $aaStartDay[2]= ($aaStartDay[2] == 7 ? 1 : $aaStartDay[2]);
                        $sStartDay = $aaStartDay[0].'|'.$aaStartDay[1].'|'.$aaStartDay[2];
                    }
                }
                $dCurrentDay = date("d");
                $dCurrentMonth = date("m");
                $aStartDay = explode( "|", $aData['SCH_START_DAY'] );
                $sDateTmp = '';
                if ($sOption == '3' && $aStartDay[0] == '1') {
                    $monthsArray = explode( "|", $sMonths );
                    foreach ($monthsArray as $row) {
                        if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                            $startTime = $aData['SCH_START_TIME'] . ":00";
                            $aData['SCH_TIME_NEXT_RUN'] = date('Y') . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                            break;
                        } else {
                            $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                        }
                    }
                } else {
                    $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                }
            } else {
                if ($sOption == '4') {
                    $sDateTmp = date('Y-m-d');
                    $aData['SCH_START_TIME'] = date('Y-m-d', strtotime( $sDateTmp )) . ' ' . date('H:i:s', strtotime( $sTimeTmp ));
                    $aData['SCH_START_DATE'] = $aData['SCH_START_TIME'];
                    $aData['SCH_END_DATE'] = $aData['SCH_START_TIME'];
                }
                $aData['SCH_TIME_NEXT_RUN'] = $aData['SCH_START_TIME'];
                if ($sOption == '5') {
                    if ($aData['SCH_START_DATE'] != '') {
                        $sDateTmp = $aData['SCH_START_DATE'];
                    } else {
                        $sDateTmp = date('Y-m-d');
                        $aData['SCH_START_DATE'] = $sDateTmp;
                    }
                    $aData['SCH_END_DATE'] = date('Y-m-d', strtotime($sDateTmp)) . ' ' . date('H:i:s', strtotime($sTimeTmp));
                    $aData['SCH_START_TIME'] = time();
                    $aData['SCH_START_DATE'] = $aData['SCH_START_TIME'];
                    if ($aData['SCH_REPEAT_EVERY'] == "") {
                        throw (new \Exception( '`sch_repeat_every` can`t be null'));
                    }
                    $patternHour="/^([0-1][0-9]|[2][0-3])[\.]([0-5][0-9])$/";
                    if (!preg_match($patternHour, $aData['SCH_REPEAT_EVERY'])) {
                        throw (new \Exception( 'invalid value specified for `sch_repeat_every`. Expecting time in HH.MM format (The time can not be increased to 23.59)'));
                    }
                    $nextRun = $aData['SCH_REPEAT_EVERY'] * 60 * 60;
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                    $date = $aData['SCH_START_TIME'];
                    $date += $nextRun;
                    $date = date("Y-m-d H:i", $date);
                    $aData['SCH_TIME_NEXT_RUN'] = $date;
                }
            }
            if (trim( $aData['SCH_END_DATE'] ) != '') {
                $aData['SCH_END_DATE'] = $aData['SCH_END_DATE'];
            }
            if (! empty( $aData['SCH_REPEAT_TASK_CHK'] )) {
                $nOptEvery = $aData['SCH_REPEAT_EVERY_OPT'];
                if ($nOptEvery == 2) {
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'] * 60;
                } else {
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                }
            }
            if ((isset( $aData['CASE_SH_PLUGIN_UID'] )) && ($aData['CASE_SH_PLUGIN_UID'] != "")) {
                $aData['CASE_SH_PLUGIN_UID'] = $aData['CASE_SH_PLUGIN_UID'];
            }
            // check this data
            $aData['SCH_REPEAT_UNTIL'] = '';
            $aData['SCH_REPEAT_STOP_IF_RUNNING'] = '0';
            $aData['CASE_SH_PLUGIN_UID'] = null;
            //
            $oCaseScheduler->Update($aData);
            $oCriteria = $this->getCaseScheduler($sProcessUID, $sSchUID);
            return $oCriteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a case scheduler of a project
     *
     * @param string $sProcessUID
     * @param string $sSchUID
     *
     * @access public
     */
    public function deleteCaseScheduler($sProcessUID, $sSchUID)
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes". PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $oCaseScheduler = new \CaseScheduler();
            if (!isset($sSchUID)) {
                return;
            }
            $oCaseScheduler->remove($sSchUID);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

