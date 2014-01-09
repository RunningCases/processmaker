<?php
namespace BusinessModel\Step;

class Trigger
{
    /**
     * Verify if exists the record in table STEP_TRIGGER
     *
     * @param string $stepUid           Unique id of Step
     * @param string $type              Type (BEFORE, AFTER)
     * @param string $triggerUid        Unique id of Trigger
     * @param int    $position          Position
     * @param string $triggerUidExclude Unique id of Trigger to exclude
     *
     * return bool Return true if exists the record in table STEP_TRIGGER, false otherwise
     */
    public function existsRecord($stepUid, $type, $triggerUid, $position = 0, $triggerUidExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepTriggerPeer::STEP_UID);
            $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);
            $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);

            if ($triggerUid != "") {
                $criteria->add(\StepTriggerPeer::TRI_UID, $triggerUid, \Criteria::EQUAL);
            }

            if ($position > 0) {
                $criteria->add(\StepTriggerPeer::ST_POSITION, $position, \Criteria::EQUAL);
            }

            if ($triggerUidExclude != "") {
                $criteria->add(\StepTriggerPeer::TRI_UID, $triggerUidExclude, \Criteria::NOT_EQUAL);
            }

            $rsCriteria = \StepTriggerPeer::doSelectRS($criteria);
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
     * Assign Trigger to a Step
     *
     * @param string $stepUid    Unique id of Step
     * @param string $type       Type (BEFORE, AFTER)
     * @param string $triggerUid Unique id of Trigger
     * @param array  $arrayData  Data
     *
     * return array Data of the Trigger assigned to a Step
     */
    public function create($stepUid, $type, $triggerUid, $arrayData)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

            //Verify data
            $step = new \Step();

            if (!$step->StepExists($stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $trigger = new \Triggers();

            if (!$trigger->TriggerExists($triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($triggerUid, "TRIGGERS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if ($this->existsRecord($stepUid, $type, $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid . ", " . $type . ", " . $triggerUid, "STEP_TRIGGER"), "The record \"{0}\", exists in table {1}")));
            }

            if (isset($arrayData["st_position"]) && $this->existsRecord($stepUid, $type, "", $arrayData["st_position"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["st_position"], $stepUid . ", " . $type . ", " . $arrayData["st_position"], "STEP_TRIGGER"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Create
            $stepTrigger = new \StepTrigger();

            $stepTrigger->create(array("STEP_UID" => $stepUid, "TAS_UID" => $taskUid, "TRI_UID" => $triggerUid, "ST_TYPE" => $type));

            if (!isset($arrayData["st_position"]) || $arrayData["st_position"] == "") {
                $arrayData["st_position"] = $stepTrigger->getNextPosition($stepUid, $type, $taskUid) - 1;
            }

            $arrayData = $this->update($stepUid, $type, $triggerUid, $arrayData);

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Trigger of a Step
     *
     * @param string $stepUid    Unique id of Step
     * @param string $type       Type (BEFORE, AFTER)
     * @param string $triggerUid Unique id of Trigger
     * @param array  $arrayData  Data
     *
     * return array Data updated of the Trigger assigned to a Step
     */
    public function update($stepUid, $type, $triggerUid, $arrayData)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

            //Verify data
            $step = new \Step();

            if (!$step->StepExists($stepUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $trigger = new \Triggers();

            if (!$trigger->TriggerExists($triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($triggerUid, "TRIGGERS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["st_position"]) && $this->existsRecord($stepUid, $type, "", $arrayData["st_position"], $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["st_position"], $stepUid . ", " . $type . ", " . $arrayData["st_position"], "STEP_TRIGGER"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Update
            $stepTrigger = new \StepTrigger();

            $arrayUpdateData = array();

            $arrayUpdateData["STEP_UID"] = $stepUid;
            $arrayUpdateData["TAS_UID"] = $taskUid;
            $arrayUpdateData["TRI_UID"] = $triggerUid;
            $arrayUpdateData["ST_TYPE"] = $type;

            if (isset($arrayData["st_condition"])) {
                $arrayUpdateData["ST_CONDITION"] = $arrayData["st_condition"];
            }

            if (isset($arrayData["st_position"]) && $arrayData["st_position"] != "") {
                $arrayUpdateData["ST_POSITION"] = (int)($arrayData["st_position"]);
            }

            $stepTrigger->update($arrayUpdateData);

            return array_change_key_case($arrayUpdateData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Trigger of a Step
     *
     * @param string $stepUid    Unique id of Step
     * @param string $type       Type (BEFORE, AFTER)
     * @param string $triggerUid Unique id of Trigger
     *
     * return void
     */
    public function delete($stepUid, $type, $triggerUid)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

            //Verify data
            if (!$this->existsRecord($stepUid, $type, $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid . ", " . $type . ", " . $triggerUid, "STEP_TRIGGER"), "The record \"{0}\", doesn't exist in table {1}")));
            }

            //Get position
            $stepTrigger = new \StepTrigger();

            $arrayData = $stepTrigger->load($stepUid, $taskUid, $triggerUid, $type);

            $position = (int)($arrayData["ST_POSITION"]);

            //Delete
            $stepTrigger = new \StepTrigger();

            $stepTrigger->reOrder($stepUid, $taskUid, $type, $position);
            $stepTrigger->remove($stepUid, $taskUid, $triggerUid, $type);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Trigger from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data of a Trigger
     */
    public function getTriggerDataFromRecord($record)
    {
        try {
            return array(
                "tri_uid"         => $record["TRI_UID"],
                "tri_title"       => $record["TRI_TITLE"],
                "tri_description" => $record["TRI_DESCRIPTION"],
                "st_type"         => $record["ST_TYPE"],
                "st_condition"    => $record["ST_CONDITION"],
                "st_position"     => (int)($record["ST_POSITION"])
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Trigger
     *
     * @param string $stepUid    Unique id of Step
     * @param string $type       Type (BEFORE, AFTER)
     * @param string $triggerUid Unique id of Trigger
     *
     * return array Return an array with data of a Trigger
     */
    public function getTrigger($stepUid, $type, $triggerUid)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

            //Verify data
            if (!$this->existsRecord($stepUid, $type, $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid . ", " . $type . ", " . $triggerUid, "STEP_TRIGGER"), "The record \"{0}\", doesn't exist in table {1}")));
            }

            //Get data
            $trigger = new \BusinessModel\Trigger();

            $criteria = $trigger->getTriggerCriteria();

            $criteria->addSelectColumn(\StepTriggerPeer::ST_TYPE);
            $criteria->addSelectColumn(\StepTriggerPeer::ST_CONDITION);
            $criteria->addSelectColumn(\StepTriggerPeer::ST_POSITION);
            $criteria->addJoin(\StepTriggerPeer::TRI_UID, \TriggersPeer::TRI_UID, \Criteria::LEFT_JOIN);
            $criteria->add(\TriggersPeer::TRI_UID, $triggerUid, \Criteria::EQUAL);
            $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);
            $criteria->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);

            $rsCriteria = \StepTriggerPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            return $this->getTriggerDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

