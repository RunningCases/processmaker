<?php
namespace BusinessModel;

class Step
{
    /**
     * Create Step of an Task
     *
     * @param string $taskUid
     * @param string $processUid
     * @param array  $arrayData
     *
     * return string Unique id of the new Step
     */
    public function create($taskUid, $processUid, $arrayData)
    {
        try {
            $step = new \Step();

            $stepUid = $step->create(array("PRO_UID" => $processUid, "TAS_UID" => $taskUid));

            if (!isset($arrayData["step_position"]) || $arrayData["step_position"] == "") {
                $arrayData["step_position"] = $step->getNextPosition($taskUid) - 1;
            }

            $this->update($stepUid, $arrayData);

            return $stepUid;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Step of an Task
     *
     * @param string $stepUid
     * @param array  $arrayData
     *
     * return void
     */
    public function update($stepUid, $arrayData)
    {
        try {
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Step of an Task
     *
     * @param string $stepUid
     *
     * return void
     */
    public function delete($stepUid)
    {
        try {
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
     * Get data of an Step
     *
     * @param string $stepUid Unique id of the Step
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
                "step_position"   => $row["STEP_POSITION"],
                "step_mode"       => $row["STEP_MODE"],
                "obj_title"       => $titleObj,
                "obj_description" => $descriptionObj
            );

            return $arrayStep;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

