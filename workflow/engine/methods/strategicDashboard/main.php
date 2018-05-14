<?php
$RBAC->requirePermissions('PM_DASHBOARD');
$licensedFeatures = PMLicensedFeatures::getSingleton();

if (!$licensedFeatures->verifyfeature('r19Vm5DK1UrT09MenlLYjZxejlhNUZ1b1NhV0JHWjBsZEJ6dnpJa3dTeWVLVT0=')) {
    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
    G::header('location: ../login/login');
    die;
}

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'DASHBOARD+';

$G_PUBLISH = new Publisher();
$G_PUBLISH->AddContent('view', 'strategicDashboard/load');
$oHeadPublisher = headPublisher::getSingleton();
$oHeadPublisher->addScriptFile('/jscore/src/PM.js');
$oHeadPublisher->addScriptFile('/jscore/src/Sessions.js');
G::RenderPage('publish');
