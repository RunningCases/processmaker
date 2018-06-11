<?php

use Illuminate\Support\Facades\DB;
use ProcessMaker\Core\System;

CLI::taskName('upgrade');
CLI::taskDescription("Upgrade workspaces.\n\n This command should be run after upgrading ProcessMaker to a new version so that all workspaces are also upgraded to the\n  new version.");

CLI::taskOpt('buildACV', 'If this option is enabled, the Cache View is built.', 'ACV', 'buildACV');
CLI::taskOpt('noxml', 'If this option is enabled, the XML files translation is not built.', 'NoXml', 'no-xml');
/*----------------------------------********---------------------------------*/
CLI::taskOpt('include_dyn_content', "Include the DYN_CONTENT_HISTORY value. Ex: --include_dyn_content", 'i', 'include_dyn_content');
/*----------------------------------********---------------------------------*/
CLI::taskRun("run_upgrade");
/*----------------------------------********---------------------------------*/
CLI::taskName('unify-database');
CLI::taskDescription(
    <<<EOT
    Unify RBAC, Reports and Workflow database schemas to match the latest version

    Specify the workspaces whose databases schemas should be unified.
  If no workspace is specified, then the database schema will be upgraded or
  repaired on all available workspaces.

  This command will read the system schema and attempt to modify the workspaces'
  tables to match this new schema. In version 2.8 and later, it will merge the 3
  databases used in previous versions of ProcessMaker into one database. This
  command may be used after upgrading from ProcessMaker 2.5 to a later version
  of ProcessMaker.
EOT
);
/*----------------------------------********---------------------------------*/
CLI::taskArg('workspace');
/*----------------------------------********---------------------------------*/
CLI::taskRun("run_unify_database");
/*----------------------------------********---------------------------------*/

/**
 * A version of rm_dir which does not exits on error.
 *
 * @param  string $filename directory or file to remove
 * @param  bool $filesOnly either to remove the containing directory as well or not
 */
function rm_dir($filename, $filesOnly = false)
{
    if (is_file($filename)) {
        @unlink($filename) or CLI::logging(CLI::error("Could not remove file $filename")."\n");
    } else {
        foreach (glob("$filename/*") as $f) {
            rm_dir($f);
        }
        if (!$filesOnly) {
            @rmdir($filename) or CLI::logging(CLI::error("Could not remove directory $filename")."\n");
        }
    }
}

function run_upgrade($command, $args)
{
    CLI::logging("UPGRADE", PROCESSMAKER_PATH . "upgrade.log");
    CLI::logging("Checking files integrity...\n");
    //setting flag to true to check into sysGeneric.php
    $workspaces = get_workspaces_from_args($command);
    $oneWorkspace = 'true';
    if (count($workspaces) == 1) {
        foreach ($workspaces as $index => $workspace) {
            $oneWorkspace = $workspace->name;
        }
    }
    $flag = G::isPMUnderUpdating(1, $oneWorkspace);
    //start to upgrade
    $checksum = System::verifyChecksum();
    if ($checksum === false) {
        CLI::logging(CLI::error("checksum.txt not found, integrity check is not possible") . "\n");
        if (!CLI::question("Integrity check failed, do you want to continue the upgrade?")) {
            CLI::logging("Upgrade failed\n");
            $flag = G::isPMUnderUpdating(0);
            die();
        }
    } else {
        if (!empty($checksum['missing'])) {
            CLI::logging(CLI::error("The following files were not found in the installation:")."\n");
            foreach ($checksum['missing'] as $missing) {
                CLI::logging(" $missing\n");
            }
        }
        if (!empty($checksum['diff'])) {
            CLI::logging(CLI::error("The following files have modifications:")."\n");
            foreach ($checksum['diff'] as $diff) {
                CLI::logging(" $diff\n");
            }
        }
        if (!(empty($checksum['missing']) || empty($checksum['diff']))) {
            if (!CLI::question("Integrity check failed, do you want to continue the upgrade?")) {
                CLI::logging("Upgrade failed\n");
                $flag = G::isPMUnderUpdating(0);
                die();
            }
        }
    }
    CLI::logging("Clearing cache...\n");
    if (defined('PATH_C')) {
        G::rm_dir(PATH_C);
        G::mk_dir(PATH_C, 0777);
    }

    $count = count($workspaces);
    $first = true;
    $errors = false;
    $countWorkspace = 0;
    $buildCacheView = array_key_exists('buildACV', $args);
    $flagUpdateXml  = !array_key_exists('noxml', $args);
    $optionMigrateHistoryData = [
        /*----------------------------------********---------------------------------*/
        'includeDynContent' => array_key_exists('include_dyn_content', $args)
        /*----------------------------------********---------------------------------*/
    ];

    foreach ($workspaces as $index => $workspace) {
        if (empty(config("system.workspace"))) {
            define("SYS_SYS", $workspace->name);
            config(["system.workspace" => $workspace->name]);
        }

        if (!defined("PATH_DATA_SITE")) {
            define("PATH_DATA_SITE", PATH_DATA . "sites" . PATH_SEP . config("system.workspace") . PATH_SEP);
        }

        if (!defined('DB_ADAPTER')) {
            define('DB_ADAPTER', 'mysql');
        }

        try {
            $countWorkspace++;
            CLI::logging("Upgrading workspaces ($countWorkspace/$count): " . CLI::info($workspace->name) . "\n");
            $workspace->upgrade($buildCacheView, $workspace->name, false, 'en', ['updateXml' => $flagUpdateXml, 'updateMafe' => $first], $optionMigrateHistoryData);
            $workspace->close();
            $first = false;
            $flagUpdateXml = false;
        } catch (Exception $e) {
            CLI::logging("Errors upgrading workspace " . CLI::info($workspace->name) . ": " . CLI::error($e->getMessage()) . "\n");
            $errors = true;
        }
    }

    //Verify the information of the singleton ServConf by changing the name of the class if is required.
    CLI::logging("\nCheck/Fix serialized instance in serverConf.singleton file\n\n");
    $serverConf = ServerConf::getSingleton();
    $serverConf->updateClassNameInFile();

    // SAVE Upgrades/Patches
    $arrayPatch = glob(PATH_TRUNK . 'patch-*');

    if ($arrayPatch) {
        foreach ($arrayPatch as $value) {
            if (file_exists($value)) {
                // copy content the patch
                $names = pathinfo($value);
                $nameFile = $names['basename'];

                $contentFile = file_get_contents($value);
                $contentFile = preg_replace("[\n|\r|\n\r]", '', $contentFile);
                CLI::logging($contentFile . ' installed (' . $nameFile . ')', PATH_DATA . 'log/upgrades.log');

                // move file of patch
                $newFile = PATH_DATA . $nameFile;
                G::rm_dir($newFile);
                copy($value, $newFile);
                G::rm_dir($value);
            }
        }
    } else {
        CLI::logging('ProcessMaker ' . System::getVersion(). ' installed', PATH_DATA . 'log/upgrades.log');
    }

    //Safe upgrade for JavaScript files
    CLI::logging("\nSafe upgrade for files cached by the browser\n\n");

    G::browserCacheFilesSetUid();

    //Status
    if ($errors) {
        CLI::logging("Upgrade finished but there were errors upgrading workspaces.\n");
        CLI::logging(CLI::error("Please check the log above to correct any issues.") . "\n");
    } else {
        CLI::logging("Upgrade successful\n");
    }

    //setting flag to false
    $flag = G::isPMUnderUpdating(0);
}

function listFiles($dir)
{
    $files = array();
    $lista = glob($dir.'/*');
    foreach ($lista as $valor) {
        if (is_dir($valor)) {
            $inner_files =  listFiles($valor);
            if (is_array($inner_files)) {
                $files = array_merge($files, $inner_files);
            }
        }
        if (is_file($valor)) {
            array_push($files, $valor);
        }
    }
    return $files;
}
/*----------------------------------********---------------------------------*/
function run_unify_database($args)
{
    $workspaces = array();

    if (count($args) > 2) {
        $filename = array_pop($args);
        foreach ($args as $arg) {
            $workspaces[] = new WorkspaceTools($arg);
        }
    } elseif (count($args) > 0) {
        $workspace = new WorkspaceTools($args[0]);
        $workspaces[] = $workspace;
    }

    CLI::logging("UPGRADE", PROCESSMAKER_PATH . "upgrade.log");
    CLI::logging("Checking workspaces...\n");
    //setting flag to true to check into sysGeneric.php
    $flag = G::isPMUnderUpdating(0);

    //start to unify
    $count = count($workspaces);

    if ($count > 1) {
        if (!Bootstrap::isLinuxOs()) {
            CLI::error("This is not a Linux enviroment, please specify workspace.\n");
            return;
        }
    }

    $first = true;
    $errors = false;
    $countWorkspace = 0;
    $buildCacheView = array_key_exists("buildACV", $args);

    foreach ($workspaces as $workspace) {
        try {
            $countWorkspace++;

            if (! $workspace->workspaceExists()) {
                echo "Workspace {$workspace->name} not found\n";
                return false;
            }

            $ws = $workspace->name;
            $sContent = file_get_contents(PATH_DB . $ws . PATH_SEP . 'db.php');

            if (strpos($sContent, 'rb_')) {
                $workspace->onedb = false;
            } else {
                $workspace->onedb = true;
            }

            if ($workspace->onedb) {
                CLI::logging("The \"$workspace->name\" workspace already using one database...\n");
            } else {
                //create destination path
                $parentDirectory = PATH_DATA . "upgrade";
                if (! file_exists($parentDirectory)) {
                    mkdir($parentDirectory);
                }
                $tempDirectory = $parentDirectory . basename(tempnam(__FILE__, ''));
                if (is_writable($parentDirectory)) {
                    mkdir($tempDirectory);
                } else {
                    throw new Exception("Could not create directory:" . $parentDirectory);
                }
                $metadata = $workspace->getMetadata();
                CLI::logging("Exporting rb and rp databases to a temporal location...\n");
                $metadata["databases"] = $workspace->exportDatabase($tempDirectory, true);
                $metadata["version"] = 1;

                list($dbHost, $dbUser, $dbPass) = @explode(SYSTEM_HASH, G::decrypt(HASH_INSTALLATION, SYSTEM_HASH));
                $connectionName = 'UPGRADE';
                InstallerModule::setNewConnection($connectionName, $dbHost, $dbUser, $dbPass,'', '');

                foreach ($metadata['databases'] as $db) {
                    $dbName = $metadata['DB_NAME'];
                    CLI::logging("+> Restoring {$db['name']} to $dbName database\n");

                    $aParameters = ['dbHost'=>$dbHost,'dbUser'=>$dbUser,'dbPass'=>$dbPass];

                    $restore = $workspace->executeScript($dbName, "$tempDirectory/{$db['name']}.sql", $aParameters, $connectionName);

                    if ($restore) {
                        CLI::logging("+> Remove {$db['name']} database\n");

                        DB::connection($connectionName)->statement("DROP DATABASE IF EXISTS {$db['name']}");
                    }
                }
                DB::disconnect($connectionName);

                CLI::logging("Removing temporary files\n");
                G::rm_dir($tempDirectory);

                $newDBNames = $workspace->resetDBInfo($dbHost, true, true, true);

                CLI::logging(CLI::info("Done restoring databases") . "\n");
            }
        } catch (Exception $e) {
            CLI::logging("Errors upgrading workspace " . CLI::info($workspace->name) . ": " . CLI::error($e->getMessage()) . "\n");
            $errors = true;
        }
    }
    $flag = G::isPMUnderUpdating(0);
}
/*----------------------------------********---------------------------------*/
