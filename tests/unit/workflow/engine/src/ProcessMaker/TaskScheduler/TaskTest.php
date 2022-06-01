<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\TaskScheduler;

use App\Jobs\TaskScheduler;
use Faker\Factory;
use Illuminate\Support\Facades\Queue;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppThread;
use ProcessMaker\Model\Delegation;
use ProcessMaker\TaskScheduler\Task;
use Tests\TestCase;

/**
 * Class TaskTest
 *
 * @coversDefaultClass \ProcessMaker\TaskScheduler\Task
 */
class TaskTest extends TestCase
{
    private $faker;

    /**
     * Method setUp.
     */
    protected function setUp()
    {
        if (version_compare(phpversion(), 7.3, '>') ) {
            $this->markTestSkipped('The changes in third party are not available');
        }
        parent::setUp();
        $this->faker = Factory::create();
        Delegation::truncate();
        AppThread::truncate();
        Application::truncate();
    }

    /**
     * Method tearDown.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test synchronous asynchronous cases.
     */
    public function asynchronousCases()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * This test verify the setExecutionMessage method.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::setExecutionMessage()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_setExecutionMessage_method($asynchronous)
    {
        $task = new Task($asynchronous, '');
        $message = $this->faker->paragraph;

        ob_start();
        $task->setExecutionMessage($message);
        $printing = ob_get_clean();

        //assert if message is contained in output buffer
        if ($asynchronous === false) {
            $this->assertRegExp("/{$message}/", $printing);
        }
        //assert if not showing message
        if ($asynchronous === true) {
            $this->assertEmpty($printing);
        }
    }

    /**
     * This test verify the setExecutionResultMessage method.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::setExecutionResultMessage()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_setExecutionResultMessage_method($asynchronous)
    {
        $task = new Task($asynchronous, '');
        $message = $this->faker->paragraph;

        ob_start();
        $task->setExecutionResultMessage($message, 'error');
        $printing = ob_get_clean();
        //assert if message is contained in output buffer
        if ($asynchronous === false) {
            $this->assertRegExp("/{$message}/", $printing);
        }
        //assert if not showing message
        if ($asynchronous === true) {
            $this->assertEmpty($printing);
        }

        ob_start();
        $task->setExecutionResultMessage($message, 'info');
        $printing = ob_get_clean();
        //assert if message is contained in output buffer
        if ($asynchronous === false) {
            $this->assertRegExp("/{$message}/", $printing);
        }
        //assert if not showing message
        if ($asynchronous === true) {
            $this->assertEmpty($printing);
        }

        ob_start();
        $task->setExecutionResultMessage($message, 'warning');
        $printing = ob_get_clean();
        //assert if message is contained in output buffer
        if ($asynchronous === false) {
            $this->assertRegExp("/{$message}/", $printing);
        }
        //assert if not showing message
        if ($asynchronous === true) {
            $this->assertEmpty($printing);
        }
    }

    /**
     * This test verify the saveLog method.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::saveLog()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_saveLog_method($asynchronous)
    {
        $task = new Task(false, '');
        $task->saveLog('', '', $this->faker->paragraph);
        $file = PATH_DATA . "log/cron.log";
        $this->assertFileExists($file);

        if ($asynchronous === false) {
            $description = $this->faker->paragraph;
            $task = new Task($asynchronous, '');
            $task->saveLog('', '', $description);
            $contentLog = file_get_contents($file);

            $this->assertRegExp("/{$description}/", $contentLog);
        }
        if ($asynchronous === true) {
            $description = $this->faker->paragraph;
            $task = new Task($asynchronous, '');
            $task->saveLog('', '', $description);
            $contentLog = file_get_contents($file);

            $this->assertNotRegExp("/{$description}/", $contentLog);
        }
    }

    /**
     * This test verify the resendEmails activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::resendEmails()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_resendEmails_method($asynchronous)
    {
        $task = new Task($asynchronous, '');
        $dateSystem = $this->faker->date();

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->resendEmails('', $dateSystem);
            $printing = ob_get_clean();
            $this->assertRegExp("/DONE/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->resendEmails('', $dateSystem);
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the unpauseApplications activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::unpauseApplications()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_unpauseApplications_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->unpauseApplications('');
            $printing = ob_get_clean();
            $this->assertRegExp("/DONE/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->unpauseApplications('');
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the calculateDuration activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::calculateDuration()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_calculateDuration_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->calculateDuration();
            $printing = ob_get_clean();
            $this->assertRegExp("/DONE/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->calculateDuration();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the calculateDuration activity method for synchronous and asynchronous execution.
     * @covers ProcessMaker\TaskScheduler\Task::executeCaseSelfService()
     * @test
     * @dataProvider asynchronousCases
     */
    public function it_should_test_unassignedcase($asynchronous)
    {
        $task = new Task($asynchronous, '');

        // Assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->executeCaseSelfService();
            $printing = ob_get_clean();
            $this->assertRegExp("/Unassigned case/", $printing);
        }

        // Assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->executeCaseSelfService();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the calculateAppDuration activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::calculateAppDuration()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_calculateAppDuration_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->calculateAppDuration();
            $printing = ob_get_clean();
            $this->assertRegExp("/DONE/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->calculateAppDuration();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the cleanSelfServiceTables activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::cleanSelfServiceTables()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_cleanSelfServiceTables_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->cleanSelfServiceTables();
            $printing = ob_get_clean();
            $this->assertRegExp("/DONE/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->cleanSelfServiceTables();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the executePlugins activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::executePlugins()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_executePlugins_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->executePlugins();
            $printing = ob_get_clean();
            $this->assertRegExp("/plugins/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->executePlugins();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the fillReportByUser activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::fillReportByUser()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_fillReportByUser_method($asynchronous)
    {
        $task = new Task($asynchronous, '');
        $dateInit = $this->faker->dateTime->format("Y-m-d H:i:s");
        $dateFinish = $this->faker->dateTime->format("Y-m-d H:i:s");

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->fillReportByUser($dateInit, $dateFinish);
            $printing = ob_get_clean();
            $this->assertRegExp("/User Reporting/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->fillReportByUser($dateInit, $dateFinish);
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the fillReportByProcess activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::fillReportByProcess()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_fillReportByProcess_method($asynchronous)
    {
        $task = new Task($asynchronous, '');
        $dateInit = $this->faker->dateTime->format("Y-m-d H:i:s");
        $dateFinish = $this->faker->dateTime->format("Y-m-d H:i:s");

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->fillReportByProcess($dateInit, $dateFinish);
            $printing = ob_get_clean();
            $this->assertRegExp("/Process Reporting/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->fillReportByProcess($dateInit, $dateFinish);
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the ldapcron activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::ldapcron()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_ldapcron_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->ldapcron(false);
            $printing = ob_get_clean();
            $this->assertRegExp("/\+---/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->ldapcron(false);
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the sendNotifications activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::sendNotifications()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_sendNotifications_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->sendNotifications();
            $printing = ob_get_clean();
            $this->assertRegExp("/Resending Notifications/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->sendNotifications();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the actionsByEmailResponse activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::actionsByEmailResponse()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_actionsByEmailResponse_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->actionsByEmailResponse();
            $printing = ob_get_clean();
            $this->assertEmpty($printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->actionsByEmailResponse();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * This test verify the messageeventcron activity method for synchronous and asynchronous execution.
     * @test 
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::messageeventcron()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_messageeventcron_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->messageeventcron();
            $printing = ob_get_clean();
            $this->assertRegExp("/Message-Events/", $printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->messageeventcron();
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * Tests the timerEventCron method with jobs in the task scheduler 
     * 
     * @test
     * @covers ProcessMaker\TaskScheduler\Task::timerEventCron()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_the_timer_event_cron_method($asynchronous)
    {
        //Creates a new task
        $task = new Task($asynchronous, '');
        //Sets the currect date
        $date = date('Y-m-d H:i:s');
        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            //Calls the timerEventCron method 
            $task->timerEventCron($date, true);
            //Gets the result
            $printing = ob_get_clean();
            //Asserts the result is printing that there is no exisiting records to continue a case in the determined date
            $this->assertRegExp('/There are no records to start new cases, on date "' . $date . '/', $printing);
        }
        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->timerEventCron($date, true);
            Queue::assertPushed(TaskScheduler::class);
        }
    }

    /**
     * Tests the webEntriesCron method with jobs in the task scheduler
     *
     * @test
     * @covers ProcessMaker\TaskScheduler\Task::runTask()
     * @covers ProcessMaker\TaskScheduler\Task::webEntriesCron()
     * @dataProvider asynchronousCases
     */
    public function it_should_test_webEntriesCron_method($asynchronous)
    {
        $task = new Task($asynchronous, '');

        //assert synchronous for cron file
        if ($asynchronous === false) {
            ob_start();
            $task->webEntriesCron();
            $printing = ob_get_clean();
            $this->assertEmpty($printing);
        }

        //assert asynchronous for job process
        if ($asynchronous === true) {
            Queue::fake();
            Queue::assertNothingPushed();
            $task->webEntriesCron();
            Queue::assertPushed(TaskScheduler::class);
        }
    }
}
