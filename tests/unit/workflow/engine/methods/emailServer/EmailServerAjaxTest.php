<?php

namespace Tests\unit\workflow\engine\methods\emailServer;

use Faker\Factory;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class EmailServerAjaxTest extends TestCase
{
    /**
     * This set initial parameters for each test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->settingUserLogged();
    }

    /**
     * This starts a valid user in session with the appropriate permissions.
     * @global object $RBAC
     */
    private function settingUserLogged()
    {
        global $RBAC;

        $user = User::where('USR_ID', '=', 1)
                ->get()
                ->first();

        $_SESSION['USER_LOGGED'] = $user['USR_UID'];

        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);
    }
    /**
     * This allows multiple calls to the require_once statement, use the original 
     * content by creating several documents in the temporary folder.
     * @global object $RBAC
     * @return string
     */
    private function requireOnceForEmailServerAjax(): string
    {
        $fileName = PATH_METHODS . 'emailServer/emailServerAjax.php';
        $tempFile = tempnam(sys_get_temp_dir(), basename($fileName, '.php'));
        if ($tempFile !== false) {
            file_put_contents($tempFile, file_get_contents($fileName));

            global $RBAC;
            $RBAC->authorizedActions[basename($tempFile)] = [
                'INS' => ['PM_SETUP'],
                'UPD' => ['PM_SETUP'],
                'DEL' => ['PM_SETUP'],
                'LST' => ['PM_SETUP'],
                'TEST' => ['PM_SETUP'],
                'createAuthUrl' => ['PM_SETUP']
            ];
            ob_start();
            require_once $tempFile;
            return ob_get_clean();
        }
        return "";
    }

    /**
     * This tests the createAuthUrl option at the endpoint emailServerAjax.php.
     * @test
     */
    public function it_should_verify_the_option_create_auth_url()
    {
        $faker = Factory::create();
        $post = [
            'option' => 'createAuthUrl',
            'clientID' => $faker->regexify('[0-9]{12}\-[a-zA-Z]{20}'),
            'clientSecret' => $faker->regexify('[a-zA-Z]{10}'),
            'emailEngine' => 'GMAILAPI',
            'fromAccount' => $faker->email,
            'senderEmail' => $faker->email,
            'senderName' => $faker->name,
            'sendTestMail' => 1,
            'mailTo' => $faker->email,
            'setDefaultConfiguration' => 1
        ];
        $_POST = array_merge($_POST, $post);

        $content = $this->requireOnceForEmailServerAjax();
        $data = json_decode($content, JSON_OBJECT_AS_ARRAY);

        $this->assertContains(500, $data);
    }

    /**
     * This tests the INS option at the endpoint emailServerAjax.php.
     * @test
     */
    public function it_should_verify_the_option_ins()
    {
        $faker = Factory::create();
        $post = [
            'option' => 'INS',
            'cboEmailEngine' => 'PHPMAILER',
            'server' => 'smtp.gmail.com',
            'port' => '465',
            'incomingServer' => '',
            'incomingPort' => '',
            'reqAuthentication' => 1,
            'accountFrom' => $faker->email,
            'password' => $faker->password,
            'fromMail' => $faker->email,
            'fromName' => $faker->name,
            'smtpSecure' => 'ssl',
            'sendTestMail' => 1,
            'mailTo' => $faker->email,
            'emailServerDefault' => 1,
        ];
        $_POST = array_merge($_POST, $post);

        $content = $this->requireOnceForEmailServerAjax();
        $data = json_decode($content, JSON_OBJECT_AS_ARRAY);

        $this->assertContains("OK", $data);
    }

    /**
     * This tests the UPD option at the endpoint emailServerAjax.php.
     * @test
     */
    public function it_should_verify_the_option_upd()
    {
        $faker = Factory::create();

        $emailServer = factory(EmailServerModel::class)->create([
            'MESS_ENGINE' => 'PHPMAILER',
        ]);

        $post = [
            'option' => 'UPD',
            'emailServerUid' => $emailServer->MESS_UID,
            'cboEmailEngine' => 'PHPMAILER',
            'server' => 'smtp.gmail.com',
            'port' => '465',
            'incomingServer' => '',
            'incomingPort' => '',
            'reqAuthentication' => 1,
            'accountFrom' => $faker->email,
            'password' => $faker->password,
            'fromMail' => $faker->email,
            'fromName' => $faker->name,
            'smtpSecure' => 'ssl',
            'sendTestMail' => 1,
            'mailTo' => $faker->email,
            'emailServerDefault' => 1,
        ];
        $_POST = array_merge($_POST, $post);

        $content = $this->requireOnceForEmailServerAjax();
        $data = json_decode($content, JSON_OBJECT_AS_ARRAY);

        $this->assertContains("OK", $data);
    }

    /**
     * This tests the DEL option at the endpoint emailServerAjax.php.
     * @test
     */
    public function it_should_verify_the_option_del()
    {
        $emailServer = factory(EmailServerModel::class)->create([
            'MESS_ENGINE' => 'PHPMAILER',
        ]);

        $post = [
            'option' => 'DEL',
            'emailServerUid' => $emailServer->MESS_UID
        ];
        $_POST = array_merge($_POST, $post);

        $content = $this->requireOnceForEmailServerAjax();
        $data = json_decode($content, JSON_OBJECT_AS_ARRAY);

        $this->assertContains("OK", $data);
    }

    /**
     * This tests the LST option at the endpoint emailServerAjax.php.
     * @test
     */
    public function it_should_verify_the_option_lst()
    {
        $post = [
            'option' => 'LST',
            'pageSize' => 25,
            'search' => ''
        ];
        $_POST = array_merge($_POST, $post);

        $content = $this->requireOnceForEmailServerAjax();
        $data = json_decode($content, JSON_OBJECT_AS_ARRAY);

        $this->assertContains("OK", $data);
        $this->assertTrue(isset($data["resultRoot"]));
        $this->assertTrue(is_array($data["resultRoot"]));
    }
}
