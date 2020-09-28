<?php

namespace ProcessMaker\TaskScheduler;

use Application;
use AppAssignSelfServiceValueGroupPeer;
use AppAssignSelfServiceValuePeer;
use AppCacheView;
use AppDelegation;
use App\Jobs\TaskScheduler;
use Bootstrap;
use Cases;
use ConfigurationPeer;
use Criteria;
use Exception;
use G;
use Illuminate\Support\Facades\Log;
use ldapadvancedClassCron;
use NotificationQueue;
use ProcessMaker\BusinessModel\Light\PushMessageAndroid;
use ProcessMaker\BusinessModel\Light\PushMessageIOS;
use ProcessMaker\Core\JobsManager;
use ProcessMaker\Plugins\PluginRegistry;
use Propel;
use ResultSet;
use SpoolRun;

class Task
{
    /**
     * Property asynchronous,
     * @var bool 
     */
    private $asynchronous;

    /**
     * Property object
     * @var mix 
     */
    private $object;

    /**
     * Constructor class.
     * @param bool $async
     * @param mix $object
     */
    public function __construct(bool $asynchronous, $object)
    {
        $this->asynchronous = $asynchronous;
        $this->object = $object;
    }

    /**
     * Run job, the property async indicate if is synchronous or asynchronous.
     * @param callable $job
     */
    private function runTask(callable $job)
    {
        if ($this->asynchronous === false) {
            $job();
        }
        if ($this->asynchronous === true) {
            JobsManager::getSingleton()->dispatch(TaskScheduler::class, $job);
        }
    }

    /**
     * Print start message in console.
     * @param string $message
     */
    public function setExecutionMessage(string $message)
    {
        Log::channel('taskScheduler:taskScheduler')->info($message, Bootstrap::context());
        if ($this->asynchronous === false) {
            $len = strlen($message);
            $linesize = 60;
            $rOffset = $linesize - $len;

            eprint("* $message");

            for ($i = 0; $i < $rOffset; $i++) {
                eprint('.');
            }
        }
    }

    /**
     * Print result message in console.
     * @param string $message
     * @param string $type
     */
    public function setExecutionResultMessage(string $message, string $type = '')
    {
        $color = 'green';
        if ($type == 'error') {
            $color = 'red';
            Log::channel('taskScheduler:taskScheduler')->error($message, Bootstrap::context());
        }
        if ($type == 'info') {
            $color = 'yellow';
            Log::channel('taskScheduler:taskScheduler')->info($message, Bootstrap::context());
        }
        if ($type == 'warning') {
            $color = 'yellow';
            Log::channel('taskScheduler:taskScheduler')->warning($message, Bootstrap::context());
        }
        if ($this->asynchronous === false) {
            eprintln("[$message]", $color);
        }
    }

    /**
     * Save logs.
     * @param string $source
     * @param string $type
     * @param string $description
     */
    public function saveLog(string $source, string $type, string $description)
    {
        if ($this->asynchronous === true) {
            $context = [
                'type' => $type,
                'description' => $description
            ];
            Log::channel('taskScheduler:taskScheduler')->info($source, Bootstrap::context($context));
        }
        if ($this->asynchronous === false) {
            try {
                G::verifyPath(PATH_DATA . "log" . PATH_SEP, true);
                G::log("| $this->object | " . $source . " | $type | " . $description, PATH_DATA);
            } catch (Exception $e) {
                Log::channel('taskScheduler:taskScheduler')->error($e->getMessage(), Bootstrap::context($context));
            }
        }
    }

    /**
     * This resend the emails.
     * @param string $now
     * @param string $dateSystem
     */
    public function resendEmails($now, $dateSystem)
    {
        $job = function() use($now, $dateSystem) {
            $this->setExecutionMessage("Resending emails");

            try {
                $dateResend = $now;

                if ($now == $dateSystem) {
                    $arrayDateSystem = getdate(strtotime($dateSystem));

                    $mktDateSystem = mktime(
                        $arrayDateSystem["hours"],
                        $arrayDateSystem["minutes"],
                        $arrayDateSystem["seconds"],
                        $arrayDateSystem["mon"],
                        $arrayDateSystem["mday"],
                        $arrayDateSystem["year"]
                    );

                    $dateResend = date("Y-m-d H:i:s", $mktDateSystem - (7 * 24 * 60 * 60));
                }

                $spoolRun = new SpoolRun();
                $spoolRun->resendEmails($dateResend, 1);

                $this->saveLog("resendEmails", "action", "Resending Emails", "c");

                $spoolWarnings = $spoolRun->getWarnings();

                if ($spoolWarnings !== false) {
                    foreach ($spoolWarnings as $warning) {
                        print("MAIL SPOOL WARNING: " . $warning . "\n");
                        $this->saveLog("resendEmails", "warning", "MAIL SPOOL WARNING: " . $warning);
                    }
                }

                $this->setExecutionResultMessage("DONE");
            } catch (Exception $e) {
                $context = [
                    "trace" => $e->getTraceAsString()
                ];
                Log::channel('taskScheduler:taskScheduler')->error($e->getMessage(), Bootstrap::context($context));
                $criteria = new Criteria("workflow");
                $criteria->clearSelectColumns();
                $criteria->addSelectColumn(ConfigurationPeer::CFG_UID);
                $criteria->add(ConfigurationPeer::CFG_UID, "Emails");
                $result = ConfigurationPeer::doSelectRS($criteria);
                $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                if ($result->next()) {
                    $this->setExecutionResultMessage("WARNING", "warning");
                    $message = "Emails won't be sent, but the cron will continue its execution";
                    if ($this->asynchronous === false) {
                        eprintln("  '-" . $message, "yellow");
                    }
                } else {
                    $this->setExecutionResultMessage("WITH ERRORS", "error");
                    if ($this->asynchronous === false) {
                        eprintln("  '-" . $e->getMessage(), "red");
                    }
                }

                $this->saveLog("resendEmails", "error", "Error Resending Emails: " . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * This unpause applications.
     * @param string $now
     */
    public function unpauseApplications($now)
    {
        $job = function() use($now) {
            $this->setExecutionMessage("Unpausing applications");
            try {
                $cases = new Cases();
                $cases->ThrowUnpauseDaemon($now, 1);

                $this->setExecutionResultMessage('DONE');
                $this->saveLog('unpauseApplications', 'action', 'Unpausing Applications');
            } catch (Exception $e) {
                $this->setExecutionResultMessage('WITH ERRORS', 'error');
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), 'red');
                }
                $this->saveLog('unpauseApplications', 'error', 'Error Unpausing Applications: ' . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * This calculate duration.
     */
    public function calculateDuration()
    {
        $job = function() {
            $this->setExecutionMessage("Calculating Duration");
            try {
                $appDelegation = new AppDelegation();
                $appDelegation->calculateDuration(1);
                $this->setExecutionResultMessage('DONE');
                $this->saveLog('calculateDuration', 'action', 'Calculating Duration');
            } catch (Exception $e) {
                $this->setExecutionResultMessage('WITH ERRORS', 'error');
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), 'red');
                }
                $this->saveLog('calculateDuration', 'error', 'Error Calculating Duration: ' . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * This calculate application duration.
     */
    public function calculateAppDuration()
    {
        $job = function() {
            $this->setExecutionMessage("Calculating Duration by Application");
            try {
                $application = new Application();
                $application->calculateAppDuration(1);
                $this->setExecutionResultMessage('DONE');
                $this->saveLog('calculateDurationByApp', 'action', 'Calculating Duration by Application');
            } catch (Exception $e) {
                $this->setExecutionResultMessage('WITH ERRORS', 'error');
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), 'red');
                }
                $this->saveLog('calculateDurationByApp', 'error', 'Error Calculating Duration: ' . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * Clean unused records in tables related to the Self-Service Value Based feature.
     */
    public function cleanSelfServiceTables()
    {
        $job = function() {
            try {
                // Start message
                $this->setExecutionMessage("Clean unused records for Self-Service Value Based feature");

                // Get Propel connection
                $cnn = Propel::getConnection(AppAssignSelfServiceValueGroupPeer::DATABASE_NAME);

                // Delete related rows and missing relations, criteria don't execute delete with joins
                $cnn->begin();
                $stmt = $cnn->createStatement();
                $stmt->executeQuery("DELETE " . AppAssignSelfServiceValueGroupPeer::TABLE_NAME . "
                             FROM " . AppAssignSelfServiceValueGroupPeer::TABLE_NAME . "
                             LEFT JOIN " . AppAssignSelfServiceValuePeer::TABLE_NAME . "
                             ON (" . AppAssignSelfServiceValueGroupPeer::ID . " = " . AppAssignSelfServiceValuePeer::ID . ")
                             WHERE " . AppAssignSelfServiceValuePeer::ID . " IS NULL");
                $cnn->commit();

                // Success message
                $this->setExecutionResultMessage("DONE");
            } catch (Exception $e) {
                $cnn->rollback();
                $this->setExecutionResultMessage("WITH ERRORS", "error");
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), "red");
                }
                $this->saveLog("ExecuteCleanSelfServiceTables", "error", "Error when try to clean self-service tables " . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * This execute plugins cron.
     * @return boolean
     */
    public function executePlugins()
    {
        $job = function() {
            $pathCronPlugins = PATH_CORE . 'bin' . PATH_SEP . 'plugins' . PATH_SEP;

            // Executing cron files in bin/plugins directory
            if (!is_dir($pathCronPlugins)) {
                return false;
            }

            if ($handle = opendir($pathCronPlugins)) {
                $this->setExecutionMessage('Executing cron files in bin/plugins directory in Workspace: ' . config("system.workspace"));
                while (false !== ($file = readdir($handle))) {
                    if (strpos($file, '.php', 1) && is_file($pathCronPlugins . $file)) {
                        $filename = str_replace('.php', '', $file);
                        $className = $filename . 'ClassCron';

                        // Execute custom cron function
                        $this->executeCustomCronFunction($pathCronPlugins . $file, $className);
                    }
                }
            }

            // Executing registered cron files
            // -> Get registered cron files
            $pluginRegistry = PluginRegistry::loadSingleton();
            $cronFiles = $pluginRegistry->getCronFiles();

            // -> Execute functions
            if (!empty($cronFiles)) {
                $this->setExecutionMessage('Executing registered cron files for Workspace: ' . config('system.workspace'));
                /**
                 * @var \ProcessMaker\Plugins\Interfaces\CronFile $cronFile
                 */
                foreach ($cronFiles as $cronFile) {
                    $path = PATH_PLUGINS . $cronFile->getNamespace() . PATH_SEP . 'bin' . PATH_SEP . $cronFile->getCronFile() . '.php';
                    if (file_exists($path)) {
                        $this->executeCustomCronFunction($path, $cronFile->getCronFile());
                    } else {
                        $this->setExecutionMessage('File ' . $cronFile->getCronFile() . '.php ' . 'does not exist.');
                    }
                }
            }
        };
        $this->runTask($job);
    }

    /**
     * This execute custom cron function.
     * @param string $pathFile
     * @param string $className
     */
    public function executeCustomCronFunction($pathFile, $className)
    {
        include_once $pathFile;

        $plugin = new $className();

        if (method_exists($plugin, 'executeCron')) {
            $arrayCron = unserialize(trim(@file_get_contents(PATH_DATA . "cron")));
            $arrayCron["processcTimeProcess"] = 60; //Minutes
            $arrayCron["processcTimeStart"] = time();
            @file_put_contents(PATH_DATA . "cron", serialize($arrayCron));

            //Try to execute Plugin Cron. If there is an error then continue with the next file
            $this->setExecutionMessage("\n--- Executing cron file: $pathFile");
            try {
                $plugin->executeCron();
                $this->setExecutionResultMessage('DONE');
            } catch (Exception $e) {
                $this->setExecutionResultMessage('FAILED', 'error');
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), 'red');
                }
                $this->saveLog('executePlugins', 'error', 'Error executing cron file: ' . $pathFile . ' - ' . $e->getMessage());
            }
        }
    }

    /**
     * This fills the report by user.
     * @param string $dateInit
     * @param string $dateFinish
     * @return boolean
     */
    public function fillReportByUser($dateInit, $dateFinish)
    {
        if ($dateInit == null) {
            if ($this->asynchronous === false) {
                eprintln("You must enter the starting date.", "red");
                eprintln('Example: +init-date"YYYY-MM-DD HH:MM:SS" +finish-date"YYYY-MM-DD HH:MM:SS"', "red");
            }
            if ($this->asynchronous === true) {
                $message = 'You must enter the starting date. Example: +init-date"YYYY-MM-DD HH:MM:SS" +finish-date"YYYY-MM-DD HH:MM:SS"';
                Log::channel('taskScheduler:taskScheduler')->info($message, Bootstrap::context($context));
            }
            return false;
        }
        $job = function() use($dateInit, $dateFinish) {
            try {

                $dateFinish = ($dateFinish != null) ? $dateFinish : date("Y-m-d H:i:s");

                $appCacheView = new AppCacheView();
                $appCacheView->setPathToAppCacheFiles(PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP);
                $this->setExecutionMessage("Calculating data to fill the 'User Reporting'...");
                $appCacheView->fillReportByUser($dateInit, $dateFinish);
                setExecutionResultMessage("DONE");
            } catch (Exception $e) {
                $this->setExecutionResultMessage("WITH ERRORS", "error");
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), "red");
                }
                $this->saveLog("fillReportByUser", "error", "Error in fill report by user: " . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * This fills the report by process.
     * @param string $dateInit
     * @param string $dateFinish
     * @return boolean
     */
    public function fillReportByProcess($dateInit, $dateFinish)
    {
        if ($dateInit == null) {
            if ($this->asynchronous === false) {
                eprintln("You must enter the starting date.", "red");
                eprintln('Example: +init-date"YYYY-MM-DD HH:MM:SS" +finish-date"YYYY-MM-DD HH:MM:SS"', "red");
            }
            if ($this->asynchronous === true) {
                $message = 'You must enter the starting date. Example: +init-date"YYYY-MM-DD HH:MM:SS" +finish-date"YYYY-MM-DD HH:MM:SS"';
                Log::channel('taskScheduler:taskScheduler')->info($message, Bootstrap::context());
            }
            return false;
        }
        $job = function() use($dateInit, $dateFinish) {
            try {

                $dateFinish = ($dateFinish != null) ? $dateFinish : date("Y-m-d H:i:s");
                $appcv = new AppCacheView();
                $appcv->setPathToAppCacheFiles(PATH_METHODS . 'setup' . PATH_SEP . 'setupSchemas' . PATH_SEP);

                $this->setExecutionMessage("Calculating data to fill the 'Process Reporting'...");
                $appcv->fillReportByProcess($dateInit, $dateFinish);
                $this->setExecutionResultMessage("DONE");
            } catch (Exception $e) {
                $this->setExecutionResultMessage("WITH ERRORS", "error");
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), "red");
                }
                $this->saveLog("fillReportByProcess", "error", "Error in fill report by process: " . $e->getMessage());
            }
        };
        $this->runTask($job);
    }

    /**
     * This execute ldap cron.
     * @param boolean $debug
     */
    public function ldapcron($debug)
    {
        $job = function() use($debug) {
            require_once(PATH_HOME . 'engine' . PATH_SEP . 'methods' . PATH_SEP . 'services' . PATH_SEP . 'ldapadvanced.php');
            $ldapadvancedClassCron = new ldapadvancedClassCron();
            $ldapadvancedClassCron->executeCron($debug);
        };
        $this->runTask($job);
    }

    /**
     * This execute the sending of notifications.
     */
    function sendNotifications()
    {
        $job = function() {
            try {
                $this->setExecutionMessage("Resending Notifications");
                $this->setExecutionResultMessage("PROCESSING");
                $notQueue = new NotificationQueue();
                $notQueue->checkIfCasesOpenForResendingNotification();
                $notificationsAndroid = $notQueue->loadStatusDeviceType('pending', 'android');
                if ($notificationsAndroid) {
                    $this->setExecutionMessage("|-- Send Android's Notifications");
                    $n = 0;
                    foreach ($notificationsAndroid as $key => $item) {
                        $notification = new PushMessageAndroid();
                        $notification->setSettingNotification();
                        $notification->setDevices(unserialize($item['DEV_UID']));
                        $response['android'] = $notification->send($item['NOT_MSG'], unserialize($item['NOT_DATA']));
                        $notQueue = new NotificationQueue();
                        $notQueue->changeStatusSent($item['NOT_UID']);
                        $n += $notification->getNumberDevices();
                    }
                    $this->setExecutionResultMessage("Processed $n");
                }
                $notificationsApple = $notQueue->loadStatusDeviceType('pending', 'apple');
                if ($notificationsApple) {
                    $this->setExecutionMessage("|-- Send Apple Notifications");
                    $n = 0;
                    foreach ($notificationsApple as $key => $item) {
                        $notification = new PushMessageIOS();
                        $notification->setSettingNotification();
                        $notification->setDevices(unserialize($item['DEV_UID']));
                        $response['apple'] = $notification->send($item['NOT_MSG'], unserialize($item['NOT_DATA']));
                        $notQueue = new NotificationQueue();
                        $notQueue->changeStatusSent($item['NOT_UID']);
                        $n += $notification->getNumberDevices();
                    }
                    $this->setExecutionResultMessage("Processed $n");
                }
            } catch (Exception $e) {
                $this->setExecutionResultMessage("WITH ERRORS", "error");
                if ($this->asynchronous === false) {
                    eprintln("  '-" . $e->getMessage(), "red");
                }
                $this->saveLog("ExecuteSendNotifications", "error", "Error when sending notifications " . $e->getMessage());
            }
        };
        $this->runTask($job);
    }
}
