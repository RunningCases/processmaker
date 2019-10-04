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
use Tests\TestCase;

class ActionsByEmailTest extends TestCase
{
    /**
     * Test the forwardMail method
     *
     * @covers \ProcessMaker\BusinessModel\ActionsByEmail::forwardMail()
     * @test
     */
    public function it_should_test_the_forward_mail_method()
    {
        //Create the Task factory
        factory(Task::class)->create();
        //Create the Process factory
        factory(Process::class)->create();
        //Create the Dynaform factory
        factory(Dynaform::class)->create();
        //Create the EmailServerModel factory
        factory(EmailServerModel::class)->create();
        //Create the Application factory
        factory(Application::class)->create();
        //Create the Delegation factory
        $delegation = factory(Delegation::class)->create();
        //Create the AbeConfiguration factory
        $abeConfiguration = factory(AbeConfiguration::class)->create();
        //Create the AbeConfiguration factory
        $abeRequest = factory(AbeRequest::class)->create([
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
        $this->assertContains('**ID_EMAIL_RESENT_TO**: ' . $abeRequest->ABE_REQ_SENT_TO, $res);
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
        factory(Task::class)->create();
        //Create the Process factory
        factory(Process::class)->create();
        //Create the Dynaform factory
        factory(Dynaform::class)->create();
        //Create the EmailServerModel factory
        factory(EmailServerModel::class)->create();
        //Create the Application factory
        factory(Application::class)->create();
        //Create the Delegation factory
        $delegation = factory(Delegation::class)->create();
        //Create the AbeConfiguration factory
        $abeConfiguration = factory(AbeConfiguration::class)->create();
        //Create the AbeConfiguration factory
        $abeRequest = factory(AbeRequest::class)->create([
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
        $this->assertContains('**ID_UNEXPECTED_ERROR_OCCURRED_PLEASE**', $res);
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
        factory(Task::class)->create();
        //Create the Process factory
        factory(Process::class)->create();
        //Create the Dynaform factory
        factory(Dynaform::class)->create();
        //Create the EmailServerModel factory
        factory(EmailServerModel::class)->create();
        //Create the Application factory
        factory(Application::class)->create();
        //Create the Delegation factory
        $delegation = factory(Delegation::class)->create([
            'DEL_FINISH_DATE' => '2019-09-27 14:53:06'
        ]);
        //Create the AbeConfiguration factory
        $abeConfiguration = factory(AbeConfiguration::class)->create();
        //Create the AbeConfiguration factory
        $abeRequest = factory(AbeRequest::class)->create([
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
        $this->assertContains('**ID_UNABLE_TO_SEND_EMAIL**', $res);
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
        factory(Task::class)->create();
        //Create the Process factory
        factory(Process::class)->create();
        //Create the Dynaform factory
        factory(Dynaform::class)->create();
        //Create the EmailServerModel factory
        factory(EmailServerModel::class)->create();
        //Create the Application factory
        factory(Application::class)->create();
        //Create the Delegation factory
        $delegation = factory(Delegation::class)->create([
            'DEL_FINISH_DATE' => '2019-09-27 14:53:06'
        ]);
        //Create the AbeConfiguration factory
        $abeConfiguration = factory(AbeConfiguration::class)->create();
        //Create the AbeConfiguration factory
        $abeRequest = factory(AbeRequest::class)->create([
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
        $this->assertContains('**ID_UNEXPECTED_ERROR_OCCURRED_PLEASE**', $res);
    }
}