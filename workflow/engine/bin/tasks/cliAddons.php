<?php

/*----------------------------------********---------------------------------*/
CLI::taskName('change-password-hash-method');
CLI::taskDescription(<<<EOT
     Change password hash method to md5 or sha256 for the specified workspace 
EOT
);
CLI::taskArg('workspace', false);
CLI::taskArg('hash', false);
CLI::taskRun("change_hash");
/*----------------------------------********---------------------------------*/

function run_addon_core_install($args)
{
    try {
        $workspace = $args[0];
        $storeId = $args[1];
        $addonName = $args[2];

        if (empty(config("system.workspace"))) {
            define("SYS_SYS", $workspace);
            config(["system.workspace" => $workspace]);
        }
        if (!defined("PATH_DATA_SITE")) {
            define("PATH_DATA_SITE", PATH_DATA . "sites/" . config("system.workspace") . "/");
        }
        if (!defined("DB_ADAPTER")) {
            define("DB_ADAPTER", $args[3]);
        }

        $ws = new WorkspaceTools($workspace);
        $ws->initPropel(false);

        require_once PATH_CORE . 'methods' . PATH_SEP . 'enterprise' . PATH_SEP . 'enterprise.php';

        $addon = AddonsManagerPeer::retrieveByPK($addonName, $storeId);
        if ($addon == null) {
            throw new Exception("Id $addonName not found in store $storeId");
        }

        $addon->download();
        $addon->install();

        if ($addon->isCore()) {
            $ws = new WorkspaceTools($workspace);
            $ws->initPropel(false);
            $addon->setState("install-finish");
        } else {
            $addon->setState();
        }
    } catch (Exception $e) {
        $addon->setState("error");
    }
}
/*----------------------------------********---------------------------------*/
function change_hash($command, $opts)
{
    if (count($command) < 2) {
        $hash = 'md5';
    } else {
        $hash = array_pop($command);
    }
    $workspaces = get_workspaces_from_args($command);

    foreach ($workspaces as $workspace) {
        CLI::logging("Checking workspace: ".pakeColor::colorize($workspace->name, "INFO")."\n");
        try {
            $response = new stdclass();
            $response->workspace = $workspace;
            $response->hash = $hash;
            if (empty(config("system.workspace"))) {
                define("SYS_SYS", $workspace->name);
                config(["system.workspace" => $workspace->name]);
            }
            if (!defined("PATH_DATA_SITE")) {
                define("PATH_DATA_SITE", PATH_DATA . "sites/" . config("system.workspace") . "/");
            }
            $_SESSION['__sw__'] = '';
            if (!$workspace->changeHashPassword($workspace->name, $response)) {
                CLI::logging(pakeColor::colorize("This command cannot be used because your license does not include it.", "ERROR") . "\n");
                $workspace->close();
                die;
            }
            $workspace->close();
            CLI::logging(pakeColor::colorize("Changed...", "ERROR") . "\n");
        } catch (Exception $e) {
            $token = strtotime("now");
            PMException::registerErrorLog($e, $token);
            G::outRes( "> Error:   " . CLI::error(G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token))) . "\n" );
        }
    }
}
/*----------------------------------********---------------------------------*/