<?php

namespace Tests\unit\workflow\engine\methods\emailServer;

use Faker\Factory;
use Google_Auth_Exception;
use ProcessMaker\GmailOAuth\GmailOAuth;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class EmailServerGmailOAuthTest extends TestCase
{
    /**
     * This test expects an exception from the Google client.
     * The Google client requires valid codes to obtain the clientId from a request, 
     * otherwise it will throw an exception.
     * @test
     */
    public function it_should_try_to_authenticate_on_google_oauth_with_a_fake_code()
    {
        $faker = Factory::create();
        global $RBAC;

        $user = User::where('USR_ID', '=', 1)
                ->get()
                ->first();

        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $_POST['USR_UID'] = $user['USR_UID'];

        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        $_SESSION['gmailOAuth'] = new GmailOAuth();

        /**
         * This gets a fake code, according to the nomenclature.
         */
        $_GET['code'] = $faker->regexify("/[1-9]\/[a-zA-Z]{25}-[a-zA-Z]{16}_[a-zA-Z]{19}-[a-zA-Z]{24}/");

        $this->expectException(Google_Auth_Exception::class);
        require_once PATH_METHODS . 'emailServer/emailServerGmailOAuth.php';
    }
}
