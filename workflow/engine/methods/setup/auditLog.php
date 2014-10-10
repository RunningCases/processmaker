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

$arrayAction = array (array ("ALL", G::LoadTranslation( "ID_ALL" )),
					  array ("CreateUser", G::LoadTranslation( "ID_CREATE_USER" )),
					  array ("UpdateUser", G::LoadTranslation( "ID_UPDATE_USER" )),
					  array ("DeleteUser", G::LoadTranslation( "ID_DELETE_USER" )),
					  array ("EnableUser", G::LoadTranslation( "ID_ENABLE_USER" )),
					  array ("DisableUser", G::LoadTranslation( "ID_DISABLE_USER" )),
					  array ("AssignAuthenticationSource", G::LoadTranslation( "ID_ASSIGN_AUTHENTICATION_SOURCE" )),
					  array ("AssignUsersToGroup", G::LoadTranslation( "ID_ASSIGN_USER_TO_GROUP" )),
					  array ("CreateAuthSource", G::LoadTranslation( "ID_CREATE_AUTH_SOURCE" )),
					  array ("UpdateAuthSource", G::LoadTranslation( "ID_UPDATE_AUTH_SOURCE" )),
					  array ("DeleteAuthSource", G::LoadTranslation( "ID_DELETE_AUTH_SOURCE" )),
					  array ("CreateRole", G::LoadTranslation( "ID_CREATE_ROLE" )),
					  array ("UpdateRole", G::LoadTranslation( "ID_UPDATE_ROLE" )),
					  array ("DeleteRole", G::LoadTranslation( "ID_DELETE_ROLE" )),
					  array ("AssignUsersToRole", G::LoadTranslation( "ID_ASSIGN_USER_TO_ROLE" )),
					  array ("DeleteUsersToRole", G::LoadTranslation( "ID_DELETE_USER_TO_ROLE" )),
					  array ("AddPermissionToRole", G::LoadTranslation( "ID_ADD_PERMISSION_TO_ROLE" )),
					  array ("DeletePermissionToRole", G::LoadTranslation( "ID_DELETE_PERMISSION_TO_ROLE" )),
					  array ("CreateSkin", G::LoadTranslation( "ID_CREATE_SKIN" )),
					  array ("ImportSkin", G::LoadTranslation( "ID_IMPORT_SKIN" )),
					  array ("ExportSkin", G::LoadTranslation( "ID_EXPORT_SKIN" )),
					  array ("DeleteSkin", G::LoadTranslation( "ID_DELETE_SKIN" )),					  
					  array ("CreateGroup", G::LoadTranslation( "ID_CREATE_GROUP" )),
					  array ("UpdateGroup", G::LoadTranslation( "ID_UPDATE_GROUP" )),
					  array ("DeleteGroup", G::LoadTranslation( "ID_DELETE_GROUP" )),					  
					  array ("CreateCategory", G::LoadTranslation( "ID_CREATE_CATEGORY" )),
					  array ("UpdateCategory", G::LoadTranslation( "ID_UPDATE_CATEGORY" )),
					  array ("DeleteCategory", G::LoadTranslation( "ID_DELETE_CATEGORY" )),
					  array ("BuildCache", G::LoadTranslation( "ID_BUILD_CACHE" )),
					  array ("ClearCache", G::LoadTranslation( "ID_CLEAR_CACHE" )),
					  array ("ClearCron", G::LoadTranslation( "ID_CLEAR_CRON" )),
					  array ("UpdateEnvironmentSettings", G::LoadTranslation( "ID_UPDATE_ENVIRONMENTS_SETTINGS" )),
					  array ("UpdateLoginSettings", G::LoadTranslation( "ID_UPDATE_LOGIN_SETTINGS" )),
					  array ("EnableHeartBeat", G::LoadTranslation( "ID_ENABLE_HEART_BEAT" )),
					  array ("DisableHeartBeat", G::LoadTranslation( "ID_DISABLE_HEART_BEAT" )),
					  array ("CreatePmtable", G::LoadTranslation( "ID_CREATE_PMTABLE" )),
					  array ("UpdatePmtable", G::LoadTranslation( "ID_UPDATE_PMTABLE" )),
					  array ("DeletePmtable", G::LoadTranslation( "ID_DELETE_PMTABLE" )),
					  array ("AddDataPmtable", G::LoadTranslation( "ID_ADD_DATA_PMTABLE" )),
					  array ("UpdateDataPmtable", G::LoadTranslation( "ID_UPDATE_DATA_PMTABLE" )),
					  array ("DeleteDataPmtable", G::LoadTranslation( "ID_DELETE_DATA_PMTABLE" )),
					  array ("ImportTable", G::LoadTranslation( "ID_IMPORT_TABLE" )),
					  array ("ExportTable", G::LoadTranslation( "ID_EXPORT_TABLE" )),
					  array ("CreateCalendar", G::LoadTranslation( "ID_CREATE_CALENDAR" )),
					  array ("UpdateCalendar", G::LoadTranslation( "ID_UPDATE_CALENDAR" )),
					  array ("DeleteCalendar", G::LoadTranslation( "ID_DELETE_CALENDAR" )),
					  array ("CreateDashletInstance", G::LoadTranslation( "ID_CREATE_DASHLET_INSTANCE" )),
					  array ("UpdateDashletInstance", G::LoadTranslation( "ID_UPDATE_DASHLET_INSTANCE" )),
					  array ("DeleteDashletInstance", G::LoadTranslation( "ID_DELETE_DASHLET_INSTANCE" )),
					  array ("CreateDepartament", G::LoadTranslation( "ID_CREATE_DEPARTAMENT" )),
					  array ("CreateSubDepartament", G::LoadTranslation( "ID_CREATE_SUB_DEPARTAMENT" )),
					  array ("UpdateDepartament", G::LoadTranslation( "ID_UPDATE_DEPARTAMENT" )),
					  array ("UpdateSubDepartament", G::LoadTranslation( "ID_UPDATE_SUB_DEPARTAMENT" )),
					  array ("DeleteDepartament", G::LoadTranslation( "ID_DELETE_DEPARTAMENT" )),
					  array ("AssignManagerToDepartament", G::LoadTranslation( "ID_ASSIGN_MANAGER_TO_DEPARTAMENT" )),
					  array ("AssignUsersToDepartament", G::LoadTranslation( "ID_ASSIGN_USER_TO_DEPARTAMENT" )),
					  array ("RemoveUsersFromDepartament", G::LoadTranslation( "ID_REMOVE_USERS_FROM_DEPARTAMENT" )),
					  array ("AssignUsersToGroup", G::LoadTranslation( "ID_ASSIGN_USER_TO_GROUP" )),
					  array ("UploadLanguage", G::LoadTranslation( "ID_UPLOAD_LANGUAGE" )),
					  array ("ExportLanguage", G::LoadTranslation( "ID_EXPORT_LANGUAGE" )),
					  array ("DeleteLanguage", G::LoadTranslation( "ID_DELETE_LAGUAGE" )),
					  array ("UploadSystemSettings", G::LoadTranslation( "ID_UPLOAD_SYSTEM_SETTINGS" )),
					  array ("UpdateEmailSettings", G::LoadTranslation( "ID_UPDATE_EMAIL_SETTINGS" )),
					  array ("CreateEmailSettings", G::LoadTranslation( "ID_CREATE_EMAIL_SETTINGS" )),
					  array ("UploadLogo", G::LoadTranslation( "ID_UPLOAD_LOGO" )),
					  array ("DeleteLogo", G::LoadTranslation( "ID_DELETE_LOGO" )),
					  array ("RestoreLogo", G::LoadTranslation( "ID_RESTORE_LOGO" )),
					  array ("InstallPlugin", G::LoadTranslation( "ID_INSTALL_PLUGIN" )),
					  array ("EnablePlugin", G::LoadTranslation( "ID_ENABLE_PLUGIN" )),
					  array ("DisablePlugin", G::LoadTranslation( "ID_DISABLE_PLUGIN" )),
					  array ("SetColumns", G::LoadTranslation( "ID_SET_COLUMNS" )),
					  array ("EnableAuditLog", G::LoadTranslation( "ID_ENABLE_AUDIT_LOG" )),
					  array ("DisableAuditLog", G::LoadTranslation( "ID_DISABLE_AUDIT_LOG" )),
					);

$oHeadPublisher = &headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript( "setup/auditLog", true );
$oHeadPublisher->assign( "CONFIG", $config );
$oHeadPublisher->assign( "ACTION", $arrayAction );
G::RenderPage( "publish", "extJs" );
