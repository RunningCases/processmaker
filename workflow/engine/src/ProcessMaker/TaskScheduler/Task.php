<?php

namespace ProcessMaker\TaskScheduler;

use App\Jobs\TaskScheduler;
use Bootstrap;
use ConfigurationPeer;
use Criteria;
use Exception;
use G;
use Illuminate\Support\Facades\Log;
use ProcessMaker\Core\JobsManager;
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
        $len = strlen($message);
        $linesize = 60;
        $rOffset = $linesize - $len;

        eprint("* $message");

        for ($i = 0; $i < $rOffset; $i++) {
            eprint('.');
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
        eprintln("[$message]", $color);
    }

    /**
     * Save logs.
     * @param string $source
     * @param string $type
     * @param string $description
     */
    public function saveLog(string $source, string $type, string $description)
    {
        $context = [
            'type' => $type,
            'description' => $description
        ];
        Log::channel('taskScheduler:taskScheduler')->info($source, Bootstrap::context($context));
        try {
            G::verifyPath(PATH_DATA . "log" . PATH_SEP, true);
            G::log("| $this->object | " . $source . " | $type | " . $description, PATH_DATA);
        } catch (Exception $e) {
            Log::channel('taskScheduler:taskScheduler')->error($e->getMessage(), Bootstrap::context($context));
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
                    eprintln("  '-" . $message, "yellow");
                } else {
                    $this->setExecutionResultMessage("WITH ERRORS", "error");
                    eprintln("  '-" . $e->getMessage(), "red");
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
                $cases = new \Cases();
                $cases->ThrowUnpauseDaemon($now, 1);

                $this->setExecutionResultMessage('DONE');
                $this->saveLog('unpauseApplications', 'action', 'Unpausing Applications');
            } catch (Exception $e) {
                $this->setExecutionResultMessage('WITH ERRORS', 'error');
                eprintln("  '-" . $e->getMessage(), 'red');
                $this->saveLog('unpauseApplications', 'error', 'Error Unpausing Applications: ' . $e->getMessage());
            }
        };
        $this->runTask($job);
    }
}
