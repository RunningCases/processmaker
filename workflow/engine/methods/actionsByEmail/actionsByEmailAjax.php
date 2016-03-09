<?php
// General Validations
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}

if (!isset($_REQUEST['limit'])) {
    $_REQUEST['limit'] = '';
}

if (!isset($_REQUEST['start'])) {
    $_REQUEST['start'] = '';
}

//Initialize response object
$response = new stdclass();
$response->status = 'OK';

//Main switch
try {
    $actionsByEmail = new \ProcessMaker\BusinessModel\ActionsByEmail();

    switch ($_REQUEST['action']) {
        case 'editTemplate':
            $actionsByEmail->editTemplate($_REQUEST);
            die();
            break;
        case 'updateTemplate':
            $actionsByEmail->updateTemplate($_REQUEST);
            break;
        case 'loadFields':
            $actionsByEmail->loadFields($_REQUEST);
            break;
        case 'saveConfiguration':
            $actionsByEmail->saveConfiguration2($_REQUEST);
            break;
        case 'loadActionByEmail':
            $actionsByEmail->loadActionByEmail($_REQUEST);
            break;
        case 'forwardMail':
            $actionsByEmail->forwardMail($_REQUEST);
            die;
            break;
        case 'viewForm':
            $actionsByEmail->viewForm($_REQUEST);
            die;
            break;
    }
} catch (Exception $error) {
    $response = new stdclass();
    $response->status = 'ERROR';
    $response->message = $error->getMessage();
}

header('Content-Type: application/json;');

die(G::json_encode($response));
