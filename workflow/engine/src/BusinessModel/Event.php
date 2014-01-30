<?php
namespace BusinessModel;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Event
{
    /**
     * Get list for Events
     * @var string $sProcessUID. Uid for Process
     * @var string $filter.
     * @var string $sEventUID. Uid for Process
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getEvents($sProcessUID, $filter = '', $sEventUID = '')
    {
        $oProcess = new \Process();
        if (!($oProcess->processExists($sProcessUID))) {
            throw (new \Exception( 'This process doesn\'t exist!' ));
        }

        $sDelimiter = \DBAdapter::getStringDelimiter();
        $oCriteria  = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\EventPeer::EVN_UID);
        $oCriteria->addSelectColumn(\EventPeer::EVN_ACTION);
        $oCriteria->addSelectColumn(\EventPeer::EVN_STATUS);
        $oCriteria->addSelectColumn(\EventPeer::EVN_WHEN_OCCURS);
        $oCriteria->addSelectColumn(\EventPeer::EVN_RELATED_TO);

        $oCriteria->addAsColumn('EVN_DESCRIPTION', \ContentPeer::CON_VALUE);
        $aConditions = array();
        $aConditions[] = array(\EventPeer::EVN_UID, \ContentPeer::CON_ID );
        $aConditions[] = array(\ContentPeer::CON_CATEGORY, $sDelimiter . 'EVN_DESCRIPTION' . $sDelimiter );
        $aConditions[] = array(\ContentPeer::CON_LANG, $sDelimiter . SYS_LANG . $sDelimiter );
        $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
        $oCriteria->add(\EventPeer::PRO_UID, $sProcessUID);
        if ($sEventUID != '') {
            $oCriteria->add(\EventPeer::EVN_UID, $sEventUID);
        }

        switch ($filter) {
            case 'message':
                $oCriteria->add(\EventPeer::EVN_ACTION, "SEND_MESSAGE");
                break;
            case 'conditional':
                $oCriteria->add(\EventPeer::EVN_ACTION, "EXECUTE_CONDITIONAL_TRIGGER");
                break;
            case 'multiple':
                $oCriteria->add(\EventPeer::EVN_ACTION, "EXECUTE_TRIGGER");
                break;
        }
        $eventsArray = array();

        $oDataset = \EventPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $oEvent = new \Event();
            $aFields = $oEvent->load( $aRow['EVN_UID'] );
            $aRow = array_merge($aRow, $aFields);
            $eventsArray[] = array_change_key_case($aRow, CASE_LOWER);
            $oDataset->next();
        }

        if ($sEventUID != '' && empty($eventsArray)) {
            throw (new \Exception( 'This row doesn\'t exist!' ));
        } elseif ($sEventUID != '' && !empty($eventsArray)) {
            return current($eventsArray);
        }
        return $eventsArray;
    }

    /**
     * Save Event Post Put
     *
     * @param string $eventUid
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function saveEvents($sProcessUID, $dataEvent, $create = false)
    {
        if ( ($sProcessUID == '') || (count($dataEvent) == 0) ) {
            return false;
        }
        $dataEvent = array_change_key_case($dataEvent, CASE_UPPER);

        $this->validateProcess($sProcessUID);

        if ($dataEvent['EVN_RELATED_TO'] == 'SINGLE') {
            if (empty($dataEvent['TAS_UID'])) {
                throw (new \Exception('The field "tas_uid" is required!'));
            }
            $this->validateTask($dataEvent['TAS_UID']);
        } else {
            if (empty($dataEvent['EVN_TAS_UID_FROM'])) {
                throw (new \Exception('The field "evn_tas_uid_from" is required!'));
            }
            $this->validateTask($dataEvent['EVN_TAS_UID_FROM']);

            if (empty($dataEvent['EVN_TAS_UID_TO'])) {
                throw (new \Exception('The field "evn_tas_uid_to" is required!'));
            }
            $this->validateTask($dataEvent['EVN_TAS_UID_TO']);
        }

        $this->validateTrigger($dataEvent['TRI_UID']);
        if ( $create && (isset($dataEvent['ENV_UID'])) ) {
            unset($dataEvent['ENV_UID']);
        }

        $dataEvent['PRO_UID'] = $sProcessUID;
        $oEvent = new \Event();

        if ($create) {
            $uidNewEvent = $oEvent->create( $dataEvent );
        } else {
            $uidNewEvent = $oEvent->update( $dataEvent );
        }

        $dataEvent = $this->getEvents($sProcessUID, '', $uidNewEvent);
        $dataEvent = array_change_key_case($dataEvent, CASE_LOWER);
        return $dataEvent;
    }

    /**
     * Delete Event
     *
     * @param string $eventUid
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteEvent($eventUid)
    {
        try {
            $oEvent = new \Event();
            $oEvent->remove( $eventUid );
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function validateProcess($proUid) {
        $proUid = trim($proUid);
        if ($proUid == '') {
            throw (new \Exception('This process doesn\'t exist!'));
        }

        $oProcess = new \Process();
        if (!($oProcess->processExists($proUid))) {
            throw (new \Exception('This process doesn\'t exist!'));
        }

        return $proUid;
    }

    public function validateTask($taskUid) {
        $taskUid = trim($taskUid);
        if ($taskUid == '') {
            throw (new \Exception('This task doesn\'t exist!'));
        }

        $oTask = new \Task();
        if (!($oTask->taskExists($taskUid))) {
            throw (new \Exception('This task doesn\'t exist!'));
        }

        return $taskUid;
    }

    public function validateTrigger($triUid) {
        $triUid = trim($triUid);
        if ($triUid == '') {
            throw (new \Exception('This trigger doesn\'t exist!'));
        }

        $oTriggers = new \Triggers();
        if (!($oTriggers->TriggerExists($triUid))) {
            throw (new \Exception('This trigger doesn\'t exist!'));
        }

        return $triUid;
    }
}

