<?php

/**
 * AppCacheView.php
 * @deprecated Class deprecated in Release 3.6.x
 */
class AppCacheView extends BaseAppCacheView
{
    public $confCasesList;
    public $pathToAppCacheFiles;

    public function getAllCounters($aTypes, $userUid, $flagPausedSupervisor = true)
    {
        $aResult = array();

        foreach ($aTypes as $type) {
            $aResult[$type] = $this->getListCounters($type, $userUid, $flagPausedSupervisor);
        }

        return $aResult;
    }

    public function getListCounters($type, $userUid, $flagPausedSupervisor = true)
    {
        $distinct = true;

        switch ($type) {
            case 'to_do':
                $criteria = $this->getToDoCountCriteria($userUid);
                $distinct = false;
                break;
            case 'draft':
                $criteria = $this->getDraftCountCriteria($userUid);
                break;
            case 'sent':
                $criteria = $this->getSentCountCriteria($userUid);
                break;
            case 'selfservice':
                $criteria = $this->getUnassignedCountCriteria($userUid);
                $distinct = false;
                break;
            case 'paused':
                $criteria = $this->getPausedCountCriteria($userUid, $flagPausedSupervisor);
                break;
            case 'completed':
                $criteria = $this->getCompletedCountCriteria($userUid);
                break;
            case 'cancelled':
                $criteria = $this->getCancelledCountCriteria($userUid);
                break;
            case 'to_revise':
                $criteria = $this->getToReviseCountCriteria($userUid);
                break;
            default:
                return $type;
        }
        $arrayTaskTypeToExclude = array("WEBENTRYEVENT", "END-MESSAGE-EVENT", "START-MESSAGE-EVENT", "INTERMEDIATE-THROW-MESSAGE-EVENT", "INTERMEDIATE-CATCH-MESSAGE-EVENT");
        $criteria->addJoin(AppCacheViewPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
        $criteria->add(TaskPeer::TAS_TYPE, $arrayTaskTypeToExclude, Criteria::NOT_IN);

        return AppCacheViewPeer::doCount($criteria, $distinct);
    }

    /**
     * gets the todo cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getToDo($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria  = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('todo');
        }

        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_RISK_DATE);

        $criteria->add(AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL);

        if (!empty($userUid)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * gets the todo cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToDoCountCriteria($userUid)
    {
        return $this->getToDo($userUid, true);
    }

    /**
     * gets the todo cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToDoListCriteria($userUid)
    {
        return $this->getToDo($userUid, false);
    }

    /**
     * gets the DRAFT cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getDraft($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('draft');
        }

        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $criteria->add(AppCacheViewPeer::APP_STATUS, "DRAFT", CRITERIA::EQUAL);

        if (!empty($userUid)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        //$criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * gets the DRAFT cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getDraftCountCriteria($userUid)
    {
        return $this->getDraft($userUid, true);
    }

    /**
     * gets the DRAFT cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getDraftListCriteria($userUid)
    {
        return $this->getDraft($userUid, false);
    }

    /**
     * Gets the criteria object of the sent cases
     *
     * Return the criteria object of the sent cases
     *
     * @param string $userUid The user ID
     * @param bool $doCount If true this will return the criteria for count cases only
     * @return criteria Object criteria
     */
    public function getSent($userUid, $doCount)
    {
        //Adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList["PMTable"]) && !empty($this->confCasesList["PMTable"])) {
            $criteria = new Criteria("workflow");
        } else {
            $criteria = $this->addPMFieldsToCriteria("sent");
        }

        if (!empty($userUid)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        if (!$doCount) {
            $criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
        }

        return $criteria;
    }

    /**
     * gets the SENT cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSentCountCriteria($userUid)
    {
        /*$criteria = new Criteria('workflow');
        $criteria = $this->addPMFieldsToCriteria('sent');

        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        return $criteria;*/
        //return $this->getSentListCriteria($userUid);

        return $this->getSent($userUid, true);
    }

    /**
     * gets the SENT cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSentListCriteria ($userUid)
    {
        /*
        $criteria = $this->addPMFieldsToCriteria('sent');

        //$criteria->addAsColumn('MAX_DEL_INDEX', 'MAX(' . AppDelegationPeer::DEL_INDEX . ')');
        //$criteria->addJoin(AppCacheViewPeer::APP_UID , AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        //$criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
        //$criteria->addGroupByColumn(AppCacheViewPeer::APP_);
        $criteria->add(AppCacheViewPeer::PREVIOUS_USR_UID, $userUid);
        $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);

        return $criteria;
        */

        return $this->getSent($userUid, false);
    }

    public function getSentListProcessCriteria($userUid)
    {
        $criteria = $this->addPMFieldsToCriteria('sent');
        $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        return $criteria;
    }

    /**
     * get user's SelfService tasks
     * @param string $sUIDUser
     * @return $rows
     */
    public function getSelfServiceTasks($userUid = '')
    {
        $rows[] = array();
        $tasks  = array();

        //check self service tasks assigned directly to this user
        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(
            $c->getNewCriterion(TaskPeer::TAS_GROUP_VARIABLE, '')->addOr(
                $c->getNewCriterion(TaskPeer::TAS_GROUP_VARIABLE, null, Criteria::ISNULL))
        );
        $c->add(TaskUserPeer::USR_UID, $userUid);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        //check groups assigned to SelfService task
        $group = new Groups();
        $aGroups = $group->getActiveGroupsForAnUser($userUid);

        $c = new Criteria();
        $c->clearSelectColumns();
        $c->addSelectColumn(TaskPeer::TAS_UID);
        $c->addSelectColumn(TaskPeer::PRO_UID);
        $c->addJoin(TaskPeer::PRO_UID, ProcessPeer::PRO_UID, Criteria::LEFT_JOIN);
        $c->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);
        $c->add(ProcessPeer::PRO_STATUS, 'ACTIVE');
        $c->add(TaskPeer::TAS_ASSIGN_TYPE, 'SELF_SERVICE');
        $c->add(
            $c->getNewCriterion(TaskPeer::TAS_GROUP_VARIABLE, '')->addOr(
                $c->getNewCriterion(TaskPeer::TAS_GROUP_VARIABLE, null, Criteria::ISNULL))
        );
        $c->add(TaskUserPeer::USR_UID, $aGroups, Criteria::IN);

        $rs = TaskPeer::doSelectRS($c);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();

        while (is_array($row)) {
            $tasks[] = $row['TAS_UID'];
            $rs->next();
            $row = $rs->getRow();
        }

        return $tasks;
    }

    public function getSelfServiceCasesByEvaluate($userUid)
    {
        try {
            $arrayAppAssignSelfServiceValueData = array();

            //Get APP_UIDs
            $group = new Groups();
            $arrayUid   = $group->getActiveGroupsForAnUser($userUid); //Set UIDs of Groups (Groups of User)
            $arrayUid[] = $userUid;                                   //Set UID of User

            $criteria = new Criteria("workflow");

            $criteria->setDistinct();
            $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::APP_UID);
            $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::DEL_INDEX);
            $criteria->addSelectColumn(AppAssignSelfServiceValuePeer::TAS_UID);

            $criteria->add(AppAssignSelfServiceValuePeer::ID, AppAssignSelfServiceValuePeer::ID . 
            " IN (SELECT " . AppAssignSelfServiceValueGroupPeer::ID . " FROM " . AppAssignSelfServiceValueGroupPeer::TABLE_NAME . 
            " WHERE " . AppAssignSelfServiceValueGroupPeer::GRP_UID . " IN ('" . implode("','", $arrayUid) . "'))", Criteria::CUSTOM);

            $rsCriteria = AppAssignSelfServiceValuePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayAppAssignSelfServiceValueData[] = array(
                    "APP_UID" => $row["APP_UID"],
                    "DEL_INDEX" => $row["DEL_INDEX"],
                    "TAS_UID" => $row["TAS_UID"]
                );
            }

            //Return
            return $arrayAppAssignSelfServiceValueData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * gets the UNASSIGNED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getUnassigned($userUid, $doCount)
    {
        //get the valid selfservice tasks for this user

        $oCase = new Cases();
        $tasks = $this->getSelfServiceTasks( $userUid );
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('unassigned');
        }

        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $criteria->add(AppCacheViewPeer::USR_UID, '');

        $arrayAppAssignSelfServiceValueData = $this->getSelfServiceCasesByEvaluate($userUid);

        if (!empty($arrayAppAssignSelfServiceValueData)) {
            //Self Service Value Based Assignment
            $criterionAux = null;

            foreach ($arrayAppAssignSelfServiceValueData as $value) {
                if (is_null($criterionAux)) {
                    $criterionAux = $criteria->getNewCriterion(AppCacheViewPeer::APP_UID, $value["APP_UID"], Criteria::EQUAL)->addAnd(
                                    $criteria->getNewCriterion(AppCacheViewPeer::DEL_INDEX, $value["DEL_INDEX"], Criteria::EQUAL))->addAnd(
                                    $criteria->getNewCriterion(AppCacheViewPeer::TAS_UID, $value["TAS_UID"], Criteria::EQUAL));
                } else {
                    $criterionAux = $criteria->getNewCriterion(AppCacheViewPeer::APP_UID, $value["APP_UID"], Criteria::EQUAL)->addAnd(
                                    $criteria->getNewCriterion(AppCacheViewPeer::DEL_INDEX, $value["DEL_INDEX"], Criteria::EQUAL))->addAnd(
                                    $criteria->getNewCriterion(AppCacheViewPeer::TAS_UID, $value["TAS_UID"], Criteria::EQUAL))->addOr(
                                        $criterionAux
                                    );
                }
            }

            $criteria->add(
                $criterionAux->addOr($criteria->getNewCriterion(AppCacheViewPeer::TAS_UID, $tasks, Criteria::IN))
            );
        } else {
            //Self Service
            $criteria->add(AppCacheViewPeer::TAS_UID, $tasks, Criteria::IN);
        }

        return $criteria;
    }

    /**
     * gets the UNASSIGNED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getUnassignedCountCriteria($userUid)
    {
        return $this->getUnassigned($userUid, true);
    }

    /**
     * gets the UNASSIGNED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getUnassignedListCriteria($userUid)
    {
        return $this->getUnassigned($userUid, false);
    }

    /**
     * gets the PAUSED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @param bool $flagSupervisor Flag to include the records of the supervisor
     * @return Criteria object $Criteria
     */
    public function getPaused($userUid, $doCount, $flagSupervisor = true)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('paused');
        }

        $processUser = new ProcessUser();
        $listProcess = $processUser->getProUidSupervisor($userUid);

        //add a validation to show the processes of which $userUid is supervisor
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);

        if (!empty($userUid)) {
            $criterionAux = $criteria->getNewCriterion(AppCacheViewPeer::USR_UID, $userUid, Criteria::EQUAL);

            if ($flagSupervisor && !empty($listProcess)) {
                $criterionAux = $criterionAux->addOr(
                    $criteria->getNewCriterion(AppCacheViewPeer::PRO_UID, $listProcess, Criteria::IN)
                );
            }

            $criteria->add($criterionAux);
        } else {
            if ($flagSupervisor && !empty($listProcess)) {
                $criteria->add(AppCacheViewPeer::PRO_UID, $listProcess, Criteria::IN);
            }
        }

        //join with APP_DELAY table using APP_UID and DEL_INDEX
        $arrayCondition = array();
        $arrayCondition[] = array(AppCacheViewPeer::APP_UID, AppDelayPeer::APP_UID);
        $arrayCondition[] = array(AppCacheViewPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
        $criteria->addJoinMC($arrayCondition, Criteria::LEFT_JOIN);

        $criteria->add(
            $criteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, null, Criteria::ISNULL)->
            addOr($criteria->getNewCriterion(AppDelayPeer::APP_DISABLE_ACTION_USER, 0))
        );

        $criteria->add(AppDelayPeer::APP_DELAY_UID, null, Criteria::ISNOTNULL);
        $criteria->add(AppDelayPeer::APP_TYPE, 'PAUSE');

        return $criteria;
    }

    /**
     * gets the PAUSED cases list criteria for count
     * param $userUid the current userUid
     * @param bool $flagSupervisor Flag to include the records of the supervisor
     * @return Criteria object $Criteria
     */
    public function getPausedCountCriteria($userUid, $flagSupervisor = true)
    {
        return $this->getPaused($userUid, true, $flagSupervisor);
    }

    /**
     * gets the PAUSED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getPausedListCriteria($userUid)
    {
        return $this->getPaused($userUid, false);
    }

    /**
     * gets the TO_REVISE cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getToRevise($userUid, $doCount)
    {
        $processUser = new ProcessUser();
        $listProcess = $processUser->getProUidSupervisor($userUid);

        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $c = new Criteria('workflow');
        } else {
            $c = $this->addPMFieldsToCriteria('todo');
        }

        $c->add(AppCacheViewPeer::PRO_UID, $listProcess, Criteria::IN);
        $c->add(AppCacheViewPeer::APP_STATUS, 'TO_DO');
        $c->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $c->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $c->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $c;
    }

    /**
     * gets the ToRevise cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToReviseCountCriteria($userUid)
    {
        return $this->getToRevise($userUid, true);
    }

    /**
     * gets the PAUSED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getToReviseListCriteria($userUid)
    {
        return $this->getToRevise($userUid, false);
    }

    /**
     * gets the COMPLETED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getCompleted($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('completed');
        }

        $criteria->add(AppCacheViewPeer::APP_STATUS, "COMPLETED", CRITERIA::EQUAL);

        if (!empty($userUid)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        if (!$doCount) {
            $criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
        }

        return $criteria;
    }

    /**
     * gets the COMPLETED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCompletedCountCriteria($userUid)
    {
        return $this->getCompleted($userUid, true);
    }

    /**
     * gets the COMPLETED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCompletedListCriteria($userUid)
    {
        return $this->getCompleted($userUid, false);
    }

    /**
     * gets the CANCELLED cases list criteria
     * param $userUid the current userUid
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getCancelled($userUid, $doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('cancelled');
        }

        $criteria->add(AppCacheViewPeer::APP_STATUS, "CANCELLED", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::DEL_LAST_INDEX, '1', Criteria::EQUAL);

        if (!empty($userUid)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'CLOSED');

        return $criteria;
    }

    /**
     * gets the CANCELLED cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCancelledCountCriteria($userUid)
    {
        return $this->getCancelled($userUid, true);
    }

    /**
     * gets the CANCELLED cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getCancelledListCriteria($userUid)
    {
        return $this->getCancelled($userUid, false);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSearchCountCriteria()
    {
        //$criteria = new Criteria('workflow');
        //$criteria = $this->addPMFieldsToCriteria('sent');

        //return $criteria;
        //return $this->getSearchCriteria(true);
        return $this->getSearchListCriteria();
    }

    public function getSearchAllCount()
    {
        $criteriaCount = new Criteria('workflow');
        $totalCount = ApplicationPeer::doCount($criteriaCount);

        return $totalCount;
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSearchListCriteria()
    {
        $criteria = $this->addPMFieldsToCriteria('sent');

        $criteria->addAsColumn("MAX_DEL_INDEX", AppCacheViewPeer::DEL_INDEX);
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        $criteria->add(AppCacheViewPeer::DEL_LAST_INDEX, 1);

        return $criteria;
        //return $this->getSearchCriteria(false);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for count
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSimpleSearchCountCriteria()
    {
        //$criteria = new Criteria('workflow');
        $criteria = $this->addPMFieldsToCriteria('sent');
        $criteria->add(AppCacheViewPeer::USR_UID, $_SESSION['USER_LOGGED']);

        return $criteria;
        //return $this->getSearchCriteria(true);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSimpleSearchListCriteria()
    {
        $criteria = $this->addPMFieldsToCriteria('sent');
        $criteria->addAsColumn("DEL_INDEX", AppCacheViewPeer::DEL_INDEX);
        $criteria->add(AppCacheViewPeer::USR_UID, $_SESSION['USER_LOGGED']);
        //$criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        $criteria->add(AppCacheViewPeer::DEL_LAST_INDEX, 1);

        return $criteria;
        //return $this->getSearchCriteria(false);
    }

    /**
     * gets the ADVANCED SEARCH cases list criteria for STATUS
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */
    public function getSearchStatusCriteria()
    {
        $criteria = new Criteria('workflow');

        return $criteria;
    }

    /**
     * gets the SENT cases list criteria for list
     * param $userUid the current userUid
     * @return Criteria object $Criteria
     */

    /**
     * gets the cases list criteria using the advanced search
     * param $doCount if true this will return the criteria for count cases only
     * @return Criteria object $Criteria
     */
    public function getSearchCriteria($doCount)
    {
        //adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount) {
            $criteria = new Criteria('workflow');
        } else {
            $criteria = $this->addPMFieldsToCriteria('sent');
        }

        //$criteria->add(AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL);
        $criteria->add(AppCacheViewPeer::DEL_INDEX, 1);

        //$criteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        //$criteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        //$criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');

        return $criteria;
    }

    /**
     * loads the configuration fields from the database based in an action parameter
     * then assemble the Criteria object with these data.
     * @param  String $action
     * @return Criteria object $Criteria
     */
    public function addPMFieldsToCriteria($action)
    {
        $caseColumns = array();

        if (!class_exists('AdditionalTables')) {
            require_once ("classes/model/AdditionalTables.php");
        }

        $caseReaderFields = array();
        $oCriteria = new Criteria('workflow');
        $oCriteria->clearSelectColumns();

        //Default configuration fields array
        $defaultFields = $this->getDefaultFields();

        //If there is PMTable for this case list
        if (is_array($this->confCasesList) && count($this->confCasesList) > 0 && isset($this->confCasesList["PMTable"]) && trim($this->confCasesList["PMTable"]) != "") {
            //Getting the table name
            $additionalTableUid = $this->confCasesList["PMTable"];

            $additionalTable = AdditionalTablesPeer::retrieveByPK($additionalTableUid);
            $tableName = $additionalTable->getAddTabName();

            $additionalTable = new AdditionalTables();
            $tableData = $additionalTable->load($additionalTableUid, true);

            $tableField = array();

            foreach ($tableData["FIELDS"] as $arrayField) {
                $tableField[] = $arrayField["FLD_NAME"];
            }

            foreach ($this->confCasesList["second"]["data"] as $fieldData) {
                $fieldData['name'] = ($fieldData['name'] == 'APP_STATUS_LABEL')? 'APP_STATUS' : $fieldData['name'];

                if (in_array($fieldData["name"], $defaultFields)) {
                    switch ($fieldData["fieldType"]) {
                        case "case field":
                            $configTable = "APP_CACHE_VIEW";
                            break;
                        case "delay field":
                            $configTable = "APP_DELAY";
                            break;
                        default:
                            $configTable = "APP_CACHE_VIEW";
                            break;
                    }

                    $fieldName = $configTable . "." . $fieldData["name"];
                    $oCriteria->addSelectColumn($fieldName);
                } else {
                    if (in_array($fieldData["name"], $tableField)) {
                        $fieldName = $tableName . "." . $fieldData["name"];
                        $oCriteria->addSelectColumn($fieldName);
                    }
                }
            }

            //add the default and hidden DEL_INIT_DATE
            $oCriteria->addSelectColumn('APP_CACHE_VIEW.DEL_INIT_DATE');
            //$oCriteria->addAlias("PM_TABLE", $tableName);

            //Add the JOIN
            $oCriteria->addJoin(AppCacheViewPeer::APP_UID, $tableName.'.APP_UID', Criteria::LEFT_JOIN);

            return $oCriteria;
        } else {
            //This list do not have a PMTable
            if (is_array($this->confCasesList) && isset($this->confCasesList["second"]) && count($this->confCasesList["second"]["data"]) > 0) {
                foreach ($this->confCasesList["second"]["data"] as $fieldData) {
                    if (in_array($fieldData["name"], $defaultFields)) {
                        switch ($fieldData["fieldType"]) {
                            case "case field":
                                $configTable = "APP_CACHE_VIEW";
                                break;
                            case "delay field":
                                $configTable = "APP_DELAY";
                                break;
                            default:
                                $configTable = "APP_CACHE_VIEW";
                                break;
                        }

                        $fieldName = $configTable . "." . $fieldData["name"];
                        $oCriteria->addSelectColumn($fieldName);
                    }
                }
            } else {
                foreach (AppCacheViewPeer::getFieldNames(BasePeer::TYPE_FIELDNAME) as $field) {
                    $oCriteria->addSelectColumn("APP_CACHE_VIEW.$field");
                }
            }

            //add the default and hidden DEL_INIT_DATE
            $oCriteria->addSelectColumn('APP_CACHE_VIEW.DEL_INIT_DATE');

            return $oCriteria;
        }
    }

    /**
     * gets the Criteria object for the general cases list.
     * @param Boolean $doCount
     * @return Criteria
     */
    public function getGeneralCases($doCount = 'false')
    {
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $oCriteria = new Criteria('workflow');
        } else {
            $oCriteria = $this->addPMFieldsToCriteria('completed');
        }

        $oCriteria->addAsColumn('DEL_INDEX', 'MIN(' . AppCacheViewPeer::DEL_INDEX . ')');
        $oCriteria->addJoin(AppCacheViewPeer::APP_UID, AppDelegationPeer::APP_UID, Criteria::LEFT_JOIN);
        $oCriteria->add(
            $oCriteria->getNewCriterion(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN')->
            addOr(
                $oCriteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, 'COMPLETED')->
                addAnd($oCriteria->getNewCriterion(AppDelegationPeer::DEL_PREVIOUS, 0))
            )
        );

        if (!$doCount) {
            $oCriteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
        }

        //$oCriteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for count
     * @return Criteria object $Criteria
     */
    public function getAllCasesCountCriteria($userUid)
    {
        $oCriteria = $this->getGeneralCases(true);

        if (!empty($userUid)) {
            $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for list
     * @return Criteria object $Criteria
     */
    public function getAllCasesListCriteria($userUid)
    {
        $oCriteria = $this->getGeneralCases(false);

        if (!empty($userUid)) {
            $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        return $oCriteria;
    }

    /**
     * Gets the SQL string for an case
     *
     * Return the SQL string for an case
     *
     * @param string $fieldAppUid The field APP_UID
     * @param string $fieldDelIndex The field DEL_INDEX
     * @return string SQL string
     */
    public function getAppDelaySql($fieldAppUid, $fieldDelIndex)
    {
        $sql = "SELECT DISTINCT " . AppDelayPeer::APP_UID . "
                FROM   " . AppDelayPeer::TABLE_NAME . "
                WHERE  " . AppDelayPeer::APP_UID . " = $fieldAppUid AND
                       " . AppDelayPeer::APP_DEL_INDEX . " = $fieldDelIndex AND
                       (" . AppDelayPeer::APP_DISABLE_ACTION_USER . " IS NULL OR " . AppDelayPeer::APP_DISABLE_ACTION_USER . " = '0') AND
                       " . AppDelayPeer::APP_DELAY_UID . " IS NOT NULL AND
                       " . AppDelayPeer::APP_TYPE . " = 'PAUSE'";

        return $sql;
    }

    /**
     * Gets the criteria object of all cases
     *
     * Return the criteria object of all cases
     *
     * @param string $userUid The user ID
     * @param bool $doCount If true this will return the criteria for count cases only
     * @return criteria Object criteria
     */
    public function getAllCases2($userUid, $doCount)
    {
        //Adding configuration fields from the configuration options
        //and forming the criteria object
        if ($doCount && !isset($this->confCasesList["PMTable"]) && !empty($this->confCasesList["PMTable"])) {
            $criteria = new Criteria("workflow");
        } else {
            $criteria = $this->addPMFieldsToCriteria("all");
        }

        if (!empty($userUid)) {
            $criteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        //Paused
        $sqlAppDelay = $this->getAppDelaySql(AppCacheViewPeer::APP_UID, AppCacheViewPeer::DEL_INDEX);

        $criteria->add(
            //ToDo - getToDo()
            $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, "TO_DO", CRITERIA::EQUAL)->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL))->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::APP_THREAD_STATUS, "OPEN"))->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN"))
        )->addOr(
            //Draft - getDraft()
            $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, "DRAFT", CRITERIA::EQUAL)->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::APP_THREAD_STATUS, "OPEN"))->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN"))
        )->addOr(
            //Paused
            $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, array("DRAFT", "TO_DO"), Criteria::IN)->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::APP_UID, AppCacheViewPeer::APP_UID . " IN ($sqlAppDelay)", Criteria::CUSTOM))
        )->addOr(
            //Cancelled - getCancelled()
            $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, "CANCELLED", CRITERIA::EQUAL)->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::DEL_THREAD_STATUS, "CLOSED"))->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::DEL_LAST_INDEX, '1', Criteria::EQUAL))
        )->addOr(
            $criteria->getNewCriterion(AppCacheViewPeer::APP_STATUS, "COMPLETED", CRITERIA::EQUAL)->addAnd(
            $criteria->getNewCriterion(AppCacheViewPeer::DEL_LAST_INDEX, '1', Criteria::EQUAL))
        );

        if (!$doCount) {
            //Completed - getCompleted()
            $criteria->addGroupByColumn(AppCacheViewPeer::APP_UID);
            $criteria->addGroupByColumn(AppCacheViewPeer::DEL_INDEX);
        }

        return $criteria;
    }

    /**
     * Gets the criteria object of all cases for the list
     *
     * Return the criteria object of all cases for the list
     *
     * @param string $userUid The user ID
     * @return criteria Object criteria
     */
    public function getAllCasesListCriteria2($userUid)
    {
        return $this->getAllCases2($userUid, false);
    }

    /**
     * Gets the criteria object of all cases for the count
     *
     * Return the criteria object of all cases for the count
     *
     * @param string $userUid The user ID
     * @return criteria Object criteria
     */
    public function getAllCasesCountCriteria2($userUid)
    {
        return $this->getAllCases2($userUid, true);
    }

    /**
     * gets the ALL cases list criteria for count
     * @return Criteria object $Criteria
     */
    public function getGeneralCountCriteria()
    {
        return $this->getGeneralCases(true);
    }

    /**
     * gets the ALL cases list criteria for list
     * @return Criteria object $Criteria
     */
    public function getGeneralListCriteria()
    {
        return $this->getGeneralCases(false);
    }

    public function getToReassign($userUid, $doCount)
    {
        if ($doCount && !isset($this->confCasesList['PMTable']) && !empty($this->confCasesList['PMTable'])) {
            $oCriteria = new Criteria('workflow');
        } else {
            $oCriteria = $this->addPMFieldsToCriteria('to_do');
        }

        $oCriteria->add(AppCacheViewPeer::APP_STATUS, 'TO_DO');

        if (!empty($userUid)) {
            $oCriteria->add(AppCacheViewPeer::USR_UID, $userUid);
        }

        $oCriteria->add(AppCacheViewPeer::DEL_FINISH_DATE, null, Criteria::ISNULL);
        $oCriteria->add(AppCacheViewPeer::APP_THREAD_STATUS, 'OPEN');
        $oCriteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, 'OPEN');
        //$oCriteria->addDescendingOrderByColumn(AppCacheViewPeer::APP_NUMBER);

        return $oCriteria;
    }

    /**
     * gets the ALL cases list criteria for count
     * @return Criteria object $Criteria
     */
    public function getToReassignCountCriteria($userUid)
    {
        return $this->getToReassign($userUid, true);
    }

    /**
     * gets the ALL cases list criteria for list
     * @return Criteria object $Criteria
     */
    public function getToReassignListCriteria($userUid)
    {
        return $this->getToReassign($userUid, false);
    }

    /**
     * gets the ALL cases list criteria for count by Supervisor
     * @return Criteria object $Criteria
     */
    public function getToReassignSupervisorCountCriteria($userUid)
    {
        GLOBAL $RBAC;
        $aUser = $RBAC->userObj->load( $_SESSION['USER_LOGGED'] );

        $processUser = new ProcessUser();
        $listProcess = $processUser->getProUidSupervisor($aUser['USR_UID']);
        $criteria = $this->getToReassign($userUid, true);
        $criteria->add(AppCacheViewPeer::PRO_UID, $listProcess, Criteria::IN);
        return $criteria;
    }

    /**
     * gets the ALL cases list criteria for list  by Supervisor
     * @return Criteria object $Criteria
     */
    public function getToReassignSupervisorListCriteria($userUid)
    {
        GLOBAL $RBAC;
        $aUser = $RBAC->userObj->load( $_SESSION['USER_LOGGED'] );

        $processUser = new ProcessUser();
        $listProcess = $processUser->getProUidSupervisor($aUser['USR_UID']);
        $criteria = $this->getToReassign($userUid, false);
        $criteria->add(AppCacheViewPeer::PRO_UID, $listProcess, Criteria::IN);
        return $criteria;
    }

    public function getDefaultFields()
    {
        return array_merge(
            AppCacheViewPeer::getFieldNames(BasePeer::TYPE_FIELDNAME),
            array(
                "APP_DELAY_UID",
                "APP_THREAD_INDEX",
                "APP_DEL_INDEX",
                "APP_TYPE",
                "APP_DELEGATION_USER",
                "APP_ENABLE_ACTION_USER",
                "APP_ENABLE_ACTION_DATE",
                "APP_DISABLE_ACTION_USER",
                "APP_DISABLE_ACTION_DATE",
                "APP_AUTOMATIC_DISABLED_DATE"
            )
        );
    }

    public function setPathToAppCacheFiles($path)
    {
        $this->pathToAppCacheFiles = $path;
    }

    public function getMySQLVersion()
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();
        $sql = "select version()  ";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
        $rs1->next();
        $row = $rs1->getRow();

        return $row[0];
    }

    public function checkGrantsForUser($root = false)
    {
        try {
            if ($root) {
                $con = Propel::getConnection("root");
            } else {
                $con = Propel::getConnection("workflow");
            }

            $stmt = $con->createStatement();
            $sql = "select CURRENT_USER(), USER() ";
            $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
            $rs1->next();
            $row = $rs1->getRow();
            $mysqlUser = str_replace('@', "'@'", $row[0]);
            $super = false;

            $sql = "SELECT *
                    FROM `information_schema`.`USER_PRIVILEGES`
                    WHERE GRANTEE = \"'$mysqlUser'\" ";

            $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
            $rs1->next();
            $row = $rs1->getRow();

            if ($row['PRIVILEGE_TYPE'] == 'SUPER') {
                $super = G::LoadTranslation('ID_TRUE'); //true;
            }

            return array('user' => $mysqlUser, 'super' => $super);
        } catch (Exception $e) {
            return array('error' => true, 'msg' => $e->getMessage());
        }
    }

    /**
     * search for table APP_CACHE_VIEW
     * @return void
     *
     */
    public function checkAppCacheView()
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        //check if table APP_CACHE_VIEW exists
        $sql = "SHOW TABLES";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
        $rs1->next();
        $found = false;

        while (is_array($row = $rs1->getRow()) && !$found) {
            if (strtolower($row[0]) == 'app_cache_view') {
                $found = true;
            }

            $rs1->next();
        }

        //now count how many records there are ..
        $count = '-';

        if ($found) {
            $oCriteria = new Criteria('workflow');
            $count = AppCacheViewPeer::doCount($oCriteria);
        }
        $found = $found ? G::LoadTranslation('ID_TRUE') : G::LoadTranslation('ID_FALSE');
        return array('found' => $found, 'count' => $count);
    }

    /**
     * Update the field APP_DELEGATION.DEL_LAST_INDEX
     */
    public function updateAppDelegationDelLastIndex($lang, $recreate = false)
    {
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $filenameSql = $this->pathToAppCacheFiles . "app_delegation_del_last_index_update.sql";

        if (!file_exists($filenameSql)) {
            throw (new Exception("file app_delegation_del_last_index_update.sql does not exist"));
        }

        //Delete trigger
        $rs = $stmt->executeQuery("DROP TRIGGER IF EXISTS APP_DELEGATION_UPDATE");

        //Update field
        $rs = $stmt->executeQuery(file_get_contents($filenameSql));

        //Create trigger
        $res = $this->triggerAppDelegationUpdate($lang, $recreate);

        return "done updated field in table APP_DELEGATION";
    }

    /**
     * populate (fill) the table APP_CACHE_VIEW
     * @return void
     */
    public function fillAppCacheView($lang)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $sql = "truncate table APP_CACHE_VIEW ";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        $filenameSql = $this->pathToAppCacheFiles . 'app_cache_view_insert.sql';

        if (!file_exists($filenameSql)) {
            throw (new Exception("file app_cache_view_insert.sql does not exist "));
        }

        $sql = explode(';', file_get_contents($filenameSql));

        foreach ($sql as $key => $val) {
            $val = str_replace('{lang}', $lang, $val);
            $stmt->executeQuery($val);
        }

        $sql = "select count(*) as CANT from APP_CACHE_VIEW ";
        $rs1 = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);
        $rs1->next();
        $row1 = $rs1->getRow();
        $cant = $row1['CANT'];

        return "done $cant rows in table APP_CACHE_VIEW";
    }

    /**
     * Insert an app delegatiojn trigger
     * @return void
     */
    public function triggerAppDelegationInsert($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery('Show TRIGGERS', ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APP_DELEGATION_INSERT') &&
                strtoupper($row['Table']) == 'APP_DELEGATION'
            ) {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APP_DELEGATION_INSERT');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . 'triggerAppDelegationInsert.sql';

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerAppDelegationInsert.sql does not exist "));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return G::LoadTranslation('ID_CREATED');
        }
            return G::LoadTranslation('ID_EXIST');
    }

    /**
     * update the App Delegation triggers
     * @return void
     */
    public function triggerAppDelegationUpdate($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APP_DELEGATION_UPDATE') &&
                strtoupper($row['Table']) == 'APP_DELEGATION'
            ) {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APP_DELEGATION_UPDATE');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . "triggerAppDelegationUpdate.sql";

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerAppDelegationUpdate.sql does not exist "));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return G::LoadTranslation('ID_CREATED');
        }

        return G::LoadTranslation('ID_EXIST');
    }

    /**
     * update the Application triggers
     * @return void
     */
    public function triggerApplicationUpdate($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APPLICATION_UPDATE') && strtoupper($row['Table']) == 'APPLICATION') {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APPLICATION_UPDATE');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . "triggerApplicationUpdate.sql";

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerApplicationUpdate.sql doesn't exist "));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return G::LoadTranslation('ID_CREATED');
        }

        return G::LoadTranslation('ID_EXIST');
    }

    /**
     * update the Application triggers
     * @return void
     */
    public function triggerApplicationDelete($lang, $recreate = false)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $rs->next();
        $row = $rs->getRow();
        $found = false;

        while (is_array($row)) {
            if (strtolower($row['Trigger'] == 'APPLICATION_DELETE') && strtoupper($row['Table']) == 'APPLICATION') {
                $found = true;
            }

            $rs->next();
            $row = $rs->getRow();
        }

        if ($recreate) {
            $rs = $stmt->executeQuery('DROP TRIGGER IF EXISTS APPLICATION_DELETE');
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . "triggerApplicationDelete.sql";

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerApplicationDelete.sql doesn't exist"));
            }

            $sql = file_get_contents ($filenameSql);
            $sql = str_replace('{lang}', $lang, $sql);
            $stmt->executeQuery($sql);

            return G::LoadTranslation('ID_CREATED');
        }

        return G::LoadTranslation('ID_EXIST');
    }

    public function triggerContentUpdate($lang, $recreate = false)
    {
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $rs = $stmt->executeQuery("Show TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $found = false;

        while ($rs->next()) {
            $row = $rs->getRow();

            if (strtolower($row["Trigger"] == "CONTENT_UPDATE") && strtoupper($row["Table"]) == "CONTENT") {
                $found = true;
            }
        }

        if ($recreate) {
            $rs = $stmt->executeQuery("DROP TRIGGER IF EXISTS CONTENT_UPDATE");
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . "triggerContentUpdate.sql";

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerContentUpdate.sql doesn't exist"));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace("{lang}", $lang, $sql);

            $stmt->executeQuery($sql);

            return G::LoadTranslation('ID_CREATED');
        }

        return G::LoadTranslation('ID_EXIST');
    }

    public function triggerSubApplicationInsert($lang, $recreate = false)
    {
        $cnn = Propel::getConnection("workflow");
        $stmt = $cnn->createStatement();

        $rs = $stmt->executeQuery("SHOW TRIGGERS", ResultSet::FETCHMODE_ASSOC);
        $found = false;

        while ($rs->next()) {
            $row = $rs->getRow();

            if (strtolower($row["Trigger"] == "SUB_APPLICATION_INSERT") && strtoupper($row["Table"]) == "SUB_APPLICATION") {
                $found = true;
            }
        }

        if ($recreate) {
            $rs = $stmt->executeQuery("DROP TRIGGER IF EXISTS SUB_APPLICATION_INSERT");
            $found = false;
        }

        if (!$found) {
            $filenameSql = $this->pathToAppCacheFiles . "triggerSubApplicationInsert.sql";

            if (!file_exists($filenameSql)) {
                throw (new Exception("file triggerSubApplicationInsert.sql doesn't exist"));
            }

            $sql = file_get_contents($filenameSql);
            $sql = str_replace("{lang}", $lang, $sql);

            $stmt->executeQuery($sql);

            return G::LoadTranslation("ID_CREATED");
        }

        return G::LoadTranslation("ID_EXIST");
    }


    /**
     * Retrieve the SQL code to create the APP_CACHE_VIEW triggers.
     *
     * @return array each value is a SQL statement to create a trigger.
     */
    public function getTriggers($lang)
    {
        $triggerFiles = array(
            'triggerApplicationDelete.sql',
            'triggerApplicationUpdate.sql',
            'triggerAppDelegationUpdate.sql',
            'triggerAppDelegationInsert.sql',
            "triggerSubApplicationInsert.sql",
            'triggerContentUpdate.sql'
        );

        $triggers = array();

        foreach ($triggerFiles as $triggerFile) {
            $trigger = file_get_contents($this->pathToAppCacheFiles . $triggerFile);

            if ($trigger === false) {
                throw new Exception("Could not read trigger contents in $triggerFile");
            }

            $trigger = str_replace('{lang}', $lang, $trigger);
            $triggers[$triggerFile] = $trigger;
        }

        return $triggers;
    }

    public function getFormatedUser($sFormat, $aCaseUser, $userIndex)
    {
        require_once('classes/model/Users.php');

        $oUser = new Users();

        try {
            $aCaseUserRecord = $oUser->load($aCaseUser[$userIndex]);
            $sCaseUser = G::getFormatUserList ($sFormat,$aCaseUserRecord);
            // . ' (' . $aCaseUserRecord['USR_USERNAME'] . ')';]
        } catch (Exception $e) {
            $sCaseUser = '';
        }

        return $sCaseUser;
    }

    public function replaceRowUserData($rowData)
    {
        try {
            $oConfig = new Configuration();
            $aConfig = $oConfig->load('ENVIRONMENT_SETTINGS');
            $aConfig = unserialize($aConfig['CFG_VALUE']);
        } catch (Exception $e) {
            // if there is no configuration record then.
            $aConfig['format'] = '@userName';
        }

        if (isset($rowData['USR_UID'])&&isset($rowData['APP_CURRENT_USER'])) {
            $rowData['APP_CURRENT_USER'] = $this->getFormatedUser($aConfig['format'],$rowData,'USR_UID');
        }

        if (isset($rowData['PREVIOUS_USR_UID'])&&isset($rowData['APP_DEL_PREVIOUS_USER'])) {
            $rowData['APP_DEL_PREVIOUS_USER'] = $this->getFormatedUser($aConfig['format'],$rowData,'PREVIOUS_USR_UID');
        }

        return ($rowData);
    }

    //Added By Qennix
    public function getTotalCasesByAllUsers()
    {
        $oCriteria = new Criteria("workflow");
        $oCriteria->addSelectColumn(AppCacheViewPeer::USR_UID);
        $oCriteria->addAsColumn("CNT", "COUNT(DISTINCT(APP_CACHE_VIEW.APP_UID))");

        $oCriteria->addJoin(AppCacheViewPeer::DEL_INDEX , AppDelayPeer::APP_DEL_INDEX, Criteria::LEFT_JOIN);

        $oCriteria->add(
            $oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, NULL, Criteria::ISNULL)->addOr(
            $oCriteria->getNewCriterion(AppDelayPeer::APP_TYPE, 'REASSIGN', Criteria::NOT_EQUAL))
        );

        $oCriteria->addGroupByColumn(AppCacheViewPeer::USR_UID);
        $dat = AppCacheViewPeer::doSelectRS($oCriteria);
        $dat->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $aRows = array();
        while ($dat->next()) {
            $row = $dat->getRow();
            $aRows[$row["USR_UID"]] = $row["CNT"];
        }

        return $aRows;
    }

    public function appTitleByTaskCaseLabelUpdate($taskUid, $lang, $cron = 0)
    {
        $taskDefTitle = null;

        $criteria = new Criteria("workflow");

        $criteria->addSelectColumn(ContentPeer::CON_VALUE);
        $criteria->add(ContentPeer::CON_CATEGORY, "TAS_DEF_TITLE");
        $criteria->add(ContentPeer::CON_ID, $taskUid);
        $criteria->add(ContentPeer::CON_LANG, $lang);

        $rsCriteria = ContentPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rsCriteria->next()) {
            $row = $rsCriteria->getRow();

            $taskDefTitle = $row["CON_VALUE"];
        }

        //Get cases
        $criteriaAPPCV = new Criteria("workflow");

        $criteriaAPPCV->setDistinct();
        $criteriaAPPCV->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteriaAPPCV->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
        $criteriaAPPCV->add(AppCacheViewPeer::TAS_UID, $taskUid);

        $rsCriteriaAPPCV = AppCacheViewPeer::doSelectRS($criteriaAPPCV);
        $rsCriteriaAPPCV->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        while ($rsCriteriaAPPCV->next()) {
            if ($cron == 1) {
                $arrayCron = unserialize(trim(@file_get_contents(PATH_DATA . "cron")));
                $arrayCron["processcTimeStart"] = time();
                @file_put_contents(PATH_DATA . "cron", serialize($arrayCron));
            }

            $row = $rsCriteriaAPPCV->getRow();

            $appcvAppUid = $row["APP_UID"];

            //Current task?
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
            $criteria->add(AppCacheViewPeer::APP_UID, $appcvAppUid);
            $criteria->add(AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN");
            $criteria->add(AppCacheViewPeer::TAS_UID, $taskUid);

            $rsCriteria = AppCacheViewPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $appTitle = $taskDefTitle;

                $app = new Application();
                $arrayAppField = $app->Load($appcvAppUid);

                $appTitle    = (!empty($appTitle))? $appTitle : "#" . $arrayAppField["APP_NUMBER"];
                $appTitleNew = G::replaceDataField($appTitle, unserialize($arrayAppField["APP_DATA"]), 'mysql', false);

                if (isset($arrayAppField["APP_TITLE"]) && $arrayAppField["APP_TITLE"] != $appTitleNew) {
                    //Updating the value in content, where...
                    $criteria1 = new Criteria("workflow");

                    $criteria1->add(ContentPeer::CON_CATEGORY, "APP_TITLE");
                    $criteria1->add(ContentPeer::CON_ID, $appcvAppUid);
                    $criteria1->add(ContentPeer::CON_LANG, $lang);

                    //Update set
                    $criteria2 = new Criteria("workflow");

                    $criteria2->add(ContentPeer::CON_VALUE, $appTitleNew);

                    BasePeer::doUpdate($criteria1, $criteria2, Propel::getConnection("workflow"));
                }
            }
        }
    }
    /**
     * Get all columns by APP_CACHE_VIEW
     *
     * @return object criteria
     */
    public function getSelAllColumns(){
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INDEX);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_LAST_INDEX);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_NUMBER);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_STATUS);
        $criteria->addSelectColumn(AppCacheViewPeer::USR_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::PREVIOUS_USR_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::TAS_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::PRO_UID);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELEGATE_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_INIT_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_TASK_DUE_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISH_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_THREAD_STATUS);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_THREAD_STATUS);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_TITLE);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_PRO_TITLE);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_TAS_TITLE);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_CURRENT_USER);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_DEL_PREVIOUS_USER);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_PRIORITY);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DURATION);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_QUEUE_DURATION);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELAY_DURATION);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_STARTED);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_FINISHED);
        $criteria->addSelectColumn(AppCacheViewPeer::DEL_DELAYED);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_CREATE_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_FINISH_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_UPDATE_DATE);
        $criteria->addSelectColumn(AppCacheViewPeer::APP_OVERDUE_PERCENTAGE);
        return $criteria;
    }

    /*----------------------------------********---------------------------------*/
    public function fillReportByUser ($dateInit, $dateFinish)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $filenameSql = $this->pathToAppCacheFiles . "triggerFillReportByUser.sql";

        if (!file_exists($filenameSql)) {
            throw (new Exception("file triggerFillReportByUser.sql doesn't exist"));
        }

        $sql = "TRUNCATE TABLE USR_REPORTING";
        $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        $sql = explode(';', file_get_contents($filenameSql));

        foreach ($sql as $key => $val) {
            $val = str_replace('{init_date}', $dateInit, $val);
            $val = str_replace('{finish_date}', $dateFinish, $val);
            $stmt->executeQuery($val);
        }
    }

    public function fillReportByProcess ($dateInit, $dateFinish)
    {
        $con = Propel::getConnection("workflow");
        $stmt = $con->createStatement();

        $filenameSql = $this->pathToAppCacheFiles . "triggerFillReportByProcess.sql";

        if (!file_exists($filenameSql)) {
            throw (new Exception("file triggerFillReportByProcess.sql doesn't exist"));
        }

        $sql = "TRUNCATE TABLE PRO_REPORTING";
        $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

        $sql = explode(';', file_get_contents($filenameSql));

        foreach ($sql as $key => $val) {
            $val = str_replace('{init_date}', $dateInit, $val);
            $val = str_replace('{finish_date}', $dateFinish, $val);
            $stmt->executeQuery($val);
        }
    }
    /*----------------------------------********---------------------------------*/
}

