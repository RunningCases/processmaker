<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\TaskScheduler;

use App\Jobs\TaskScheduler;
use Faker\Factory;
use Illuminate\Support\Facades\Queue;
use ProcessMaker\TaskScheduler\Task;
use Tests\TestCase;

class TaskTest extends TestCase
{
    private $faker;

    /**
     * Method setUp.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
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
        $this->assertRegExp("/{$message}/", $printing);
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
        $this->assertRegExp("/{$message}/", $printing);

        ob_start();
        $task->setExecutionResultMessage($message, 'info');
        $printing = ob_get_clean();
        //assert if message is contained in output buffer
        $this->assertRegExp("/{$message}/", $printing);

        ob_start();
        $task->setExecutionResultMessage($message, 'warning');
        $printing = ob_get_clean();
        //assert if message is contained in output buffer
        $this->assertRegExp("/{$message}/", $printing);
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
        $task = new Task($asynchronous, '');
        $description = $this->faker->paragraph;

        $task->saveLog('', '', $description);
        $file = PATH_DATA . "log/cron.log";
        $this->assertFileExists($file);
        $contentLog = file_get_contents($file);
        $this->assertRegExp("/{$description}/", $contentLog);
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
}
