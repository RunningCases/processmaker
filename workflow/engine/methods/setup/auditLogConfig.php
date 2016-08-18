<?php

global $RBAC;
$RBAC->requirePermissions( 'PM_SETUP' );

$conf = new Configurations();
$sflag = $conf->getConfiguration('AUDIT_LOG', 'log');

$auditLogChecked = $sflag == 'true' ? true : false;

$oHeadPublisher->addExtJsScript( 'setup/auditLogConfig', true ); //adding a javascript file .js
$oHeadPublisher->assign( 'auditLogChecked', $auditLogChecked );
G::RenderPage( 'publish', 'extJs' );