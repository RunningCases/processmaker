<?php
/**
 * This service is to start PM with the anonymous user.
 */

/* @var $RBAC RBAC */
global $RBAC;
G::LoadClass('pmFunctions');
try {
    if (empty($_REQUEST['we_uid'])) {
        throw new \Exception('Missing required field "we_uid"');
    }

    $weUid = $_REQUEST['we_uid'];

    $webEntry = \WebEntryPeer::retrieveByPK($weUid);
    if (empty($webEntry)) {
        throw new \Exception('Undefined WebEntry');
    }

    $userUid = $webEntry->getUsrUid();
    $userInfo = PMFInformationUser($userUid);
    if (empty($userInfo)) {
        throw new \Exception('WebEntry User not found');
    }

    initUserSession($userUid, $userInfo['username']);

    $result = [
        'user_logged'  => $userUid,
        'userName' => $userInfo['username'],
        'firstName' => $userInfo['firstname'],
        'lastName' => $userInfo['lastname'],
        'mail' => $userInfo['mail'],
        'image' => '../users/users_ViewPhoto?t='.microtime(true),
    ];
} catch (\Exception $e) {
    $result = [
        'error' => $e->getMessage(),
    ];
    http_response_code(500);
}
echo G::json_encode($result);
