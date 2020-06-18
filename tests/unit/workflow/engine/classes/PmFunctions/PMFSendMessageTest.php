<?php
namespace Tests\unit\workflow\engine\classes\PmFunctions;

use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppMessage;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use Tests\TestCase;
/**
 * Test the PMFSendMessage() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Email_Functions#PMFSendMessage.28.29
 */
class PMFSendMessageTest extends TestCase
{
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
     * Test send a message when the case in $_SESSION['APPLICATION'] is the same
     *
     * @test
     */
    public function it_send_message_related_to_same_case()
    {
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        $app = factory(Application::class)->create(['PRO_UID' => $process->PRO_UID]);
        $template = $this->createTemplate($process->PRO_UID, $user->USR_UID);
        $emailServer = $this->createEmailServer();
        // Set the same case in session
        $_SESSION['APPLICATION'] = $app->APP_UID;
        // Call the function
        $result = PMFSendMessage($app->APP_UID, $emailServer->MESS_ACCOUNT, $emailServer->MESS_ACCOUNT, '', '', 'open case other', basename($template->PRF_PATH));
        $this->assertEquals(1, $result);
        // Check if the appNumber matches
        $query = AppMessage::query()->select();
        $query->where('APP_UID', $app->APP_UID);
        $row = $query->get()->toArray();
        $this->assertEquals($app->APP_NUMBER, $row[0]['APP_NUMBER']);
    }
    /**
     * Test send a message when the case in $_SESSION['APPLICATION'] is different
     *
     * @test
     */
    public function it_send_message_related_to_different_case()
    {
        $user = factory(User::class)->create();
        $process = factory(Process::class)->create();
        $app = factory(Application::class)->create(['PRO_UID' => $process->PRO_UID]);
        $app2 = factory(Application::class)->create(['PRO_UID' => $process->PRO_UID]);
        $template = $this->createTemplate($process->PRO_UID, $user->USR_UID);
        $emailServer = $this->createEmailServer();
        // Set different case in session
        $_SESSION['APPLICATION'] = $app2->APP_UID;
        // Call the functions
        $result = PMFSendMessage($app->APP_UID, $emailServer->MESS_ACCOUNT, $emailServer->MESS_ACCOUNT, '', '', 'open case other', basename($template->PRF_PATH));
        $this->assertEquals(1, $result);
        // Check if the appNumber matches
        $query = AppMessage::query()->select();
        $query->where('APP_UID', $app->APP_UID);
        $row = $query->get()->toArray();
        $this->assertEquals($app->APP_NUMBER, $row[0]['APP_NUMBER']);
    }
}
