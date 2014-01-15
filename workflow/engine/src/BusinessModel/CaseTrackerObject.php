<?php
namespace BusinessModel;

class CaseTrackerObject
{
    /**
     * Verify if exists the record in table CASE_TRACKER_OBJECT
     *
     * @param string $processUid Unique id of Process
     * @param string $type       Type of Step (DYNAFORM, INPUT_DOCUMENT, OUTPUT_DOCUMENT)
     * @param string $objectUid  Unique id of Object
     * @param int    $position   Position
     * @param string $caseTrackerObjectUidExclude Unique id of Case Tracker Object to exclude
     *
     * return bool Return true if exists the record in table CASE_TRACKER_OBJECT, false otherwise
     */
    public function existsRecord($processUid, $type, $objectUid, $position = 0, $caseTrackerObjectUidExclude = "")
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\CaseTrackerObjectPeer::CTO_UID);
            $criteria->add(\CaseTrackerObjectPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($caseTrackerObjectUidExclude != "") {
                $criteria->add(\CaseTrackerObjectPeer::CTO_UID, $caseTrackerObjectUidExclude, \Criteria::NOT_EQUAL);
            }

            if ($type != "") {
                $criteria->add(\CaseTrackerObjectPeer::CTO_TYPE_OBJ, $type, \Criteria::EQUAL);
            }

            if ($objectUid != "") {
                $criteria->add(\CaseTrackerObjectPeer::CTO_UID_OBJ, $objectUid, \Criteria::EQUAL);
            }

            if ($position > 0) {
                $criteria->add(\CaseTrackerObjectPeer::CTO_POSITION, $position, \Criteria::EQUAL);
            }

            $rsCriteria = \CaseTrackerObjectPeer::doSelectRS($criteria);
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
     * Create Case Tracker Object for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Case Tracker Object created
     */
    public function create($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["CTO_UID"]);

            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (!isset($arrayData["CTO_TYPE_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array("CTO_TYPE_OBJ"), "The \"{0}\" attribute is not defined")));
            }

            if (!isset($arrayData["CTO_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array("CTO_UID_OBJ"), "The \"{0}\" attribute is not defined")));
            }

            $step = new \BusinessModel\Step();

            $msg = $step->existsObjectUid($arrayData["CTO_TYPE_OBJ"], $arrayData["CTO_UID_OBJ"]);

            if ($msg != "") {
                throw (new \Exception($msg));
            }

            if ($this->existsRecord($processUid, $arrayData["CTO_TYPE_OBJ"], $arrayData["CTO_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid . ", " . $arrayData["CTO_TYPE_OBJ"] . ", " . $arrayData["CTO_UID_OBJ"], "CASE_TRACKER_OBJECT"), "The record \"{0}\", exists in table {1}")));
            }

            if (isset($arrayData["CTO_POSITION"]) && $this->existsRecord($processUid, "", "", $arrayData["CTO_POSITION"])) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["CTO_POSITION"], $processUid . ", " . $arrayData["CTO_POSITION"], "CASE_TRACKER_OBJECT"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Create
            $caseTrackerObject = new \CaseTrackerObject();

            $arrayData["PRO_UID"] = $processUid;

            if (!isset($arrayData["CTO_POSITION"])) {
                $criteria = new \Criteria("workflow");

                $criteria->add(\CaseTrackerObjectPeer::PRO_UID, $processUid);

                $arrayData["CTO_POSITION"] = \CaseTrackerObjectPeer::doCount($criteria) + 1;
            }

            $caseTrackerObjectUid = $caseTrackerObject->create($arrayData);

            //Return
            unset($arrayData["PRO_UID"]);

            $arrayData = array_change_key_case($arrayData, CASE_LOWER);

            unset($arrayData["cto_uid"]);

            return array_merge(array("cto_uid" => $caseTrackerObjectUid), $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Case Tracker Object
     *
     * @param string $caseTrackerObjectUid Unique id of Case Tracker Object
     * @param array  $arrayData Data
     *
     * return array Return data of the Case Tracker Object updated
     */
    public function update($caseTrackerObjectUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $caseTrackerObject = new \CaseTrackerObject();

            $arrayCaseTrackerObjectData = $caseTrackerObject->load($caseTrackerObjectUid);

            //Uids
            $processUid = $arrayCaseTrackerObjectData["PRO_UID"];

            //Verify data
            if (!$caseTrackerObject->caseTrackerObjectExists($caseTrackerObjectUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($caseTrackerObjectUid, "CASE_TRACKER_OBJECT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["CTO_TYPE_OBJ"]) && !isset($arrayData["CTO_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array("CTO_UID_OBJ"), "The \"{0}\" attribute is not defined")));
            }

            if (!isset($arrayData["CTO_TYPE_OBJ"]) && isset($arrayData["CTO_UID_OBJ"])) {
                throw (new \Exception(str_replace(array("{0}"), array("CTO_TYPE_OBJ"), "The \"{0}\" attribute is not defined")));
            }

            if (isset($arrayData["CTO_TYPE_OBJ"]) && isset($arrayData["CTO_UID_OBJ"])) {
                $step = new \BusinessModel\Step();

                $msg = $step->existsObjectUid($arrayData["CTO_TYPE_OBJ"], $arrayData["CTO_UID_OBJ"]);

                if ($msg != "") {
                    throw (new \Exception($msg));
                }

                if ($this->existsRecord($processUid, $arrayData["CTO_TYPE_OBJ"], $arrayData["CTO_UID_OBJ"], 0, $caseTrackerObjectUid)) {
                    throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid . ", " . $arrayData["CTO_TYPE_OBJ"] . ", " . $arrayData["CTO_UID_OBJ"], "CASE_TRACKER_OBJECT"), "The record \"{0}\", exists in table {1}")));
                }
            }

            if (isset($arrayData["CTO_POSITION"]) && $this->existsRecord($processUid, "", "", $arrayData["CTO_POSITION"], $caseTrackerObjectUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}", "{2}"), array($arrayData["CTO_POSITION"], $processUid . ", " . $arrayData["CTO_POSITION"], "CASE_TRACKER_OBJECT"), "The \"{0}\" position for the record \"{1}\", exists in table {2}")));
            }

            //Flags
            $flagDataOject     = (isset($arrayData["CTO_TYPE_OBJ"]) && isset($arrayData["CTO_UID_OBJ"]))? 1 : 0;
            $flagDataCondition = (isset($arrayData["CTO_CONDITION"]))? 1 : 0;
            $flagDataPosition  = (isset($arrayData["CTO_POSITION"]))? 1 : 0;

            //Update
            $arrayData["CTO_UID"] = $caseTrackerObjectUid;

            $arrayData = array_merge($arrayCaseTrackerObjectData, $arrayData);

            $result = $caseTrackerObject->update($arrayData);

            //Return
            unset($arrayData["CTO_UID"]);

            if ($flagDataOject == 0) {
                unset($arrayData["CTO_TYPE_OBJ"]);
                unset($arrayData["CTO_UID_OBJ"]);
            }

            if ($flagDataCondition == 0) {
                unset($arrayData["CTO_CONDITION"]);
            }

            if ($flagDataPosition == 0) {
                unset($arrayData["CTO_POSITION"]);
            }

            unset($arrayData["PRO_UID"]);

            return array_change_key_case($arrayData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Case Tracker Object
     *
     * @param string $caseTrackerObjectUid Unique id of Case Tracker Object
     *
     * return void
     */
    public function delete($caseTrackerObjectUid)
    {
        try {
            $caseTrackerObject = new \CaseTrackerObject();

            $arrayCaseTrackerObjectData = $caseTrackerObject->load($caseTrackerObjectUid);

            //Uids
            $processUid = $arrayCaseTrackerObjectData["PRO_UID"];

            //Verify data
            if (!$caseTrackerObject->caseTrackerObjectExists($caseTrackerObjectUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($caseTrackerObjectUid, "CASE_TRACKER_OBJECT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Delete
            $result = $caseTrackerObject->remove($caseTrackerObjectUid);

            $caseTrackerObject->reorderPositions($processUid, $arrayCaseTrackerObjectData["CTO_POSITION"]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Case Tracker Object
     *
     * @param string $caseTrackerObjectUid Unique id of Case Tracker Object
     *
     * return array Return an array with data of a Case Tracker Object
     */
    public function getCaseTrackerObject($caseTrackerObjectUid)
    {
        try {
            //Verify data
            $caseTrackerObject = new \CaseTrackerObject();

            if (!$caseTrackerObject->caseTrackerObjectExists($caseTrackerObjectUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($caseTrackerObjectUid, "CASE_TRACKER_OBJECT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $dynaform = new \Dynaform();
            $inputDocument = new \InputDocument();
            $outputDocument = new \OutputDocument();

            $criteria = new \Criteria("workflow");

            $criteria->add(\CaseTrackerObjectPeer::CTO_UID, $caseTrackerObjectUid, \Criteria::EQUAL);

            $rsCriteria = \CaseTrackerObjectPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $titleObj = "";
            $descriptionObj = "";

            switch ($row["CTO_TYPE_OBJ"]) {
                case "DYNAFORM":
                    $arrayData = $dynaform->load($row["CTO_UID_OBJ"]);

                    $titleObj = $arrayData["DYN_TITLE"];
                    $descriptionObj = $arrayData["DYN_DESCRIPTION"];
                    break;
                case "INPUT_DOCUMENT":
                    $arrayData = $inputDocument->getByUid($row["CTO_UID_OBJ"]);

                    $titleObj = $arrayData["INP_DOC_TITLE"];
                    $descriptionObj = $arrayData["INP_DOC_DESCRIPTION"];
                    break;
                case "OUTPUT_DOCUMENT":
                    $arrayData = $outputDocument->getByUid($row["CTO_UID_OBJ"]);

                    $titleObj = $arrayData["OUT_DOC_TITLE"];
                    $descriptionObj = $arrayData["OUT_DOC_DESCRIPTION"];
                    break;
            }

            return array(
                "cto_uid"         => $row["CTO_UID"],
                "cto_type_obj"    => $row["CTO_TYPE_OBJ"],
                "cto_uid_obj"     => $row["CTO_UID_OBJ"],
                "cto_condition"   => $row["CTO_CONDITION"],
                "cto_position"    => (int)($row["CTO_POSITION"]),
                "obj_title"       => $titleObj,
                "obj_description" => $descriptionObj
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

