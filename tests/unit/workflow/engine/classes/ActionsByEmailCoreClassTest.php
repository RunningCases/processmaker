<?php

use ProcessMaker\Model\AbeConfiguration;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

class ActionsByEmailCoreClassTest extends TestCase
{
    private $actionsByEmailCoreClass;

    /**
     * Method set up.
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!defined('PATH_IMAGES_ENVIRONMENT_USERS')) {
            define('PATH_IMAGES_ENVIRONMENT_USERS', PATH_DATA_SITE . 'usersPhotographies' . PATH_SEP);
        }

        $path = PATH_HOME . 'public_html' . PATH_SEP . 'lib';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . PATH_SEP . 'pmdynaform';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . PATH_SEP . 'build';
        if (!file_exists($path)) {
            mkdir($path);
        }
        $path = $path . PATH_SEP . 'pmdynaform.html';
        if (!file_exists($path)) {
            $template = file_get_contents(PATH_TPL . 'cases/pmdynaform.html');
            file_put_contents($path, $template);
        }
    }

    /**
     * This test checks if the sendActionsByEmail method throws an exception.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_with_exception()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        //assertion Exception
        $this->expectException(Exception::class);

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, []);
    }

    /**
     * This test checks if the sendActionsByEmail method handles an undefined configuration.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_if_abe_configuration_is_undefined()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $abeConfiguration = [
            'ABE_EMAIL_SERVER_UID' => ''
        ];
        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $result = $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);

        $this->assertNull($result);
    }

    /**
     * This test checks if the sendActionsByEmail method throws an exception if 
     * the task properties do not exist.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_with_exception_if_task_property_is_undefined()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => '',
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'CUSTOM',
            'ABE_CUSTOM_GRID' => serialize([])
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        //assertion Exception
        $this->expectException(Exception::class);

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);
    }

    /**
     * This test checks if the sendActionsByEmail method throws an exception if the 
     * email address is empty.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_with_exception_if_email_to_is_empty()
    {
        $user = User::factory()->create([
            'USR_EMAIL' => ''
        ]);

        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'CUSTOM',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_FIELD' => ''
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $result = $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);

        $this->assertNull($result);
    }

    /**
     * This test checks if the sendActionsByEmail method throws an exception if 
     * the email type is empty.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_with_exception_if_email_type_is_empty()
    {
        $user = User::factory()->create();

        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => '',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_FIELD' => ''
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $result = $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);

        $this->assertNull($result);
    }

    /**
     * This test verifies if the sendActionsByEmail method supports the 'CUSTOM' setting.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_custom()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'CUSTOM',
            'ABE_CUSTOM_GRID' => serialize([])
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);
        $result = $this->actionsByEmailCoreClass->getAbeRequest();

        $this->assertArrayHasKey('ABE_REQ_UID', $result);
    }

    /**
     * This test verifies if the sendActionsByEmail method supports the 'RESPONSE' setting.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_response()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'RESPONSE',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_SERVER_RECEIVER_UID' => $emailServer->MESS_UID
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);
        $result = $this->actionsByEmailCoreClass->getAbeRequest();

        $this->assertArrayHasKey('ABE_REQ_UID', $result);
    }

    /**
     * This test verifies if the sendActionsByEmail method supports the 'LINK' setting.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_link()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'LINK',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_SERVER_RECEIVER_UID' => $emailServer->MESS_UID
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);
        $result = $this->actionsByEmailCoreClass->getAbeRequest();

        $this->assertArrayHasKey('ABE_REQ_UID', $result);
    }

    /**
     * This test verifies if the sendActionsByEmail method supports the 'FIELD' setting.
     * @test
     * @covers \ActionsByEmailCoreClass::sendActionsByEmail
     */
    public function it_should_test_sendActionsByEmail_method_field()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DYN_CONTENT' => file_get_contents(PATH_TRUNK . "/tests/resources/dynaform2.json")
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'FIELD',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_SERVER_RECEIVER_UID' => $emailServer->MESS_UID
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);
        $result = $this->actionsByEmailCoreClass->getAbeRequest();

        $this->assertArrayHasKey('ABE_REQ_UID', $result);
    }

    /**
     * This test verifies if the getFieldTemplate method supports the 'dropdown' control.
     * @test
     * @covers \ActionsByEmailCoreClass::getFieldTemplate
     */
    public function it_should_test_getFieldTemplate_method_dropdown_control()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DYN_CONTENT' => file_get_contents(PATH_TRUNK . "/tests/resources/dynaform3.json")
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'LINK',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_SERVER_RECEIVER_UID' => $emailServer->MESS_UID,
            'ABE_ACTION_FIELD' => '@@option'
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);

        $reflection = new ReflectionClass($this->actionsByEmailCoreClass);
        $reflectionMethod = $reflection->getMethod('getFieldTemplate');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->actionsByEmailCoreClass, []);

        $this->assertStringContainsString('jsondata', $result);
        $this->assertStringContainsString('httpServerHostname', $result);
        $this->assertStringContainsString('pm_run_outside_main_app', $result);
        $this->assertStringContainsString('pathRTLCss', $result);
        $this->assertStringContainsString('fieldsRequired', $result);
    }

    /**
     * This test verifies if the getFieldTemplate method supports the 'checkbox' control.
     * @test
     * @covers \ActionsByEmailCoreClass::getFieldTemplate
     */
    public function it_should_test_getFieldTemplate_method_checkbox_control()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DYN_CONTENT' => file_get_contents(PATH_TRUNK . "/tests/resources/dynaform3.json")
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'LINK',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_SERVER_RECEIVER_UID' => $emailServer->MESS_UID,
            'ABE_ACTION_FIELD' => '@@checkboxVar001'
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);

        $reflection = new ReflectionClass($this->actionsByEmailCoreClass);
        $reflectionMethod = $reflection->getMethod('getFieldTemplate');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->actionsByEmailCoreClass, []);

        $this->assertStringContainsString('jsondata', $result);
        $this->assertStringContainsString('httpServerHostname', $result);
        $this->assertStringContainsString('pm_run_outside_main_app', $result);
        $this->assertStringContainsString('pathRTLCss', $result);
        $this->assertStringContainsString('fieldsRequired', $result);
    }

    /**
     * This test verifies if the getFieldTemplate method supports the 'yesno' control.
     * The 'yesno' control is obsolete and not used in pmdynaform.
     * @test
     * @covers \ActionsByEmailCoreClass::getFieldTemplate
     */
    public function it_should_test_getFieldTemplate_method_yesno_control()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->get()
                ->first();
        $process = Process::factory()->create();
        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $dynaform = Dynaform::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DYN_CONTENT' => file_get_contents(PATH_TRUNK . "/tests/resources/dynaform3.json")
        ]);
        $emailServer = ProcessMaker\Model\EmailServerModel::factory()->create();
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'DYN_UID' => $dynaform->DYN_UID,
            'ABE_EMAIL_SERVER_UID' => $emailServer->MESS_UID,
            'ABE_TYPE' => 'LINK',
            'ABE_CUSTOM_GRID' => serialize([]),
            'ABE_EMAIL_SERVER_RECEIVER_UID' => $emailServer->MESS_UID,
            'ABE_ACTION_FIELD' => '@@radioVar001'
        ]);
        $abeConfiguration = $abeConfiguration->toArray();

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $data = [
            'TAS_UID' => $task->TAS_UID,
            'APP_UID' => $application->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'USR_UID' => $user->USR_UID,
            'PREVIOUS_USR_UID' => $user->USR_UID
        ];
        $data = (object) $data;

        $_SERVER["REQUEST_URI"] = '';

        $this->actionsByEmailCoreClass = new ActionsByEmailCoreClass();
        $this->actionsByEmailCoreClass->setUser($user->USR_UID);
        $this->actionsByEmailCoreClass->setIndex($delegation->DEL_INDEX);
        $this->actionsByEmailCoreClass->sendActionsByEmail($data, $abeConfiguration);

        $reflection = new ReflectionClass($this->actionsByEmailCoreClass);
        $reflectionMethod = $reflection->getMethod('getFieldTemplate');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->actionsByEmailCoreClass, []);

        $this->assertStringContainsString('jsondata', $result);
        $this->assertStringContainsString('httpServerHostname', $result);
        $this->assertStringContainsString('pm_run_outside_main_app', $result);
        $this->assertStringContainsString('pathRTLCss', $result);
        $this->assertStringContainsString('fieldsRequired', $result);
    }
}
