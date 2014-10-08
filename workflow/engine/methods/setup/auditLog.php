<?php
global $RBAC;

if ($RBAC->userCanAccess("PM_SETUP") != 1) {
    G::SendTemporalMessage("ID_USER_HAVENT_RIGHTS_PAGE", "error", "labels");
    exit(0);
}

$c = new Configurations();
$configPage = $c->getConfiguration( "auditLogList", "pageSize", null, $_SESSION["USER_LOGGED"] );

$config = array ();
$config["pageSize"] = (isset( $configPage["pageSize"] )) ? $configPage["pageSize"] : 20;

$oHeadPublisher = &headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( "setup/auditLog", true );
$oHeadPublisher->assign( "CONFIG", $config );
G::RenderPage( "publish", "extJs" );
