<?php
class EmailServer extends BaseEmailServer
{
    /**
     * Get all columns by EMAIL_SERVER
     *
     * @return object criteria 
     */
    public function getSelAllColumns(){
        $criteria = new Criteria("workflow");
        $criteria->addSelectColumn( EmailServerPeer::MESS_UID);
        $criteria->addSelectColumn( EmailServerPeer::MESS_ENGINE);
        $criteria->addSelectColumn( EmailServerPeer::MESS_SERVER);
        $criteria->addSelectColumn( EmailServerPeer::MESS_PORT);
        $criteria->addSelectColumn( EmailServerPeer::MESS_RAUTH);
        $criteria->addSelectColumn( EmailServerPeer::MESS_ACCOUNT);
        $criteria->addSelectColumn( EmailServerPeer::MESS_PASSWORD);
        $criteria->addSelectColumn( EmailServerPeer::MESS_FROM_MAIL);
        $criteria->addSelectColumn( EmailServerPeer::MESS_FROM_NAME);
        $criteria->addSelectColumn( EmailServerPeer::SMTPSECURE);
        $criteria->addSelectColumn( EmailServerPeer::MESS_TRY_SEND_INMEDIATLY);
        $criteria->addSelectColumn( EmailServerPeer::MAIL_TO);
        $criteria->addSelectColumn( EmailServerPeer::MESS_DEFAULT);
        return $criteria;
    }
}

