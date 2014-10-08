<?php

global $RBAC;
$RBAC->requirePermissions( 'PM_SETUP' );

$oHeadPublisher = & headPublisher::getSingleton();
G::LoadClass( 'serverConfiguration' );

$oServerConf = & serverConf::getSingleton();

$sflag = $oServerConf->getAuditLogProperty( 'AL_OPTION', SYS_SYS );
$auditLogChecked = $sflag == 1 ? true : false;

$oHeadPublisher->addExtJsScript( 'setup/auditLogConfig', true ); //adding a javascript file .js
$oHeadPublisher->assign( 'auditLogChecked', $auditLogChecked );
G::RenderPage( 'publish', 'extJs' );