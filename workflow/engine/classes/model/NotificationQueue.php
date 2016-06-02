<?php

require_once 'classes/model/om/BaseNotificationQueue.php';


/**
 * Skeleton subclass for representing a row from the 'NOTIFICATION_QUEUE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class NotificationQueue extends BaseNotificationQueue
{
    public function create(array $arrayData)
    {
        $cnn = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);
        try {
            $this->setNotUid(G::generateUniqueID());
            $this->setDevType($arrayData['DEV_TYPE']);
            $this->setDevUid($arrayData['DEV_UID']);
            $this->setNotMsg($arrayData['NOT_MSG']);
            $this->setNotData($arrayData['NOT_DATA']);
            $this->setNotStatus($arrayData['NOT_STATUS']);
            $this->setNotSendDate('now');

            if ($this->validate()) {
                $cnn->begin();
                $result = $this->save();
                $cnn->commit();
            } else {
                throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED"));
            }
        } catch (Exception $e) {
            $cnn->rollback();
            throw $e;
        }
        return $result;
    }

    public function loadStatus($status)
    {
        try {
            $criteria = new Criteria();
            $criteria->clearSelectColumns();
            $criteria->add(NotificationQueuePeer::NOT_STATUS, $status, Criteria::EQUAL);

            $rs = NotificationQueuePeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $notifications = array();
            while ($rs->next()) {
                $row = $rs->getRow();
                $notifications[] = $row;
            }
        } catch (Exception $error) {
            throw $error;
        }
        return $notifications;
    }

    public function loadStatusDeviceType($status, $devType)
    {
        try {
            $criteria = new Criteria();
            $criteria->clearSelectColumns();
            $criteria->add(NotificationQueuePeer::NOT_STATUS, $status, Criteria::EQUAL);
            $criteria->add(NotificationQueuePeer::DEV_TYPE, $devType, Criteria::EQUAL);

            $rs = NotificationQueuePeer::doSelectRS($criteria);
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $notifications = array();
            while ($rs->next()) {
                $row = $rs->getRow();
                $notifications[] = $row;
            }
        } catch (Exception $error) {
            throw $error;
        }
        return $notifications;
    }
    
    public function changeStatusSent($not_uid)
    {
        $cnn = Propel::getConnection(NotificationDevicePeer::DATABASE_NAME);
        $rs = NotificationQueuePeer::retrieveByPK($not_uid);
        try {
            $arrayData['NOT_STATUS'] = "sent";
            $arrayData['NOT_SEND_DATE'] = date('Y-m-d H:i:s');
            $rs->fromArray($arrayData, BasePeer::TYPE_FIELDNAME);
            if ($this->validate()) {
                $cnn->begin();
                $result = $rs->save();
                $cnn->commit();
            } else {
                throw new Exception(G::LoadTranslation("ID_RECORD_CANNOT_BE_CREATED"));
            }
        } catch (Exception $e) {
            $cnn->rollback();
            throw $e;
        }
        return $result;
    }
} // NotificationQueue
