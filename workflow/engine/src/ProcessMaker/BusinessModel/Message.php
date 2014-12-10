<?php
namespace ProcessMaker\BusinessModel;

class Message
{
    /**
     * Create Message for a Process
     *
     * @param string $processUid Unique id of Message
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Message created
     */
    public function create($processUid, array $arrayData)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $this->existsName($processUid, $arrayData["MES_NAME"]);

            $this->throwExceptionFieldDefinition($arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");
            try {
                $message = new \Message();

                $sPkMessage = \ProcessMaker\Util\Common::generateUID();

                $message->setMesUid($sPkMessage);
                $message->setPrjUid($processUid);

                if ($message->validate()) {
                    $cnn->begin();

                    if (isset($arrayData["MES_NAME"])) {
                        $message->setMesName($arrayData["MES_NAME"]);
                    } else {
                        throw new \Exception(\G::LoadTranslation("ID_CAN_NOT_BE_NULL", array('$mes_name' )));
                    }
                    if (isset($arrayData["MES_DETAIL"])) {

                        foreach ($arrayData["MES_DETAIL"] as $i => $type) {
                            $messageDetail = new \MessageDetail();

                            $sPkMessageDetail = \ProcessMaker\Util\Common::generateUID();

                            $messageDetail->setMdUid($sPkMessageDetail);
                            $messageDetail->setMdType($type["md_type"]);
                            $messageDetail->setMdName($type["md_name"]);
                            $messageDetail->setMesUid($sPkMessage);
                            $messageDetail->save();
                        }
                    }

                    $message->save();
                    $cnn->commit();
                } else {

                    $msg = "";

                    foreach ($message->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . "\n" . $msg);
                }

            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }

            //Return
            $message = $this->getMessage($processUid, $sPkMessage);

            return $message;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Message
     *
     * @param string $processUid Unique id of Process
     * @param string $messageUid Unique id of Message
     * @param array  $arrayData   Data
     *
     * return array Return data of the Message updated
     */
    public function update($processUid, $messageUid, $arrayData)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            $this->throwExceptionFieldDefinition($arrayData);

            //Update
            $cnn = \Propel::getConnection("workflow");
            try {
                $message = \MessagePeer::retrieveByPK($messageUid);

                if (is_null($message)) {
                    throw new \Exception('mes_uid: '.$messageUid. ' '.\G::LoadTranslation("ID_DOES_NOT_EXIST"));
                } else {
                    $cnn->begin();
                    if (isset($arrayData["MES_NAME"])) {
                        $this->existsName($processUid, $arrayData["MES_NAME"]);
                        $message->setMesName($arrayData["MES_NAME"]);
                    }
                    if (isset($arrayData["MES_DETAIL"])) {

                        foreach ($arrayData["MES_DETAIL"] as $i => $type) {

                            $messageDetail = \MessageDetailPeer::retrieveByPK($type["md_uid"]);
                            if (is_null($messageDetail)) {
                                throw new \Exception('md_uid: '.$type["md_uid"]. ' '.\G::LoadTranslation("ID_DOES_NOT_EXIST"));
                            } else {
                                $messageDetail->setMdType($type["md_type"]);
                                $messageDetail->setMdName($type["md_name"]);

                                $messageDetail->save();
                            }
                        }
                    }
                    $message->save();
                    $cnn->commit();
                } 

            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Message
     *
     * @param string $processUid Unique id of Process
     * @param string $messageUid Unique id of Message
     *
     * return void
     */
    public function delete($processUid, $messageUid)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            $this->throwExceptionIfNotExistsMessage($messageUid);

            //Delete
            $criteria = new \Criteria("workflow");

            $criteria->add(\MessagePeer::MES_UID, $messageUid);

            \MessagePeer::doDelete($criteria);

            //Delete Detail
            $criteriaDetail = new \Criteria("workflow");

            $criteriaDetail->add(\MessageDetailPeer::MES_UID, $messageUid);

            \MessageDetailPeer::doDelete($criteriaDetail);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Message
     * @param string $processUid Unique id of Process
     * @param string $messageUid Unique id of Message
     *
     * return array Return an array with data of a Message
     */
    public function getMessage($processUid, $messageUid)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            $this->throwExceptionIfNotExistsMessage($messageUid);

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessagePeer::MES_UID);
            $criteria->addSelectColumn(\MessagePeer::MES_NAME);
            $criteria->addSelectColumn(\MessagePeer::PRJ_UID);

            $criteria->add(\MessagePeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\MessagePeer::MES_UID, $messageUid, \Criteria::EQUAL);

            $rsCriteria = \MessagePeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();
            $arrayMessage = array();

            while ($aRow = $rsCriteria->getRow()) {
                $oCriteriaU = new \Criteria('workflow');
                $oCriteriaU->setDistinct();
                $oCriteriaU->addSelectColumn(\MessageDetailPeer::MD_UID);
                $oCriteriaU->addSelectColumn(\MessageDetailPeer::MD_NAME);
                $oCriteriaU->addSelectColumn(\MessageDetailPeer::MD_TYPE);
                $oCriteriaU->add(\MessageDetailPeer::MES_UID, $aRow['MES_UID']);
                $oDatasetU = \MessageDetailPeer::doSelectRS($oCriteriaU);
                $oDatasetU->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $aType = array();
                while ($oDatasetU->next()) {
                    $aRowU = $oDatasetU->getRow();
                    $aType[] = array('md_uid' => $aRowU['MD_UID'],
                                     'md_name' => $aRowU['MD_NAME'],
                                     'md_type' => $aRowU['MD_TYPE']);

                }
                $arrayMessage = array('mes_uid' => $aRow['MES_UID'],
                                      'prj_uid' => $aRow['PRJ_UID'],
                                      'mes_name' => $aRow['MES_NAME'],
                                      'mes_detail' => $aType);
                $rsCriteria->next();
            }
            //Return
            return $arrayMessage;

        } catch (\Exception $e) {
             throw $e;
        }
    }

    /**
     * Get data of Message
     *
     * @param string $processUid Unique id of Message
     *
     * return array Return an array with data of a Message
     */
    public function getMessages($processUid)
    {
        try {
            //Verify data
            Validator::proUid($processUid, '$prj_uid');

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessagePeer::MES_UID);
            $criteria->addSelectColumn(\MessagePeer::MES_NAME);
            $criteria->addSelectColumn(\MessagePeer::PRJ_UID);

            $criteria->add(\MessagePeer::PRJ_UID, $processUid, \Criteria::EQUAL);

            $rsCriteria = \MessagePeer::doSelectRS($criteria);

            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();
            $arrayMessages = array();

            while ($aRow = $rsCriteria->getRow()) {
                $oCriteriaU = new \Criteria('workflow');
                $oCriteriaU->setDistinct();
                $oCriteriaU->addSelectColumn(\MessageDetailPeer::MD_UID);
                $oCriteriaU->addSelectColumn(\MessageDetailPeer::MD_NAME);
                $oCriteriaU->addSelectColumn(\MessageDetailPeer::MD_TYPE);
                $oCriteriaU->add(\MessageDetailPeer::MES_UID, $aRow['MES_UID']);
                $oDatasetU = \MessageDetailPeer::doSelectRS($oCriteriaU);
                $oDatasetU->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $aType = array();
                while ($oDatasetU->next()) {
                    $aRowU = $oDatasetU->getRow();
                    $aType[] = array('md_uid' => $aRowU['MD_UID'],
                                     'md_name' => $aRowU['MD_NAME'],
                                     'mes_type' => $aRowU['MD_TYPE']);

                }
                $arrayMessages[] = array('mes_uid' => $aRow['MES_UID'],
                                         'prj_uid' => $aRow['PRJ_UID'],
                                         'mes_name' => $aRow['MES_NAME'],
                                         'mes_detail' => $aType);
                $rsCriteria->next();
            }
            //Return
            return $arrayMessages;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify field definition
     *
     * @param array $aData Unique id of Message to exclude
     *
     */
    public function throwExceptionFieldDefinition($aData)
    {
        try {
            if (isset($aData["MES_NAME"])) {
                Validator::isString($aData['MES_NAME'], '$mes_name');
                Validator::isNotEmpty($aData['MES_NAME'], '$mes_name');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the name of a message
     *
     * @param string $processUid         Unique id of Process
     * @param string $messageName        Name
     *
     */
    public function existsName($processUid, $messageName)
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\MessagePeer::MES_UID);
            $criteria->add(\MessagePeer::MES_NAME, $messageName, \Criteria::EQUAL);
            $criteria->add(\MessagePeer::PRJ_UID, $processUid, \Criteria::EQUAL);
            $rsCriteria = \MessagePeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            if ($rsCriteria->getRow()) {
                throw new \Exception(\G::LoadTranslation("DYNAFIELD_ALREADY_EXIST"));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get required variables in the SQL
     *
     * @param string $sql SQL
     *
     * return array Return an array with required variables in the SQL
     */
    public function sqlGetRequiredVariables($sql)
    {
        try {
            $arrayVariableRequired = array();

            preg_match_all("/@[@%#\?\x24\=]([A-Za-z_]\w*)/", $sql, $arrayMatch, PREG_SET_ORDER);

            foreach ($arrayMatch as $value) {
                $arrayVariableRequired[] = $value[1];
            }

            return $arrayVariableRequired;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if some required variable in the SQL is missing in the variables
     *
     * @param string $variableName  Variable name
     * @param string $variableSql   SQL
     * @param array  $arrayVariable The variables
     *
     * return void Throw exception if some required variable in the SQL is missing in the variables
     */
    public function throwExceptionIfSomeRequiredVariableSqlIsMissingInVariables($variableName, $variableSql, array $arrayVariable)
    {
        try {
            $arrayResult = array_diff(array_unique($this->sqlGetRequiredVariables($variableSql)), array_keys($arrayVariable));

            if (count($arrayResult) > 0) {
                throw new \Exception(\G::LoadTranslation("ID_PROCESS_VARIABLE_REQUIRED_VARIABLES_FOR_QUERY", array($variableName, implode(", ", $arrayResult))));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exist the message in table MESSAGE
     *
     * @param string $messageUid           Unique id of variable
     *
     * return void Throw exception if does not exist the message in table MESSAGE
     */
    public function throwExceptionIfNotExistsMessage($messageUid)
    {
        try {
            $obj = \MessagePeer::retrieveByPK($messageUid);

            if (is_null($obj)) {
                throw new \Exception('mes_uid: '.$messageUid. ' '.\G::LoadTranslation("ID_DOES_NOT_EXIST"));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

