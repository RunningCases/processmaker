<?php
namespace ProcessMaker\BusinessModel;

class MessageEventTaskRelation
{
    private $arrayFieldDefinition = array(
        "MSGETR_UID" => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "messageEventTaskRelationUid"),

        "PRJ_UID"    => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "projectUid"),
        "EVN_UID"    => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "eventUid"),
        "TAS_UID"    => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "taskUid")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array();

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

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

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException(array $arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
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
     * Verify if exists the Message-Event-Task-Relation
     *
     * @param string $messageEventTaskRelationUid Unique id of Message-Event-Task-Relation
     *
     * return bool Return true if exists the Message-Event-Task-Relation, false otherwise
     */
    public function exists($messageEventTaskRelationUid)
    {
        try {
            $obj = \MessageEventTaskRelationPeer::retrieveByPK($messageEventTaskRelationUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exists the Message-Event-Task-Relation
     *
     * @param string $messageEventTaskRelationUid Unique id of Message-Event-Task-Relation
     * @param string $fieldNameForException       Field name for the exception
     *
     * return void Throw exception if does not exists the Message-Event-Task-Relation
     */
    public function throwExceptionIfNotExistsMessageEventTaskRelation($messageEventTaskRelationUid, $fieldNameForException)
    {
        try {
            if (!$this->exists($messageEventTaskRelationUid)) {
                throw new \Exception(\G::LoadTranslation("ID_MESSAGE_EVENT_TASK_RELATION_DOES_NOT_EXIST", array($fieldNameForException, $messageEventTaskRelationUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Validate the data if they are invalid (INSERT and UPDATE)
     *
     * @param string $messageEventTaskRelationUid Unique id of Message-Event-Task-Relation
     * @param string $projectUid                  Unique id of Project
     * @param array  $arrayData                   Data
     *
     * return void Throw exception if data has an invalid value
     */
    public function throwExceptionIfDataIsInvalid($messageEventTaskRelationUid, $projectUid, array $arrayData)
    {
        try {
            //Set variables
            $arrayMessageEventTaskRelationData = ($messageEventTaskRelationUid == "")? array() : $this->getMessageEventTaskRelation($messageEventTaskRelationUid, true);
            $flagInsert = ($messageEventTaskRelationUid == "")? true : false;

            $arrayFinalData = array_merge($arrayMessageEventTaskRelationData, $arrayData);

            //Verify data - Field definition
            $process = new \ProcessMaker\BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, $flagInsert);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create Message-Event-Task-Relation for a Project
     *
     * @param string $projectUid Unique id of Project
     * @param array  $arrayData  Data
     *
     * return array Return data of the new Message-Event-Task-Relation created
     */
    public function create($projectUid, array $arrayData)
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["MSGETR_UID"]);
            unset($arrayData["PRJ_UID"]);

            //Verify data
            $process->throwExceptionIfNotExistsProcess($projectUid, $this->arrayFieldNameForException["projectUid"]);

            $this->throwExceptionIfDataIsInvalid("", $projectUid, $arrayData);

            //Create
            $cnn = \Propel::getConnection("workflow");

            try {
                $messageEventTaskRelation = new \MessageEventTaskRelation();

                $messageEventTaskRelationUid = \ProcessMaker\Util\Common::generateUID();

                $messageEventTaskRelation->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);

                $messageEventTaskRelation->setMsgetrUid($messageEventTaskRelationUid);
                $messageEventTaskRelation->setPrjUid($projectUid);

                if ($messageEventTaskRelation->validate()) {
                    $cnn->begin();

                    $result = $messageEventTaskRelation->save();

                    $cnn->commit();

                    //Return
                    return $this->getMessageEventTaskRelation($messageEventTaskRelationUid);
                } else {
                    $msg = "";

                    foreach ($messageEventTaskRelation->getValidationFailures() as $validationFailure) {
                        $msg = $msg . (($msg != "")? "\n" : "") . $validationFailure->getMessage();
                    }

                    throw new \Exception(\G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED") . (($msg != "")? "\n" . $msg : ""));
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
     * Delete Message-Event-Task-Relation
     *
     * @param array $arrayCondition Conditions
     *
     * return void
     */
    public function deleteWhere(array $arrayCondition)
    {
        try {
            //Delete
            $criteria = new \Criteria("workflow");

            foreach ($arrayCondition as $key => $value) {
                $criteria->add($key, $value, \Criteria::EQUAL);
            }

            $result = \MessageEventTaskRelationPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Message-Event-Task-Relation
     *
     * return object
     */
    public function getMessageEventTaskRelationCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\MessageEventTaskRelationPeer::MSGETR_UID);
            $criteria->addSelectColumn(\MessageEventTaskRelationPeer::PRJ_UID);
            $criteria->addSelectColumn(\MessageEventTaskRelationPeer::EVN_UID);
            $criteria->addSelectColumn(\MessageEventTaskRelationPeer::TAS_UID);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Message-Event-Task-Relation from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Message-Event-Task-Relation
     */
    public function getMessageEventTaskRelationDataFromRecord(array $record)
    {
        try {
            return array(
                $this->getFieldNameByFormatFieldName("MSGETR_UID") => $record["MSGETR_UID"],
                $this->getFieldNameByFormatFieldName("EVN_UID")    => $record["EVN_UID"],
                $this->getFieldNameByFormatFieldName("TAS_UID")    => $record["TAS_UID"]
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Message-Event-Task-Relation
     *
     * @param string $messageEventTaskRelationUid Unique id of Message-Event-Task-Relation
     * @param bool   $flagGetRecord               Value that set the getting
     *
     * return array Return an array with data of a Message-Event-Task-Relation
     */
    public function getMessageEventTaskRelation($messageEventTaskRelationUid, $flagGetRecord = false)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsMessageEventTaskRelation($messageEventTaskRelationUid, $this->arrayFieldNameForException["messageEventTaskRelationUid"]);

            //Get data
            $criteria = $this->getMessageEventTaskRelationCriteria();

            $criteria->add(\MessageEventTaskRelationPeer::MSGETR_UID, $messageEventTaskRelationUid, \Criteria::EQUAL);

            $rsCriteria = \MessageEventTaskRelationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            //Return
            return (!$flagGetRecord)? $this->getMessageEventTaskRelationDataFromRecord($row) : $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Message-Event-Task-Relation
     *
     * @param array $arrayCondition Conditions
     * @param bool  $flagGetRecord  Value that set the getting
     *
     * return array Return an array with data of a Message-Event-Task-Relation
     */
    public function getMessageEventTaskRelationWhere(array $arrayCondition, $flagGetRecord = false)
    {
        try {
            //Get data
            $criteria = $this->getMessageEventTaskRelationCriteria();

            foreach ($arrayCondition as $key => $value) {
                $criteria->add($key, $value, \Criteria::EQUAL);
            }

            $rsCriteria = \MessageEventTaskRelationPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                //Return
                return (!$flagGetRecord)? $this->getMessageEventTaskRelationDataFromRecord($row) : $row;
            } else {
                //Return
                return null;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

