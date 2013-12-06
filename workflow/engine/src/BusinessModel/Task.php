<?php
namespace BusinessModel;

use \G;

class Task
{
    /**
     * Get all properties of an Task
     *
     * @param string $taskUid
     * @param bool   $keyCaseToLower
     *
     * return array  Return data array with all properties of an Task
     *
     * @access public
     */
    public function getProperties($taskUid, $keyCaseToLower = false, $groupData = true)
    {
        try {
            //G::LoadClass("configuration");
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.configuration.php");

            $task = new \Task();

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
            $calendar = new \Calendar();

            $calendarInfo = $calendar->getCalendarFor("", "", $taskUid);

            //If the function returns a DEFAULT calendar it means that this object doesn"t have assigned any calendar
            $arrayDataAux["TAS_CALENDAR"] = ($calendarInfo["CALENDAR_APPLIED"] != "DEFAULT")? $calendarInfo["CALENDAR_UID"] : "";

            //Notifications
            $conf = new \Configurations();
            $conf->loadConfig($x, "TAS_EXTRA_PROPERTIES", $taskUid, "", "");

            if (isset($conf->aConfig["TAS_DEF_MESSAGE_TYPE"]) && isset($conf->aConfig["TAS_DEF_MESSAGE_TYPE"])) {
                $arrayDataAux["TAS_DEF_MESSAGE_TYPE"] = $conf->aConfig["TAS_DEF_MESSAGE_TYPE"];
                $arrayDataAux["TAS_DEF_MESSAGE_TEMPLATE"] = $conf->aConfig["TAS_DEF_MESSAGE_TEMPLATE"];
            }

            //Set data
            $arrayData = array();
            $keyCase = ($keyCaseToLower) ? CASE_LOWER : CASE_UPPER;

            if (!$groupData) {
                $arrayData = array_change_key_case($arrayDataAux, $keyCase);
                return $arrayData;
            }

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

    /**
     * Update properties of an Task
     *
     * @param string $taskUid
     * @param string $processUid
     * @param array  $arrayProperty
     *
     * return array
     *
     * @access public
     */
    public function updateProperties($taskUid, $processUid, $arrayProperty)
    {
        //Copy of processmaker/workflow/engine/methods/tasks/tasks_Ajax.php //case "saveTaskData":
        try {
            if (isset($arrayProperty['properties'])) {
                $arrayProperty = array_change_key_case($arrayProperty['properties'], CASE_UPPER);
            }
            $arrayProperty["TAS_UID"] = $taskUid;
            $arrayProperty["PRO_UID"] = $processUid;

            $task = new \Task();
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
                //G::LoadClass("configuration");
                require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.configuration.php");

                $oConf = new \Configurations();
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

    /**
     * Delete a Task
     *
     * @param string $taskUid
     *
     * return void
     *
     * @access public
     */
    public function deleteTask($taskUid)
    {
        try {
            G::LoadClass('tasks');
            $tasks = new \Tasks();
            $tasks->deleteTask($taskUid);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of unique ids of an Task (Unique id of Process)
     *
     * @param string $taskUid Unique id of the Task
     *
     * return array
     */
    public function getDataUids($taskUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TaskPeer::PRO_UID);
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
     * Get available steps of an Task
     *
     * @param string $taskUid
     *
     * return array
     */
    public function getAvailableSteps($taskUid)
    {
        try {
            $arrayAvailableStep = array();

            $arrayDataUid = $this->getDataUids($taskUid);

            $processUid = $arrayDataUid["PRO_UID"];

            //Get Uids
            $arrayUid = array();

            $tasks = new \Tasks();
            $arrayStep = $tasks->getStepsOfTask($taskUid);

            foreach ($arrayStep as $step) {
                $arrayUid[] = $step["STEP_UID_OBJ"];
            }

            //Array DB
            $arraydbStep = array();

            $arraydbStep[] = array(
                "obj_uid" => "char",
                "obj_title" => "char",
                "obj_description" => "char",
                "obj_type" => "char"
            );

            $delimiter = \DBAdapter::getStringDelimiter();

            //DynaForms
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $criteria->addAsColumn("DYN_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("DYN_DESCRIPTION", "CD.CON_VALUE");

            $criteria->addAlias("CT", "CONTENT");
            $criteria->addAlias("CD", "CONTENT");

            $arrayCondition = array();
            $arrayCondition[] = array(\DynaformPeer::DYN_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "DYN_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\DynaformPeer::DYN_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "DYN_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\DynaformPeer::DYN_UID, $arrayUid, \Criteria::NOT_IN);
            $criteria->add(\DynaformPeer::DYN_TYPE, "xmlform", \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                if ($row["DYN_TITLE"] . "" == "") {
                    //There is no transaltion for this Document name, try to get/regenerate the label
                    $row["DYN_TITLE"] = \Content::Load("DYN_TITLE", "", $row["DYN_UID"], SYS_LANG);
                }

                $arraydbStep[] = array(
                    "obj_uid" => $row["DYN_UID"],
                    "obj_title" => $row["DYN_TITLE"],
                    "obj_description" => $row["DYN_DESCRIPTION"],
                    "obj_type" => "DYNAFORM"
                );
            }

            //InputDocuments
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);
            $criteria->addAsColumn("INP_DOC_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("INP_DOC_DESCRIPTION", "CD.CON_VALUE");

            $criteria->addAlias("CT", "CONTENT");
            $criteria->addAlias("CD", "CONTENT");

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "INP_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "INP_DOC_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $arrayUid, \Criteria::NOT_IN);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                if ($row["INP_DOC_TITLE"] . "" == "") {
                    //There is no transaltion for this Document name, try to get/regenerate the label
                    $row["INP_DOC_TITLE"] = \Content::Load("INP_DOC_TITLE", "", $row["INP_DOC_UID"], SYS_LANG);
                }

                $arraydbStep[] = array(
                    "obj_uid" => $row["INP_DOC_UID"],
                    "obj_title" => $row["INP_DOC_TITLE"],
                    "obj_description" => $row["INP_DOC_DESCRIPTION"],
                    "obj_type" => "INPUT_DOCUMENT"
                );
            }

            //OutputDocuments
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_UID);
            $criteria->addAsColumn("OUT_DOC_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("OUT_DOC_DESCRIPTION", "CD.CON_VALUE");

            $criteria->addAlias("CT", "CONTENT");
            $criteria->addAlias("CD", "CONTENT");

            $arrayCondition = array();
            $arrayCondition[] = array(\OutputDocumentPeer::OUT_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "OUT_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\OutputDocumentPeer::OUT_DOC_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "OUT_DOC_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\OutputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\OutputDocumentPeer::OUT_DOC_UID, $arrayUid, \Criteria::NOT_IN);

            $rsCriteria = \OutputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                if ($row["OUT_DOC_TITLE"] . "" == "") {
                    //There is no transaltion for this Document name, try to get/regenerate the label
                    $row["OUT_DOC_TITLE"] = \Content::Load("OUT_DOC_TITLE", "", $row["OUT_DOC_UID"], SYS_LANG);
                }

                $arraydbStep[] = array(
                    "obj_uid" => $row["OUT_DOC_UID"],
                    "obj_title" => $row["OUT_DOC_TITLE"],
                    "obj_description" => $row["OUT_DOC_DESCRIPTION"],
                    "obj_type" => "OUTPUT_DOCUMENT"
                );
            }

            //Call plugin
            $pluginRegistry = &\PMPluginRegistry::getSingleton();
            $externalSteps = $pluginRegistry->getSteps();

            if (is_array($externalSteps) && count($externalSteps) > 0) {
                foreach ($externalSteps as $key => $value) {
                    $arraydbStep[] = array(
                        "obj_uid" => $value->sStepId,
                        "obj_title" => $value->sStepTitle,
                        "obj_description" => "",
                        "obj_type" => "EXTERNAL"
                    );
                }
            }

            \G::LoadClass("ArrayPeer");

            global $_DBArray;

            $_DBArray = (isset($_SESSION["_DBArray"]))? $_SESSION["_DBArray"] : "";
            $_DBArray["STEP"] = $arraydbStep;

            $_SESSION["_DBArray"] = $_DBArray;

            $criteria = new \Criteria("dbarray");

            $criteria->setDBArrayTable("STEP");
            $criteria->addAscendingOrderByColumn("obj_type");
            $criteria->addAscendingOrderByColumn("obj_title");

            $rsCriteria = \ArrayBasePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayAvailableStep[] = $row;
            }

            return $arrayAvailableStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all steps of an Task
     *
     * @param string $taskUid Unique id of the Task
     *
     * return array
     */
    public function getSteps($taskUid)
    {
        try {
            $arrayStep = array();

            $step = new \BusinessModel\Step();

            $criteria = new \Criteria("workflow");

            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn(\StepPeer::STEP_POSITION);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData = $step->getStep($row["STEP_UID"]);

                if (count($arrayData) > 0) {
                    $arrayStep[] = $arrayData;
                }
            }

            return $arrayStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all users of the Task
     *
     * @param string $taskUid
     * @param int    $taskUserType
     * @param bool   $keyCaseToLower
     *
     * return array
     *
     * @access public
     */
    public function getUsers($taskUid, $taskUserType, $keyCaseToLower = false)
    {
        try {
            //G::LoadClass("BasePeer");
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.BasePeer.php");

            $arrayData = array();
            $keyCase = ($keyCaseToLower)? CASE_LOWER : CASE_UPPER;

            //Criteria
            $processMap = new \ProcessMap();

            $criteria = $processMap->getTaskUsersCriteria($taskUid, $taskUserType);

            if ($criteria->getDbName() == "dbarray") {
                $rsCriteria = \ArrayBasePeer::doSelectRS($criteria);
            } else {
                $rsCriteria = \GulliverBasePeer::doSelectRS($criteria);
            }

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

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

