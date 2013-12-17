<?php
namespace BusinessModel;

class InputDocument
{
    /**
     * Get data of unique ids of an InputDocument (Unique id of Process)
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return array
     */
    public function getDataUids($inputDocumentUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::PRO_UID);
            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                return $rsCriteria->getRow();
            } else {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($inputDocumentUid, "INPUT_DOCUMENT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checks if the title exists in the InputDocuments of Process
     *
     * @param string $processUid Unique id of Process
     * @param string $title      Title
     * @param string $inputDocumentUidExclude Unique id of InputDocument to exclude
     *
     * return bool Return true if the title exists in the InputDocuments of Process, false otherwise
     */
    public function titleExists($processUid, $title, $inputDocumentUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);

            $criteria->addAlias("CT", "CONTENT");

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "INP_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($inputDocumentUidExclude != "") {
                $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $title, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
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
     * Get data from a request data
     *
     * @param object $requestData Request data
     *
     * return array Return an array with data of request data
     */
    public function getArrayDataFromRequestData($requestData)
    {
        try {
            $arrayData = array();

            $requestData = (array)($requestData);

            foreach ($requestData as $key => $value) {
                $arrayData[$key] = $value;
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create InputDocument for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new InputDocument created
     */
    public function create($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["INP_DOC_UID"]);

            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["INP_DOC_TITLE"]) && $this->titleExists($processUid, $arrayData["INP_DOC_TITLE"])) {
                throw (new \Exception(\G::LoadTranslation("ID_INPUT_NOT_SAVE")));
            }

            //Create
            $inputdoc = new \InputDocument();

            $arrayData["PRO_UID"] = $processUid;

            $inputDocumentUid = $inputdoc->create($arrayData);

            //Return
            unset($arrayData["PRO_UID"]);

            $arrayData = array_change_key_case($arrayData, CASE_LOWER);

            $arrayData["inp_doc_uid"] = $inputDocumentUid;

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     * @param array  $arrayData        Data
     *
     * return array Return data of the InputDocument updated
     */
    public function update($inputDocumentUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $inputdoc = new \InputDocument();

            if (!$inputdoc->InputExists($inputDocumentUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($inputDocumentUid, "INPUT_DOCUMENT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Uids
            $arrayDataUid = $this->getDataUids($inputDocumentUid);

            $processUid = $arrayDataUid["PRO_UID"];

            //Verify data
            if (isset($arrayData["INP_DOC_TITLE"]) && $this->titleExists($processUid, $arrayData["INP_DOC_TITLE"], $inputDocumentUid)) {
                throw (new \Exception(\G::LoadTranslation("ID_INPUT_NOT_SAVE")));
            }

            //Update
            $arrayData["INP_DOC_UID"] = $inputDocumentUid;

            $result = $inputdoc->update($arrayData);

            //Return
            unset($arrayData["INP_DOC_UID"]);

            return array_change_key_case($arrayData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return void
     */
    public function delete($inputDocumentUid)
    {
        try {
            //Verify data
            $inputdoc = new \InputDocument();

            if (!$inputdoc->InputExists($inputDocumentUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($inputDocumentUid, "INPUT_DOCUMENT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Delete
            //StepSupervisor
            $stepSupervisor = new \StepSupervisor();

            $arrayData = $stepSupervisor->loadInfo($inputDocumentUid);
            $result = $stepSupervisor->remove($arrayData["STEP_UID"]);

            //ObjectPermission
            $objectPermission = new \ObjectPermission();

            $arrayData = $objectPermission->loadInfo($inputDocumentUid);

            if (is_array($arrayData)) {
                $result = $objectPermission->remove($arrayData["OP_UID"]);
            }

            //InputDocument
            $inputdoc = new \InputDocument();

            $result = $inputdoc->remove($inputDocumentUid);

            //Step
            $step = new \Step();

            $step->removeStep("INPUT_DOCUMENT", $inputDocumentUid);

            //ObjectPermission
            $objectPermission = new \ObjectPermission();

            $objectPermission->removeByObject("INPUT", $inputDocumentUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for InputDocument
     *
     * return object
     */
    public function getInputDocumentCriteria()
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);
            $criteria->addAsColumn("INP_DOC_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("INP_DOC_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_FORM_NEEDED);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_ORIGINAL);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_PUBLISHED);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_VERSIONING);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_DESTINATION_PATH);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_TAGS);

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

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an InputDocument from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data of an InputDocument
     */
    public function getInputDocumentDataFromRecord($record)
    {
        try {
            return array(
                "inp_doc_uid"         => $record["INP_DOC_UID"],
                "inp_doc_title"       => $record["INP_DOC_TITLE"],
                "inp_doc_description" => $record["INP_DOC_DESCRIPTION"] . "",
                "inp_doc_form_needed" => $record["INP_DOC_FORM_NEEDED"],
                "inp_doc_original"    => $record["INP_DOC_ORIGINAL"],
                "inp_doc_published"   => $record["INP_DOC_PUBLISHED"],
                "inp_doc_versioning"  => (int)($record["INP_DOC_VERSIONING"]),
                "inp_doc_destination_path" => $record["INP_DOC_DESTINATION_PATH"] . "",
                "inp_doc_tags"             => $record["INP_DOC_TAGS"] . ""
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return array Return an array with data of an InputDocument
     */
    public function getInputDocument($inputDocumentUid)
    {
        try {
            //Verify data
            $inputdoc = new \InputDocument();

            if (!$inputdoc->InputExists($inputDocumentUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($inputDocumentUid, "INPUT_DOCUMENT"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $criteria = $this->getInputDocumentCriteria();

            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            if ($row["INP_DOC_TITLE"] . "" == "") {
                //There is no transaltion for this Document name, try to get/regenerate the label
                $arrayInputdocData = $inputdoc->load($inputDocumentUid);

                $row["INP_DOC_TITLE"] = $arrayInputdocData["INP_DOC_TITLE"];
                $row["INP_DOC_DESCRIPTION"] = $arrayInputdocData["INP_DOC_DESCRIPTION"];
            }

            return $this->getInputDocumentDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

