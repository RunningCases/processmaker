<?php

use Illuminate\Support\Facades\DB;
use ProcessMaker\Core\System;

CLI::taskName('upgrade');
CLI::taskDescription("Upgrade workspaces.\n\n This command should be run after upgrading ProcessMaker to a new version so that all workspaces are also upgraded to the\n  new version.");
CLI::taskOpt('child', "Used by the main upgrade thread", 'child', 'child');
CLI::taskOpt('buildACV', 'If this option is enabled, the Cache View is built.', 'ACV', 'buildACV');
CLI::taskOpt('noxml', 'If this option is enabled, the XML files translation is not built.', 'NoXml', 'no-xml');
CLI::taskOpt('nomafe', 'If this option is enabled, the MAFE files translation is not built.', 'nomafe', 'no-mafe');
/*----------------------------------********---------------------------------*/
CLI::taskOpt('keep_dyn_content', "Include the DYN_CONTENT_HISTORY value. Ex: --keep_dyn_content", 'i', 'keep_dyn_content');
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
CLI::taskName('upgrade-query');
CLI::taskRun('runUpgradeQuery');

/**
 * Execute the upgrade
 *
 * @param array $parameters
 * @param array $args
 */
function run_upgrade($parameters, $args)
{
    // Get values from command and arguments
    $workspaces = get_workspaces_from_args($parameters);
    $mainThread = $printHF = !array_key_exists('child', $args);
    $updateXmlForms = !array_key_exists('noxml', $args);
    $updateMafe = !array_key_exists('nomafe', $args);
    $keepDynContent = false;
    /*----------------------------------********---------------------------------*/
    $keepDynContent = array_key_exists('keep_dyn_content', $args); //In community version this section will be removed
    /*----------------------------------********---------------------------------*/

    // Initializing variables
    $globalStartTime = microtime(true);
    $numberOfWorkspaces = count($workspaces);
    $countWorkspace = 1;

    if ($printHF) {
        // Set upgrade flag
        if (count($workspaces) === 1) {
            // For the specific workspace send in the command
            G::isPMUnderUpdating(1, $workspaces[0]->name);
        } else {
            // For all workspaces
            G::isPMUnderUpdating(1);
        }

        // Print information when start the upgrade process
        CLI::logging('UPGRADE LOG INITIALIZED', PROCESSMAKER_PATH . 'upgrade.log');
        CLI::logging("UPGRADE STARTED\n");
    }

    foreach ($workspaces as $workspace) {
        if ($mainThread) {
            CLI::logging("FOLDERS AND FILES OF THE SYSTEM\n");
            // Upgrade actions for global files
            CLI::logging("* Start cleaning compiled folder...\n");
            $start = microtime(true);
            if (defined('PATH_C')) {
                G::rm_dir(PATH_C);
                G::mk_dir(PATH_C, 0777);
            }
            CLI::logging("* End cleaning compiled folder...(Completed on " . (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start to remove deprecated files...\n");
            $start = microtime(true);
            $workspace->removeDeprecatedFiles();
            CLI::logging("* End to remove deprecated files...(Completed on " . (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start checking Enterprise folder/files...\n");
            $start = microtime(true);
            $workspace->verifyFilesOldEnterprise();
            CLI::logging("* End checking Enterprise folder/files...(Completed on " . (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start checking framework paths...\n");
            $start = microtime(true);
            $workspace->checkFrameworkPaths();
            CLI::logging("* End checking framework paths...(Completed on " . (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start fixing serialized instance in serverConf.singleton file...\n");
            $start = microtime(true);
            $serverConf = ServerConf::getSingleton();
            $serverConf->updateClassNameInFile();
            CLI::logging("* End fixing serialized instance in serverConf.singleton file...(Completed on " .
                (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start the safe upgrade for javascript files cached by the browser (Maborak, ExtJs)...\n");
            $start = microtime(true);
            G::browserCacheFilesSetUid();
            CLI::logging("* End the safe upgrade for javascript files cached by the browser (Maborak, ExtJs)...(Completed on " .
                (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start to backup patch files...\n");
            $arrayPatch = glob(PATH_TRUNK . 'patch-*');
            if ($arrayPatch) {
                foreach ($arrayPatch as $value) {
                    if (file_exists($value)) {
                        // Copy patch content
                        $names = pathinfo($value);
                        $nameFile = $names['basename'];

                        $contentFile = file_get_contents($value);
                        $contentFile = preg_replace("[\n|\r|\n\r]", '', $contentFile);
                        CLI::logging($contentFile . ' installed (' . $nameFile . ')', PATH_DATA . 'log/upgrades.log');

                        // Move patch file
                        $newFile = PATH_DATA . $nameFile;
                        G::rm_dir($newFile);
                        copy($value, $newFile);
                        G::rm_dir($value);
                    }
                }
            }
            CLI::logging("* End to backup patch files...(Completed on " . (microtime(true) - $start) . " seconds)\n");

            CLI::logging("* Start to backup log files...\n");
            $start = microtime(true);
            $workspace->backupLogFiles();
            CLI::logging("* End to backup log files... (Completed on " . (microtime(true) - $start) . " seconds)\n");

            // The previous actions should be executed only the first time
            $mainThread = false;

            if ($numberOfWorkspaces === 1) {
                // Displaying information of the unique workspace to upgrade
                CLI::logging("UPGRADING DATABASE AND FILES OF WORKSPACE '{$workspace->name}' (1/1)\n");
            }
        }
        if ($numberOfWorkspaces === 1) {
            // Build parameters
            $arrayOptTranslation = [
                'updateXml' => $updateXmlForms,
                'updateMafe' => $updateMafe
            ];
            $optionMigrateHistoryData = [
                'keepDynContent' => $keepDynContent
            ];

            // Upgrade database and files from a specific workspace
            $workspace->upgrade($workspace->name, SYS_LANG, $arrayOptTranslation, $optionMigrateHistoryData);
            $workspace->close();
        } else {
            // Displaying information of the current workspace to upgrade
            CLI::logging("UPGRADING DATABASE AND FILES OF WORKSPACE '{$workspace->name}' ($countWorkspace/$numberOfWorkspaces)\n");

            // Build arguments
            $args = '--child';
            $args .= $updateXmlForms ? '' : ' --no-xml';
            $args .= $updateMafe ? '' : ' --no-mafe';
            $args .= $keepDynContent ? ' --keep_dyn_content' : '';

            // Build and execute command in another thread
            $command = PHP_BINARY . ' processmaker upgrade ' . $args . ' ' . $workspace->name;
            passthru($command);
        }

        // After the first execution is required set this values to false
        $updateXmlForms = false;
        $updateMafe = false;

        // Increment workspaces counter
        $countWorkspace++;
    }

    if ($printHF) {
        // Print information when finish the upgrade process
        CLI::logging('UPGRADE FINISHED (Completed on ' . (microtime(true) - $globalStartTime) .
            ' seconds), ProcessMaker ' . System::getVersion() . ' installed)' . "\n\n");

        // Delete upgrade flag
        G::isPMUnderUpdating(0);
    }
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

/**
 * Execute a query, used internally for upgrade process
 *
 * @param array $options
 */
function runUpgradeQuery($options)
{
    // Initializing variables
    $workspaceName = $options[0];
    $query = base64_decode($options[1]);
    $isRbac = (bool)$options[2];

    // Creating a new instance of the extended class
    $workspace = new WorkspaceTools($workspaceName);

    // Execute the query
    $workspace->upgradeQuery($query, $isRbac);

    // Terminate without error
    exit('success');
}
