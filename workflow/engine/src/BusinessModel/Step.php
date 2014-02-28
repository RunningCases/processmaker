<?php
namespace BusinessModel;

class Step
{
    private $formatFieldNameInUppercase = true;
    private $arrayParamException = array(
        "stepUid"       => "STEP_UID",
        "taskUid"       => "TAS_UID",
        "processUid"    => "PRO_UID",
        "stepTypeObj"   => "STEP_TYPE_OBJ",
        "stepUidObj"    => "STEP_UID_OBJ",
        "stepCondition" => "STEP_CONDITION",
        "stepPosition"  => "STEP_POSITION",
        "stepMode"      => "STEP_MODE"
    );

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayParamException($this->arrayParamException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for parameters
     *
     * @param array $arrayData Data with the params
     *
     * return void
     */
    public function setArrayParamException($arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayParamException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

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
     * Verify if Type Object has invalid value
     *
     * @param string $stepTypeObj Type Object
     *
     * return void Throw exception if Type Object has invalid value
     */
    public function throwExceptionIfHaveInvalidValueInTypeObj($stepTypeObj)
    {
        if (!in_array($stepTypeObj, array("DYNAFORM", "INPUT_DOCUMENT", "OUTPUT_DOCUMENT", "EXTERNAL"))) {
            $field = $this->arrayParamException["stepTypeObj"];
            throw (new \Exception(str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"")));
        }
    }

    /**
     * Verify if Mode has invalid value
     *
     * @param string $stepMode Mode
     *
     * return void Throw exception if Mode has invalid value
     */
    public function throwExceptionIfHaveInvalidValueInMode($stepMode)
    {
        if (!in_array($stepMode, array("EDIT", "VIEW"))) {
            $field = $this->arrayParamException["stepMode"];

            throw (new \Exception(str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"")));
        }
    }

    /**
     * Verify if doesn't exist the Step in table STEP
     *
     * @param string $stepUid Unique id of Step
     *
     * return void Throw exception if doesn't exist the Step in table STEP
     */
    public function throwExceptionIfNoExistsStep($stepUid)
    {
        $step = new \Step();

        if (!$step->StepExists($stepUid)) {
            $field = $this->arrayParamException["stepUid"];

            $msg = str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"") . " / ";
            $msg = $msg . str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}");

            throw (new \Exception($msg));
        }
    }

    /**
     * Verify if doesn't exist the Task in table TASK
     *
     * @param string $taskUid Unique id of Task
     *
     * return void Throw exception if doesn't exist the Task in table TASK
     */
    public function throwExceptionIfNoExistsTask($taskUid)
    {
        $task = new \Task();

        if (!$task->taskExists($taskUid)) {
            $field = $this->arrayParamException["taskUid"];

            $msg = str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"") . " / ";
            $msg = $msg . str_replace(array("{0}", "{1}"), array($taskUid, "TASK"), "The UID \"{0}\" doesn't exist in table {1}");

            throw (new \Exception($msg));
        }
    }

    /**
     * Verify if doesn't exist the Process in table PROCESS
     *
     * @param string $processUid Unique id of Process
     *
     * return void Throw exception if doesn't exist the Process in table PROCESS
     */
    public function throwExceptionIfNoExistsProcess($processUid)
    {
        $process = new \Process();

        if (!$process->exists($processUid)) {
            $field = $this->arrayParamException["processUid"];

            $msg = str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"") . " / ";
            $msg = $msg . str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}");

            throw (new \Exception($msg));
        }
    }

    /**
     * Create Step for a Task
     *
     * @param string $taskUid    Unique id of Task
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Step created
     */
    public function create($taskUid, $processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["STEP_UID"]);

            //Verify data
            $this->throwExceptionIfNoExistsTask($taskUid);

            $this->throwExceptionIfNoExistsProcess($processUid);

            if (!isset($arrayData["STEP_TYPE_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepTypeObj"]), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["STEP_TYPE_OBJ"] = trim($arrayData["STEP_TYPE_OBJ"]);

            if ($arrayData["STEP_TYPE_OBJ"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepTypeObj"]), "The \"{0}\" attribute is empty")));
            }

            if (!isset($arrayData["STEP_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepUidObj"]), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["STEP_UID_OBJ"] = trim($arrayData["STEP_UID_OBJ"]);

            if ($arrayData["STEP_UID_OBJ"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepUidObj"]), "The \"{0}\" attribute is empty")));
            }

            if (!isset($arrayData["STEP_MODE"])) {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepMode"]), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["STEP_MODE"] = trim($arrayData["STEP_MODE"]);

            if ($arrayData["STEP_MODE"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepMode"]), "The \"{0}\" attribute is empty")));
            }

            $this->throwExceptionIfHaveInvalidValueInTypeObj($arrayData["STEP_TYPE_OBJ"]);

            $this->throwExceptionIfHaveInvalidValueInMode($arrayData["STEP_MODE"]);

            $msg = $this->existsObjectUid($arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"]);

            if ($msg != "") {
                throw (new \Exception($msg));
            }

            if ($this->existsRecord($taskUid, $arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid . ", " . $arrayData["STEP_TYPE_OBJ"] . ", " . $arrayData["STEP_UID_OBJ"], "STEP"), "The record \"{0}\", exists in table {1}")));
            }

            //Create
            $step = new \Step();

            $stepUid = $step->create(array(
                "PRO_UID" => $processUid,
                "TAS_UID" => $taskUid,
                "STEP_POSITION" => $step->getNextPosition($taskUid)
            ));

            if (!isset($arrayData["STEP_POSITION"]) || $arrayData["STEP_POSITION"] == "") {
                unset($arrayData["STEP_POSITION"]);
            }

            $arrayData = $this->update($stepUid, $arrayData);

            //Return
            unset($arrayData["STEP_UID"]);

            $arrayData = array_merge(array("STEP_UID" => $stepUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Step of a Task
     *
     * @param string $stepUid   Unique id of Step
     * @param array  $arrayData Data
     *
     * return array Return data of the Step updated
     */
    public function update($stepUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNoExistsStep($stepUid);

            //Load Step
            $step = new \Step();
            $arrayStepData = $step->load($stepUid);

            $taskUid = $arrayStepData["TAS_UID"];
            $proUid = $arrayStepData["PRO_UID"];

            //Verify data
            if (isset($arrayData["STEP_TYPE_OBJ"]) && !isset($arrayData["STEP_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepUidObj"]), "The \"{0}\" attribute is not defined")));
            }

            if (!isset($arrayData["STEP_TYPE_OBJ"]) && isset($arrayData["STEP_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepTypeObj"]), "The \"{0}\" attribute is not defined")));
            }

            if (isset($arrayData["STEP_TYPE_OBJ"])) {
                $arrayData["STEP_TYPE_OBJ"] = trim($arrayData["STEP_TYPE_OBJ"]);

                if ($arrayData["STEP_TYPE_OBJ"] == "") {
                    throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepTypeObj"]), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["STEP_UID_OBJ"])) {
                $arrayData["STEP_UID_OBJ"] = trim($arrayData["STEP_UID_OBJ"]);

                if ($arrayData["STEP_UID_OBJ"] == "") {
                    throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepUidObj"]), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["STEP_MODE"])) {
                $arrayData["STEP_MODE"] = trim($arrayData["STEP_MODE"]);

                if ($arrayData["STEP_MODE"] == "") {
                    throw (new \Exception(str_replace(array("{0}"), array($this->arrayParamException["stepMode"]), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["STEP_TYPE_OBJ"])) {
                $this->throwExceptionIfHaveInvalidValueInTypeObj($arrayData["STEP_TYPE_OBJ"]);
            }

            if (isset($arrayData["STEP_MODE"])) {
                $this->throwExceptionIfHaveInvalidValueInMode($arrayData["STEP_MODE"]);
            }

            if (isset($arrayData["STEP_TYPE_OBJ"]) && isset($arrayData["STEP_UID_OBJ"])) {
                $msg = $this->existsObjectUid($arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"]);

                if ($msg != "") {
                    throw (new \Exception($msg));
                }

                if ($this->existsRecord($taskUid, $arrayData["STEP_TYPE_OBJ"], $arrayData["STEP_UID_OBJ"], 0, $stepUid)) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid . ", " . $arrayData["STEP_TYPE_OBJ"] . ", " . $arrayData["STEP_UID_OBJ"], "STEP"), "The record \"{0}\", exists in table {1}")));
                }
            }

            //Update
            $step = new \Step();

            $arrayData["STEP_UID"] = $stepUid;
            $tempPosition = (isset($arrayData["STEP_POSITION"])) ? $arrayData["STEP_POSITION"] : $arrayStepData["STEP_POSITION"];
            $arrayData["STEP_POSITION"] = $arrayStepData["STEP_POSITION"];
            $result = $step->update($arrayData);

            if (isset($tempPosition) && ($tempPosition != $arrayStepData["STEP_POSITION"])) {
                $this->moveSteps($proUid, $taskUid, $stepUid, $tempPosition);
            }

            //Return
            unset($arrayData["STEP_UID"]);
            $arrayData["STEP_POSITION"] = $tempPosition;

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Step of a Task
     *
     * @param string $stepUid Unique id of Step
     *
     * return void
     */
    public function delete($stepUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNoExistsStep($stepUid);

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
     * Get all Steps of a Task
     *
     * @param string $taskUid Unique id of Task
     *
     * return array Return an array with all Steps of a Task
     */
    public function getSteps($taskUid)
    {
        try {
            $arrayStep = array();

            $step = new \BusinessModel\Step();
            $step->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);
            $step->setArrayParamException($this->arrayParamException);

            //Verify data
            $this->throwExceptionIfNoExistsTask($taskUid);

            //Get data
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

            //Return
            return $arrayStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Step
     *
     * @param string $stepUid Unique id of Step
     *
     * return array Return an array with data of a Step
     */
    public function getStep($stepUid)
    {
        try {
            $arrayStep = array();

            //Verify data
            $this->throwExceptionIfNoExistsStep($stepUid);

            //Get data
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

            //Return
            $arrayStep = array(
                $this->getFieldNameByFormatFieldName("STEP_UID")        => $stepUid,
                $this->getFieldNameByFormatFieldName("STEP_TYPE_OBJ")   => $row["STEP_TYPE_OBJ"],
                $this->getFieldNameByFormatFieldName("STEP_UID_OBJ")    => $row["STEP_UID_OBJ"],
                $this->getFieldNameByFormatFieldName("STEP_CONDITION")  => $row["STEP_CONDITION"],
                $this->getFieldNameByFormatFieldName("STEP_POSITION")   => (int)($row["STEP_POSITION"]),
                $this->getFieldNameByFormatFieldName("STEP_MODE")       => $row["STEP_MODE"],
                $this->getFieldNameByFormatFieldName("OBJ_TITLE")       => $titleObj,
                $this->getFieldNameByFormatFieldName("OBJ_DESCRIPTION") => $descriptionObj
            );

            return $arrayStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get available Triggers of a Step
     *
     * @param string $stepUid Unique id of Step
     * @param string $type    Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid Unique id of Task
     *
     * return array Return an array with the Triggers available of a Step
     */
    public function getAvailableTriggers($stepUid, $type, $taskUid = "")
    {
        try {
            $arrayAvailableTrigger = array();

            //Verify data
            if ($stepUid != "") {
                $this->throwExceptionIfNoExistsStep($stepUid);
            }

            if ($stepUid == "") {
                $this->throwExceptionIfNoExistsTask($taskUid);
            }

            //Get data
            $trigger = new \BusinessModel\Trigger();

            $flagStepAssignTask = 0;

            if ($stepUid != "") {
                //Load Step
                $step = new \Step();

                $arrayStepData = $step->load($stepUid);

                $processUid = $arrayStepData["PRO_UID"];
            } else {
                //Load Task
                $task = new \Task();

                $arrayTaskData = $task->load($taskUid);

                $processUid = $arrayTaskData["PRO_UID"];

                //Set variables
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

            //Get data
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
                    $this->getFieldNameByFormatFieldName("TRI_UID")         => $row["TRI_UID"],
                    $this->getFieldNameByFormatFieldName("TRI_TITLE")       => $row["TRI_TITLE"],
                    $this->getFieldNameByFormatFieldName("TRI_DESCRIPTION") => $row["TRI_DESCRIPTION"],
                    $this->getFieldNameByFormatFieldName("TRI_TYPE")        => $row["TRI_TYPE"],
                    $this->getFieldNameByFormatFieldName("TRI_WEBBOT")      => $row["TRI_WEBBOT"],
                    $this->getFieldNameByFormatFieldName("TRI_PARAM")       => $row["TRI_PARAM"]
                );
            }

            //Return
            return $arrayAvailableTrigger;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Triggers of a Step
     *
     * @param string $stepUid Unique id of Step
     * @param string $taskUid Unique id of Task
     *
     * return array Return an array with all Triggers of a Step
     */
    public function getTriggers($stepUid, $taskUid = "")
    {
        try {
            $arrayTrigger = array();

            //Verify data
            if ($stepUid != "") {
                $this->throwExceptionIfNoExistsStep($stepUid);
            }

            if ($stepUid == "") {
                $this->throwExceptionIfNoExistsTask($taskUid);
            }

            //Get data
            $bmTrigger = new \BusinessModel\Trigger();
            $bmStepTrigger = new \BusinessModel\Step\Trigger();

            $stepTrigger = new \StepTrigger();

            if ($stepUid != "") {
                //Load Step
                $step = new \Step();

                $arrayStepData = $step->load($stepUid);

                $taskUid = $arrayStepData["TAS_UID"];
            }

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

    /**
     * Validate Process Uid
     * @var string $pro_uid. Uid for Process
     * @var string $tas_uid. Uid for Task
     * @var string $step_uid. Uid for Step
     * @var string $step_pos. Position for Step
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function moveSteps($pro_uid, $tas_uid, $step_uid, $step_pos) {
        $this->setFormatFieldNameInUppercase(false);
        $this->setArrayParamException(array("taskUid" => "act_uid"));
        $aSteps = $this->getSteps($tas_uid);

        foreach ($aSteps as $dataStep) {
            if ($dataStep['step_uid'] == $step_uid) {
                $prStepPos = (int)$dataStep['step_position'];
            }
        }
        $seStepPos = $step_pos;

        //Principal Step is up
        if ($prStepPos == $seStepPos) {
            return true;
        } elseif ($prStepPos < $seStepPos) {
            $modPos = 'UP';
            $newPos = $seStepPos;
            $iniPos = $prStepPos+1;
            $finPos = $seStepPos;
        } else {
            $modPos = 'DOWN';
            $newPos = $seStepPos;
            $iniPos = $seStepPos;
            $finPos = $prStepPos-1;
        }

        $range = range($iniPos, $finPos);
        foreach ($aSteps as $dataStep) {
            if ((in_array($dataStep['step_position'], $range)) && ($dataStep['step_uid'] != $step_uid)) {
                $stepChangeIds[] = $dataStep['step_uid'];
                $stepChangePos[] = $dataStep['step_position'];
            }
        }

        foreach ($stepChangeIds as $key => $value) {
            if ($modPos == 'UP') {
                $tempPos = ((int)$stepChangePos[$key])-1;
                $this->changePosStep($value, $tempPos);
            } else {
                $tempPos = ((int)$stepChangePos[$key])+1;
                $this->changePosStep($value, $tempPos);
            }
        }
        $this->changePosStep($step_uid, $newPos);
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
    public function changePosStep ($step_uid, $pos)
    {
        $data = array(
            'STEP_UID' => $step_uid,
            'STEP_POSITION' => $pos
        );
        $oStep = new \Step();
        $oStep->update($data);
    }
}

