<?php

use App\Jobs\EmailEvent;
use Faker\Factory;
use Illuminate\Support\Facades\Queue;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppDelay;
use ProcessMaker\Model\AppThread;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use ProcessMaker\Model\UserReporting;
use Tests\TestCase;

/**
 * Class WsBaseTest
 *
 * @coversDefaultClass WsBase
 */
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
        Application::query()->truncate();
        AppThread::query()->truncate();
        Delegation::query()->truncate();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
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
        $userUid = G::generateUniqueID();
        $processUid = G::generateUniqueID();
        $applicationUid = G::generateUniqueID();
        if (empty($applicationNumber)) {
            $faker = Factory::create();
            $applicationNumber = $faker->unique()->numberBetween(1, 10000000);
        }

        $appData = [
            'SYS_LANG' => 'en',
            'SYS_SKIN' => 'neoclassic',
            'SYS_SYS' => 'workflow',
            'APPLICATION' => $applicationUid,
            'PROCESS' => $processUid,
            'TASK' => '',
            'INDEX' => 2,
            'USER_LOGGED' => $userUid,
            'USR_USERNAME' => 'admin',
            'APP_NUMBER' => $applicationNumber,
            'PIN' => '97ZN'
        ];

        $user = User::factory()->create([
            'USR_UID' => $userUid
        ]);

        $process = Process::factory()->create([
            'PRO_UID' => $processUid
        ]);

        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $applicationUid,
            'APP_NUMBER' => $applicationNumber,
            'APP_DATA' => serialize($appData)
        ]);

        $result = new stdClass();
        $result->application = $application;
        $result->user = $user;
        $result->process = $process;
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
        $emailServer = EmailServerModel::factory()->create([
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
        if (!file_exists(PATH_DB)) {
            mkdir(PATH_DB);
        }
        if (!file_exists(PATH_DATA_SITE)) {
            mkdir(PATH_DATA_SITE);
        }
        $data = file_get_contents(PATH_TRUNK . 'tests/resources/template.html');
        if (!file_exists(PATH_DATA_SITE . 'mailTemplates')) {
            mkdir(PATH_DATA_SITE . 'mailTemplates');
        }
        file_put_contents(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . 'template.html', $data);
        if (!file_exists(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $proUid)) {
            mkdir(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $proUid);
        }
        file_put_contents(PATH_DATA_SITE . 'mailTemplates' . PATH_SEP . $proUid . PATH_SEP . 'template.html', $data);
        $template = \ProcessMaker\Model\ProcessFiles::factory()->create([
            'PRO_UID' => $proUid,
            'USR_UID' => $usrUid,
            'PRF_PATH' => 'template.html'
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
     *
     * @test
     * @dataProvider messageTypesWithQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_send_an_sendMessage_with_queue_jobs($messageType)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     *
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_execute_an_sendMessage_without_queue_jobs($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     *
     * @test
     * @dataProvider messageTypesWithQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_send_an_sendMessage_with_queue_jobs_and_empty_config_parameter($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     *
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_send_an_sendMessage_without_queue_jobs_and_empty_config_parameter($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     *
     * @test
     * @dataProvider messageTypesWithQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_send_an_sendMessage_with_queue_jobs_and_config_parameter_like_id($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     *
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_send_an_sendMessage_without_queue_jobs_and_config_parameter_like_id($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     *
     * @test
     * @dataProvider messageTypesWithoutQueue
     * @covers WsBase::sendMessage()
     */
    public function it_should_send_an_sendMessage_without_queue_jobs_and_gmail_parameter_like_one($messageTypes)
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
     * @covers WsBase::caseList()
     */
    public function it_should_test_that_the_cases_list_method_returns_the_case_title()
    {
        //Create the user factory
        $user = User::factory()->create();

        //Create the application factory
        $application1 = Application::factory()->create(
            [
                'APP_STATUS' => 'TO_DO',
                'APP_TITLE' => 'Title1'
            ]
        );
        $application2 = Application::factory()->create(
            [
                'APP_STATUS' => 'DRAFT',
                'APP_TITLE' => 'Title2'
            ]
        );

        //Create the delegation factory
        $delegation1 = Delegation::factory()->create(
            [
                'USR_UID' => $user->USR_UID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_FINISH_DATE' => null,
                'APP_NUMBER' => $application1->APP_NUMBER
            ]
        );
        $delegation2 = Delegation::factory()->create(
            [
                'USR_UID' => $user->USR_UID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_FINISH_DATE' => null,
                'APP_NUMBER' => $application2->APP_NUMBER
            ]
        );

        //Create app thread factory
        AppThread::factory()->create(
            [
                'APP_THREAD_STATUS' => 'OPEN',
                'APP_UID' => $delegation1->APP_UID
            ]
        );
        AppThread::factory()->create(
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
     * @covers WsBase::caseList()
     */
    public function it_should_test_the_cases_list_method_when_there_are_no_results()
    {
        //Create the user factory
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //Create the application factory
        $application1 = Application::factory()->create(
            [
                'APP_STATUS' => 'TO_DO',
                'APP_TITLE' => 'Title1'
            ]
        );
        $application2 = Application::factory()->create(
            [
                'APP_STATUS' => 'DRAFT',
                'APP_TITLE' => 'Title2'
            ]
        );

        //Create the delegation factory
        $delegation1 = Delegation::factory()->create(
            [
                'USR_UID' => $user1->USR_UID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_FINISH_DATE' => null,
                'APP_NUMBER' => $application1->APP_NUMBER
            ]
        );
        $delegation2 = Delegation::factory()->create(
            [
                'USR_UID' => $user1->USR_UID,
                'DEL_THREAD_STATUS' => 'OPEN',
                'DEL_FINISH_DATE' => null,
                'APP_NUMBER' => $application2->APP_NUMBER
            ]
        );

        //Create app thread factory
        AppThread::factory()->create(
            [
                'APP_THREAD_STATUS' => 'OPEN',
                'APP_UID' => $delegation1->APP_UID
            ]
        );
        AppThread::factory()->create(
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
     * @covers WsBase::sendMessage()
     */
    public function it_should_get_email_configuration()
    {
        $faker = Factory::create();

        //data
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
        $this->assertInstanceOf(WsResponse::class, $result);
    }

    /**
     * This test ensures the response when the default configuration does not exist.
     * @test
     * @covers WsBase::sendMessage
     */
    public function it_should_test_an_send_message_without_default_configuration()
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
        $config = [];
        $gmail = 0;
        $appMsgType = '';

        //for empty configuration
        EmailServerModel::truncate();

        $wsBase = new WsBase();
        $result = $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);

        //assertions
        $this->assertObjectHasAttribute('status_code', $result);
        $this->assertObjectHasAttribute('message', $result);
        $this->assertObjectHasAttribute('timestamp', $result);
        $this->assertObjectHasAttribute('extraParams', $result);
        $this->assertEquals(29, $result->status_code);
    }

    /**
     * This test ensures the response when the template is not found.
     * @test
     * @covers WsBase::sendMessage
     */
    public function it_should_test_an_send_message_missing_template()
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = $case->application->APP_UID;
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
        $appMsgType = '';

        //for a missing template
        $templateName = 'MissingFile';
        G::rm_dir(PATH_DATA_SITE . 'mailTemplates');

        $wsBase = new WsBase();
        $result = $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);

        //assertions
        $this->assertObjectHasAttribute('status_code', $result);
        $this->assertObjectHasAttribute('message', $result);
        $this->assertObjectHasAttribute('timestamp', $result);
        $this->assertObjectHasAttribute('extraParams', $result);
        $this->assertEquals(28, $result->status_code);
    }

    /**
     * This test ensures the response when there is an exception.
     * @test
     * @covers WsBase::sendMessage
     */
    public function it_should_test_an_send_message_when_appears_an_exception()
    {
        //data
        $emailServer = $this->createEmailServer();
        $case = $this->createNewCase();
        $template = $this->createTemplate($case->process->PRO_UID, $case->user->USR_UID);

        //parameters
        $appUid = null;
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
        $appMsgType = '';

        $wsBase = new WsBase();
        $result = $wsBase->sendMessage($appUid, $from, $to, $cc, $bcc, $subject, $templateName, $appFields, $attachment, $showMessage, $delIndex, $config, $gmail, $appMsgType);

        //assertions
        $this->assertObjectHasAttribute('status_code', $result);
        $this->assertObjectHasAttribute('message', $result);
        $this->assertObjectHasAttribute('timestamp', $result);
        $this->assertObjectHasAttribute('extraParams', $result);
        $this->assertEquals(100, $result->status_code);
    }

    /**
     * Review if the flag is true when the cancel case is related to the same case in SESSION
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_set_flag_when_is_same_case()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $_SESSION["APPLICATION"] = $delegation->APP_UID;
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DEL_INDEX, $delegation->APP_UID);
        $this->assertEquals($ws->getFlagSameCase(), true);
        $this->assertNotEmpty($response);
    }

    /**
     * Review the required field caseUid
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_validate_required_app_uid()
    {
        $delegation = Delegation::factory()->foreign_keys()->create();
        $ws = new WsBase();
        $response = (object) $ws->cancelCase('', $delegation->DE_INDEX, $delegation->URS_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_REQUIRED_FIELD") . ' caseUid');
    }

    /**
     * Review the required field status = TO_DO
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_validate_required_status_todo()
    {
        // Create a case in DRAFT status
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 1,
            'APP_STATUS' => 'DRAFT'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DE_INDEX, $delegation->URS_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_CASE_IN_STATUS") . ' DRAFT');

        // Create a case in COMPLETED status
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 3,
            'APP_STATUS' => 'COMPLETED'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DE_INDEX, $delegation->URS_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_CASE_IN_STATUS") . ' COMPLETED');

        // Create a case in CANCELLED status
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 4,
            'APP_STATUS' => 'CANCELLED'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DE_INDEX, $delegation->URS_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_CASE_IN_STATUS") . ' CANCELLED');
    }

    /**
     * Review the required field delIndex
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_validate_required_del_index()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, '', $delegation->USR_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_REQUIRED_FIELD") . ' delIndex');
    }

    /**
     * Review the required field open thread
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_validate_required_open_thread()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'CLOSED'
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DEL_INDEX, '');
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_CASE_DELEGATION_ALREADY_CLOSED"));
    }

    /**
     * Review the required field userUid
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_validate_required_usr_uid()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DEL_INDEX, '');
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_REQUIRED_FIELD") . ' userUid');
    }

    /**
     * Review cancel case with parallel threads
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_validate_only_one_thread_opened()
    {
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        AppThread::factory()->create([
            'APP_UID' => $application->APP_UID,
            'APP_THREAD_INDEX' => 1,
            'APP_THREAD_PARENT' => 1,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1
        ]);
        Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN'
        ]);
        AppThread::factory()->create([
            'APP_UID' => $application->APP_UID,
            'APP_THREAD_INDEX' => 2,
            'APP_THREAD_PARENT' => 1,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
        ]);

        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DEL_INDEX, $delegation->USR_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertEquals($response->message, G::LoadTranslation("ID_CASE_CANCELLED_PARALLEL"));
    }

    /**
     * Review the cancel case with one thread open was executed successfully
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_cancel_case()
    {
        // Definition for avoid the error: Trying to get property 'aUserInfo' of non-object in the action buildAppDelayRow()
        global $RBAC;
        $user = User::where('USR_ID', '=', 1)->first();
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        // Create the data related to the cancel a case
        $process = Process::factory()->create([
            'PRO_CREATE_USER' => $user->USR_UID
        ]);
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_USER' => $user->USR_UID
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        UserReporting::factory()->create([
            'TAS_UID' => $task->TAS_UID
        ]);
        $application = Application::factory()->foreign_keys()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        AppThread::factory()->create([
            'APP_UID' => $application->APP_UID,
            'APP_THREAD_INDEX' => 1,
            'APP_THREAD_PARENT' => 1,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'TAS_UID' => $task->TAS_UID,
            'PRO_UID' => $application->PRO_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => 2
        ]);

        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, $delegation->DEL_INDEX, $delegation->USR_UID);
        $this->assertNotEmpty($response);
        $this->assertObjectHasAttribute('status_code', $response);
        $this->assertEquals($response->message, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));
    }

    /**
     * Review the cancel case with parallel threads was executed successfully
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_should_cancel_case_parallel()
    {
        // Definition for avoid the error: Trying to get property 'aUserInfo' of non-object in the action buildAppDelayRow()
        global $RBAC;
        $user = User::where('USR_ID', '=', 1)->first();
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        // Create the data related to the cancel a case
        $task = Task::factory()->create();
        UserReporting::factory()->create([
            'TAS_UID' => $task->TAS_UID
        ]);
        $application = Application::factory()->foreign_keys()->create([
            'APP_STATUS_ID' => 2,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID,
            'APP_STATUS' => 'TO_DO'
        ]);
        // Create the first thread
        AppThread::factory()->create([
            'APP_UID' => $application->APP_UID,
            'APP_THREAD_INDEX' => 2,
            'APP_THREAD_PARENT' => 1,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);
        Delegation::factory()->foreign_keys()->create([
            'TAS_UID' => $task->TAS_UID,
            'PRO_UID' => $application->PRO_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
            'DEL_PREVIOUS' => 2,
        ]);
        // Create the second thread
        AppThread::factory()->create([
            'APP_UID' => $application->APP_UID,
            'APP_THREAD_INDEX' => 3,
            'APP_THREAD_PARENT' => 1,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 3
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'TAS_UID' => $task->TAS_UID,
            'PRO_UID' => $application->PRO_UID,
            'USR_UID' => $user->USR_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 3,
            'DEL_PREVIOUS' => 3,
        ]);

        $ws = new WsBase();
        $response = (object) $ws->cancelCase($delegation->APP_UID, null, null);
        $this->assertNotEmpty($response);
        $this->assertObjectHasAttribute('status_code', $response);
        $this->assertEquals($response->message, G::LoadTranslation("ID_COMMAND_EXECUTED_SUCCESSFULLY"));
    }

    /**
     * Review the cancel case when the applications does not exist
     *
     * @covers WsBase::cancelCase()
     * @test
     */
    public function it_tried_cancel_an_undefined_case()
    {
        $fakeApp = G::generateUniqueID();
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2,
            'APP_STATUS' => 'TO_DO'
        ]);
        AppThread::factory()->create([
            'APP_UID' => $application->APP_UID,
            'APP_THREAD_INDEX' => 1,
            'APP_THREAD_PARENT' => 1,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->cancelCase($fakeApp, $delegation->DEL_INDEX, $delegation->USR_UID);
        $this->assertEquals($response->status_code, 100);
        $this->assertStringContainsString($fakeApp, $response->message);
    }

    /**
     * Test the unassigned case list method with unassigned cases
     *
     * @test
     * @covers WsBase::unassignedCaseList()
     */
    public function it_should_test_the_unassigned_case_list_method_with_unassigned_cases()
    {
        //Create process
        $process1 = Process::factory()->create([
            'PRO_TITLE' => 'China Supplier Payment Proposal'
        ]);
        $process2 = Process::factory()->create([
            'PRO_TITLE' => 'Egypt Supplier Payment Proposal'
        ]);
        //Create application
        $application1 = Application::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID
        ]);
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        Delegation::factory(2)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID,
            'PRO_ID' => $process1->id,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);
        Delegation::factory(2)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID,
            'PRO_ID' => $process2->id,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
        ]);

        $wsBase = new WsBase();
        $res = $wsBase->unassignedCaseList($user->USR_UID);
        //Assert the expected number of unassigned cases
        $this->assertCount(4, $res);
    }

    /**
     * Test the unassigned case list method without unassigned cases
     *
     * @test
     * @covers WsBase::unassignedCaseList()
     */
    public function it_should_test_the_unassigned_case_list_method_without_unassigned_cases()
    {
        //Create process
        $process1 = Process::factory()->create([
            'PRO_TITLE' => 'China Supplier Payment Proposal'
        ]);
        $process2 = Process::factory()->create([
            'PRO_TITLE' => 'Egypt Supplier Payment Proposal'
        ]);
        //Create application
        $application1 = Application::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        //Create user
        $user = User::factory()->create();
        //Create a task self service
        $task1 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID
        ]);
        $task2 = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task1->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Assign a user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task2->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);
        //Create the register in delegation relate to self-service
        Delegation::factory(2)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task1->TAS_ID,
            'PRO_ID' => $process1->id,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 5,
        ]);
        Delegation::factory(2)->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task2->TAS_ID,
            'PRO_ID' => $process2->id,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 3,
        ]);

        $wsBase = new WsBase();
        $res = $wsBase->unassignedCaseList($user->USR_UID);

        //Assert the expected number of unassigned cases
        $this->assertCount(0, $res);
    }

    /**
     * Review the required fields in pause case
     *
     * @covers WsBase::pauseCase()
     * @test
     */
    public function it_review_fields_to_pause_case()
    {
        // Validate the appUid
        $ws = new WsBase();
        $response = (object) $ws->pauseCase('', 0, '');
        $this->assertEquals($response->status_code, 100);
        // Validate the status
        $application = Application::factory()->draft()->create();
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($application->APP_UID, 0, '');
        $this->assertEquals($response->status_code, 100);
        // Validate the index
        $application = Application::factory()->todo()->create();
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($application->APP_UID, '', '');
        $this->assertEquals($response->status_code, 100);
        // Validate the user
        $application = Application::factory()->todo()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($application->APP_UID, $delegation->DEL_INDEX, '');
        $this->assertEquals($response->status_code, 100);
        // If needs to validate the current user
        $user = User::factory()->create();
        $response = (object) $ws->pauseCase($application->APP_UID, $delegation->DEL_INDEX, $user->USR_UID, null, true);
        $this->assertEquals($response->status_code, 100);
        // Validate if status is closed
        $application = Application::factory()->todo()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'DEL_INDEX' => 2,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($application->APP_UID, $delegation->DEL_INDEX, $delegation->USR_UID, null);
        $this->assertEquals($response->status_code, 100);
        // Validate if the case is paused
        $application = Application::factory()->todo()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $delegation->USR_UID,
            'PRO_UID' => $delegation->PRO_UID,
            'APP_NUMBER' => $delegation->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppThread::factory()->create([
            'APP_UID' => $delegation->APP_UID,
            'APP_THREAD_INDEX' => 1,
            'APP_THREAD_PARENT' => 0,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => $delegation->DEL_INDEX
        ]);
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($application->APP_UID, $delegation->DEL_INDEX, $delegation->USR_UID, null);
        // Review the unpaused date
        $application = Application::factory()->todo()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($delegation->APP_UID, $delegation->DEL_INDEX, $delegation->USR_UID, '06/13/2019 5:35 PM');
        $this->assertEquals($response->status_code, 100);
    }

    /**
     * Review the required fields in pause case
     *
     * @covers WsBase::pauseCase()
     * @test
     */
    public function it_pause_case()
    {
        $application = Application::factory()->todo()->create();
        $delegation = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2,
        ]);
        AppDelay::factory()->create([
            'APP_DELEGATION_USER' => $delegation->USR_UID,
            'PRO_UID' => $delegation->PRO_UID,
            'APP_NUMBER' => $delegation->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppThread::factory()->create([
            'APP_UID' => $delegation->APP_UID,
            'APP_THREAD_INDEX' => 1,
            'APP_THREAD_PARENT' => 0,
            'APP_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => $delegation->DEL_INDEX
        ]);
        $ws = new WsBase();
        $response = (object) $ws->pauseCase($delegation->APP_UID, $delegation->DEL_INDEX, $delegation->USR_UID);
        $this->assertEquals(0, $response->status_code);
    }
}
