<?php

use App\Jobs\EmailEvent;
use Faker\Factory;
use Illuminate\Support\Facades\Queue;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppThread;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use ProcessMaker\Util\WsMessageResponse;
use Tests\TestCase;

class WsBaseTest extends TestCase
{
    /**
     * Constructor of the class.
     * 
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        Application::query()->truncate();
        AppThread::query()->truncate();
        Delegation::query()->truncate();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Create an application.
     * 
     * @param int $applicationNumber
     * @return \stdClass
     */
    private function createNewCase($applicationNumber = null)
    {
        if (empty($applicationNumber)) {
            $faker = Factory::create();
            $applicationNumber = $faker->unique()->numberBetween(1, 10000000);
        }
        $userUid = G::generateUniqueID();
        $processUid = G::generateUniqueID();
        $taskUid = G::generateUniqueID();
        $applicationUid = G::generateUniqueID();

        $appData = [
            'SYS_LANG' => 'en',
            'SYS_SKIN' => 'neoclassic',
            'SYS_SYS' => 'workflow',
            'APPLICATION' => G::generateUniqueID(),
            'PROCESS' => G::generateUniqueID(),
            'TASK' => '',
            'INDEX' => 2,
            'USER_LOGGED' => $userUid,
            'USR_USERNAME' => 'admin',
            'APP_NUMBER' => $applicationNumber,
            'PIN' => '97ZN'
        ];

        $user = factory(User::class)->create([
            'USR_UID' => $userUid
        ]);

        $process = factory(Process::class)->create([
            'PRO_UID' => $processUid
        ]);

        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $application = factory(Application::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $applicationUid,
            'APP_NUMBER' => $applicationNumber,
            'APP_DATA' => serialize($appData)
        ]);

        $result = new stdClass();
        $result->userUid = $userUid;
        $result->processUid = $processUid;
        $result->taskUid = $taskUid;
        $result->applicationUid = $applicationUid;
        $result->applicationNumber = $applicationNumber;
        $result->appData = $appData;
        $result->user = $user;
        $result->process = $process;
        $result->task = $task;
        $result->application = $application;
        return $result;
    }

    /**
     * Create a email server configuration.
     * 
     * @return ProcessMaker\Model\EmailServerModel;
     */
    private function createEmailServer()
    {
        $passwordEnv = env('emailAccountPassword');
        $password = G::encrypt("hash:" . $passwordEnv, 'EMAILENCRYPT');
        $emailServer = factory(EmailServerModel::class)->create([
            'MESS_ENGINE' => env('emailEngine'),
            'MESS_SERVER' => env('emailServer'),
            'MESS_PORT' => env('emailPort'),
            'MESS_INCOMING_SERVER' => '',
            'MESS_INCOMING_PORT' => 0,
            'MESS_RAUTH' => 1,
            'MESS_ACCOUNT' => env('emailAccount'),
            'MESS_PASSWORD' => $password,
            'MESS_FROM_MAIL' => env('emailAccount'),
            'MESS_FROM_NAME' => '',
            'SMTPSECURE' => 'ssl',
            'MESS_TRY_SEND_INMEDIATLY' => 1,
            'MAIL_TO' => $password,
            'MESS_DEFAULT' => 1,
        ]);
        return $emailServer;
    }

    /**
     * Create a new template for send email.
     * 
     * @param string $proUid
     * @param string $usrUid
     * @return \ProcessMaker\Model\ProcessFiles
     */
    private function createTemplate($proUid, $usrUid)
    {
        $template = factory(\ProcessMaker\Model\ProcessFiles::class)->create([
            'PRO_UID' => $proUid,
            'USR_UID' => $usrUid,
            'PRF_PATH' => '/'
        ]);
        return $template;
    }

    /**
     * Create empty html.
     * 
     * @param string $content
     * @return string
     */
    private function createDefaultHtmlContent($content = '')
    {
        $string = ''
                . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
                . '<html>'
                . '<head>'
                . '</head>'
                . '<body>'
                . $content
                . '</body>'
                . '</html>';
        return $string;
    }

    /**
     * This represents a collection of "messageType" for queue elements.
     */
    public function messageTypesWithQueue()
    {
        return [
            [WsBase::MESSAGE_TYPE_EMAIL_EVENT],
            [WsBase::MESSAGE_TYPE_PM_FUNCTION],
        ];
    }

    /**
     * This represents a collection of "messageType" for no queueable elements.
     */
    public function messageTypesWithoutQueue()
    {
        return [
            [WsBase::MESSAGE_TYPE_ACTIONS_BY_EMAIL],
            [WsBase::MESSAGE_TYPE_CASE_NOTE],
            [WsBase::MESSAGE_TYPE_EXTERNAL_REGISTRATION],
            [WsBase::MESSAGE_TYPE_RETRIEVE_PASSWORD],
            [WsBase::MESSAGE_TYPE_SOAP],
            [WsBase::MESSAGE_TYPE_TASK_NOTIFICATION],
            [WsBase::MESSAGE_TYPE_TEST_EMAIL],
        ];
    }

    /**
     * This should send an email of types elements to the work queue jobs.
     * Queue-fake has been used, see more at: https://laravel.com/docs/5.7/mocking#queue-fake
     * @test
     * @dataProvider messageTypesWithQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_send_an_sendMessage_with_queue_jobs($messageType)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = $emailServer->MESS_ACCOUNT;
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = $emailServer->toArray();
        $gmail = 0;
        $appMsgType = $messageType;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertPushed(EmailEvent::class);
    }

    /**
     * This should send an email of types elements without work queue jobs.
     * Queue-fake has been used, see more at: https://laravel.com/docs/5.7/mocking#queue-fake
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_execute_an_sendMessage_without_queue_jobs($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = "";
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = $emailServer->toArray();
        $gmail = 0;
        $appMsgType = $messageTypes;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertNotPushed(EmailEvent::class);
    }

    /**
     * It should send an sendMessage with queue jobs and empty config parameter.
     * @test
     * @dataProvider messageTypesWithQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_send_an_sendMessage_with_queue_jobs_and_empty_config_parameter($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = $emailServer->MESS_ACCOUNT;
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = []; //with empty configuration
        $gmail = 0;
        $appMsgType = $messageTypes;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertPushed(EmailEvent::class);
    }

    /**
     * It should send an sendMessage without queue jobs and empty config parameter.
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_send_an_sendMessage_without_queue_jobs_and_empty_config_parameter($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = "";
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = []; //with empty configuration
        $gmail = 0;
        $appMsgType = $messageTypes;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertNotPushed(EmailEvent::class);
    }

    /**
     * It should send an sendMessage with queue jobs and config parameter like id.
     * @test
     * @dataProvider messageTypesWithQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_send_an_sendMessage_with_queue_jobs_and_config_parameter_like_id($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = $emailServer->MESS_ACCOUNT;
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = $emailServer->MESS_UID; //With a valid Email Server Uid
        $gmail = 0;
        $appMsgType = $messageTypes;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertPushed(EmailEvent::class);
    }

    /**
     * It should send an sendMessage without queue jobs and config parameter like id.
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_send_an_sendMessage_without_queue_jobs_and_config_parameter_like_id($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = "";
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = $emailServer->MESS_UID; //With a valid Email Server Uid
        $gmail = 0;
        $appMsgType = $messageTypes;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertNotPushed(EmailEvent::class);
    }

    /**
     * It should send an sendMessage without queue jobs and gmail parameter like one.
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers \WsBase::sendMessage
     */
    public function it_should_send_an_sendMessage_without_queue_jobs_and_gmail_parameter_like_one($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $emailServer->MESS_ACCOUNT;
        $to = "";
        $cc = "";
        $bcc = "";
        $subject = "test";
        $templateName = basename($template->PRF_PATH);
        $appFields = [];
        $attachment = [];
        $showMessage = true;
        $delIndex = 0;
        $config = $emailServer->MESS_UID;
        $gmail = 1; //GMail flag enabled
        $appMsgType = $messageTypes;

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $wsBase = new WsBase();
        $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);
        Queue::assertNotPushed(EmailEvent::class);
    }

    /**
     * Test that the casesList method returns the case title value
     *
     * @test
     * @covers \WsBase::caseList
     */
    public function it_should_test_that_the_cases_list_method_returns_the_case_title()
    {
        //Create the user factory
        $user = factory(User::class)->create();

        //Create the application factory
        $application1 = factory(Application::class)->create(
                [
                    'APP_STATUS' => 'TO_DO',
                    'APP_TITLE' => 'Title1'
                ]
        );
        $application2 = factory(Application::class)->create(
                [
                    'APP_STATUS' => 'DRAFT',
                    'APP_TITLE' => 'Title2'
                ]
        );

        //Create the delegation factory
        $delegation1 = factory(Delegation::class)->create(
                [
                    'USR_UID' => $user->USR_UID,
                    'DEL_THREAD_STATUS' => 'OPEN',
                    'DEL_FINISH_DATE' => null,
                    'APP_NUMBER' => $application1->APP_NUMBER
                ]
        );
        $delegation2 = factory(Delegation::class)->create(
                [
                    'USR_UID' => $user->USR_UID,
                    'DEL_THREAD_STATUS' => 'OPEN',
                    'DEL_FINISH_DATE' => null,
                    'APP_NUMBER' => $application2->APP_NUMBER
                ]
        );

        //Create app thread factory
        factory(AppThread::class)->create(
                [
                    'APP_THREAD_STATUS' => 'OPEN',
                    'APP_UID' => $delegation1->APP_UID
                ]
        );
        factory(AppThread::class)->create(
                [
                    'APP_THREAD_STATUS' => 'OPEN',
                    'APP_UID' => $delegation2->APP_UID
                ]
        );

        //Instance the object
        $wsBase = new WsBase();
        //Call the caseList method
        $res = $wsBase->caseList($user->USR_UID);

        //Assert the result has 2 rows
        $this->assertCount(2, $res);

        //Assert the status of the case
        $this->assertTrue('TO_DO' || 'DRAFT' == $res[0]['status']);
        $this->assertTrue('TO_DO' || 'DRAFT' == $res[1]['status']);

        //Assert the case title is returned
        $this->assertTrue($application1->APP_TITLE || $application2->APP_TITLE == $res[0]['name']);
        $this->assertTrue($application1->APP_TITLE || $application2->APP_TITLE == $res[1]['name']);
    }

    /**
     * Test the casesList method when the result is empty
     *
     * @test
     * @covers \WsBase::caseList
     */
    public function it_should_test_the_cases_list_method_when_there_are_no_results()
    {
        //Create the user factory
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        //Create the application factory
        $application1 = factory(Application::class)->create(
                [
                    'APP_STATUS' => 'TO_DO',
                    'APP_TITLE' => 'Title1'
                ]
        );
        $application2 = factory(Application::class)->create(
                [
                    'APP_STATUS' => 'DRAFT',
                    'APP_TITLE' => 'Title2'
                ]
        );

        //Create the delegation factory
        $delegation1 = factory(Delegation::class)->create(
                [
                    'USR_UID' => $user1->USR_UID,
                    'DEL_THREAD_STATUS' => 'OPEN',
                    'DEL_FINISH_DATE' => null,
                    'APP_NUMBER' => $application1->APP_NUMBER
                ]
        );
        $delegation2 = factory(Delegation::class)->create(
                [
                    'USR_UID' => $user1->USR_UID,
                    'DEL_THREAD_STATUS' => 'OPEN',
                    'DEL_FINISH_DATE' => null,
                    'APP_NUMBER' => $application2->APP_NUMBER
                ]
        );

        //Create app thread factory
        factory(AppThread::class)->create(
                [
                    'APP_THREAD_STATUS' => 'OPEN',
                    'APP_UID' => $delegation1->APP_UID
                ]
        );
        factory(AppThread::class)->create(
                [
                    'APP_THREAD_STATUS' => 'OPEN',
                    'APP_UID' => $delegation2->APP_UID
                ]
        );

        //Instance the object
        $wsBase = new WsBase();

        //Call the caseList method
        $res = $wsBase->caseList($user2->USR_UID);

        //Assert the result his empty
        $this->assertEmpty($res);
    }

    /**
     * This test ensures obtaining the email configuration with all fields.
     * @test
     * @covers \WsBase::sendMessage()
     */
    public function it_should_get_email_configuration()
    {
        $faker = Factory::create();

        //data
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->applicationUid;
        $from = $faker->email;
        $to = "";
        $cc = "";
        $bcc = "";
        $subject = $faker->title;
        $templateName = basename($template->PRF_PATH);
        $appFields = [
            'var1' => $faker->numberBetween(1, 100)
        ];

        $wsBase = new WsBase();
        $result = $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields);

        //assertions
        $this->assertInstanceOf(WsMessageResponse::class, $result);
    }
}
