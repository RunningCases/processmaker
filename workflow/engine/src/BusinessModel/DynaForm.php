<?php
namespace BusinessModel;

class DynaForm
{
    /**
     * Get data of unique ids of a DynaForm (Unique id of Process)
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array
     */
    public function getDataUids($dynaFormUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\DynaformPeer::PRO_UID);
            $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                return $rsCriteria->getRow();
            } else {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($dynaFormUid, "DYNAFORM"), "The UID \"{0}\" doesn't exist in table {1}")));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if the title exists in the DynaForms of Process
     *
     * @param string $processUid Unique id of Process
     * @param string $title      Title
     * @param string $dynaFormUidExclude Unique id of DynaForm to exclude
     *
     * return bool Return true if the title exists in the DynaForms of Process, false otherwise
     */
    public function titleExists($processUid, $title, $dynaFormUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\DynaformPeer::DYN_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "DYN_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($dynaFormUidExclude != "") {
                $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $title, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
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
     * Verify if a DynaForm belongs to Process
     *
     * @param string $dynaFormUid Unique id of DynaForm
     * @param string $processUid  Unique id of Process
     *
     * return bool Return true if a DynaForm belongs to Process, false otherwise
     */
    public function dynaFormBelongsProcess($dynaFormUid, $processUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);
            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
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
     * Verify if a DynaForm is assigned some Steps
     *
     * @param string $dynaFormUid Unique id of DynaForm
     * @param string $processUid  Unique id of Process
     *
     * return bool Return true if a DynaForm is assigned some Steps, false otherwise
     */
    public function dynaFormAssignedStep($dynaFormUid, $processUid)
    {
        try {
            $step = new \Step();

            $arrayData = $step->loadInfoAssigDynaform($processUid, $dynaFormUid);

            if (is_array($arrayData)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if a DynaForm has relation with a Step Supervisor
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return bool Return true if a DynaForm has relation with a Step Supervisor, false otherwise
     */
    public function dynaFormRelationStepSupervisor($dynaFormUid)
    {
        try {
            $stepSupervisor = new \StepSupervisor();

            $arrayData = $stepSupervisor->loadInfo($dynaFormUid);

            if (is_array($arrayData)) {
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
     * Create DynaForm for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function create($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["DYN_UID"]);
            unset($arrayData["COPY_IMPORT"]);
            unset($arrayData["PMTABLE"]);

            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["DYN_TITLE"]) && $this->titleExists($processUid, $arrayData["DYN_TITLE"])) {
                throw (new \Exception(\G::LoadTranslation("ID_EXIST_DYNAFORM")));
            }

            //Create
            $dynaForm = new \Dynaform();

            $arrayData["PRO_UID"] = $processUid;

            $dynaFormUid = $dynaForm->create($arrayData);

            //Return
            unset($arrayData["PRO_UID"]);

            $arrayData = array_change_key_case($arrayData, CASE_LOWER);

            unset($arrayData["dyn_uid"]);

            return array_merge(array("dyn_uid" => $dynaFormUid), $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update DynaForm
     *
     * @param string $dynaFormUid Unique id of DynaForm
     * @param array  $arrayData   Data
     *
     * return array Return data of the DynaForm updated
     */
    public function update($dynaFormUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $dynaForm = new \Dynaform();

            if (!$dynaForm->dynaformExists($dynaFormUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($dynaFormUid, "DYNAFORM"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Uids
            $arrayDataUid = $this->getDataUids($dynaFormUid);

            $processUid = $arrayDataUid["PRO_UID"];

            //Verify data
            if (isset($arrayData["DYN_TITLE"]) && $this->titleExists($processUid, $arrayData["DYN_TITLE"], $dynaFormUid)) {
                throw (new \Exception(\G::LoadTranslation("ID_EXIST_DYNAFORM")));
            }

            //Update
            $arrayData["DYN_UID"] = $dynaFormUid;

            $result = $dynaForm->update($arrayData);

            //Return
            unset($arrayData["DYN_UID"]);

            return array_change_key_case($arrayData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete DynaForm
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return void
     */
    public function delete($dynaFormUid)
    {
        try {
            //Verify data
            $dynaForm = new \Dynaform();

            if (!$dynaForm->dynaformExists($dynaFormUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($dynaFormUid, "DYNAFORM"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Uids
            $arrayDataUid = $this->getDataUids($dynaFormUid);

            $processUid = $arrayDataUid["PRO_UID"];

            //Verify data
            if ($this->dynaFormAssignedStep($dynaFormUid, $processUid)) {
                throw (new \Exception("You cannot delete this Dynaform while it is assigned to a step"));
            }

            //Delete
            //In table DYNAFORM
            $result = $dynaForm->remove($dynaFormUid);

            //In table STEP
            $step = new \Step();
            $step->removeStep("DYNAFORM", $dynaFormUid);

            //In table OBJECT_PERMISSION
            $objPermission = new \ObjectPermission();
            $objPermission->removeByObject("DYNAFORM", $dynaFormUid);

            //In table STEP_SUPERVISOR
            $stepSupervisor = new \StepSupervisor();
            $stepSupervisor->removeByObject("DYNAFORM", $dynaFormUid);

            //In table CASE_TRACKER_OBJECT
            $caseTrackerObject = new \CaseTrackerObject();
            $caseTrackerObject->removeByObject("DYNAFORM", $dynaFormUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Copy/Import a DynaForm
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function copyImport($processUid, $arrayData)
    {
        try {
            $arrayData = \G::array_change_key_case2($arrayData, CASE_UPPER);

            //Verify data
            if (!isset($arrayData["COPY_IMPORT"]["PRJ_UID"])) {
                throw (new \Exception(str_replace(array("{0}"), array("PRJ_UID"), "For the creation the DynaForm, the attribute \"{0}\" doesn't exist")));
            }

            if (!isset($arrayData["COPY_IMPORT"]["DYN_UID"])) {
                throw (new \Exception(str_replace(array("{0}"), array("DYN_UID"), "For the creation the DynaForm, the attribute \"{0}\" doesn't exist")));
            }

            //Copy/Import Uids
            $processUidCopyImport  = $arrayData["COPY_IMPORT"]["PRJ_UID"];
            $dynaFormUidCopyImport = $arrayData["COPY_IMPORT"]["DYN_UID"];

            unset($arrayData["COPY_IMPORT"]);

            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (!$process->exists($processUidCopyImport)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUidCopyImport, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            $dynaForm = new \Dynaform();

            if (!$dynaForm->dynaformExists($dynaFormUidCopyImport)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($dynaFormUidCopyImport, "DYNAFORM"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (!$this->dynaFormBelongsProcess($dynaFormUidCopyImport, $processUidCopyImport)) {
                throw (new \Exception("The DynaForm for Copy/Import doesn't belongs to the Process"));
            }

            //Copy/Import
            //Create
            $arrayData = $this->create($processUid, $arrayData);

            $dynaFormUid = $arrayData["dyn_uid"];

            //Copy files of the DynaForm
            $umaskOld = umask(0);

            $fileXml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormUidCopyImport . ".xml";

            if (file_exists($fileXml)) {
                $fileXmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormUid . ".xml";

                $fhXml = fopen($fileXml, "r");
                $fhXmlCopy = fopen($fileXmlCopy, "w");

                while (!feof($fhXml)) {
                    $strLine = fgets($fhXml, 4096);
                    $strLine = str_replace($processUidCopyImport . "/" . $dynaFormUidCopyImport, $processUid . "/" . $dynaFormUid, $strLine);

                    //DynaForm Grid
                    preg_match_all("/<.*type\s*=\s*[\"\']grid[\"\'].*xmlgrid\s*=\s*[\"\']\w{32}\/(\w{32})[\"\'].*\/>/", $strLine, $arrayMatch, PREG_SET_ORDER);

                    foreach ($arrayMatch as $value) {
                        $dynaFormGridUidCopyImport = $value[1];

                        //Get data
                        $criteria = new \Criteria();

                        $criteria->addSelectColumn(\ContentPeer::CON_VALUE);
                        $criteria->add(\ContentPeer::CON_ID, $dynaFormGridUidCopyImport);
                        $criteria->add(\ContentPeer::CON_CATEGORY, "DYN_TITLE");
                        $criteria->add(\ContentPeer::CON_LANG, SYS_LANG);

                        $rsCriteria = \ContentPeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                        $rsCriteria->next();
                        $row = $rsCriteria->getRow();

                        $dynGrdTitleCopyImport = $row["CON_VALUE"];

                        $criteria = new \Criteria();

                        $criteria->addSelectColumn(\ContentPeer::CON_VALUE);
                        $criteria->add(\ContentPeer::CON_ID, $dynaFormGridUidCopyImport);
                        $criteria->add(\ContentPeer::CON_CATEGORY, "DYN_DESCRIPTION");
                        $criteria->add(\ContentPeer::CON_LANG, SYS_LANG);

                        $rsCriteria = \ContentPeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                        $rsCriteria->next();
                        $row = $rsCriteria->getRow();

                        $dynGrdDescriptionCopyImport = $row["CON_VALUE"];

                        //Create Grid
                        $arrayDataAux = array(
                            "PRO_UID"   => $processUid,
                            "DYN_TITLE" => $dynGrdTitleCopyImport,
                            "DYN_DESCRIPTION" => $dynGrdDescriptionCopyImport,
                            "DYN_TYPE" => "grid"
                        );

                        $dynaFormGrid = new \Dynaform();

                        $dynaFormGridUid = $dynaFormGrid->create($arrayDataAux);

                        //Copy files of the DynaForm Grid
                        $fileGridXml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormGridUidCopyImport . ".xml";

                        if (file_exists($fileGridXml)) {
                            $fileGridXmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormGridUid . ".xml";

                            $fhGridXml = fopen($fileGridXml, "r");
                            $fhGridXmlCopy = fopen($fileGridXmlCopy, "w");

                            while (!feof($fhGridXml)) {
                                $strLineAux = fgets($fhGridXml, 4096);
                                $strLineAux = str_replace($processUidCopyImport . "/" . $dynaFormGridUidCopyImport, $processUid . "/" . $dynaFormGridUid, $strLineAux);

                                fwrite($fhGridXmlCopy, $strLineAux);
                            }

                            fclose($fhGridXmlCopy);
                            fclose($fhGridXml);

                            chmod($fileGridXmlCopy, 0777);
                        }

                        $fileGridHtml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormGridUidCopyImport . ".html";

                        if (file_exists($fileGridHtml)) {
                            $fileGridHtmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormGridUid . ".html";

                            copy($fileGridHtml, $fileGridHtmlCopy);

                            chmod($fileGridHtmlCopy, 0777);
                        }

                        $strLine = str_replace($processUidCopyImport . "/" . $dynaFormGridUidCopyImport, $processUid . "/" . $dynaFormGridUid, $strLine);
                    }

                    fwrite($fhXmlCopy, $strLine);
                }

                fclose($fhXmlCopy);
                fclose($fhXml);

                chmod($fileXmlCopy, 0777);
            }

            $fileHtml = PATH_DYNAFORM . $processUidCopyImport . PATH_SEP . $dynaFormUidCopyImport . ".html";

            if (file_exists($fileHtml)) {
                $fileHtmlCopy = PATH_DYNAFORM . $processUid . PATH_SEP . $dynaFormUid . ".html";

                copy($fileHtml, $fileHtmlCopy);

                chmod($fileHtmlCopy, 0777);
            }

            //Copy if there are conditions attached to the DynaForm
            $fieldCondition = new \FieldCondition();

            $arrayCondition = $fieldCondition->getAllByDynUid($dynaFormUidCopyImport);

            foreach ($arrayCondition as $condition) {
                $condition["FCD_UID"] = "";
                $condition["FCD_DYN_UID"] = $dynaFormUid;

                $fieldCondition->quickSave($condition);
            }

            umask($umaskOld);

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a Dynaform based on a PMTable
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function createBasedPmTable($processUid, $arrayData)
    {
        try {
            $arrayData = \G::array_change_key_case2($arrayData, CASE_UPPER);

            unset($arrayData["DYN_UID"]);
            unset($arrayData["COPY_IMPORT"]);

            //Verify data
            if (!isset($arrayData["PMTABLE"]["TAB_UID"])) {
                throw (new \Exception(str_replace(array("{0}"), array("TAB_UID"), "For the creation the DynaForm, the attribute \"{0}\" doesn't exist")));
            }

            if (!isset($arrayData["PMTABLE"]["FIELDS"])) {
                throw (new \Exception(str_replace(array("{0}"), array("FIELDS"), "For the creation the DynaForm, the attribute \"{0}\" doesn't exist")));
            }

            if (count($arrayData["PMTABLE"]["FIELDS"]) == 0) {
                throw (new \Exception(str_replace(array("{0}"), array("FIELDS"), "For the creation the DynaForm, the attribute \"{0}\" is empty")));
            }

            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            if (isset($arrayData["DYN_TITLE"]) && $this->titleExists($processUid, $arrayData["DYN_TITLE"])) {
                throw (new \Exception(\G::LoadTranslation("ID_EXIST_DYNAFORM")));
            }

            if (isset($arrayData["DYN_TYPE"]) && $arrayData["DYN_TYPE"] == "grid") {
                throw (new \Exception(str_replace(array("{0}"), array("DYN_TYPE"), "For the creation the DynaForm, the attribute \"{0}\" is invalid")));
            }

            if (is_null(\AdditionalTablesPeer::retrieveByPK($arrayData["PMTABLE"]["TAB_UID"]))) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($arrayData["PMTABLE"]["TAB_UID"], "ADDITIONAL_TABLES"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Set data
            $tableUid    = $arrayData["PMTABLE"]["TAB_UID"];
            $arrayFields = $arrayData["PMTABLE"]["FIELDS"];

            unset($arrayData["PMTABLE"]);

            //Create
            $dynaForm = new \Dynaform();

            $arrayData["PRO_UID"] = $processUid;
            $arrayData["DYN_TYPE"] = "xmlform";
            $arrayData["FIELDS"] = $arrayFields;

            $dynaForm->createFromPMTable($arrayData, $tableUid);

            $dynaFormUid = $dynaForm->getDynUid();

            //Return
            unset($arrayData["PRO_UID"]);
            unset($arrayData["FIELDS"]);

            $arrayData = \G::array_change_key_case2($arrayData, CASE_LOWER);

            unset($arrayData["dyn_uid"]);

            return array_merge(array("dyn_uid" => $dynaFormUid), $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Defines the method for create a DynaForm
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new DynaForm created
     */
    public function defineCreate($processUid, $arrayData)
    {
        try {
            $option = "NORMAL";

            //Validate data
            $count = 0;
            $msgMethod = "";

            if (isset($arrayData["copy_import"])) {
                $count = $count + 1;
                $msgMethod = (($msgMethod != "")? ", " : "") . $msgMethod . "COPY_IMPORT";

                $option = "COPY_IMPORT";
            }

            if (isset($arrayData["pmtable"])) {
                $count = $count + 1;
                $msgMethod = (($msgMethod != "")? ", " : "") . $msgMethod . "PMTABLE";

                $option = "PMTABLE";
            }

            if ($count <= 1) {
                $arrayDataAux = array();

                switch ($option) {
                    case "COPY_IMPORT":
                        $arrayDataAux = $this->copyImport($processUid, $arrayData);
                        break;
                    case "PMTABLE":
                        $arrayDataAux = $this->createBasedPmTable($processUid, $arrayData);
                        break;
                    default:
                        //NORMAL
                        $arrayDataAux = $this->create($processUid, $arrayData);
                        break;
                }

                //Return
                return $arrayDataAux;
            } else {
                throw (new \Exception(str_replace(array("{0}"), array($msgMethod), "It is trying to create a DynaForm by \"{0}\", please send only one attribute for creation")));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for DynaForm
     *
     * return object
     */
    public function getDynaFormCriteria()
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $criteria->addAsColumn("DYN_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("DYN_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\DynaformPeer::DYN_TYPE);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);
            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);

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

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a DynaForm from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data of a DynaForm
     */
    public function getDynaFormDataFromRecord($record)
    {
        try {
            if ($record["DYN_TITLE"] . "" == "") {
                //There is no transaltion for this Document name, try to get/regenerate the label
                $record["DYN_TITLE"] = \Content::load("DYN_TITLE", "", $record["DYN_UID"], SYS_LANG);
            }

            if ($record["DYN_DESCRIPTION"] . "" == "") {
                //There is no transaltion for this Document name, try to get/regenerate the label
                $record["DYN_DESCRIPTION"] = \Content::load("DYN_DESCRIPTION", "", $record["DYN_UID"], SYS_LANG);
            }

            return array(
                "dyn_uid"         => $record["DYN_UID"],
                "dyn_title"       => $record["DYN_TITLE"],
                "dyn_description" => $record["DYN_DESCRIPTION"] . "",
                "dyn_type"        => $record["DYN_TYPE"] . ""
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a DynaForm
     *
     * @param string $dynaFormUid Unique id of DynaForm
     *
     * return array Return an array with data of a DynaForm
     */
    public function getDynaForm($dynaFormUid)
    {
        try {
            //Verify data
            $dynaForm = new \Dynaform();

            if (!$dynaForm->dynaformExists($dynaFormUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($dynaFormUid, "DYNAFORM"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $criteria = $this->getDynaFormCriteria();

            $criteria->add(\DynaformPeer::DYN_UID, $dynaFormUid, \Criteria::EQUAL);

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            return $this->getDynaFormDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

