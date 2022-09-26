<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\GmailOAuth;

use Exception;
use Faker\Factory;
use Google_Client;
use Google_Service_Gmail_Message;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Core\System;
use ProcessMaker\EmailOAuth\PHPMailerOAuth;
use ProcessMaker\GmailOAuth\GmailOAuth;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;
use BadMethodCallException;

class GmailOAuthTest extends TestCase
{
    use DatabaseTransactions;
    private $faker;

    /**
     * Init properties
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();

        global $RBAC;
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();
    }

    /**
     * This ensures that the properties of the GmailOAuth object have consistency.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setEmailServerUid()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setEmailEngine()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setClientID()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setClientSecret()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setRedirectURI()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setEmailEngine()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setFromAccount()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setSenderEmail()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setSenderName()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setSendTestMail()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setMailTo()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setSetDefaultConfiguration()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::setRefreshToken()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getEmailServerUid()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getClientID()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getClientSecret()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getRedirectURI()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getEmailEngine()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getFromAccount()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getSenderEmail()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getSenderName()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getSendTestMail()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getMailTo()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getSetDefaultConfiguration()
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getRefreshToken()
     */
    public function it_should_set_and_get_properties()
    {
        $faker = $this->faker;

        $expected = $faker->word;
        $digit = $faker->randomDigitNotNull;

        $gmailOAuth = new GmailOAuth();

        $gmailOAuth->setEmailServerUid($expected);
        $actual = $gmailOAuth->getEmailServerUid();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setClientID($expected);
        $actual = $gmailOAuth->getClientID();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setClientSecret($expected);
        $actual = $gmailOAuth->getClientSecret();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setRedirectURI($expected);
        $actual = $gmailOAuth->getRedirectURI();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setEmailEngine($expected);
        $actual = $gmailOAuth->getEmailEngine();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setFromAccount($expected);
        $actual = $gmailOAuth->getFromAccount();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setSenderEmail($expected);
        $actual = $gmailOAuth->getSenderEmail();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setSenderName($expected);
        $actual = $gmailOAuth->getSenderName();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setSendTestMail($expected);
        $actual = $gmailOAuth->getSendTestMail();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setMailTo($expected);
        $actual = $gmailOAuth->getMailTo();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setSetDefaultConfiguration($expected);
        $actual = $gmailOAuth->getSetDefaultConfiguration();
        $this->assertEquals($expected, $actual);

        $gmailOAuth->setRefreshToken($expected);
        $actual = $gmailOAuth->getRefreshToken();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Obtenga una instancia de Google_Client.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getGoogleClient()
     */
    public function it_should_()
    {
        $gmailOAuth = new GmailOAuth();
        $gmailOAuth->setClientID("");
        $gmailOAuth->setClientSecret("");
        $gmailOAuth->setRedirectURI("");
        $googleClient = $gmailOAuth->getGoogleClient();
        $this->assertTrue($googleClient instanceof Google_Client);
    }

    /**
     * Create Email Server data.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::saveEmailServer()
     */
    public function it_should_create_email_server()
    {
        global $RBAC;
        $user = User::where('USR_ID', '=', 1)->first();
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        $faker = $this->faker;
        $clientId = $faker->uuid;
        $clientSecret = $faker->uuid;
        $refreshToken = $faker->uuid;

        $gmailOAuth = new GmailOAuth();
        $gmailOAuth->setEmailEngine("GMAILAPI");
        $gmailOAuth->setClientID($clientId);
        $gmailOAuth->setClientSecret($clientSecret);
        $gmailOAuth->setRefreshToken($refreshToken);
        $gmailOAuth->setFromAccount($faker->email);
        $gmailOAuth->setSenderEmail(1);
        $gmailOAuth->setSenderName($faker->word);
        $gmailOAuth->setSendTestMail(1);
        $gmailOAuth->setMailTo($faker->email);
        $gmailOAuth->setSetDefaultConfiguration(0);

        $result = $gmailOAuth->saveEmailServer();

        $this->assertEquals($clientId, $result['OAUTH_CLIENT_ID']);
        $this->assertEquals($clientSecret, $result['OAUTH_CLIENT_SECRET']);
        $this->assertEquals($refreshToken, $result['OAUTH_REFRESH_TOKEN']);
    }

    /**
     * Update Email Server data.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::saveEmailServer()
     */
    public function it_should_udpate_email_server()
    {
        $faker = $this->faker;

        $gmailOAuth = new GmailOAuth();
        $gmailOAuth->setEmailServerUid($faker->uuid);
        $gmailOAuth->setEmailEngine("GMAILAPI");
        $gmailOAuth->setClientID($faker->uuid);
        $gmailOAuth->setClientSecret($faker->uuid);
        $gmailOAuth->setRefreshToken($faker->uuid);
        $gmailOAuth->setFromAccount($faker->email);
        $gmailOAuth->setSenderEmail(1);
        $gmailOAuth->setSenderName($faker->word);
        $gmailOAuth->setSendTestMail(1);
        $gmailOAuth->setMailTo($faker->email);
        $gmailOAuth->setSetDefaultConfiguration(0);

        $this->expectException(Exception::class);
        $result = $gmailOAuth->saveEmailServer();
    }

    /**
     * This ensures proof of email delivery with Google_Service_Gmail.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::sendTestEmailWithGoogleServiceGmail()
     */
    public function it_should_send_an_email_test_with_google_service_gmail()
    {
        $faker = $this->faker;
        $gmailOauth = new GmailOAuth();
        $result = $gmailOauth->sendTestEmailWithGoogleServiceGmail();
        $this->assertTrue($result instanceof Google_Service_Gmail_Message);

        $gmailOauth->setFromAccount($faker->email);
        $result = $gmailOauth->sendTestEmailWithGoogleServiceGmail();
        $this->assertTrue($result instanceof Google_Service_Gmail_Message);

        $gmailOauth->setSenderEmail($faker->email);
        $result = $gmailOauth->sendTestEmailWithGoogleServiceGmail();
        $this->assertTrue($result instanceof Google_Service_Gmail_Message);

        $gmailOauth->setMailTo($faker->email);
        $gmailOauth->setSendTestMail(0);
        $result = $gmailOauth->sendTestEmailWithGoogleServiceGmail();
        $this->assertTrue($result instanceof Google_Service_Gmail_Message);

        $this->expectException(Exception::class);
        $gmailOauth->setSendTestMail(1);
        $result = $gmailOauth->sendTestEmailWithGoogleServiceGmail();
    }

    /**
     * This test ensures that the message body for the email test.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getRawMessage()
     */
    public function it_should_get_raw_message_for_test_email()
    {
        $gmailOAuth = new GmailOAuth();
        $result = $gmailOAuth->getRawMessage();
        $this->assertTrue(is_string($result));
    }

    /**
     * This ensures proof of email delivery with PHPMailerOAuth.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::sendTestMailWithPHPMailerOAuth()
     */
    public function it_should_send_an_email_test_with_PHPMailerOAuth()
    {
        $faker = $this->faker;
        $gmailOauth = new GmailOAuth();

        $result = $gmailOauth->sendTestMailWithPHPMailerOAuth();
        $this->assertTrue($result instanceof PHPMailerOAuth);

        $gmailOauth->setFromAccount($faker->email);
        $result = $gmailOauth->sendTestMailWithPHPMailerOAuth();
        $this->assertTrue($result instanceof PHPMailerOAuth);

        $gmailOauth->setSenderEmail($faker->email);
        $result = $gmailOauth->sendTestMailWithPHPMailerOAuth();
        $this->assertTrue($result instanceof PHPMailerOAuth);

        $gmailOauth->setMailTo($faker->email);
        $gmailOauth->setSendTestMail(0);
        $result = $gmailOauth->sendTestMailWithPHPMailerOAuth();
        $this->assertTrue($result instanceof PHPMailerOAuth);

        $gmailOauth = new GmailOAuth();
        $gmailOauth->setFromAccount($faker->email);
        $gmailOauth->setSenderEmail($faker->email);
        $gmailOauth->setMailTo($faker->email);
        $gmailOauth->setSendTestMail(1);
        
        //We cannot get a valid 'refresh token', therefore we wait for an exception 
        //when trying to send a email.
        $this->expectException(BadMethodCallException::class);
        $gmailOauth->sendTestMailWithPHPMailerOAuth();
    }

    /**
     * This ensures proof of get message body.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getMessageBody()
     */
    public function it_should_get_message_body()
    {
        $gmailOauth = new GmailOAuth();
        $result = $gmailOauth->getMessageBody();
        $this->assertTrue(is_string($result));
    }

    /**
     * This ensures that it is saved in the APP_MESSAGE table.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::saveIntoAppMessage()
     */
    public function it_should_save_into_app_message_table()
    {
        $faker = $this->faker;
        $gmailOauth = new GmailOAuth();

        $gmailOauth->setFromAccount($faker->email);
        $gmailOauth->setSenderEmail($faker->email);
        $gmailOauth->setMailTo($faker->email);

        try {
            $gmailOauth->saveIntoAppMessage("pending");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertTrue(true);

        try {
            $gmailOauth->saveIntoAppMessage("sent");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertTrue(true);
    }

    /**
     * This ensures that it is saved in the Standard Log table.
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::saveIntoStandardLogs()
     */
    public function it_should_save_into_standard_log()
    {
        $faker = $this->faker;
        $gmailOauth = new GmailOAuth();

        $gmailOauth->setFromAccount($faker->email);
        $gmailOauth->setSenderEmail($faker->email);
        $gmailOauth->setMailTo($faker->email);

        try {
            $gmailOauth->saveIntoStandardLogs("pending");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertTrue(true);

        try {
            $gmailOauth->saveIntoStandardLogs("sent");
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->assertTrue(true);
    }

    /**
     * It tests that the message body contains the link to the image
     *
     * @test
     * @covers \ProcessMaker\GmailOAuth\GmailOAuth::getMessageBody()
     */
    public function it_should_tests_the_get_message_body_method()
    {
        // Create the GmailOAuth object
        $gmailOauth = new GmailOAuth();

        // Call the getMessageBody method
        $res = $gmailOauth->getMessageBody();

        // Assert the result contains the server protocol and host
        $this->assertMatchesRegularExpression("#" . System::getServerProtocol() . System::getServerHost() . "#", $res);
    }
}
