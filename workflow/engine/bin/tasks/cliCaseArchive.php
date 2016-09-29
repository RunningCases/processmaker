<?php
G::LoadClass("system");
G::LoadClass("wsTools");


CLI::taskName("workspace-archive-cases");
CLI::taskDescription(<<<EOT
    Archive historical cases for workspace especified.

    This command archive of cases for workspace, the cases disappear of the
    list cases and can be restored if is necesary.

    Specify the workspaces whose cases should be saved in an historical file.
    If no workspace is specified, then the command will return and error.
    If no date is specified, then the command will return and error.
EOT
);

CLI::taskArg("workspace-name", false);
CLI::taskArg("limit-date", false);
CLI::taskRun('runArchive');


CLI::taskName("workspace-unarchive-cases");
CLI::taskDescription(<<<EOT
    Unarchive historical cases for workspace and specific tar file.

    This command unarchive of cases for workspace.
EOT
);

CLI::taskArg("workspace-name", false);
CLI::taskArg("namefile-tar", false);
CLI::taskRun('runUnarchive');


function runArchive($command, $args)
{
    CLI::logging("ProcessMaker Case Archive\n");

    if (!$command[0]) {
        throw new Exception("Must be specified the workspace name");
    }

    if (!$command[1]) {
        throw new Exception("Must be specified the limit date");
    }

    if ($command[1] != "" && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $command[1])) {
        throw new Exception("Date format is invalid");
    }

    CLI::logging("Workspace: " . $command[0] . "\n");
    CLI::logging("Archiving cases until: " . $command[1] . "\n");
    CLI::logging("Archive action Date: " . Date("Y-m-d H:i:s") . "\n");

    $config = System::getSystemConfiguration();
    require_once("propel/Propel.php");
    require_once("creole/Creole.php");



    if (!defined("PATH_SEP")) {
        define("PATH_SEP", (substr(PHP_OS, 0, 3) == "WIN")? "\\" : "/");
    }

    if (!extension_loaded("mysql")) {
        dl("mysql.so");
    }

    ini_set("display_errors", $config["debug"]);
    ini_set("error_reporting", $config['error_reporting']);
    ini_set("short_open_tag", "On");
    ini_set("default_charset", "UTF-8");
    ini_set("memory_limit", $config["memory_limit"]);
    ini_set("soap.wsdl_cache_enabled", $config["wsdl_cache"]);
    ini_set("date.timezone", $config["time_zone"]);

    set_include_path(PATH_PLUGINS . "pmCaseArchive" . PATH_SEPARATOR . get_include_path());

    include_once(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths_installed.php");
    if (!defined('PATH_RBAC_HOME')) {
        include_once(PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
    }
    include_once(PATH_CORE . "plugins" . PATH_SEP . "pmCaseArchive" . PATH_SEP . "classes" . PATH_SEP . "class.pmFunctions.php");

    define("SYS_SYS", $command[0]);

    $oWorkspaces = new workspaceTools($command[0]);
    $oWorkspaces->initPropel();

    G::isPMUnderUpdating(1);

    try {
        pmCaseArchive($command[0], $command[1]);
    } catch (Exception $e) {
        CLI::logging($e->getMessage() . "\n");
    }

    G::isPMUnderUpdating(0);

    CLI::logging("Archive ended at : " . Date("Y-m-d H:i:s") . "\n");
}

function runUnarchive($command, $args)
{
    CLI::logging("ProcessMaker Case Restore\n");

    if (!$command[0]) {
        throw new Exception("Must be specified the workspace name");
    }

    if (!$command[1]) {
        throw new Exception("Must be specified the name file tar");
    }

    CLI::logging("Workspace: " . $command[0] . "\n");
    CLI::logging("Restoring cases until: " . $command[1] . "\n");
    CLI::logging("Restore action Date: " . Date("Y-m-d H:i:s") . "\n\n");

    $config = System::getSystemConfiguration();
    require_once("propel/Propel.php");
    require_once("creole/Creole.php");

    if (!defined("PATH_SEP")) {
        define("PATH_SEP", (substr(PHP_OS, 0, 3) == "WIN") ? "\\" : "/");
    }

    if (!extension_loaded("mysql")) {
        dl("mysql.so");
    }

    ini_set("display_errors", $config["debug"]);
    ini_set("error_reporting", $config['error_reporting']);
    ini_set("short_open_tag", "On");
    ini_set("default_charset", "UTF-8");
    ini_set("memory_limit", $config["memory_limit"]);
    ini_set("soap.wsdl_cache_enabled", $config["wsdl_cache"]);
    ini_set("date.timezone", $config["time_zone"]);

    set_include_path(PATH_PLUGINS . "pmCaseArchive" . PATH_SEPARATOR . get_include_path());

    include_once(PATH_HOME . "engine" . PATH_SEP . "config" . PATH_SEP . "paths_installed.php");
    if (!defined('PATH_RBAC_HOME')) {
        include_once(PATH_HOME . 'engine' . PATH_SEP . 'config' . PATH_SEP . 'paths.php');
    }
    include_once(PATH_CORE . "plugins" . PATH_SEP . "pmCaseArchive" . PATH_SEP . "classes" . PATH_SEP . "class.pmFunctions.php");

    define("SYS_SYS", $command[0]);

    $oWorkspaces = new workspaceTools($command[0]);
    $oWorkspaces->initPropel();

    G::isPMUnderUpdating(1);

    try {
        pmCaseUnarchive($command[0], $command[1]);
    } catch (Exception $e) {
        CLI::logging($e->getMessage() . "\n");
    }

    G::isPMUnderUpdating(0);
    CLI::logging("Unarchive ended at : " . Date("Y-m-d H:i:s") . "\n");
    //CLI::logging("End -- Unarchive file tar ...\n");
}
