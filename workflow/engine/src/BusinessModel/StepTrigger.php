<?php
namespace BusinessModel;

class StepTrigger
{
    /**
     * Assign Trigger to an Step
     *
     * @param string $stepUid    Unique id of the Step
     * @param string $triggerUid Unique id of the Trigger
     * @param string $type       Type (BEFORE, AFTER)
     * @param array  $arrayData  Data
     *
     * return void
     */
    public function create($stepUid, $triggerUid, $type, $arrayData)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

            //Create
            $stepTrigger = new \StepTrigger();

            $stepTrigger->create(array("STEP_UID" => $stepUid, "TAS_UID" => $taskUid, "TRI_UID" => $triggerUid, "ST_TYPE" => $type));

            if (!isset($arrayData["st_position"]) || $arrayData["st_position"] == "") {
                $arrayData["st_position"] = $stepTrigger->getNextPosition($stepUid, $type, $taskUid) - 1;
            }

            $this->update($stepUid, $triggerUid, $type, $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Trigger of an Step
     *
     * @param string $stepUid    Unique id of the Step
     * @param string $triggerUid Unique id of the Trigger
     * @param string $type       Type (BEFORE, AFTER)
     * @param array  $arrayData  Data
     *
     * return void
     */
    public function update($stepUid, $triggerUid, $type, $arrayData)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Trigger of an Step
     *
     * @param string $stepUid    Unique id of the Step
     * @param string $triggerUid Unique id of the Trigger
     * @param string $type       Type (BEFORE, AFTER)
     *
     * return void
     */
    public function delete($stepUid, $triggerUid, $type)
    {
        try {
            $step = new \BusinessModel\Step();

            $arrayDataUid = $step->getDataUids($stepUid);

            $taskUid = $arrayDataUid["TAS_UID"];

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
}

