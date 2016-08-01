<?php

namespace ProcessMaker\Core;


class RoutingScreen extends \Derivation
{
    /**
     * @param array $arrayData
     * @param string $taskUid
     * @return array
     * @throws \Exception
     */
    public function prepareInformation(array $arrayData, $taskUid = "")
    {
        try {
            $this->case = new \Cases();
            $task = new \Task();
            $arrayApplicationData = $this->case->loadCase($arrayData["APP_UID"]);

            $arrayNextTask = array();
            $arrayNextTaskDefault = array();
            $i = 0;

            $flagDefault = false;
            $aSecJoin = array();
            $count = 0;
            $routeData = $this->getRouteData($arrayData, $taskUid);
            foreach ($routeData as $arrayRouteData) {
                if ((int)($arrayRouteData["ROU_DEFAULT"]) == 1) {
                    $arrayNextTaskDefault = $arrayRouteData;
                    $flagDefault = true;
                    continue;
                }

                $flagAddDelegation = $this->executeScript($arrayRouteData, $arrayApplicationData["APP_DATA"]);

                if (trim($arrayRouteData['ROU_CONDITION']) == '' && $arrayRouteData['ROU_NEXT_TASK'] != '-1') {
                    $arrayTaskData = $task->load($arrayRouteData['ROU_NEXT_TASK']);

                    if ($arrayRouteData['ROU_TYPE'] != 'SEC-JOIN' && $arrayTaskData['TAS_TYPE'] == 'GATEWAYTOGATEWAY') {
                        $flagAddDelegation = true;
                    }

                    if ($arrayRouteData['ROU_TYPE'] == 'SEC-JOIN') {
                        $aSecJoin[$count]['ROU_PREVIOUS_TASK'] = $arrayRouteData['ROU_NEXT_TASK'];
                        $aSecJoin[$count]['ROU_PREVIOUS_TYPE'] = 'SEC-JOIN';
                        $count++;
                    }
                }

                if ($arrayRouteData['ROU_TYPE'] == 'EVALUATE' && !empty($arrayNextTask)) {
                    $flagAddDelegation = false;
                }

                if ($flagAddDelegation &&
                    preg_match("/^(?:EVALUATE|PARALLEL-BY-EVALUATION)$/", $arrayRouteData["ROU_TYPE"]) &&
                    trim($arrayRouteData["ROU_CONDITION"]) == ""
                ) {
                    $flagAddDelegation = false;
                }

                if ($flagAddDelegation) {
                    $arrayNextTask[++$i] = $this->prepareInformationTask($arrayRouteData);
                }
            }

            if (!$arrayNextTask && count($arrayNextTaskDefault) > 0) {
                $arrayNextTask[++$i] = $this->prepareInformationTask($arrayNextTaskDefault);
            }

            //Check Task GATEWAYTOGATEWAY, END-MESSAGE-EVENT, END-EMAIL-EVENT
            $arrayNextTaskBackup = $arrayNextTask;
            $arrayNextTask = array();
            $i = 0;
            foreach ($arrayNextTaskBackup as $value) {
                $arrayNextTaskData = $value;

                $regexpTaskTypeToInclude = "GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT|SCRIPT-TASK|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT";

                if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] != "-1" &&
                    preg_match("/^(?:" . $regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"])
                ) {
                    $arrayAux = $this->prepareInformation($arrayData, $arrayNextTaskData["NEXT_TASK"]["TAS_UID"]);

                    foreach ($arrayAux as $value2) {
                        $arrayNextTask[++$i] = $value2;
                        foreach ($aSecJoin as $rsj) {
                            $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TASK"] = $rsj["ROU_PREVIOUS_TASK"];
                            $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] = "SEC-JOIN";
                        }
                    }
                } else {
                    $regexpTaskTypeToInclude = "END-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT";

                    if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] == "-1" &&
                        preg_match("/^(?:" . $regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["TAS_TYPE"])
                    ) {
                        $arrayNextTaskData["NEXT_TASK"]["TAS_UID"] = $arrayNextTaskData["TAS_UID"] . "/" . $arrayNextTaskData["NEXT_TASK"]["TAS_UID"];
                    }

                    $arrayNextTask[++$i] = $arrayNextTaskData;
                    foreach ($aSecJoin as $rsj) {
                        $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TASK"] = $rsj["ROU_PREVIOUS_TASK"];
                        $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] = "SEC-JOIN";
                    }
                    //Start-Timer with Script-task
                    $criteriaE = new \Criteria("workflow");
                    $criteriaE->addSelectColumn(\ElementTaskRelationPeer::ELEMENT_UID);
                    $criteriaE->addJoin(\BpmnEventPeer::EVN_UID, \ElementTaskRelationPeer::ELEMENT_UID, \Criteria::LEFT_JOIN);
                    $criteriaE->add(\ElementTaskRelationPeer::TAS_UID, $arrayNextTaskData["TAS_UID"], \Criteria::EQUAL);
                    $criteriaE->add(\BpmnEventPeer::EVN_TYPE, 'START', \Criteria::EQUAL);
                    $criteriaE->add(\BpmnEventPeer::EVN_MARKER, 'TIMER', \Criteria::EQUAL);
                    $rsCriteriaE = \AppDelegationPeer::doSelectRS($criteriaE);
                    $rsCriteriaE->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                    while ($rsCriteriaE->next()) {
                        if ($arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"] == "SCRIPT-TASK") {
                            if (isset($arrayNextTaskData["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"]) && $arrayNextTaskData["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"] == "") {
                                $useruid = "00000000000000000000000000000001";
                                $userFields = $this->getUsersFullNameFromArray($useruid);
                                $arrayNextTask[$i]["NEXT_TASK"]["USER_ASSIGNED"] = $userFields;
                            }
                        }
                    }
                }
            }

            //1. There is no rule
            if (empty($arrayNextTask)) {
                $bpmn = new \ProcessMaker\Project\Bpmn();

                throw new \Exception(\G::LoadTranslation(
                    'ID_NO_DERIVATION_' . (($bpmn->exists($arrayApplicationData['PRO_UID'])) ? 'BPMN_RULE' : 'RULE')
                ));
            }

            //Return
            return $arrayNextTask;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getRouteData($arrayData, $taskUid)
    {
        $criteria = new \Criteria("workflow");

        $criteria->addSelectColumn(\RoutePeer::TAS_UID);
        $criteria->addSelectColumn(\RoutePeer::ROU_NEXT_TASK);
        $criteria->addSelectColumn(\RoutePeer::ROU_TYPE);
        $criteria->addSelectColumn(\RoutePeer::ROU_DEFAULT);
        $criteria->addSelectColumn(\RoutePeer::ROU_CONDITION);

        if ($taskUid != "") {
            $criteria->add(\RoutePeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn(\RoutePeer::ROU_CASE);

            $rsCriteria = \RoutePeer::doSelectRS($criteria);
        } else {
            $criteria->addJoin(\AppDelegationPeer::TAS_UID, \TaskPeer::TAS_UID, \Criteria::LEFT_JOIN);
            $criteria->addJoin(\AppDelegationPeer::TAS_UID, \RoutePeer::TAS_UID, \Criteria::LEFT_JOIN);
            $criteria->add(\AppDelegationPeer::APP_UID, $arrayData["APP_UID"], \Criteria::EQUAL);
            $criteria->add(\AppDelegationPeer::DEL_INDEX, $arrayData["DEL_INDEX"], \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn(\RoutePeer::ROU_CASE);

            $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);
        }
        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $array = array();
        while ($rsCriteria->next()) {
            $array[] = \G::array_merges($rsCriteria->getRow(), $arrayData);
        }
        return $array;
    }

    public function executeScript($arrayRouteData, $appData)
    {
        //Evaluate the condition if there are conditions defined
        $flagAddDelegation = true;
        if (trim($arrayRouteData["ROU_CONDITION"]) != "" && $arrayRouteData["ROU_TYPE"] != "SELECT") {
            \G::LoadClass("pmScript");

            $pmScript = new \PMScript();
            $pmScript->setFields($appData);
            $pmScript->setScript($arrayRouteData["ROU_CONDITION"]);
            $flagAddDelegation = $pmScript->evaluate();
        }
        return $flagAddDelegation;
    }

}