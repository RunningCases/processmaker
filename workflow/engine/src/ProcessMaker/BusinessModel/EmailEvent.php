<?php
namespace ProcessMaker\BusinessModel;

class EmailEvent
{
    /*private $arrayFieldDefinition = array(
        "EMAIL_EVENT_UID"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "emailEventUid"),
        "PRJ_UID"           => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "projectUid"),
        "ACT_UID"           => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(), "fieldNameAux" => "eventUid"),
        "EMAIL_EVENT_FROM"          => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "messageTypeUid"),
        "EMAIL_EVENT_TO"     => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(), "fieldNameAux" => "EmailEventUserUid"),
        "EMAIL_EVENT_SUBJECT"   => array("type" => "array",  "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "EmailEventVariables"),
        "EMAIL_EVENT_BODY" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(), "fieldNameAux" => "EmailEventCorrelation")
    );
    */
    
    /**
     * Get the email accounts of the current workspace
     *     
     * return array
     */
    public function getEmailEventAccounts()
    {
        try {
            $criteria = new \Criteria("workflow");
            $criteria->clearSelectColumns();
            $criteria->addSelectColumn(\UsersPeer::USR_UID);
            $criteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $criteria->add(\UsersPeer::USR_STATUS, "ACTIVE");
            $result = \UsersPeer::doSelectRS($criteria);
            $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $result->next();
            $accountsArray = array();
            while ($aRow = $result->getRow()) {
                if (($aRow['USR_EMAIL'] != null) || ($aRow['USR_EMAIL'] != "")) {
                    $accountsArray[] = array_change_key_case($aRow, CASE_LOWER);
                } 
                $result->next();
            }
            return $accountsArray;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get the Email-Event data
     * @var string $act_uid. uid for activity  
     * return array
     */
    public function getEmailEventData($act_uid)
    {
        try {
            //Get data
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::ACT_UID, $act_uid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();
            if(is_array($row)) {
                $row = array_change_key_case($row, CASE_LOWER);
            }
            return $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Save Data for Email-Event
     * @var string $prj_uid. Uid for Process
     * @var string $arrayData. Data for Trigger
     *     
     * return array
     */
    public function save($prj_uid = '', $arrayData = array())
    {
        try {
            //Verify data
            $process = new \ProcessMaker\BusinessModel\Process();
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $process->throwExceptionIfNotExistsProcess($prj_uid, "projectUid");

            //Create
            $db = \Propel::getConnection("workflow");

            try {
                $emailEvent = new \EmailEvent();
                $emailEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);
                
                $emailEventUid = \ProcessMaker\Util\Common::generateUID();

                $emailEvent->setEmailEventUid($emailEventUid);
                $emailEvent->setProUid($prj_uid);

                $db->begin();
                $result = $emailEvent->save();
                $db->commit();
                
                return $this->getEmailEvent($emailEventUid);
            } catch (\Exception $e) {
                $db->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }    
    }
    
    /**
     * Update Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     * @param array  $arrayData Data
     *
     * return array Return data of the Email-Event updated
     */
    public function update($emailEventUid, array $arrayData)
    {
        try {
            //Verify data
            $validator = new \ProcessMaker\BusinessModel\Validator();

            $validator->throwExceptionIfDataIsNotArray($arrayData, "\$arrayData");
            $validator->throwExceptionIfDataIsEmpty($arrayData, "\$arrayData");

            //Set data
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $arrayDataBackup = $arrayData;

            //Set variables
            $arrayEmailEventData = $this->getEmailEvent($emailEventUid);

            //Verify data
            $this->verifyIfEmailEventExists($emailEventUid); 

            //Update
            $db = \Propel::getConnection("workflow");

            try {
                $emailEvent = \EmailEventPeer::retrieveByPK($emailEventUid);
                $emailEvent->fromArray($arrayData, \BasePeer::TYPE_FIELDNAME);
         
                $db->begin();
                $result = $emailEvent->save();
                $db->commit();

                $arrayData = $arrayDataBackup;
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
                return $arrayData;
                
            } catch (\Exception $e) {
                $cnn->rollback();

                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    } 
    
    /**
     * Delete Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     *
     * return void
     */
    public function delete($emailEventUid)
    {
        try {
            //Verify data
            $this->verifyIfEmailEventExists($emailEventUid);

            //Delete
            $criteria = new \Criteria("workflow");
            $criteria->add(\EmailEventPeer::EMAIL_EVENT_UID, $emailEventUid, \Criteria::EQUAL);
            $result = \EmailEventPeer::doDelete($criteria);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get data of a Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     * @param bool   $flagGetRecord             Value that set the getting
     *
     * return array Return an array with data of a Email-Event
     */
    public function getEmailEvent($emailEventUid)
    {
        try {
            //Verify data
           $this->verifyIfEmailEventExists($emailEventUid);

            //Get data
            $criteria = $this->getEmailEventCriteria();
            $criteria->add(\EmailEventPeer::EMAIL_EVENT_UID, $emailEventUid, \Criteria::EQUAL);
            $rsCriteria = \EmailEventPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $rsCriteria->next();
            $row = $rsCriteria->getRow();

            //Return
            return $row;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Verify if exists the Email-Event
     *
     * @param string $emailEventUid Unique id of Email-Event
     *
     * return bool Return true if exists the Email-Event, false otherwise
     */
    public function exists($emailEventUid)
    {
        try {
            $obj = \EmailEventPeer::retrieveByPK($emailEventUid);

            return (!is_null($obj))? true : false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Get criteria for Email-Event
     *
     * return object
     */
    public function getEmailEventCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_UID);
            $criteria->addSelectColumn(\EmailEventPeer::PRO_UID);
            $criteria->addSelectColumn(\EmailEventPeer::ACT_UID);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_FROM);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_TO);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_SUBJECT);
            $criteria->addSelectColumn(\EmailEventPeer::EMAIL_EVENT_BODY);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function verifyIfEmailEventExists($emailEventUid)
    {
        if (!$this->exists($emailEventUid)) {
            throw new \Exception(\G::LoadTranslation("ID_EMAIL_EVENT_DEFINITION_DOES_NOT_EXIST", array("Email Event Uid", $emailEventUid)));
        }
    }

}

