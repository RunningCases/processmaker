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
            $aData = array_change_key_case($aData, CASE_UPPER);
            $oCaseScheduler = new \CaseScheduler();
            if (empty($aData)) {
                die( 'The information sended is empty!' );
            }        
            $aData['SCH_UID'] = \G::generateUniqueID();
            $aData['SCH_DEL_USER_PASS'] = md5( $aData['SCH_DEL_USER_PASS']);
            $aData['SCH_STATE'] = 'ACTIVE';
            $aData['SCH_LAST_STATE'] = 'CREATED'; // 'ACTIVE';
            $aData['USR_UID'] = $userUID;
            $sOption = $aData['SCH_OPTION'];
            if ($aData['SCH_START_DATE'] != '') {
                $sDateTmp = $aData['SCH_START_DATE'];
            } else {
                $sDateTmp = date( 'Y-m-d' );
            }
            $sTimeTmp = $aData['SCH_START_TIME'];
         //   $aData['SCH_START_TIME'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );
         //   $aData['SCH_START_DATE'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );
            $nActualTime = $aData['SCH_START_TIME']; // time();
            $sValue = '';
            $sDaysPerformTask = '';
            $sWeeks = '';
            $sMonths = '';
            $sMonths = '';
            $sStartDay = '';
            $nSW = 0;
            switch ($sOption) {
                case '1': // Option 1
                    $sValue = $aData['SCH_DAYS_PERFORM_TASK'];
                    switch ($sValue) {
                        case '1':
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'] . '|1';
                            break;
                        case '2':
                            $aData['SCH_OPTION'] = '2';
                            $aData['SCH_EVERY_DAYS'] = '1'; //check
                            $aData['SCH_WEEK_DAYS'] = '1|2|3|4|5|'; //check
                            break;
                        case '3': // Every [n] Days
                            $sDaysPerformTask = $aData['SCH_DAYS_PERFORM_TASK_OPT_3'];
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'] . '|' . $aData['SCH_DAYS_PERFORM_TASK_OPT_3'];
                            break;
                    }
                    break;
                case '2': // If the option is zero, set by default 1
                    if (empty( $aData['SCH_EVERY_DAYS'] )) {
                        $nEveryDays = 1;
                    } else {
                        $nEveryDays = $aData['SCH_EVERY_DAYS'];
                    }
                    $aData['SCH_EVERY_DAYS'] = $nEveryDays;
                    $sWeeks = '';
                    if (! empty( $aData['SCH_WEEK_DAYS'] )) {
                        $aWeekDays = $aData['SCH_WEEK_DAYS'];
                        foreach ($aWeekDays as $value) {
                            $sWeeks = $sWeeks . $value . '|';
                        }
                    }
                    if (! empty( $aData['SCH_WEEK_DAYS_2'] )) {
                        $aWeekDays2 = $aData['SCH_WEEK_DAYS_2'];
                        foreach ($aWeekDays2 as $value) {
                            $sWeeks = $sWeeks . $value . '|';
                        }
                    }
                    $sStartTime = $aData['SCH_START_TIME'];
                    $aData['SCH_WEEK_DAYS'] = $sWeeks;
                    break;
                case '3':
                    $nStartDay = $aData['SCH_START_DAY'];
                    if ($nStartDay == 1) {
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_1'];
                    } else {
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_2_WEEKS'] . '|' . $aData['SCH_START_DAY_OPT_2_DAYS_WEEK'];
                    }

                    $sMonths = '';
                    if (! empty( $aData['SCH_MONTHS'] )) {
                        $aMonths = $aData['SCH_MONTHS'];
                        foreach ($aMonths as $value) {
                            $sMonths = $sMonths . $value . '|';
                        }
                    }
                    if (! empty( $aData['SCH_MONTHS_2'] )) {
                        $aMonths2 = $aData['SCH_MONTHS_2'];
                        foreach ($aMonths2 as $value) {
                            $sMonths = $sMonths . $value . '|';
                        }
                    }
                    if (! empty( $aData['SCH_MONTHS_3'] )) {
                        $aMonths3 = $aData['SCH_MONTHS_3'];
                        foreach ($aMonths3 as $value) {
                            $sMonths = $sMonths . $value . '|';
                        }
                    }
                    $aData['SCH_MONTHS'] = $sMonths;
                    $sStartDay = $aData['SCH_START_DAY'];
                    $sValue = $nStartDay;
                    break;
            }
            if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
                if ($sStartDay == '') {
                    $sStartDay = date( 'Y-m-d' );
                }
                $dCurrentDay = date( "d" );
                $dCurrentMonth = date( "m" );
                $aStartDay = explode( "|", $aData['SCH_START_DAY'] );
                if ($sOption == '3' && $aStartDay[0] == '1') {
                    $monthsArray = explode( "|", $sMonths );
                    foreach ($monthsArray as $row) {
                        if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                            $startTime = $aData['SCH_START_TIME'] . ":00";
                            $aData['SCH_TIME_NEXT_RUN'] = date( 'Y' ) . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                            break;
                        } else {
                            $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                        }
                    }
                } else {
/*                    echo $sOption; echo " - ";
                    echo $sValue; echo " - "; echo $nActualTime; echo " - "; echo $sDaysPerformTask; echo " - "; echo $sWeeks; 
                    echo " - "; echo $sStartDay; echo " - "; echo $sMonths; echo " - "; echo $sDateTmp; die();
                    
                    */
               echo $sOption."*". $sValue."*". $nActualTime."*". $sDaysPerformTask."*". $sWeeks."*". $sStartDay ."*". $sMonths."<br>";
                    $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                echo $aData['SCH_TIME_NEXT_RUN']; die ();
                }
                
            } else {
                if ($sOption == '4') {
                    $aData['SCH_END_DATE'] = $aData['SCH_START_TIME'];
                }

                $aData['SCH_TIME_NEXT_RUN'] = $aData['SCH_START_TIME'];

                if ($sOption == 5) {
                    $aData['SCH_START_TIME'] = time();
                    $aData['SCH_START_DATE'] = $aData['SCH_START_TIME'];
                    $nextRun = $aData['SCH_REPEAT_EVERY'] * 60 * 60;
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                    $date = $aData['SCH_START_TIME'];
                    $date += $nextRun;
                    $date = date( "Y-m-d H:i", $date );
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
     * @param string $sSchUID
     * @param array  $aData
     * @param string $userUID
     *
     * @access public
     */
    public function updateCaseScheduler($sProcessUID, $sSchUID = '', $aData, $userUID)
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes". PATH_SEP . "model" . PATH_SEP . "CaseScheduler.php");
            $aData = array_change_key_case($aData, CASE_UPPER);
            if (empty( $aData )) {
                die( 'The information sended is empty!' );
            }
            $oCaseScheduler = new \CaseScheduler();
            
            $oCaseScheduler->Load($sSchUID);
            $aData['SCH_DEL_USER_NAME'] = $aData['SCH_USER_NAME'];
            if ($aData['SCH_USER_PASSWORD'] != 'DefaultPM') {
                $aData['SCH_DEL_USER_PASS'] = md5( $aData['SCH_USER_PASSWORD'] );
            }
            $aData['SCH_DEL_USER_UID'] = $aData['SCH_USER_UID'];
            $aData['USR_UID'] = $userUID;
            $sOption = $aData['SCH_OPTION'];
            $sDateTmp = $aData['SCH_START_DATE'];
            $sTimeTmp = $aData['SCH_START_TIME'];
//            $aData['SCH_START_TIME'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );
//            $aData['SCH_START_DATE'] = date( 'Y-m-d', strtotime( $sDateTmp ) ) . ' ' . date( 'H:i:s', strtotime( $sTimeTmp ) );
            $previousStartTime = date( 'Y-m-d', strtotime( $aData['PREV_SCH_START_DATE'] ) ) . ' ' . date( 'H:i:s', strtotime( $aData['PREV_SCH_START_TIME'] ) );
            $previousStartDate = date( 'Y-m-d', strtotime( $aData['PREV_SCH_START_DATE'] ) ) . ' ' . date( 'H:i:s', strtotime( $aData['PREV_SCH_START_TIME'] ) );
            $sValue = '';
            $sDaysPerformTask = '';
            $sWeeks = '';
            $sMonths = '';
            $sMonths = '';
            $sStartDay = '';
            $nSW = 0;
            switch ($sOption) {
                case '1':
                    // Option 1
                    $sValue = $aData['SCH_DAYS_PERFORM_TASK'];
                    switch ($sValue) {
                        case '1':
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'] . '|1';
                            break;
                        case '2':
                            $aData['SCH_OPTION'] = '2';
                            $aData['SCH_EVERY_DAYS'] = '1';
                            $aData['SCH_WEEK_DAYS'] = '1|2|3|4|5|';
                            break;
                        case '3': // Every [n] Days
                            $sDaysPerformTask = $aData['SCH_DAYS_PERFORM_TASK_OPT_3'];
                            $aData['SCH_DAYS_PERFORM_TASK'] = $aData['SCH_DAYS_PERFORM_TASK'] . '|' . $aData['SCH_DAYS_PERFORM_TASK_OPT_3'];
                            break;
                    }
                    break;
                case '2':
                    // If the option is zero, set by default 1
                    if (empty( $aData['SCH_EVERY_DAYS'] )) {
                        $nEveryDays = 1;
                    } else {
                        $nEveryDays = $aData['SCH_EVERY_DAYS'];
                    }
                    $aData['SCH_EVERY_DAYS'] = $nEveryDays;
                    $sWeeks = '';
                    if (! empty( $aData['SCH_WEEK_DAYS'] )) {
                        $aWeekDays = $aData['SCH_WEEK_DAYS'];
                        foreach ($aWeekDays as $value) {
                            $sWeeks = $sWeeks . $value . '|';
                        }
                    }
                    if (! empty( $aData['SCH_WEEK_DAYS_2'] )) {
                        $aWeekDays2 = $aData['SCH_WEEK_DAYS_2'];
                        foreach ($aWeekDays2 as $value) {
                            $sWeeks = $sWeeks . $value . '|';
                        }
                    }
                    $sStartTime = $aData['SCH_START_TIME'];
                    $aData['SCH_WEEK_DAYS'] = $sWeeks;
                    break;
                case '3':
                    $nStartDay = $aData['SCH_START_DAY'];
                    if ($nStartDay == 1) {
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_1'];
                    } else {
                        $aData['SCH_START_DAY'] = $nStartDay . '|' . $aData['SCH_START_DAY_OPT_2_WEEKS'] . '|' . $aData['SCH_START_DAY_OPT_2_DAYS_WEEK'];
                    }

                    $sMonths = '';
                    if (! empty( $aData['SCH_MONTHS'] )) {
                        $aMonths = $aData['SCH_MONTHS'];
                        foreach ($aMonths as $value) {
                            $sMonths = $sMonths . $value . '|';
                        }
                    }
                    if (! empty( $aData['SCH_MONTHS_2'] )) {
                        $aMonths2 = $aData['SCH_MONTHS_2'];
                        foreach ($aMonths2 as $value) {
                            $sMonths = $sMonths . $value . '|';
                        }
                    }
                    if (! empty( $aData['SCH_MONTHS_3'] )) {
                        $aMonths3 = $aData['SCH_MONTHS_3'];
                        foreach ($aMonths3 as $value) {
                            $sMonths = $sMonths . $value . '|';
                        }
                    }
                    $aData['SCH_MONTHS'] = $sMonths;
                    $sStartDay = $aData['SCH_START_DAY'];
                    $sValue = $nStartDay;
                    break;

            }
            if (trim( $aData['SCH_END_DATE'] ) != '') {
                $aData['SCH_END_DATE'] = $aData['SCH_END_DATE'];
            }
            // if the start date has changed then recalculate the next run time
            if ($aData['SCH_START_DATE'] == $aData['PREV_SCH_START_DATE']) {
                $recalculateDate = false;
            } else {
                $recalculateDate = true;
            }
            if (date( 'H:i:s', strtotime( $aData['SCH_START_TIME'] ) ) == date( 'H:i:s', strtotime( $aData['PREV_SCH_START_TIME'] ) )) {
                $recalculateTime = false;
            } else {
                $recalculateTime = true;
            }
            // if the start date has changed then recalculate the next run time
            $nActualTime = $aData['SCH_START_TIME'];
            if (($sOption != '1') && ($sOption != '4') && ($sOption != '5')) {
                if ($sStartDay == '') {
                    $sStartDay = date( 'Y-m-d' );
                }
                $dCurrentDay = date( "d" );
                $dCurrentMonth = date( "m" );
                $aStartDay = explode( "|", $aData['SCH_START_DAY'] );
                if ($sOption == '3' && $aStartDay[0] == '1') {
                    $monthsArray = explode( "|", $sMonths );
                    foreach ($monthsArray as $row) {
                        if ($dCurrentMonth == $row && $dCurrentDay < $aStartDay[1]) {
                            $startTime = $_POST['form']['SCH_START_TIME'] . ":00";
                            if ($recalculateDate) {
                                $aData['SCH_TIME_NEXT_RUN'] = date( 'Y' ) . '-' . $row . '-' . $aStartDay[1] . ' ' . $startTime;
                            } elseif ($recalculateTime) {
                                $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->getSchTimeNextRun( "Y-m-d" ) . " " . $_POST['form']['SCH_START_TIME'] . ":00";
                            }
                            break;
                        } else {
                            if ($recalculateDate) {
                                $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                            } elseif ($recalculateTime) {
                                $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->getSchTimeNextRun( "Y-m-d" ) . " " . $_POST['form']['SCH_START_TIME'] . ":00";
                            }
                        }
                    }
                } else {
                    if ($recalculateDate) {
                        $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->updateNextRun( $sOption, $sValue, $nActualTime, $sDaysPerformTask, $sWeeks, $sStartDay, $sMonths, $sDateTmp );
                    } elseif ($recalculateTime) {
                        $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->getSchTimeNextRun( "Y-m-d" ) . " " . $_POST['form']['SCH_START_TIME'] . ":00";
                    }
                }
            } else {
                if ($sOption == '4') {
                    $aData['SCH_END_DATE'] = $aData['SCH_START_TIME'];
                }
                if ($recalculateDate) {
                    $aData['SCH_TIME_NEXT_RUN'] = $aData['SCH_START_TIME'];
                } elseif ($recalculateTime) {
                    $aData['SCH_TIME_NEXT_RUN'] = $oCaseScheduler->getSchTimeNextRun( "Y-m-d" ) . " " . $aData['SCH_START_TIME'] . ":00";
                }
                if ($sOption == '5') {
                    $date = $oCaseScheduler->getSchLastRunTime();
                    if ($date == null) {
                        $date = $oCaseScheduler->getSchStartTime();
                    }
                    $date = strtotime( $date );
                    $nextRun = $aData['SCH_REPEAT_EVERY'] * 60 * 60;
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                    $date += $nextRun;
                    $date = date( "Y-m-d H:i", $date );
                    $aData['SCH_TIME_NEXT_RUN'] = $date;
                }
            }
            if (! empty( $aData['SCH_REPEAT_TASK_CHK'] )) {
                $nOptEvery = $aData['SCH_REPEAT_EVERY_OPT'];
                if ($nOptEvery == 2) {
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'] * 60;
                } else {
                    $aData['SCH_REPEAT_EVERY'] = $aData['SCH_REPEAT_EVERY'];
                }
            }
            $aData["SCH_UID"] = $sSchUID;
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
            if ( !isset($sSchUID ) ) {
                return;
            }
            $oCaseScheduler->remove($sSchUID);
        } catch (\Exception $e) {
                throw $e;
        }
    }

}

