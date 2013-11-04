<?php
class TaskBpmn
{
    public function getProperties($taskUid, $keyCaseToLower = false)
    {
        try {
            G::LoadClass("configuration");

            $task = new Task();

            $arrayDataAux = $task->load($taskUid);

            //$arrayDataAux["INDEX"] = 0;
            //$arrayDataAux["IFORM"] = 1;
            //$arrayDataAux["LANG"] = SYS_LANG;

            //Assignment rules
            switch ($arrayDataAux["TAS_ASSIGN_TYPE"]) {
                case "SELF_SERVICE":
                    $arrayDataAux["TAS_ASSIGN_TYPE"] = (!empty($arrayDataAux["TAS_GROUP_VARIABLE"])) ? "SELF_SERVICE_EVALUATE" : $arrayDataAux["TAS_ASSIGN_TYPE"];
                    break;
            }

            //Timing control
            //Load Calendar Information
            $calendar = new Calendar();

            $calendarInfo = $calendar->getCalendarFor("", "", $taskUid);

            //If the function returns a DEFAULT calendar it means that this object doesn"t have assigned any calendar
            $arrayDataAux["TAS_CALENDAR"] = ($calendarInfo["CALENDAR_APPLIED"] != "DEFAULT")? $calendarInfo["CALENDAR_UID"] : "";

            //Notifications
            $conf = new Configurations();
            $conf->loadConfig($x, "TAS_EXTRA_PROPERTIES", $taskUid, "", "");

            if (isset($conf->aConfig["TAS_DEF_MESSAGE_TYPE"]) && isset($conf->aConfig["TAS_DEF_MESSAGE_TYPE"])) {
                $arrayDataAux["TAS_DEF_MESSAGE_TYPE"] = $conf->aConfig["TAS_DEF_MESSAGE_TYPE"];
                $arrayDataAux["TAS_DEF_MESSAGE_TEMPLATE"] = $conf->aConfig["TAS_DEF_MESSAGE_TEMPLATE"];
            }

            //Set data
            $arrayData = array();
            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;

            //Definition
            $arrayData["DEFINITION"] = array_change_key_case(
                array(
                    "TAS_PRIORITY_VARIABLE"     => $arrayDataAux["TAS_PRIORITY_VARIABLE"],
                    "TAS_DERIVATION_SCREEN_TPL" => $arrayDataAux["TAS_DERIVATION_SCREEN_TPL"]
                ),
                $keyCase
            );

            //Assignment Rules
            $arrayData["ASSIGNMENT_RULES"] = array_change_key_case(
                array(
                    "TAS_ASSIGN_TYPE"     => $arrayDataAux["TAS_ASSIGN_TYPE"],
                    "TAS_ASSIGN_VARIABLE" => $arrayDataAux["TAS_ASSIGN_VARIABLE"],
                    "TAS_GROUP_VARIABLE"  => $arrayDataAux["TAS_GROUP_VARIABLE"],
                    "TAS_SELFSERVICE_TIMEOUT" => $arrayDataAux["TAS_SELFSERVICE_TIMEOUT"],
                    "TAS_SELFSERVICE_TIME"    => $arrayDataAux["TAS_SELFSERVICE_TIME"],
                    "TAS_SELFSERVICE_TIME_UNIT"   => $arrayDataAux["TAS_SELFSERVICE_TIME_UNIT"],
                    "TAS_SELFSERVICE_TRIGGER_UID" => $arrayDataAux["TAS_SELFSERVICE_TRIGGER_UID"]
                ),
                $keyCase
            );

            //Timing control
            $arrayData["TIMING_CONTROL"] = array_change_key_case(
                array(
                    "TAS_TRANSFER_FLY" => $arrayDataAux["TAS_TRANSFER_FLY"],
                    "TAS_DURATION"     => $arrayDataAux["TAS_DURATION"],
                    "TAS_TIMEUNIT"     => $arrayDataAux["TAS_TIMEUNIT"],
                    "TAS_TYPE_DAY"     => $arrayDataAux["TAS_TYPE_DAY"],
                    "TAS_CALENDAR"     => $arrayDataAux["TAS_CALENDAR"]
                ),
                $keyCase
            );

            //Permissions
            $arrayData["PERMISSIONS"] = array_change_key_case(
                array(
                    "TAS_TYPE" => $arrayDataAux["TAS_TYPE"]
                ),
                $keyCase
            );

            //Case Labels
            $arrayData["CASE_LABELS"] = array_change_key_case(
                array(
                    "TAS_DEF_TITLE"       => $arrayDataAux["TAS_DEF_TITLE"],
                    "TAS_DEF_DESCRIPTION" => $arrayDataAux["TAS_DEF_DESCRIPTION"]
                ),
                $keyCase
            );

            //Notifications
            $arrayData["NOTIFICATIONS"] = array_change_key_case(
                array(
                    "SEND_EMAIL"               => $arrayDataAux["TAS_SEND_LAST_EMAIL"],
                    "TAS_DEF_SUBJECT_MESSAGE"  => $arrayDataAux["TAS_DEF_SUBJECT_MESSAGE"],
                    "TAS_DEF_MESSAGE_TYPE"     => $arrayDataAux["TAS_DEF_MESSAGE_TYPE"],
                    "TAS_DEF_MESSAGE"          => $arrayDataAux["TAS_DEF_MESSAGE"],
                    "TAS_DEF_MESSAGE_TEMPLATE" => $arrayDataAux["TAS_DEF_MESSAGE_TEMPLATE"]
                ),
                $keyCase
            );

            $arrayData = array_change_key_case($arrayData, $keyCase);

            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateProperties($taskUid, $processUid, $arrayProperty)
    {
        //Copy of processmaker/workflow/engine/methods/tasks/tasks_Ajax.php //case "saveTaskData":

        try {
            $arrayProperty["TAS_UID"] = $taskUid;
            $arrayProperty["PRO_UID"] = $processUid;

            $task = new Task();
            $aTaskInfo = $task->load($arrayProperty["TAS_UID"]);

            $arrayResult = array();

            /**
             * routine to replace @amp@ by &
             * that why the char "&" can't be passed by XmlHttpRequest directly
             * @autor erik <erik@colosa.com>
             */

            foreach ($arrayProperty as $k => $v) {
                $arrayProperty[$k] = str_replace("@amp@", "&", $v);
            }

            if (isset($arrayProperty["SEND_EMAIL"])) {
                $arrayProperty["TAS_SEND_LAST_EMAIL"] = ($arrayProperty["SEND_EMAIL"] == "TRUE")? "TRUE" : "FALSE";
            } else {
                //$aTaskInfo = $task->load($arrayProperty["TAS_UID"]);
                $arrayProperty["TAS_SEND_LAST_EMAIL"] = (is_null($aTaskInfo["TAS_SEND_LAST_EMAIL"]))? "FALSE" : $aTaskInfo["TAS_SEND_LAST_EMAIL"];
            }

            //Additional configuration
            if (isset($arrayProperty["TAS_DEF_MESSAGE_TYPE"]) && isset($arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"])) {
                G::LoadClass("configuration");

                $oConf = new Configurations();
                $oConf->aConfig = array("TAS_DEF_MESSAGE_TYPE" => $arrayProperty["TAS_DEF_MESSAGE_TYPE"], "TAS_DEF_MESSAGE_TEMPLATE" => $arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"]);

                $oConf->saveConfig("TAS_EXTRA_PROPERTIES", $arrayProperty["TAS_UID"], "", "");

                unset($arrayProperty["TAS_DEF_MESSAGE_TYPE"]);
                unset($arrayProperty["TAS_DEF_MESSAGE_TEMPLATE"]);
            }

            //Validating TAS_ASSIGN_VARIABLE value

            if (!isset($arrayProperty["TAS_ASSIGN_TYPE"])) {
                $derivateType = $task->kgetassigType($arrayProperty["PRO_UID"], $arrayProperty["TAS_UID"]);

                if (is_null($derivateType)) {
                    $arrayProperty["TAS_ASSIGN_TYPE"] = "BALANCED";
                } else {
                    $arrayProperty["TAS_ASSIGN_TYPE"] = $derivateType["TAS_ASSIGN_TYPE"];
                }
            }

            if ($arrayProperty["TAS_ASSIGN_TYPE"] == "SELF_SERVICE_EVALUATE") {
                $arrayProperty["TAS_ASSIGN_TYPE"] = "SELF_SERVICE";

                if (trim($arrayProperty["TAS_GROUP_VARIABLE"]) == "") {
                   $arrayProperty["TAS_GROUP_VARIABLE"] = "@@SYS_GROUP_TO_BE_ASSIGNED";
                }
            } else {
                $arrayProperty["TAS_GROUP_VARIABLE"] = "";
            }

            $result = $task->update($arrayProperty);

            $arrayResult["status"] = "OK";

            if ($result == 3) {
                $arrayResult["status"] = "CRONCL";
            }

            return $arrayResult;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getStepsList($taskUid, $processUid, $keyCaseToLower = false, $start = 0, $limit = 25)
    {
        try {
            G::LoadClass("BasePeer");

            $arrayData = array();
            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;

            //Criteria
            $processMap = new ProcessMap();

            $criteria = $processMap->getAvailableBBCriteria($processUid, $taskUid);

            if ($criteria->getDbName() == "dbarray") {
                $rsCriteria = ArrayBasePeer::doSelectRS($criteria);
            } else {
                $rsCriteria = GulliverBasePeer::doSelectRS($criteria);
            }

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[] = array_change_key_case($row, $keyCase);
            }

            return array_change_key_case(
                array("NUM_RECORDS" => count($arrayData), "DATA" => array_slice($arrayData, $start, $limit)),
                $keyCase
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getSteps($taskUid, $keyCaseToLower = false)
    {
        try {
            G::LoadClass("BasePeer");

            $arrayData = array();
            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;

            //Criteria
            $processMap = new ProcessMap();

            $criteria = $processMap->getStepsCriteria($taskUid);

            if ($criteria->getDbName() == "dbarray") {
                $rsCriteria = ArrayBasePeer::doSelectRS($criteria);
            } else {
                $rsCriteria = GulliverBasePeer::doSelectRS($criteria);
            }

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[] = array_change_key_case($row, $keyCase);
            }

            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getTriggers($taskUid, $keyCaseToLower = false)
    {
        try {
            G::LoadClass("BasePeer");

            $arrayData = array();
            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;

            $arrayTriggerType1 = array(
                "BEFORE" => "BEFORE",
                "AFTER"  => "AFTER"
            );
            $arrayTriggerType2 = array(
                "BEFORE_ASSIGNMENT" => "BEFORE",
                "BEFORE_ROUTING"    => "BEFORE",
                "AFTER_ROUTING"     => "AFTER"
            );

            $processMap = new ProcessMap();
            $stepTgr = new StepTrigger();

            $arraySteps = $this->getSteps($taskUid);
            $n = count($arraySteps) + 1;

            $arraySteps[] = array(
                "STEP_UID"   => "",
                "STEP_TITLE" => G::LoadTranslation("ID_ASSIGN_TASK"),
                "STEP_TYPE_OBJ" => "",
                "STEP_MODE"     => "",
                "STEP_CONDITION" => "",
                "STEP_POSITION"  => $n
            );

            foreach ($arraySteps as $index1 => $value1) {
                $step = $value1;

                $stepUid = $step["STEP_UID"];

                //Set data
                $arrayDataAux1 = array();

                $arrayDataAux1["STEP_UID"] = $stepUid;

                $arrayTriggerType = ($stepUid != "")? $arrayTriggerType1 : $arrayTriggerType2;

                foreach ($arrayTriggerType as $index2 => $value2) {
                    $triggerType = $index2;
                    $type = $value2;

                    switch ($triggerType) {
                        case "BEFORE_ASSIGNMENT":
                            $stepUid = "-1";
                            break;
                        case "BEFORE_ROUTING":
                            $stepUid = "-2";
                            break;
                        case "AFTER_ROUTING":
                            $stepUid = "-2";
                            break;
                    }

                    $stepTgr->orderPosition($stepUid, $taskUid, $type);

                    $arrayDataAux2 = array();

                    //Criteria
                    $criteria = $processMap->getStepTriggersCriteria($stepUid, $taskUid, $type);

                    if ($criteria->getDbName() == "dbarray") {
                        $rsCriteria = ArrayBasePeer::doSelectRS($criteria);
                    } else {
                        $rsCriteria = GulliverBasePeer::doSelectRS($criteria);
                    }

                    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                    while ($rsCriteria->next()) {
                        $row = $rsCriteria->getRow();

                        $arrayDataAux2[] = array_change_key_case($row, $keyCase);
                    }

                    $arrayDataAux1[$triggerType] = $arrayDataAux2;
                }

                $arrayData[] = array_change_key_case($arrayDataAux1, $keyCase);
            }

            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getUsers($taskUid, $taskUserType, $keyCaseToLower = false)
    {
        try {
            G::LoadClass("BasePeer");

            $arrayData = array();
            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;

            //Criteria
            $processMap = new ProcessMap();

            $criteria = $processMap->getTaskUsersCriteria($taskUid, $taskUserType);

            if ($criteria->getDbName() == "dbarray") {
                $rsCriteria = ArrayBasePeer::doSelectRS($criteria);
            } else {
                $rsCriteria = GulliverBasePeer::doSelectRS($criteria);
            }

            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[] = array_change_key_case($row, $keyCase);
            }

            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }
}

