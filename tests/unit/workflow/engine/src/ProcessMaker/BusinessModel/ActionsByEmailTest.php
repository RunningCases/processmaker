<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\ActionsByEmail;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AbeConfiguration;
use ProcessMaker\Model\AbeRequest;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

class ActionsByEmailTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test the forwardMail method
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::forwardMail()
     * @test
     */
    public function it_should_test_the_forward_mail_method()
    {
        //Create the Task factory
        Task::factory()->create();
        //Create the Process factory
        Process::factory()->create();
        //Create the Dynaform factory
        Dynaform::factory()->create();
        //Create the EmailServerModel factory
        EmailServerModel::factory()->create();
        //Create the Application factory
        Application::factory()->create();
        //Create the Delegation factory
        $delegation = Delegation::factory()->create();
        //Create the AbeConfiguration factory
        $abeConfiguration = AbeConfiguration::factory()->create();
        //Create the AbeConfiguration factory
        $abeRequest = AbeRequest::factory()->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $delegation->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'ABE_REQ_UID' => $abeConfiguration->ABE_UID,
        ]);

        //Prepare the array send to the method
        $arrayData = [
            'action' => 'forwardMail',
            'REQ_UID' => $abeRequest->ABE_REQ_UID,
            'limit' => '',
            'start' => ''
        ];

        //Create the ActionsByEmail object
        $abe = new ActionsByEmail();
        //Call the forwardMail method
        $res = $abe->forwardMail($arrayData);

        //Assert the email was sent successfully
        $this->assertStringContainsString('**ID_EMAIL_RESENT_TO**: ' . $abeRequest->ABE_REQ_SENT_TO, $res);
    }

    /**
     * Test the forwardMail method when an error occurs
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::forwardMail()
     * @test
     */
    public function it_should_test_the_forward_mail_method_when_an_error_occurs()
    {
        //Create the Task factory
        Task::factory()->create();
        //Create the Process factory
        Process::factory()->create();
        //Create the Dynaform factory
        Dynaform::factory()->create();
        //Create the EmailServerModel factory
        EmailServerModel::factory()->create();
        //Create the Application factory
        Application::factory()->create();
        //Create the Delegation factory
        $delegation = Delegation::factory()->create();
        //Create the AbeConfiguration factory
        $abeConfiguration = AbeConfiguration::factory()->create();
        //Create the AbeConfiguration factory
        $abeRequest = AbeRequest::factory()->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $delegation->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'ABE_REQ_UID' => $abeConfiguration->ABE_UID
        ]);

        //Prepare the array send to the method
        $arrayData = [
            'action' => 'forwardMail',
            'REQ_UID' => '',
            'limit' => '',
            'start' => ''
        ];

        //Create the ActionsByEmail object
        $abe = new ActionsByEmail();
        //Call the forwardMail method
        $res = $abe->forwardMail($arrayData);

        //Assert that an unexpected error occur
        $this->assertStringContainsString('**ID_UNEXPECTED_ERROR_OCCURRED_PLEASE**', $res);
    }

    /**
     * Test the forwardMail method when the email cannot be sent
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::forwardMail()
     * @test
     */
    public function it_should_test_the_forward_mail_method_when_the_email_cannot_be_sent()
    {
        //Create the Task factory
        Task::factory()->create();
        //Create the Process factory
        Process::factory()->create();
        //Create the Dynaform factory
        Dynaform::factory()->create();
        //Create the EmailServerModel factory
        EmailServerModel::factory()->create();
        //Create the Application factory
        Application::factory()->create();
        //Create the Delegation factory
        $delegation = Delegation::factory()->create([
            'DEL_FINISH_DATE' => '2019-09-27 14:53:06'
        ]);
        //Create the AbeConfiguration factory
        $abeConfiguration = AbeConfiguration::factory()->create();
        //Create the AbeConfiguration factory
        $abeRequest = AbeRequest::factory()->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $delegation->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'ABE_REQ_UID' => $abeConfiguration->ABE_UID,
        ]);

        //Prepare the array send to the method
        $arrayData = [
            'action' => 'forwardMail',
            'REQ_UID' => $abeRequest->ABE_REQ_UID,
            'limit' => '',
            'start' => ''
        ];

        //Create the ActionsByEmail object
        $abe = new ActionsByEmail();
        //Call the forwardMail method
        $res = $abe->forwardMail($arrayData);

        //Assert the email was not sent
        $this->assertStringContainsString('**ID_UNABLE_TO_SEND_EMAIL**', $res);
    }

    /**
     * Test the forwardMail method when the REQ_UID is not set
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::forwardMail()
     * @test
     */
    public function it_should_test_the_forward_mail_method_when_the_req_uid_is_not_set()
    {
        //Create the Task factory
        Task::factory()->create();
        //Create the Process factory
        Process::factory()->create();
        //Create the Dynaform factory
        Dynaform::factory()->create();
        //Create the EmailServerModel factory
        EmailServerModel::factory()->create();
        //Create the Application factory
        Application::factory()->create();
        //Create the Delegation factory
        $delegation = Delegation::factory()->create([
            'DEL_FINISH_DATE' => '2019-09-27 14:53:06'
        ]);
        //Create the AbeConfiguration factory
        $abeConfiguration = AbeConfiguration::factory()->create();
        //Create the AbeConfiguration factory
        $abeRequest = AbeRequest::factory()->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $delegation->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'ABE_REQ_UID' => $abeConfiguration->ABE_UID,
        ]);

        //Prepare the array send to the method
        $arrayData = [
            'action' => 'forwardMail',
            'limit' => '',
            'start' => ''
        ];

        //Create the ActionsByEmail object
        $abe = new ActionsByEmail();
        //Call the forwardMail method
        $res = $abe->forwardMail($arrayData);

        //Assert the email was not sent
        $this->assertStringContainsString('**ID_UNEXPECTED_ERROR_OCCURRED_PLEASE**', $res);
    }

    /**
     * Test the forwardMail method with ssl
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::forwardMail()
     * @test
     */
    public function it_should_test_the_forward_mail_method_with_ssl()
    {
        //Create the Task factory
        Task::factory()->create();
        //Create the Process factory
        Process::factory()->create();
        //Create the Dynaform factory
        Dynaform::factory()->create();
        //Create the EmailServerModel factory with smtp secure
        EmailServerModel::factory()->create(
            ['SMTPSECURE' => 'ssl']
        );
        //Create the Application factory
        Application::factory()->create();
        //Create the Delegation factory
        $delegation = Delegation::factory()->create();
        //Create the AbeConfiguration factory
        $abeConfiguration = AbeConfiguration::factory()->create();
        //Create the AbeConfiguration factory
        $abeRequest = AbeRequest::factory()->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $delegation->APP_UID,
            'DEL_INDEX' => $delegation->DEL_INDEX,
            'ABE_REQ_UID' => $abeConfiguration->ABE_UID,
        ]);

        //Prepare the array send to the method
        $arrayData = [
            'action' => 'forwardMail',
            'REQ_UID' => $abeRequest->ABE_REQ_UID,
            'limit' => '',
            'start' => ''
        ];

        //Create the ActionsByEmail object
        $abe = new ActionsByEmail();
        //Call the forwardMail method
        $res = $abe->forwardMail($arrayData);

        //Assert the email was sent successfully
        $this->assertStringContainsString('**ID_EMAIL_RESENT_TO**: ' . $abeRequest->ABE_REQ_SENT_TO, $res);
    }

    /**
     * Test the loadActionByEmail method
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::loadActionByEmail()
     * @test
     */
    public function it_should_test_the_load_action_by_email_method()
    {
        self::truncateNonInitialModels();
        $user = User::factory()->create();
        $application = Application::factory()->create([
            'APP_UID' => '123456asse'
        ]);
        $delegation = Delegation::factory()->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 0,
            'DEL_INDEX' => 1
        ]);
        $delegation2 = Delegation::factory()->create([
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        $abeConfiguration = AbeConfiguration::factory()->create([
            'PRO_UID' => $delegation->PRO_UID,
            'TAS_UID' => $delegation->TAS_UID,
            'ABE_TYPE' => 'LINK',
        ]);
        $abeRequest = AbeRequest::factory()->create([
            'ABE_UID' => $abeConfiguration->ABE_UID,
            'APP_UID' => $delegation2->APP_UID,
            'DEL_INDEX' => $delegation2->DEL_INDEX,
        ]);
        $arrayData = [
            'action' => 'forwardMail',
            'REQ_UID' => $abeRequest->ABE_REQ_UID,
            'limit' => '',
            'start' => ''
        ];
        $abe = new ActionsByEmail();
        $res = $abe->loadActionByEmail($arrayData);
        $this->assertEquals(1, $res["totalCount"]);
    }
}
