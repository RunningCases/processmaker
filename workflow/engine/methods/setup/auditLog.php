<?php

use ProcessMaker\AuditLog\AuditLog;

global $RBAC;

if ($RBAC->userCanAccess("PM_SETUP") != 1) {
    G::SendTemporalMessage("ID_USER_HAVENT_RIGHTS_PAGE", "error", "labels");
    exit(0);
}

$auditLog = new AuditLog();
$auditLog->setUserLogged($_SESSION["USER_LOGGED"]);

$oHeadPublisher = headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript("setup/auditLog", true);
$oHeadPublisher->assign("CONFIG", $auditLog->getConfig());
$oHeadPublisher->assign("ACTION", $auditLog->getActions());
G::RenderPage("publish", "extJs");
