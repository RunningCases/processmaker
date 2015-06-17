<?php
register_shutdown_function(
    create_function(
        "",
        "
        if (class_exists(\"Propel\")) {
            Propel::close();
        }
        "
    )
);

ini_set("memory_limit", "512M");

try {
    //Verify data
    if (count($argv) != 6) {
        throw new Exception("Error: Invalid number of arguments");
    }

    for ($i = 3; $i <= count($argv) - 1; $i++) {
        $argv[$i] = base64_decode($argv[$i]);

        if (!is_dir($argv[$i])) {
            throw new Exception("Error: The path \"" . $argv[$i] . "\" is invalid");
        }
    }

    //Set variables
    $osIsLinux = strtoupper(substr(PHP_OS, 0, 3)) != "WIN";

    $pathHome = $argv[3];
    $pathTrunk = $argv[4];
    $pathOutTrunk = $argv[5];

    //Defines constants
    define("PATH_SEP", ($osIsLinux)? "/" : "\\");

    define("PATH_HOME",     $pathHome);
    define("PATH_TRUNK",    $pathTrunk);
    define("PATH_OUTTRUNK", $pathOutTrunk);

    define("PATH_CLASSES", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP);

    define("SYS_LANG", "en");

    require_once(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths.php");

    if (file_exists(PATH_TRUNK . "framework" . PATH_SEP . "src" . PATH_SEP . "Maveriks" . PATH_SEP . "Util" . PATH_SEP . "ClassLoader.php")) {
        require_once(PATH_TRUNK . "framework" . PATH_SEP . "src" . PATH_SEP . "Maveriks" . PATH_SEP . "Util" . PATH_SEP . "ClassLoader.php");
    }

    G::LoadThirdParty("pear/json", "class.json");
    G::LoadThirdParty("smarty/libs", "Smarty.class");
    G::LoadSystem("error");
    G::LoadSystem("dbconnection");
    G::LoadSystem("dbsession");
    G::LoadSystem("dbrecordset");
    G::LoadSystem("dbtable");
    G::LoadSystem("rbac" );
    G::LoadSystem("publisher");
    G::LoadSystem("templatePower");
    G::LoadSystem("xmlDocument");
    G::LoadSystem("xmlform");
    G::LoadSystem("xmlformExtension");
    G::LoadSystem("form");
    G::LoadSystem("menu");
    G::LoadSystem("xmlMenu");
    G::LoadSystem("dvEditor");
    G::LoadSystem("table");
    G::LoadSystem("pagedTable");
    G::LoadClass("system");

    require_once("propel/Propel.php");
    require_once("creole/Creole.php");

    $config = System::getSystemConfiguration();

    $e_all = (defined("E_DEPRECATED"))? E_ALL  & ~E_DEPRECATED : E_ALL;
    $e_all = (defined("E_STRICT"))?     $e_all & ~E_STRICT     : $e_all;
    $e_all = ($config["debug"])?        $e_all                 : $e_all & ~E_NOTICE;

    //Do not change any of these settings directly, use env.ini instead
    ini_set("display_errors",  $config["debug"]);
    ini_set("error_reporting", $e_all);
    ini_set("short_open_tag",  "On");
    ini_set("default_charset", "UTF-8");
    //ini_set("memory_limit",    $config["memory_limit"]);
    ini_set("soap.wsdl_cache_enabled", $config["wsdl_cache"]);
    ini_set("date.timezone",           $config["time_zone"]);

    define("DEBUG_SQL_LOG",  $config["debug_sql"]);
    define("DEBUG_TIME_LOG", $config["debug_time"]);
    define("DEBUG_CALENDAR_LOG", $config["debug_calendar"]);
    define("MEMCACHED_ENABLED",  $config["memcached"]);
    define("MEMCACHED_SERVER",   $config["memcached_server"]);
    define("TIME_ZONE",          $config["time_zone"]);

    require_once(PATH_GULLIVER . PATH_SEP . "class.bootstrap.php");
    //define("PATH_GULLIVER_HOME", PATH_TRUNK . "gulliver" . PATH_SEP);

    spl_autoload_register(array("Bootstrap", "autoloadClass"));

    //DATABASE propel classes used in "Cases" Options
    if (file_exists(PATH_CLASSES . "class.licensedFeatures.php")) {
        Bootstrap::registerClass("PMLicensedFeatures", PATH_CLASSES . "class.licensedFeatures.php");
    }

    Bootstrap::registerClass("serverConf", PATH_CLASSES . "class.serverConfiguration.php");

    Bootstrap::registerClass("Entity_Base",        PATH_HOME . "engine/classes/entities/Base.php");

    Bootstrap::registerClass("BaseContent",        PATH_HOME . "engine/classes/model/om/BaseContent.php");
    Bootstrap::registerClass("Content",            PATH_HOME . "engine/classes/model/Content.php");
    Bootstrap::registerClass("BaseContentPeer",    PATH_HOME . "engine/classes/model/om/BaseContentPeer.php");
    Bootstrap::registerClass("ContentPeer",        PATH_HOME . "engine/classes/model/ContentPeer.php");
    //Bootstrap::registerClass("BaseApplication",    PATH_HOME . "engine/classes/model/om/BaseApplication.php");
    //Bootstrap::registerClass("ApplicationPeer",    PATH_HOME . "engine/classes/model/ApplicationPeer.php");
    //Bootstrap::registerClass("Application",        PATH_HOME . "engine/classes/model/Application.php");
    //
    //Bootstrap::registerClass("BaseAppDelegation",  PATH_HOME . "engine/classes/model/om/BaseAppDelegation.php");
    //Bootstrap::registerClass("BaseHoliday",        PATH_HOME . "engine/classes/model/om/BaseHoliday.php");
    //Bootstrap::registerClass("BaseHolidayPeer",    PATH_HOME . "engine/classes/model/om/BaseHolidayPeer.php");
    //Bootstrap::registerClass("BaseTask",           PATH_HOME . "engine/classes/model/om/BaseTask.php");
    //Bootstrap::registerClass("BaseTaskPeer",       PATH_HOME . "engine/classes/model/om/BaseTaskPeer.php");
    //Bootstrap::registerClass("HolidayPeer",        PATH_HOME . "engine/classes/model/HolidayPeer.php");
    //Bootstrap::registerClass("Holiday",            PATH_HOME . "engine/classes/model/Holiday.php");
    //Bootstrap::registerClass("Task",               PATH_HOME . "engine/classes/model/Task.php");
    //Bootstrap::registerClass("TaskPeer",           PATH_HOME . "engine/classes/model/TaskPeer.php");
    //Bootstrap::registerClass("dates",              PATH_HOME . "engine/classes/class.dates.php");
    //Bootstrap::registerClass("calendar",           PATH_HOME . "engine/classes/class.calendar.php");
    //Bootstrap::registerClass("AppDelegation",      PATH_HOME . "engine/classes/model/AppDelegation.php");
    //Bootstrap::registerClass("BaseAppDelegationPeer", PATH_HOME . "engine/classes/model/om/BaseAppDelegationPeer.php");
    //Bootstrap::registerClass("AppDelegationPeer",  PATH_HOME . "engine/classes/model/AppDelegationPeer.php");
    //Bootstrap::registerClass("BaseAppDelay",       PATH_HOME . "engine/classes/model/om/BaseAppDelay.php");
    //Bootstrap::registerClass("AppDelayPeer",       PATH_HOME . "engine/classes/model/AppDelayPeer.php");
    //Bootstrap::registerClass("AppDelay",           PATH_HOME . "engine/classes/model/AppDelay.php");
    //Bootstrap::registerClass("BaseAdditionalTables", PATH_HOME . "engine/classes/model/om/BaseAdditionalTables.php");
    //Bootstrap::registerClass("AdditionalTables",   PATH_HOME . "engine/classes/model/AdditionalTables.php");
    //Bootstrap::registerClass("BaseAppCacheView",   PATH_HOME . "engine/classes/model/om/BaseAppCacheView.php");
    //Bootstrap::registerClass("AppCacheView",       PATH_HOME . "engine/classes/model/AppCacheView.php");
    //Bootstrap::registerClass("BaseAppCacheViewPeer", PATH_HOME . "engine/classes/model/om/BaseAppCacheViewPeer.php");
    //Bootstrap::registerClass("AppCacheViewPeer",   PATH_HOME . "engine/classes/model/AppCacheViewPeer.php");
    //
    //Bootstrap::registerClass("BaseAppTimeoutActionExecuted",  PATH_HOME . "engine/classes/model/om/BaseAppTimeoutActionExecuted.php");
    //Bootstrap::registerClass("AppTimeoutActionExecuted",      PATH_HOME . "engine/classes/model/AppTimeoutActionExecuted.php");
    //Bootstrap::registerClass("BaseAppTimeoutActionExecutedPeer", PATH_HOME . "engine/classes/model/om/BaseAppTimeoutActionExecutedPeer.php");
    //Bootstrap::registerClass("AppTimeoutActionExecutedPeer",  PATH_HOME . "engine/classes/model/AppTimeoutActionExecutedPeer.php");
    //
    //Bootstrap::registerClass("BaseInputDocument",  PATH_HOME . "engine/classes/model/om/BaseInputDocument.php");
    //Bootstrap::registerClass("InputDocument",      PATH_HOME . "engine/classes/model/InputDocument.php");
    //Bootstrap::registerClass("BaseAppDocument",    PATH_HOME . "engine/classes/model/om/BaseAppDocument.php");
    //Bootstrap::registerClass("AppDocument",        PATH_HOME . "engine/classes/model/AppDocument.php");
    //Bootstrap::registerClass("AppDocumentPeer",    PATH_HOME . "engine/classes/model/AppDocumentPeer.php");
    //
    //Bootstrap::registerClass("BaseAppEvent",       PATH_HOME . "engine/classes/model/om/BaseAppEvent.php");
    //Bootstrap::registerClass("AppEvent",           PATH_HOME . "engine/classes/model/AppEvent.php");
    //Bootstrap::registerClass("AppEventPeer",       PATH_HOME . "engine/classes/model/AppEventPeer.php");
    //
    //Bootstrap::registerClass("BaseAppHistory",     PATH_HOME . "engine/classes/model/om/BaseAppHistory.php");
    //Bootstrap::registerClass("AppHistory",         PATH_HOME . "engine/classes/model/AppHistory.php");
    //Bootstrap::registerClass("AppHistoryPeer",     PATH_HOME . "engine/classes/model/AppHistoryPeer.php");
    //
    //Bootstrap::registerClass("BaseAppFolder",      PATH_HOME . "engine/classes/model/om/BaseAppFolder.php");
    //Bootstrap::registerClass("AppFolder",          PATH_HOME . "engine/classes/model/AppFolder.php");
    //Bootstrap::registerClass("AppFolderPeer",      PATH_HOME . "engine/classes/model/AppFolderPeer.php");
    //
    //Bootstrap::registerClass("BaseAppMessage",     PATH_HOME . "engine/classes/model/om/BaseAppMessage.php");
    //Bootstrap::registerClass("AppMessage",         PATH_HOME . "engine/classes/model/AppMessage.php");
    //
    //Bootstrap::registerClass("BaseAppMessagePeer", PATH_HOME . "engine/classes/model/om/BaseAppMessagePeer.php");
    //Bootstrap::registerClass("AppMessagePeer",     PATH_HOME . "engine/classes/model/AppMessagePeer.php");
    //
    //Bootstrap::registerClass("BaseAppNotesPeer",    PATH_HOME . "engine/classes/model/om/BaseAppNotesPeer.php");
    //Bootstrap::registerClass("AppNotesPeer",        PATH_HOME . "engine/classes/model/AppNotesPeer.php");
    //
    //Bootstrap::registerClass("BaseAppNotes",        PATH_HOME . "engine/classes/model/om/BaseAppNotes.php");
    //Bootstrap::registerClass("AppNotes",            PATH_HOME . "engine/classes/model/AppNotes.php");
    //
    //Bootstrap::registerClass("BaseAppOwner",        PATH_HOME . "engine/classes/model/om/BaseAppOwner.php");
    //Bootstrap::registerClass("AppOwner",            PATH_HOME . "engine/classes/model/AppOwner.php");
    //Bootstrap::registerClass("AppOwnerPeer",        PATH_HOME . "engine/classes/model/AppOwnerPeer.php");
    //
    //Bootstrap::registerClass("BaseAppSolrQueue",    PATH_HOME . "engine/classes/model/om/BaseAppSolrQueue.php");
    //Bootstrap::registerClass("Entity_AppSolrQueue", PATH_HOME . "engine/classes/entities/AppSolrQueue.php");
    //Bootstrap::registerClass("AppSolrQueue",        PATH_HOME . "engine/classes/model/AppSolrQueue.php");
    //Bootstrap::registerClass("AppSolrQueuePeer",    PATH_HOME . "engine/classes/model/AppSolrQueuePeer.php");
    //
    //Bootstrap::registerClass("BaseAppThread",       PATH_HOME . "engine/classes/model/om/BaseAppThread.php");
    //Bootstrap::registerClass("AppThread",           PATH_HOME . "engine/classes/model/AppThread.php");
    //Bootstrap::registerClass("AppThreadPeer",       PATH_HOME . "engine/classes/model/AppThreadPeer.php");
    //
    //Bootstrap::registerClass("BaseCaseScheduler",   PATH_HOME . "engine/classes/model/om/BaseCaseScheduler.php");
    //Bootstrap::registerClass("CaseScheduler",       PATH_HOME . "engine/classes/model/CaseScheduler.php");
    //
    //Bootstrap::registerClass("BaseCaseSchedulerPeer",PATH_HOME . "engine/classes/model/om/BaseCaseSchedulerPeer.php");
    //Bootstrap::registerClass("CaseSchedulerPeer",    PATH_HOME . "engine/classes/model/CaseSchedulerPeer.php");
    //
    //Bootstrap::registerClass("BaseCaseTracker",     PATH_HOME . "engine/classes/model/om/BaseCaseTracker.php");
    //Bootstrap::registerClass("CaseTracker",         PATH_HOME . "engine/classes/model/CaseTracker.php");
    //
    //Bootstrap::registerClass("BaseCaseTrackerPeer", PATH_HOME . "engine/classes/model/om/BaseCaseTrackerPeer.php");
    //Bootstrap::registerClass("CaseTrackerPeer",     PATH_HOME . "engine/classes/model/CaseTrackerPeer.php");
    //
    //Bootstrap::registerClass("BaseCaseTrackerObject",PATH_HOME . "engine/classes/model/om/BaseCaseTrackerObject.php");
    //Bootstrap::registerClass("CaseTrackerObject",    PATH_HOME . "engine/classes/model/CaseTrackerObject.php");
    //
    //Bootstrap::registerClass("BaseCaseTrackerObjectPeer",PATH_HOME . "engine/classes/model/om/BaseCaseTrackerObjectPeer.php");
    //Bootstrap::registerClass("CaseTrackerObjectPeer",    PATH_HOME . "engine/classes/model/CaseTrackerObjectPeer.php");

    Bootstrap::registerClass("BaseDbSource",        PATH_HOME . "engine/classes/model/om/BaseDbSource.php");
    Bootstrap::registerClass("DbSource",            PATH_HOME . "engine/classes/model/DbSource.php");

    //Bootstrap::registerClass("XMLDB",              PATH_HOME . "engine/classes/class.xmlDb.php");
    //Bootstrap::registerClass("dynaFormHandler",    PATH_GULLIVER . "class.dynaformhandler.php");
    //Bootstrap::registerClass("DynaFormField",      PATH_HOME . "engine/classes/class.dynaFormField.php");
    //Bootstrap::registerClass("BaseDynaform",       PATH_HOME . "engine/classes/model/om/BaseDynaform.php");
    //Bootstrap::registerClass("Dynaform",           PATH_HOME . "engine/classes/model/Dynaform.php");
    //Bootstrap::registerClass("DynaformPeer",       PATH_HOME . "engine/classes/model/DynaformPeer.php");
    //
    //Bootstrap::registerClass("BaseEvent",          PATH_HOME . "engine/classes/model/om/BaseEvent.php");
    //Bootstrap::registerClass("Event",              PATH_HOME . "engine/classes/model/Event.php");
    //
    //Bootstrap::registerClass("BaseEventPeer",      PATH_HOME . "engine/classes/model/om/BaseEventPeer.php");
    //Bootstrap::registerClass("EventPeer",          PATH_HOME . "engine/classes/model/EventPeer.php");
    //
    //Bootstrap::registerClass("BaseFields",         PATH_HOME . "engine/classes/model/om/BaseFields.php");
    //Bootstrap::registerClass("Fields",             PATH_HOME . "engine/classes/model/Fields.php");
    //
    //Bootstrap::registerClass("BaseGateway",        PATH_HOME . "engine/classes/model/om/BaseGateway.php");
    //Bootstrap::registerClass("Gateway",            PATH_HOME . "engine/classes/model/Gateway.php");

    Bootstrap::registerClass("BaseGroupUser",      PATH_HOME . "engine/classes/model/om/BaseGroupUser.php");
    Bootstrap::registerClass("Groupwf",            PATH_HOME . "engine/classes/model/Groupwf.php");
    Bootstrap::registerClass("GroupUser",          PATH_HOME . "engine/classes/model/GroupUser.php");

    Bootstrap::registerClass("BaseGroupUserPeer",  PATH_HOME . "engine/classes/model/om/BaseGroupUserPeer.php");
    Bootstrap::registerClass("GroupUserPeer",      PATH_HOME . "engine/classes/model/GroupUserPeer.php");

    Bootstrap::registerClass("BaseGroupwfPeer",    PATH_HOME . "engine/classes/model/om/BaseGroupwfPeer.php");
    Bootstrap::registerClass("GroupwfPeer",        PATH_HOME . "engine/classes/model/GroupwfPeer.php");

    //Bootstrap::registerClass("BaseInputDocumentPeer", PATH_HOME . "engine/classes/model/om/BaseInputDocumentPeer.php");
    //Bootstrap::registerClass("InputDocumentPeer",  PATH_HOME . "engine/classes/model/InputDocumentPeer.php");

    Bootstrap::registerClass("BaseIsoCountry",     PATH_HOME . "engine/classes/model/om/BaseIsoCountry.php");
    Bootstrap::registerClass("IsoCountry",         PATH_HOME . "engine/classes/model/IsoCountry.php");
    Bootstrap::registerClass("BaseTranslation",    PATH_HOME . "engine/classes/model/om/BaseTranslation.php");
    Bootstrap::registerClass("Translation",        PATH_HOME . "engine/classes/model/Translation.php");

    //Bootstrap::registerClass("BaseLogCasesScheduler", PATH_HOME . "engine/classes/model/om/BaseLogCasesScheduler.php");
    //Bootstrap::registerClass("LogCasesScheduler",  PATH_HOME . "engine/classes/model/LogCasesScheduler.php");
    //
    //Bootstrap::registerClass("BaseObjectPermission",PATH_HOME . "engine/classes/model/om/BaseObjectPermission.php");
    //Bootstrap::registerClass("ObjectPermission",    PATH_HOME . "engine/classes/model/ObjectPermission.php");
    //Bootstrap::registerClass("ObjectPermissionPeer",PATH_HOME . "engine/classes/model/ObjectPermissionPeer.php");
    //
    //Bootstrap::registerClass("BaseOutputDocument",  PATH_HOME . "engine/classes/model/om/BaseOutputDocument.php");
    //Bootstrap::registerClass("OutputDocument",      PATH_HOME . "engine/classes/model/OutputDocument.php");
    //Bootstrap::registerClass("OutputDocumentPeer",  PATH_HOME . "engine/classes/model/OutputDocumentPeer.php");
    //
    //Bootstrap::registerClass("BaseProcess",         PATH_HOME . "engine/classes/model/om/BaseProcess.php");
    //Bootstrap::registerClass("BaseProcessCategory", PATH_HOME . "engine/classes/model/om/BaseProcessCategory.php");
    //Bootstrap::registerClass("ProcessCategory",     PATH_HOME . "engine/classes/model/ProcessCategory.php");
    //Bootstrap::registerClass("ProcessCategoryPeer", PATH_HOME . "engine/classes/model/ProcessCategoryPeer.php");
    //Bootstrap::registerClass("ProcessPeer",         PATH_HOME . "engine/classes/model/ProcessPeer.php");
    //Bootstrap::registerClass("Process",             PATH_HOME . "engine/classes/model/Process.php");
    //
    //Bootstrap::registerClass("BaseProcessUser",     PATH_HOME . "engine/classes/model/om/BaseProcessUser.php");
    //Bootstrap::registerClass("ProcessUser",         PATH_HOME . "engine/classes/model/ProcessUser.php");
    //
    //Bootstrap::registerClass("BaseProcessUserPeer", PATH_HOME . "engine/classes/model/om/BaseProcessUserPeer.php");
    //Bootstrap::registerClass("ProcessUserPeer",     PATH_HOME . "engine/classes/model/ProcessUserPeer.php");
    //
    //Bootstrap::registerClass("BaseReportTable",     PATH_HOME . "engine/classes/model/om/BaseReportTable.php");
    //Bootstrap::registerClass("ReportTable",         PATH_HOME . "engine/classes/model/ReportTable.php");
    //Bootstrap::registerClass("ReportTablePeer",     PATH_HOME . "engine/classes/model/ReportTablePeer.php");
    //
    //Bootstrap::registerClass("BaseReportVar",       PATH_HOME . "engine/classes/model/om/BaseReportVar.php");
    //Bootstrap::registerClass("ReportVar",           PATH_HOME . "engine/classes/model/ReportVar.php");
    //
    //Bootstrap::registerClass("BaseReportVarPeer",   PATH_HOME . "engine/classes/model/om/BaseReportVarPeer.php");
    //Bootstrap::registerClass("ReportVarPeer",       PATH_HOME . "engine/classes/model/ReportVarPeer.php");
    //
    //Bootstrap::registerClass("BaseRoute",           PATH_HOME . "engine/classes/model/om/BaseRoute.php");
    //Bootstrap::registerClass("Route",               PATH_HOME . "engine/classes/model/Route.php");
    //Bootstrap::registerClass("RoutePeer",           PATH_HOME . "engine/classes/model/RoutePeer.php");
    //
    //Bootstrap::registerClass("BaseStep",            PATH_HOME . "engine/classes/model/om/BaseStep.php");
    //Bootstrap::registerClass("Step",                PATH_HOME . "engine/classes/model/Step.php");
    //Bootstrap::registerClass("StepPeer",            PATH_HOME . "engine/classes/model/StepPeer.php");
    //
    //Bootstrap::registerClass("BaseStepSupervisor",  PATH_HOME . "engine/classes/model/om/BaseStepSupervisor.php");
    //Bootstrap::registerClass("StepSupervisor",      PATH_HOME . "engine/classes/model/StepSupervisor.php");
    //
    //Bootstrap::registerClass("BaseStepSupervisorPeer", PATH_HOME . "engine/classes/model/om/BaseStepSupervisorPeer.php");
    //Bootstrap::registerClass("StepSupervisorPeer",  PATH_HOME . "engine/classes/model/StepSupervisorPeer.php");
    //
    //Bootstrap::registerClass("BaseStepTrigger",     PATH_HOME . "engine/classes/model/om/BaseStepTrigger.php");
    //Bootstrap::registerClass("StepTrigger",         PATH_HOME . "engine/classes/model/StepTrigger.php");
    //Bootstrap::registerClass("StepTriggerPeer",     PATH_HOME . "engine/classes/model/StepTriggerPeer.php");
    //
    //Bootstrap::registerClass("SolrRequestData",     PATH_HOME . "engine/classes/entities/SolrRequestData.php");
    //
    //Bootstrap::registerClass("SolrUpdateDocument",  PATH_HOME . "engine/classes/entities/SolrUpdateDocument.php");
    //
    //Bootstrap::registerClass("BaseSwimlanesElements",PATH_HOME . "engine/classes/model/om/BaseSwimlanesElements.php");
    //Bootstrap::registerClass("SwimlanesElements",   PATH_HOME . "engine/classes/model/SwimlanesElements.php");
    //Bootstrap::registerClass("BaseSwimlanesElementsPeer", PATH_HOME ."engine/classes/model/om/BaseSwimlanesElementsPeer.php");
    //Bootstrap::registerClass("SwimlanesElementsPeer",PATH_HOME . "engine/classes/model/SwimlanesElementsPeer.php");
    //
    //Bootstrap::registerClass("BaseSubApplication",  PATH_HOME . "engine/classes/model/om/BaseSubApplication.php");
    //Bootstrap::registerClass("SubApplication",      PATH_HOME . "engine/classes/model/SubApplication.php");
    //Bootstrap::registerClass("SubApplicationPeer",  PATH_HOME . "engine/classes/model/SubApplicationPeer.php");
    //
    //Bootstrap::registerClass("BaseSubProcess",      PATH_HOME . "engine/classes/model/om/BaseSubProcess.php");
    //Bootstrap::registerClass("SubProcess",          PATH_HOME . "engine/classes/model/SubProcess.php");
    //
    //Bootstrap::registerClass("BaseSubProcessPeer",  PATH_HOME . "engine/classes/model/om/BaseSubProcessPeer.php");
    //Bootstrap::registerClass("SubProcessPeer",      PATH_HOME . "engine/classes/model/SubProcessPeer.php");
    //
    //Bootstrap::registerClass("BaseTask",            PATH_HOME . "engine/classes/model/om/BaseTask.php");
    //Bootstrap::registerClass("Task",                PATH_HOME . "engine/classes/model/Task.php");
    //
    //Bootstrap::registerClass("BaseTaskUser",        PATH_HOME . "engine/classes/model/om/BaseTaskUser.php");
    //Bootstrap::registerClass("TaskUserPeer",        PATH_HOME . "engine/classes/model/TaskUserPeer.php");
    //Bootstrap::registerClass("TaskUser",            PATH_HOME . "engine/classes/model/TaskUser.php");
    //
    //Bootstrap::registerClass("BaseTriggers",        PATH_HOME . "engine/classes/model/om/BaseTriggers.php");
    //Bootstrap::registerClass("Triggers",            PATH_HOME . "engine/classes/model/Triggers.php");
    //Bootstrap::registerClass("BaseTriggersPeer",    PATH_HOME . "engine/classes/model/om/BaseTriggersPeer.php");
    //Bootstrap::registerClass("TriggersPeer",        PATH_HOME . "engine/classes/model/TriggersPeer.php");

    Bootstrap::registerClass("IsoCountry",          PATH_HOME . "engine/classes/model/IsoCountry.php");
    Bootstrap::registerClass("BaseIsoSubdivision",  PATH_HOME . "engine/classes/model/om/BaseIsoSubdivision.php");
    Bootstrap::registerClass("IsoSubdivision",      PATH_HOME . "engine/classes/model/IsoSubdivision.php");
    Bootstrap::registerClass("BaseIsoLocation",     PATH_HOME . "engine/classes/model/om/BaseIsoLocation.php");
    Bootstrap::registerClass("IsoLocation",         PATH_HOME . "engine/classes/model/IsoLocation.php");
    Bootstrap::registerClass("Users",               PATH_HOME . "engine/classes/model/Users.php");
    Bootstrap::registerClass("UsersPeer",           PATH_HOME . "engine/classes/model/UsersPeer.php");
    Bootstrap::registerClass("BaseUsers",           PATH_HOME . "engine/classes/model/om/BaseUsers.php");

    Bootstrap::registerClass("AuthenticationSourcePeer", PATH_RBAC . "model" . PATH_SEP . "AuthenticationSourcePeer.php");
    Bootstrap::registerClass("BaseAuthenticationSource", PATH_RBAC . "model" . PATH_SEP . "om" . PATH_SEP . "BaseAuthenticationSource.php");
    Bootstrap::registerClass("AuthenticationSource",     PATH_RBAC . "model" . PATH_SEP . "AuthenticationSource.php");
    Bootstrap::registerClass("RolesPeer",                PATH_RBAC . "model" . PATH_SEP . "RolesPeer.php");
    Bootstrap::registerClass("BaseRoles",                PATH_RBAC . "model" . PATH_SEP . "om" . PATH_SEP . "BaseRoles.php");
    Bootstrap::registerClass("Roles",                    PATH_RBAC . "model" . PATH_SEP . "Roles.php");

    //Bootstrap::registerClass("UsersRolesPeer",           PATH_RBAC . "model" . PATH_SEP . "UsersRolesPeer.php");
    //Bootstrap::registerClass("BaseUsersRoles",           PATH_RBAC . "model" . PATH_SEP . "om" . PATH_SEP . "BaseUsersRoles.php");
    //Bootstrap::registerClass("UsersRoles",               PATH_RBAC . "model" . PATH_SEP . "UsersRoles.php");
    require_once(PATH_RBAC . "model" . PATH_SEP . "UsersRolesPeer.php");
    require_once(PATH_RBAC . "model" . PATH_SEP . "om" . PATH_SEP . "BaseUsersRoles.php");
    require_once(PATH_RBAC . "model" . PATH_SEP . "UsersRoles.php");

    $arrayClass = array("Configuration", "Language");

    if (file_exists(PATH_CORE . "classes" . PATH_SEP . "model" . PATH_SEP . "AddonsManager.php")) {
        $arrayClass[] = "AddonsManager";
    }

    foreach ($arrayClass as $value) {
        Bootstrap::registerClass("Base" . $value,          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "Base" . $value . ".php");
        Bootstrap::registerClass($value,                   PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . $value . ".php");
        Bootstrap::registerClass("Base" . $value . "Peer", PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "om" . PATH_SEP . "Base" . $value . "Peer.php");
        Bootstrap::registerClass($value . "Peer",          PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . $value . "Peer.php");
    }

    //Bootstrap::registerClass("Xml_Node",            PATH_GULLIVER . "class.xmlDocument.php");
    //
    //Bootstrap::registerClass("wsResponse",          PATH_HOME . "engine/classes/class.wsResponse.php");
    //
    //G::LoadClass("dates");

    Bootstrap::registerClass("groups", PATH_CLASSES . "class.groups.php");

    $workflow = $argv[2];

    if (is_dir(PATH_DB . $workflow) && file_exists(PATH_DB . $workflow . PATH_SEP . "db.php")) {
        define("SYS_SYS", $workflow);

        include_once(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths_installed.php");
        include_once(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths.php");

        //PM Paths DATA
        define("PATH_DATA_SITE",                PATH_DATA      . "sites/" . SYS_SYS . "/");
        define("PATH_DOCUMENT",                 PATH_DATA_SITE . "files/");
        define("PATH_DATA_MAILTEMPLATES",       PATH_DATA_SITE . "mailTemplates/");
        define("PATH_DATA_PUBLIC",              PATH_DATA_SITE . "public/");
        define("PATH_DATA_REPORTS",             PATH_DATA_SITE . "reports/");
        define("PATH_DYNAFORM",                 PATH_DATA_SITE . "xmlForms/");
        define("PATH_IMAGES_ENVIRONMENT_FILES", PATH_DATA_SITE . "usersFiles" . PATH_SEP);
        define("PATH_IMAGES_ENVIRONMENT_USERS", PATH_DATA_SITE . "usersPhotographies" . PATH_SEP);

        if (is_file(PATH_DATA_SITE.PATH_SEP . ".server_info")) {
            $SERVER_INFO = file_get_contents(PATH_DATA_SITE.PATH_SEP.".server_info");
            $SERVER_INFO = unserialize($SERVER_INFO);

            define("SERVER_NAME", $SERVER_INFO ["SERVER_NAME"]);
            define("SERVER_PORT", $SERVER_INFO ["SERVER_PORT"]);
        } else {
            eprintln("WARNING! No server info found!", "red");
        }

        //DB
        $phpCode = "";

        $fileDb = fopen(PATH_DB . $workflow . PATH_SEP . "db.php", "r");

        if ($fileDb) {
            while (!feof($fileDb)) {
                $buffer = fgets($fileDb, 4096); //Read a line

                $phpCode .= preg_replace("/define\s*\(\s*[\x22\x27](.*)[\x22\x27]\s*,\s*(\x22.*\x22|\x27.*\x27)\s*\)\s*;/i", "\$$1 = $2;", $buffer);
            }

            fclose($fileDb);
        }

        $phpCode = str_replace(array("<?php", "<?", "?>"), array("", "", ""), $phpCode);

        eval($phpCode);

        $dsn     = $DB_ADAPTER . "://" . $DB_USER . ":" . $DB_PASS . "@" . $DB_HOST . "/" . $DB_NAME;
        $dsnRbac = $DB_ADAPTER . "://" . $DB_RBAC_USER . ":" . $DB_RBAC_PASS . "@" . $DB_RBAC_HOST . "/" . $DB_RBAC_NAME;
        $dsnRp   = $DB_ADAPTER . "://" . $DB_REPORT_USER . ":" . $DB_REPORT_PASS . "@" . $DB_REPORT_HOST . "/" . $DB_REPORT_NAME;

        switch ($DB_ADAPTER) {
            case "mysql":
                $dsn .= "?encoding=utf8";
                $dsnRbac .= "?encoding=utf8";
                break;
            case "mssql":
                //$dsn .= "?sendStringAsUnicode=false";
                //$dsnRbac .= "?sendStringAsUnicode=false";
                break;
            default:
                break;
        }

        $pro = array();
        $pro["datasources"]["workflow"]["connection"] = $dsn;
        $pro["datasources"]["workflow"]["adapter"] = $DB_ADAPTER;
        $pro["datasources"]["rbac"]["connection"] = $dsnRbac;
        $pro["datasources"]["rbac"]["adapter"] = $DB_ADAPTER;
        $pro["datasources"]["rp"]["connection"] = $dsnRp;
        $pro["datasources"]["rp"]["adapter"] = $DB_ADAPTER;
        //$pro["datasources"]["dbarray"]["connection"] = "dbarray://user:pass@localhost/pm_os";
        //$pro["datasources"]["dbarray"]["adapter"]    = "dbarray";

        $oFile = fopen(PATH_CORE . "config" . PATH_SEP . "_databases_.php", "w");
        fwrite($oFile, "<?php global \$pro; return \$pro; ?>");
        fclose($oFile);

        Propel::init(PATH_CORE . "config" . PATH_SEP . "_databases_.php");
        //Creole::registerDriver("dbarray", "creole.contrib.DBArrayConnection");

        //Enable RBAC
        Bootstrap::LoadSystem("rbac");

        $rbac = &RBAC::getSingleton(PATH_DATA, session_id());
        $rbac->sSystem = "PROCESSMAKER";

        eprintln("Processing workspace: " . $workflow, "green");

        try {
            require_once(PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.ldapAdvanced.php");
            require_once(PATH_HOME . "engine" . PATH_SEP . "services" . PATH_SEP . "ldapadvanced.php");

            $obj = new ldapadvancedClassCron();

            $obj->executeCron((bool)($argv[1]));
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";

            eprintln("Problem in workspace: " . $workflow . " it was omitted.", "red");
        }

        eprintln();
    }

    if (file_exists(PATH_CORE . "config" . PATH_SEP . "_databases_.php")) {
        unlink(PATH_CORE . "config" . PATH_SEP . "_databases_.php");
    }
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}

