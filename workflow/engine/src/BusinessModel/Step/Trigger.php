<?php
namespace BusinessModel\Step;

use \BusinessModel\Step;

class Trigger
{
    /**
     * Verify if exists the record in table STEP_TRIGGER
     *
     * @param string $stepUid           Unique id of Step
     * @param string $type              Type (BEFORE, AFTER)
     * @param string $taskUid           Unique id of Task
     * @param string $triggerUid        Unique id of Trigger
     * @param int    $position          Position
     * @param string $triggerUidExclude Unique id of Trigger to exclude
     *
     * return bool Return true if exists the record in table STEP_TRIGGER, false otherwise
     */
    public function existsRecord($stepUid, $type, $taskUid, $triggerUid, $position = 0, $triggerUidExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepTriggerPeer::STEP_UID);
            $criteria->add(\StepTriggerPeer::STEP_UID, $stepUid, \Criteria::EQUAL);
            $criteria->add(\StepTriggerPeer::ST_TYPE, $type, \Criteria::EQUAL);
            $criteria->add(\StepTriggerPeer::TAS_UID, $taskUid, \Criteria::EQUAL);

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
     * @param string $type       Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid    Unique id of Task
     * @param string $triggerUid Unique id of Trigger
     * @param array  $arrayData  Data
     *
     * return array Data of the Trigger assigned to a Step
     */
    public function create($stepUid, $type, $taskUid, $triggerUid, $arrayData)
    {
        try {
            $stepUidIni = $stepUid;
            $typeIni = $type;

            $flagStepAssignTask = 0;

            if ($stepUid == "") {
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

            //Verify data
            if ($flagStepAssignTask == 0) {
                $step = new \Step();

                if (!$step->StepExists($stepUid)) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
                }
            }

            $task = new \Task();

            if (!$task->taskExists($taskUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($taskUid, "TASK"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $trigger = new \Triggers();

            if (!$trigger->TriggerExists($triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($triggerUid, "TRIGGERS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if ($this->existsRecord($stepUid, $type, $taskUid, $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid . ", " . $type . ", " . $taskUid . ", " . $triggerUid, "STEP_TRIGGER"), "The record \"{0}\", exists in table {1}")));
            }

            if (isset($arrayData["st_position"]) && $this->existsRecord($stepUid, $type, $taskUid, "", $arrayData["st_position"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["st_position"], $stepUid . ", " . $type . ", " . $taskUid . ", " . $arrayData["st_position"], "STEP_TRIGGER"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Create
            $stepTrigger = new \StepTrigger();

            $stepTrigger->create(array("STEP_UID" => $stepUid, "TAS_UID" => $taskUid, "TRI_UID" => $triggerUid, "ST_TYPE" => $type));

            if (!isset($arrayData["st_position"]) || $arrayData["st_position"] == "") {
                $arrayData["st_position"] = $stepTrigger->getNextPosition($stepUid, $type, $taskUid) - 1;
            }

            $arrayData = $this->update($stepUidIni, $typeIni, $taskUid, $triggerUid, $arrayData);

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Trigger of a Step
     *
     * @param string $stepUid    Unique id of Step
     * @param string $type       Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid    Unique id of Task
     * @param string $triggerUid Unique id of Trigger
     * @param array  $arrayData  Data
     *
     * return array Data updated of the Trigger assigned to a Step
     */
    public function update($stepUid, $type, $taskUid, $triggerUid, $arrayData)
    {
        try {
            $flagStepAssignTask = 0;

            if ($stepUid == "") {
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

            //Verify data
            if ($flagStepAssignTask == 0) {
                $step = new \Step();

                if (!$step->StepExists($stepUid)) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid, "STEP"), "The UID \"{0}\" doesn't exist in table {1}")));
                }
            }

            $trigger = new \Triggers();

            if (!$trigger->TriggerExists($triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($triggerUid, "TRIGGERS"), "The UID \"{0}\" doesn't exist in table {1}")));
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
                $tempPos = (int)($arrayData["st_position"]);
            }

            $stepTrigger->update($arrayUpdateData);
            if (isset($tempPos)) {
                $this->moveStepTriggers($taskUid, $stepUid, $triggerUid, $type, $tempPos);
            }

            return array_change_key_case($arrayUpdateData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Trigger of a Step
     *
     * @param string $stepUid    Unique id of Step
     * @param string $type       Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid    Unique id of Task
     * @param string $triggerUid Unique id of Trigger
     *
     * return void
     */
    public function delete($stepUid, $type, $taskUid, $triggerUid)
    {
        try {
            if ($stepUid == "") {
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

            //Verify data
            if (!$this->existsRecord($stepUid, $type, $taskUid, $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid . ", " . $type . ", " . $taskUid . ", " . $triggerUid, "STEP_TRIGGER"), "The record \"{0}\", doesn't exist in table {1}")));
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
     * @param string $type       Type (BEFORE, AFTER, BEFORE_ASSIGNMENT, BEFORE_ROUTING, AFTER_ROUTING)
     * @param string $taskUid    Unique id of Task
     * @param string $triggerUid Unique id of Trigger
     *
     * return array Return an array with data of a Trigger
     */
    public function getTrigger($stepUid, $type, $taskUid, $triggerUid)
    {
        try {
            $typeIni = $type;

            $flagStepAssignTask = 0;

            if ($stepUid == "") {
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

            //Verify data
            if (!$this->existsRecord($stepUid, $type, $taskUid, $triggerUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($stepUid . ", " . $type . ", " . $taskUid . ", " . $triggerUid, "STEP_TRIGGER"), "The record \"{0}\", doesn't exist in table {1}")));
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

            if ($flagStepAssignTask == 1) {
                $row["ST_TYPE"] = $typeIni;
            }

            return $this->getTriggerDataFromRecord($row);
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
    public function moveStepTriggers($tasUid, $stepUid, $triUid, $type, $newPos) {
        $stepTrigger = new \BusinessModel\Step();
        $aStepTriggers = $stepTrigger->getTriggers($stepUid, $tasUid);
        foreach ($aStepTriggers as $dataStep) {
            if (($dataStep['st_type'] == $type) && ($dataStep['tri_uid'] == $triUid)) {
                $prStepPos = (int)$dataStep['st_position'];
            }
        }
        $seStepPos = $newPos;

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
        foreach ($aStepTriggers as $dataStep) {
            if (($dataStep['st_type'] == $type) && (in_array($dataStep['st_position'], $range)) && ($dataStep['tri_uid'] != $triUid)) {
                $stepChangeIds[] = $dataStep['tri_uid'];
                $stepChangePos[] = $dataStep['st_position'];
            }
        }

        foreach ($stepChangeIds as $key => $value) {
            if ($modPos == 'UP') {
                $tempPos = ((int)$stepChangePos[$key])-1;
                $this->changePosStep($stepUid, $tasUid, $value, $type, $tempPos);
            } else {
                $tempPos = ((int)$stepChangePos[$key])+1;
                $this->changePosStep($stepUid, $tasUid, $value, $type, $tempPos);
            }
        }
        $this->changePosStep($stepUid, $tasUid, $triUid, $type, $newPos);
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
    public function changePosStep ($stepUid, $tasUid, $triUid, $type, $pos)
    {
        $data = array(
            'STEP_UID' => $stepUid,
            'TAS_UID'  => $tasUid,
            'TRI_UID'  => $triUid,
            'ST_TYPE'  => $type,
            'ST_POSITION' => $pos
        );
        $StepTrigger = new \StepTrigger();
        $StepTrigger->update($data);
    }
}

