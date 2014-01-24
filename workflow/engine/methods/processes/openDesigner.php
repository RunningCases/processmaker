<?php
if (! array_key_exists('pro_uid', $_REQUEST)) {
    die('Bad Request: The param "pro_uid" is required!');
}

$proUid = $_GET['pro_uid'];
$bpmnProject = BpmnProjectPeer::retrieveByPK($proUid);


if (is_object($bpmnProject)) {
    $url = '../designer?pro_uid=' . $proUid;
} else {
    $url = 'processes_Map?PRO_UID=' . $proUid;
}

G::header("location: $url");
