<?php
G::LoadClass("system");
G::LoadClass("wsTools");

/*
//Support ProcessMaker 1.8 which doesn't have the CLI class.
define("CLI2", class_exists("CLI"));

if (CLI2) {
  CLI::taskName("addon-install");
  CLI::taskDescription(<<<EOT
    Download and install an addon
EOT
  );
  CLI::taskRun(run_addon_core_install);
} else {
  pake_desc("install addon");
  pake_task("addon-install");
}
*/

CLI::taskName('change-password-hash-method');
CLI::taskDescription(<<<EOT
    Create .po file for the plugin
EOT
);
CLI::taskArg('workspace', false);
CLI::taskArg('hash', false);
CLI::taskRun("change_hash");

//function run_addon_core_install($args, $opts) {
function run_addon_core_install($args)
{
    try {
        if (!extension_loaded("mysql")) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
                dl("mysql.dll");
            } else {
                dl("mysql.so");
            }
        }
        ///////
        /*
        if (!CLI2) {
          $args = $opts;
        }
        */
        $workspace = $args[0];
        $storeId = $args[1];
        $addonName = $args[2];

        if (!defined("SYS_SYS")) {
            define("SYS_SYS", $workspace);
        }
        if (!defined("PATH_DATA_SITE")) {
            define("PATH_DATA_SITE", PATH_DATA . "sites/" . SYS_SYS . "/");
        }
        if (!defined("DB_ADAPTER")) {
            define("DB_ADAPTER", $args[3]);
        }
        ///////
        //***************** Plugins **************************
        G::LoadClass("plugin");
        //Here we are loading all plugins registered
        //the singleton has a list of enabled plugins

        $sSerializedFile = PATH_DATA_SITE . "plugin.singleton";
        $oPluginRegistry = &PMPluginRegistry::getSingleton();
        if (file_exists($sSerializedFile)) {
            $oPluginRegistry->unSerializeInstance(file_get_contents($sSerializedFile));
        }
        ///////
        //echo "** Installation starting... (workspace: $workspace, store: $storeId, id: $addonName)\n";
        $ws = new workspaceTools($workspace);
        $ws->initPropel(false);

        require_once PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEP . 'enterprise.php';
        require_once PATH_CORE . 'classes' . PATH_SEP . 'model' . PATH_SEP . 'AddonsManagerPeer.php';

        $addon = AddonsManagerPeer::retrieveByPK($addonName, $storeId);
        if ($addon == null) {
            throw new Exception("Id $addonName not found in store $storeId");
        }
        //echo "Downloading...\n";
        $download = $addon->download();
        //echo "Installing...\n";
        $addon->install();

        if ($addon->isCore()) {
            $ws = new workspaceTools($workspace);
            $ws->initPropel(false);
            $addon->setState("install-finish");
        } else {
            $addon->setState();
        }
    } catch (Exception $e) {
        $addon->setState("error");
        //fwrite(STDERR, "\n[ERROR: {$e->getMessage()}]\n");
        //fwrite(STDOUT, "\n[ERROR: {$e->getMessage()}]\n");
    }
    //echo "** Installation finished\n";
}

function change_hash($command, $opts)
{
    if (count($command) < 2) {
        $hash = 'md5';
    } else {
        $hash = array_pop($command);
    }
    $workspaces = get_workspaces_from_args($command);

    require_once (PATH_GULLIVER . PATH_SEP . 'class.bootstrap.php');
    Bootstrap::LoadClass("plugin");
    foreach ($workspaces as $workspace) {
        CLI::logging("Checking workspace: ".pakeColor::colorize($workspace->name, "INFO")."\n");
        try {
            $response = new stdclass();
            $response->workspace = $workspace;
            $response->hash = $hash;
            $workspace->changeHashPassword($workspace->name, $response);
            $workspace->close();
            CLI::logging(pakeColor::colorize("Changed...", "ERROR") . "\n");
        } catch (Exception $e) {
            echo "> Error:   ".CLI::error($e->getMessage()) . "\n";
        }
    }
}
