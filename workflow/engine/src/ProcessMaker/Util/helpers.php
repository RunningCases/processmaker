<?php
/**
 * We will send a case note in the actions by email
 * @param object $httpData
 * @return void
*/
function postNote($httpData)
{
    $appUid = (isset($httpData->appUid)) ? $httpData->appUid : '';
    $usrUid = (isset($httpData->usrUid)) ? $httpData->usrUid : '';
    $appNotes = new AppNotes();
    $noteContent = addslashes($httpData->noteText);
    $result = $appNotes->postNewNote($appUid, $usrUid, $noteContent, false);

    //send the response to client
    @ini_set('implicit_flush', 1);
    ob_start();
    @ob_flush();
    @flush();
    @ob_end_flush();
    ob_implicit_flush(1);

    //send notification in background
    $noteRecipientsList = array();
    $oCase = new Cases();
    $p = $oCase->getUsersParticipatedInCase($appUid);
    foreach ($p['array'] as $key => $userParticipated) {
        $noteRecipientsList[] = $key;
    }

    $noteRecipients = implode(",", $noteRecipientsList);
    $appNotes->sendNoteNotification($appUid, $usrUid, $noteContent, $noteRecipients);
}

/**
 * We will get to the abeRequest data from actions by email
 * @param string $AbeRequestsUid
 * @return array $abeRequests
 */
function loadAbeRequest($AbeRequestsUid)
{
    $criteria = new Criteria();
    $criteria->add(AbeRequestsPeer::ABE_REQ_UID, $AbeRequestsUid);
    $resultRequests = AbeRequestsPeer::doSelectRS($criteria);
    $resultRequests->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $resultRequests->next();
    $abeRequests = $resultRequests->getRow();

    return $abeRequests;
}

/**
 * We will get the AbeConfiguration by actions by email
 * @param string $AbeConfigurationUid
 * @return array $abeConfiguration
 */
function loadAbeConfiguration($AbeConfigurationUid)
{
    $criteria = new Criteria();
    $criteria->add(AbeConfigurationPeer::ABE_UID, $AbeConfigurationUid);
    $result = AbeConfigurationPeer::doSelectRS($criteria);
    $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $result->next();
    $abeConfiguration = $result->getRow();

    return $abeConfiguration;
}

/**
 * We will update the request by actions by email
 * @param array $data
 * @return void
 * @throws Exception
 */
function uploadAbeRequest($data)
{
    try {
        $abeRequestsInstance = new AbeRequests();
        $abeRequestsInstance->createOrUpdate($data);
    } catch (Exception $error) {
        throw $error;
    }
}

