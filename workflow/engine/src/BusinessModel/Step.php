<?php
namespace BusinessModel;

class Step
{
    /**
     * Verify if exists the record in table STEP
     *
     * @param string $taskUid        Unique id of Task
     * @param string $type           Type of Step (DYNAFORM, INPUT_DOCUMENT, OUTPUT_DOCUMENT)
     * @param string $objectUid      Unique id of Object
     * @param int    $position       Position
     * @param string $stepUidExclude Unique id of Step to exclude
     *
     * return bool Return true if exists the record in table STEP, false otherwise
     */
    public function existsRecord($taskUid, $type, $objectUid, $position = 0, $stepUidExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepPeer::STEP_UID);
            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);

            if ($stepUidExclude != "") {
                $criteria->add(\StepPeer::STEP_UID, $stepUidExclude, \Criteria::NOT_EQUAL);
            }

            if ($type != "") {
                $criteria->add(\StepPeer::STEP_TYPE_OBJ, $type, \Criteria::EQUAL);
            }

            if ($objectUid != "") {
                $criteria->add(\StepPeer::STEP_UID_OBJ, $objectUid, \Criteria::EQUAL);
            }

            if ($position > 0) {
                $criteria->add(\StepPeer::STEP_POSITION, $position, \Criteria::EQUAL);
            }

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the "Object UID" in the corresponding table
     *
     * @param string $type      Type of Step (DYNAFORM, INPUT_DOCUMENT, OUTPUT_DOCUMENT)
     * @param string $objectUid Unique id of Object
     *
     * return strin Return empty string if $objectUid exists in the corresponding table, return string with data if $objectUid doesn't exist
     */
    public function existsObjectUid($type, $objectUid)
    {
        try {
            $msg = "";

            switch ($type) {
                case "DYNAFORM":
                    $dynaform = new \Dynaform();

                    if (!$dynaform->dynaformExists($objectUid)) {
                        $msg = str_replace(array("{0}", "{1}"), array($objectUid, "DYNAFORM"), "The UID \"{0}\" doesn't exist in table {1}");
                    }
                    break;
                case "INPUT_DOCUMENT":
                    $inputdoc = new \InputDocument();

                    if (!$inputdoc->InputExists($objectUid)) {
                        $msg = str_replace(array("{0}", "{1}"), array($objectUid, "INPUT_DOCUMENT"), "The UID \"{0}\" doesn't exist in table {1}");
                    }
                    break;
                case "OUTPUT_DOCUMENT":
                    $outputdoc = new \OutputDocument();

                    if (!$outputdoc->OutputExists($objectUid)) {
                        $msg = str_replace(array("{0}", "{1}"), array($objectUid, "OUTPUT_DOCUMENT"), "The UID \"{0}\" doesn't exist in table {1}");
                    }
                    break;
                default:
                    $msg = str_replace(array("{0}", "{1}"), array($objectUid, $type), "The UID \"{0}\" doesn't exist in table {1}");
                    break;
            }

            return $msg;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Step for a Task
     *
     * @param string $taskUid
     * @param string $processUid
     * @param array  $arrayData
     *
     * return array Data of the Step created
     */
    public function create($taskUid, $processUid, $arrayData)
    {
        try {
            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $task = new \Task();

            if (!$task->taskExists($taskUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid, "TASK"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["step_type_obj"]) && isset($arrayData["step_uid_obj"])) {
                $msg = $this->existsObjectUid($arrayData["step_type_obj"], $arrayData["step_uid_obj"]);

                if ($msg != "") {
                    throw (new \Exception($msg));
                }

                if ($this->existsRecord($taskUid, $arrayData["step_type_obj"], $arrayData["step_uid_obj"])) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid . ", " . $arrayData["step_type_obj"] . ", " . $arrayData["step_uid_obj"], "STEP"), "The record \"{0}\", exists in table {1}")));
                }
            }

            if (isset($arrayData["step_position"]) && $this->existsRecord($taskUid, "", "", $arrayData["step_position"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["step_position"], $taskUid . ", " . $arrayData["step_position"], "STEP"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Create
            $step = new \Step();

            $stepUid = $step->create(array("PRO_UID" => $processUid, "TAS_UID" => $taskUid));

            if (!isset($arrayData["step_position"]) || $arrayData["step_position"] == "") {
                $arrayData["step_position"] = $step->getNextPosition($taskUid) - 1;
            }

            $arrayData = $this->update($stepUid, $arrayData);

            //Return
            unset($arrayData["step_uid"]);

            return array_merge(array("step_uid" => $stepUid), $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Step of a Task
     *
     * @param string $stepUid
     * @param array  $arrayData
     *
     * return array Data of the Step updated
     */
    public function update($stepUid, $arrayData)
    {
        try {
            $arrayDataUid = $this->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

            //Verify data
            $step = new \Step();

            if (!$step->StepExists($stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["step_type_obj"]) && isset($arrayData["step_uid_obj"])) {
                $msg = $this->existsObjectUid($arrayData["step_type_obj"], $arrayData["step_uid_obj"]);

                if ($msg != "") {
                    throw (new \Exception($msg));
                }

                if ($this->existsRecord($taskUid, $arrayData["step_type_obj"], $arrayData["step_uid_obj"], 0, $stepUid)) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid . ", " . $arrayData["step_type_obj"] . ", " . $arrayData["step_uid_obj"], "STEP"), "The record \"{0}\", exists in table {1}")));
                }
            }

            if (isset($arrayData["step_position"]) && $this->existsRecord($taskUid, "", "", $arrayData["step_position"], $stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["step_position"], $taskUid . ", " . $arrayData["step_position"], "STEP"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Update
            $step = new \Step();

            $arrayUpdateData = array();

            $arrayUpdateData["STEP_UID"] = $stepUid;

            if (isset($arrayData["step_type_obj"]) && $arrayData["step_type_obj"] != "") {
                $arrayUpdateData["STEP_TYPE_OBJ"] = $arrayData["step_type_obj"];
            }

            if (isset($arrayData["step_uid_obj"]) && $arrayData["step_uid_obj"] != "") {
                $arrayUpdateData["STEP_UID_OBJ"] = $arrayData["step_uid_obj"];
            }

            if (isset($arrayData["step_condition"])) {
                $arrayUpdateData["STEP_CONDITION"] = $arrayData["step_condition"];
            }

            if (isset($arrayData["step_position"]) && $arrayData["step_position"] != "") {
                $arrayUpdateData["STEP_POSITION"] = (int)($arrayData["step_position"]);
            }

            if (isset($arrayData["step_mode"]) && $arrayData["step_mode"] != "") {
                $arrayUpdateData["STEP_MODE"] = $arrayData["step_mode"];
            }

            $step->update($arrayUpdateData);

            return array_change_key_case($arrayUpdateData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Step of a Task
     *
     * @param string $stepUid
     *
     * return void
     */
    public function delete($stepUid)
    {
        try {
            //Verify data
            $step = new \Step();

            if (!$step->StepExists($stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get position
            $criteria = new \Criteria("workflow");

            $criteria->add(\StepPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $position = (int)($row["STEP_POSITION"]);

            //Delete
            $step = new \Step();

            $step->reOrder($stepUid, $position);
            $step->remove($stepUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Step
     *
     * @param string $stepUid Unique id of Step
     *
     * return array
     */
    public function getStep($stepUid)
    {
        try {
            $arrayStep = array();

            //Call plugin
            $pluginRegistry = &\PMPluginRegistry::getSingleton();
            $externalSteps = $pluginRegistry->getSteps();

            $criteria = new \Criteria("workflow");

            $criteria->add(\StepPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $titleObj = "";
            $descriptionObj = "";

            switch ($row["STEP_TYPE_OBJ"]) {
                case "DYNAFORM":
                    $dynaform = new \Dynaform();
                    $arrayData = $dynaform->load($row["STEP_UID_OBJ"]);

                    $titleObj = $arrayData["DYN_TITLE"];
                    $descriptionObj = $arrayData["DYN_DESCRIPTION"];
                    break;
                case "INPUT_DOCUMENT":
                    $inputDocument = new \InputDocument();
                    $arrayData = $inputDocument->getByUid($row["STEP_UID_OBJ"]);

                    if ($arrayData === false) {
                        return $arrayStep;
                    }

                    $titleObj = $arrayData["INP_DOC_TITLE"];
                    $descriptionObj = $arrayData["INP_DOC_DESCRIPTION"];
                    break;
                case "OUTPUT_DOCUMENT":
                    $outputDocument = new \OutputDocument();
                    $arrayData = $outputDocument->getByUid($row["STEP_UID_OBJ"]);

                    if ($arrayData === false) {
                        return $arrayStep;
                    }

                    $titleObj = $arrayData["OUT_DOC_TITLE"];
                    $descriptionObj = $arrayData["OUT_DOC_DESCRIPTION"];
                    break;
                case "EXTERNAL":
                    $titleObj = "unknown " . $row["STEP_UID"];

                    if (is_array($externalSteps) && count($externalSteps) > 0) {
                        foreach ($externalSteps as $key => $value) {
                            if ($value->sStepId == $row["STEP_UID_OBJ"]) {
                                $titleObj = $value->sStepTitle;
                            }
                        }
                    }
                    break;
            }

            $arrayStep = array(
                "step_uid"        => $stepUid,
                "step_type_obj"   => $row["STEP_TYPE_OBJ"],
                "step_uid_obj"    => $row["STEP_UID_OBJ"],
                "step_condition"  => $row["STEP_CONDITION"],
                "step_position"   => (int)($row["STEP_POSITION"]),
                "step_mode"       => $row["STEP_MODE"],
                "obj_title"       => $titleObj,
                "obj_description" => $descriptionObj
            );

            return $arrayStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of unique ids of a Step (Unique id of Task and Process)
     *
     * @param string $stepUid Unique id of Step
     *
     * return array
     */
    public function getDataUids($stepUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepPeer::PRO_UID);
            $criteria->addSelectColumn(\StepPeer::TAS_UID);
            $criteria->add(\StepPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                return $rsCriteria->getRow();
            } else {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available triggers of a Step
     *
     * @param string $stepUid Unique id of Step
     * @param string $type    Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid Unique id of Task
     *
     * return array
     */
    public function getAvailableTriggers($stepUid, $type, $taskUid = "")
    {
        try {
            //Verify data
            $step = new \Step();

            if ($stepUid != "" && !$step->StepExists($stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $task = new \Task();

            if ($stepUid == "" && !$task->taskExists($taskUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid, "TASK"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $arrayAvailableTrigger = array();

            $trigger = new \BusinessModel\Trigger();

            $flagStepAssignTask = 0;

            if ($stepUid != "") {
                $arrayDataUid = $this->getDataUids($stepUid);

                $processUid = $arrayDataUid["PRO_UID"];
            } else {
                $arrayData = $task->load($taskUid);

                $processUid = $arrayData["PRO_UID"];

                $flagStepAssignTask = 1;

                switch ($type) {
                    case "BEFORE_ASSIGNMENT":
                        $stepUid = "-1";
                        $type = "BEFORE";
                        break;
                    case "BEFORE_ROUTING":
                        $stepUid = "-2";
                        $type = "BEFORE";
                        break;
                    case "AFTER_ROUTING":
                        $stepUid = "-2";
                        $type = "AFTER";
                        break;
                }
            }

            //Get Uids
            $arrayUid = array();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepTriggerPeer::TRI_UID);
            $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);

            if ($flagStepAssignTask == 1) {
                $criteria->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            }

            $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);

            $rsCriteria = \StepTriggerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayUid[] = $row["TRI_UID"];
            }

            //Criteria
            $criteria = $trigger->getTriggerCriteria();

            $criteria->add(\TriggersPeer::TRI_UID, $arrayUid, \Criteria::NOT_IN);
            $criteria->add(\TriggersPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("TRI_TITLE");

            $rsCriteria = \TriggersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayAvailableTrigger[] = array(
                    "tri_uid"   => $row["TRI_UID"],
                    "tri_title" => $row["TRI_TITLE"],
                    "tri_description" => $row["TRI_DESCRIPTION"],
                    "tri_type"   => $row["TRI_TYPE"],
                    "tri_webbot" => $row["TRI_WEBBOT"],
                    "tri_param"  => $row["TRI_PARAM"]
                );
            }

            return $arrayAvailableTrigger;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all triggers of a Step
     *
     * @param string $stepUid Unique id of Step
     * @param string $taskUid Unique id of Task
     *
     * return array
     */
    public function getTriggers($stepUid, $taskUid = "")
    {
        try {
            //Verify data
            $step = new \Step();

            if ($stepUid != "" && !$step->StepExists($stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $task = new \Task();

            if ($stepUid == "" && !$task->taskExists($taskUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid, "TASK"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $arrayTrigger = array();

            $bmTrigger = new \BusinessModel\Trigger();
            $bmStepTrigger = new \BusinessModel\Step\Trigger();

            if ($stepUid != "") {
                $arrayDataUid = $this->getDataUids($stepUid);

                $taskUid = $arrayDataUid["TAS_UID"];
            }

            $processMap = new \ProcessMap();
            $stepTrigger = new \StepTrigger();

            $arrayTriggerType1 = array(
                "BEFORE" => "BEFORE",
                "AFTER"  => "AFTER"
            );

            $arrayTriggerType2 = array(
                "BEFORE_ASSIGNMENT" => "BEFORE",
                "BEFORE_ROUTING"    => "BEFORE",
                "AFTER_ROUTING"     => "AFTER"
            );

            $arrayTriggerType = ($stepUid != "")? $arrayTriggerType1 : $arrayTriggerType2;

            foreach ($arrayTriggerType as $index => $value) {
                $triggerType = $index;
                $type = $value;

                $flagStepAssignTask = 0;

                switch ($triggerType) {
                    case "BEFORE_ASSIGNMENT":
                        $stepUid = "-1";
                        $flagStepAssignTask = 1;
                        break;
                    case "BEFORE_ROUTING":
                        $stepUid = "-2";
                        $flagStepAssignTask = 1;
                        break;
                    case "AFTER_ROUTING":
                        $stepUid = "-2";
                        $flagStepAssignTask = 1;
                        break;
                }

                $stepTrigger->orderPosition($stepUid, $taskUid, $type);

                //Criteria
                $criteria = $bmTrigger->getTriggerCriteria();

                $criteria->addSelectColumn(\StepTriggerPeer::ST_TYPE);
                $criteria->addSelectColumn(\StepTriggerPeer::ST_CONDITION);
                $criteria->addSelectColumn(\StepTriggerPeer::ST_POSITION);
                $criteria->addJoin(\StepTriggerPeer::TRI_UID, \TriggersPeer::TRI_UID, \Criteria::LEFT_JOIN);
                $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);
                $criteria->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);
                $criteria->addAscendingOrderByColumn(\StepTriggerPeer::ST_POSITION);

                $rsCriteria = \StepTriggerPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();

                    if ($flagStepAssignTask == 1) {
                        $row["ST_TYPE"] = $triggerType;
                    }

                    $arrayTrigger[] = $bmStepTrigger->getTriggerDataFromRecord($row);
                }
            }

            return $arrayTrigger;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

