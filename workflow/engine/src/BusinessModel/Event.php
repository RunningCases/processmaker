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
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return boolean
     */
    public function getEvents($sProcessUID, $filter = '', $sEventUID = '')
    {
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
        } else if ($sEventUID != '' && !empty($eventsArray)) {
            return current($eventsArray);
        }
        return $eventsArray;
    }

    /**
     * Delete Event
     *
     * @param string $eventUid
     *
     * return void
     *
     * @access public
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

    


}

