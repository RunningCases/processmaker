<?php

namespace ProcessMaker\Core;

use AppDelegationPeer;
use Task;
use Cases;
use Criteria;
use RoutePeer;
use TaskPeer;
use G;
use ElementTaskRelationPeer;
use BpmnEventPeer;
use ResultSet;
use PMScript;

class RoutingScreen extends \Derivation
{
    protected $convergent;
    protected $divergent;
    public $gateway = array('PARALLEL', 'PARALLEL-BY-EVALUATION');
    public $isFirst;
    protected $taskSecJoin;

    public function __construct()
    {
        parent::__construct();
        $this->setRegexpTaskTypeToInclude("GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-MESSAGE-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT");
    }

    /**
     * This fix only applies to classical processes when routype is SELECT
     * @param $post
     * @param $prepareInformation - The first index always starts at 1
     * @param $rouType
     * @return mixed - An array is returned whit first index 1
     */
    private function beforeMergeData($post, $prepareInformation, $rouType)
    {
        if ($rouType == 'SELECT') {
            $post = array_shift($post);
            foreach ($prepareInformation as $key => $nextTask) {
                if ($nextTask['ROU_CONDITION'] == $post['ROU_CONDITION'] &&
                    $post['SOURCE_UID'] == $nextTask['SOURCE_UID']
                ) {
                    $prepareInformationData[1] = $nextTask;
                    return $prepareInformationData;
                }
            }
        }
        return $prepareInformation;
    }

    /**
     * @param $post
     * @param $prepareInformation
     * @param $rouType
     * @return array
     */
    public function mergeDataDerivation($post, $prepareInformation, $rouType)
    {
        $prepareInformation = $this->beforeMergeData($post, $prepareInformation, $rouType);
        $aDataMerged = array();
        $flagJumpTask = false;
        foreach ($prepareInformation as $key => $nextTask) {
            $aDataMerged[$key] = $nextTask['NEXT_TASK'];
            unset($aDataMerged[$key]['USER_ASSIGNED']);
            $aDataMerged[$key]['DEL_PRIORITY'] = '';
            foreach ($post as $i => $item) {
                if(isset($post[$i]['SOURCE_UID']) && ($nextTask['NEXT_TASK']['TAS_UID'] === $post[$i]['SOURCE_UID'])){
                    $flagJumpTask = true;
                    if($post[$i]['SOURCE_UID'] === $post[$i]['TAS_UID']){
                        if (isset($post[$i]['USR_UID'])) { // Multiple instances task don't send this key
                            $aDataMerged[$key]['USR_UID'] = $post[$i]['USR_UID'];
                        }
                    } else {
                        $aDataMerged[$key]['NEXT_ROUTING'][] = $post[$i];
                    }
                    if (isset($post[$i]['NEXT_TASK'])) {
                        $aDataMerged[$key]['NEXT_TASK'] = $post[$i]['NEXT_TASK'];
                    }
                }
            }
        }
        //If flagJumpTask is false the template does not Jump Intermediate Events
        if(!$flagJumpTask){
            $aDataMerged = $post;
        }
        return $aDataMerged;
    }

    public function prepareRoutingScreen($arrayData)
    {
        $information = $this->prepareInformationForRoutingScreen($arrayData);
        $response = array();
        $this->taskSecJoin = array();
        foreach ($information as $index => $element) {
            $this->divergent = array();
            $this->convergent = array();
            $this->isFirst = true;
            $x = $this->checkElement($this->node[$element['TAS_UID']]);
            if ($x) {
                $save = false;
                foreach ($response as $task) {
                    if (!in_array($element['ROU_NEXT_TASK'], $task, true)) {
                        $save = true;
                    }
                }
                if ((!$response || $save)) {
                    $response[] = $element;
                }
            }
        }
        if (count($response) > 1) {
            foreach ($response as $index => $task) {
                $delete = false;
                foreach ($this->taskSecJoin as $tj => $type) {
                    if (in_array($tj, $task, true)) {
                        $delete = true;
                    }
                }
                if ($delete && $response[$index]["NEXT_TASK"]["TAS_UID"] === "-1") {
                    unset($response[$index]);
                }
            }
        }
        return array_combine(range(1, count($response)), array_values($response));
    }

    public function checkElement($element)
    {
        $outElement = $element['out'];
        foreach ($outElement as $indexO => $outE) {
            if (!$this->isFirst && in_array($outE, $this->gateway)) {
                $this->divergent[$indexO] = $outE;
            }
            if ($outE == 'SEC-JOIN' && strpos($indexO, 'itee') === false) {
                $this->taskSecJoin[$indexO] = $outE;
            }
        }
        if (empty($element['in'])) {
            return true;
        }
        $this->isFirst = false;
        $inElement = $element['in'];
        foreach ($inElement as $indexI => $inE) {
            if ($inE == 'SEC-JOIN' && strpos($indexI, 'itee') !== false) {
                $this->convergent[$indexI] = $inE;
            }
            $this->checkElement($this->node[$indexI]);
        }
        return count($this->convergent) == 0 || count($this->divergent) == 0 || count($this->convergent) == count($this->divergent);
    }


    /**
     * Prepares the information to show in the routing screen.
     *
     * @param array  $arrayData Data
     * @param string $taskUid   Unique id of Task
     *
     * @return array Return array
     */
    public function prepareInformationForRoutingScreen(array $arrayData, $taskUid = "")
    {
        try {
            if (!class_exists("Cases")) {
                G::LoadClass("case");
            }

            $this->case = new Cases();
            $task = new Task();

            $arrayApplicationData = $this->case->loadCase($arrayData["APP_UID"]);

            $arrayNextTask = array();
            $arrayNextTaskDefault = array();
            $i = 0;

            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(RoutePeer::TAS_UID);
            $criteria->addSelectColumn(RoutePeer::ROU_NEXT_TASK);
            $criteria->addSelectColumn(RoutePeer::ROU_TYPE);
            $criteria->addSelectColumn(RoutePeer::ROU_DEFAULT);
            $criteria->addSelectColumn(RoutePeer::ROU_CONDITION);

            if ($taskUid != "") {
                $criteria->add(\RoutePeer::TAS_UID, $taskUid, Criteria::EQUAL);
                $criteria->addAscendingOrderByColumn(RoutePeer::ROU_CASE);

                $rsCriteria = RoutePeer::doSelectRS($criteria);
            } else {
                $criteria->addJoin(AppDelegationPeer::TAS_UID, TaskPeer::TAS_UID, Criteria::LEFT_JOIN);
                $criteria->addJoin(AppDelegationPeer::TAS_UID, RoutePeer::TAS_UID, Criteria::LEFT_JOIN);
                $criteria->add(AppDelegationPeer::APP_UID, $arrayData["APP_UID"], Criteria::EQUAL);
                $criteria->add(AppDelegationPeer::DEL_INDEX, $arrayData["DEL_INDEX"], Criteria::EQUAL);
                $criteria->addAscendingOrderByColumn(\RoutePeer::ROU_CASE);

                $rsCriteria = \AppDelegationPeer::doSelectRS($criteria);
            }

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $flagDefault = false;
            $aSecJoin = array();
            $count = 0;

            while ($rsCriteria->next()) {
                $arrayRouteData = G::array_merges($rsCriteria->getRow(), $arrayData);

                if ((int)($arrayRouteData["ROU_DEFAULT"]) == 1) {
                    $arrayNextTaskDefault = $arrayRouteData;
                    $flagDefault = true;
                    continue;
                }

                $flagAddDelegation = true;

                //Evaluate the condition if there are conditions defined
                if (trim($arrayRouteData["ROU_CONDITION"]) != "" && $arrayRouteData["ROU_TYPE"] != "SELECT") {
                    G::LoadClass("pmScript");

                    $pmScript = new PMScript();
                    $pmScript->setFields($arrayApplicationData["APP_DATA"]);
                    $pmScript->setScript($arrayRouteData["ROU_CONDITION"]);
                    $flagAddDelegation = $pmScript->evaluate();
                }

                if (trim($arrayRouteData['ROU_CONDITION']) == '' && $arrayRouteData['ROU_NEXT_TASK'] != '-1') {
                    $arrayTaskData = $task->load($arrayRouteData['ROU_NEXT_TASK']);

                    if ($arrayRouteData['ROU_TYPE'] != 'SEC-JOIN' && $arrayTaskData['TAS_TYPE'] == 'GATEWAYTOGATEWAY') {
                        $flagAddDelegation = true;
                    }

                    if($arrayRouteData['ROU_TYPE'] == 'SEC-JOIN'){
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

            if (count($arrayNextTask) == 0 && count($arrayNextTaskDefault) > 0) {
                $arrayNextTask[++$i] = $this->prepareInformationTask($arrayNextTaskDefault);
            }

            //Check Task GATEWAYTOGATEWAY, END-MESSAGE-EVENT, END-EMAIL-EVENT
            $arrayNextTaskBackup = $arrayNextTask;

            $arrayNextTask = array();
            $i = 0;
            foreach ($arrayNextTaskBackup as $value) {
                $arrayNextTaskData = $value;
                $this->node[$value['TAS_UID']]['out'][$value['ROU_NEXT_TASK']] = $value['ROU_TYPE'];
                if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] != "-1" &&
                    preg_match("/^(?:" . $this->regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"])
                ) {
                    $arrayAux = $this->prepareInformationForRoutingScreen($arrayData, $arrayNextTaskData["NEXT_TASK"]["TAS_UID"]);
                    $this->node[$value['ROU_NEXT_TASK']]['in'][$value['TAS_UID']] = $value['ROU_TYPE'];
                    $notShowNextTaskWhenJoinOf = "INTERMEDIATE-THROW-MESSAGE-EVENT|INTERMEDIATE-CATCH-MESSAGE-EVENT|SCRIPT-TASK|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT";

                    foreach ($arrayAux as $value2) {
                        
                        //@TODO move this logic to the prepareInformation of the Derivation class
                        $intermediateEventAndJoinPresent = (array_key_exists('TAS_TYPE', $value2)
                                                                && array_key_exists('ROU_TYPE', $value2)
                                                                && preg_match("/^(?:" . $notShowNextTaskWhenJoinOf . ")$/", $value2["TAS_TYPE"])
                                                                && $value2['ROU_TYPE'] === 'SEC-JOIN');
                        if (!$intermediateEventAndJoinPresent) {
                            $key = ++$i;
                            $arrayNextTask[$key] = $value2;
                            $prefix = substr($value['ROU_NEXT_TASK'], 0, 4);
                            if ($prefix!=='gtg-') {
                                $arrayNextTask[$key]['SOURCE_UID'] = $value['ROU_NEXT_TASK'];
                            }
                            foreach ($aSecJoin as $rsj) {
                                $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TASK"] = $rsj["ROU_PREVIOUS_TASK"];
                                $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] = "SEC-JOIN";
                            }
                        }
                    }
                } else {
                    $regexpTaskTypeToInclude = "END-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT";

                    if ($arrayNextTaskData["NEXT_TASK"]["TAS_UID"] == "-1" &&
                        preg_match("/^(?:" . $regexpTaskTypeToInclude . ")$/", $arrayNextTaskData["TAS_TYPE"])
                    ) {
                        $arrayNextTaskData["NEXT_TASK"]["TAS_UID"] = $arrayNextTaskData["TAS_UID"] . "/" . $arrayNextTaskData["NEXT_TASK"]["TAS_UID"];
                    }
                    $prefix = substr($value['ROU_NEXT_TASK'], 0, 4);
                    if($prefix!=='gtg-'){
                        $arrayNextTaskData['SOURCE_UID'] = $value['ROU_NEXT_TASK'];
                    }
                    $arrayNextTask[++$i] = $arrayNextTaskData;
                    foreach($aSecJoin as $rsj){
                        $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TASK"] = $rsj["ROU_PREVIOUS_TASK"];
                        $arrayNextTask[$i]["NEXT_TASK"]["ROU_PREVIOUS_TYPE"] = "SEC-JOIN";
                    }
                    //Start-Timer with Script-task
                    $criteriaE = new Criteria("workflow");
                    $criteriaE->addSelectColumn(ElementTaskRelationPeer::ELEMENT_UID);
                    $criteriaE->addJoin(BpmnEventPeer::EVN_UID, ElementTaskRelationPeer::ELEMENT_UID, Criteria::LEFT_JOIN);
                    $criteriaE->add(ElementTaskRelationPeer::TAS_UID, $arrayNextTaskData["TAS_UID"], Criteria::EQUAL);
                    $criteriaE->add(BpmnEventPeer::EVN_TYPE, 'START', Criteria::EQUAL);
                    $criteriaE->add(BpmnEventPeer::EVN_MARKER, 'TIMER', Criteria::EQUAL);
                    $rsCriteriaE = AppDelegationPeer::doSelectRS($criteriaE);
                    $rsCriteriaE->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                    while ($rsCriteriaE->next()) {
                        if($arrayNextTaskData["NEXT_TASK"]["TAS_TYPE"] == "SCRIPT-TASK"){
                            if(isset($arrayNextTaskData["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"]) && $arrayNextTaskData["NEXT_TASK"]["USER_ASSIGNED"]["USR_UID"] == ""){
                                $useruid = "00000000000000000000000000000001";
                                $userFields = $this->getUsersFullNameFromArray( $useruid );
                                $arrayNextTask[$i]["NEXT_TASK"]["USER_ASSIGNED"] = $userFields;
                            }
                        }
                    }
                }
            }

            //1. There is no rule
            if (empty($arrayNextTask)) {
                $bpmn = new \ProcessMaker\Project\Bpmn();

                throw new Exception(G::LoadTranslation(
                    'ID_NO_DERIVATION_' . (($bpmn->exists($arrayApplicationData['PRO_UID']))? 'BPMN_RULE' : 'RULE')
                ));
            }

            //Return
            return $arrayNextTask;
        } catch (Exception $e) {
            throw $e;
        }
    }


}