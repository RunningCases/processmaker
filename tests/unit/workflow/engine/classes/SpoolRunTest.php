<?php

use Faker\Factory;
use Illuminate\Http\UploadedFile;
use ProcessMaker\Model\AppMessage;
use ProcessMaker\Model\EmailServerModel;
use Tests\TestCase;

class SpoolRunTest extends TestCase
{

    /**
     * Constructor of the class.
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Check if "envelope_cc" and "envelope_bcc" was set correctly in consecutive calls
     *
     * @covers \SpoolRun::setData()
     *
     * @test
     */
    public function it_should_check_if_cc_and_bcc_set_correctly_in_consecutive_calls()
    {
        // Initializing Faker instance
        $faker = Factory::create();

        // Instancing SpoolRun class
        $spoolRun = new SpoolRun();

        // Set a first set of data
        $spoolRun->setData(
                G::generateUniqueID(),
                $faker->words(3, true),
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );

        // Build the "to", "cc" an "bcc" values
        $spoolRun->runHandleEnvelopeTo();

        // Set a second set of data
        $spoolRun->setData(
                G::generateUniqueID(),
                $faker->words(3, true),
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );

        // Build the "to", "cc" an "bcc" values
        $spoolRun->runHandleEnvelopeTo();

        // Get data to check
        $fileData = $spoolRun->getFileData();

        // Asserts
        $this->assertCount(1, $fileData['envelope_to']);
        $this->assertCount(1, $fileData['envelope_cc']);
        $this->assertCount(1, $fileData['envelope_bcc']);
    }

    /**
     * This test uses the GMAILAPI option in a simple way.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_gmail_oauth_option()
    {
        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->states('GMAILAPI')->make();

        $config = $emailServer->toArray();
        $config['SMTPSecure'] = 'ssl';

        $faker = Factory::create();
        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $faker->title,
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }

    /**
     * This test uses the MAIL option in a simple way.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_mail_option()
    {
        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->create();

        $config = $emailServer->toArray();

        $faker = Factory::create();
        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $faker->title,
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }

    /**
     * This test uses the PHPMAILER option in a simple way.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_php_mailer_option()
    {
        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->states('PHPMAILER')->make();

        $config = $emailServer->toArray();
        $config['SMTPSecure'] = 'ssl';

        $faker = Factory::create();
        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $faker->title,
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }

    /**
     * This test uses the OPENMAIL option in a simple way.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_open_mail_option()
    {
        $this->markTestIncomplete("The OPENMAIL depends on the package class but this is not found in the environment.");

        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->states('OPENMAIL')->make();

        $config = $emailServer->toArray();

        $faker = Factory::create();
        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $faker->title,
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }

    /**
     * This test ensures that characters that are not utf8 are converted properly,
     * for subject and body fields.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_utf8_characters()
    {
        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->states('PHPMAILER')->make();

        $config = $emailServer->toArray();
        $config['SMTPSecure'] = 'ssl';

        $faker = Factory::create();
        $subject = "\xf8foo";
        $body = "\xf8foo\xf8foo";

        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $subject,
                $faker->companyEmail,
                $faker->freeEmail,
                $body,
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail
        );
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }

    /**
     * This test verifies the sending of attachments to the email.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_attachment_files()
    {
        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->states('PHPMAILER')->make();

        $config = $emailServer->toArray();
        $config['SMTPSecure'] = 'ssl';

        $faker = Factory::create();

        $file1 = UploadedFile::fake()->image('avatar.jpg', 400, 300);
        $file2 = UploadedFile::fake()->create('document.pdf', 200);

        $files = [
            $file1->path(),
            $file2->path()
        ];

        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $faker->title,
                $faker->companyEmail,
                $faker->freeEmail,
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->companyEmail,
                $faker->freeEmail,
                '',
                $files
        );
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }

    /**
     * This test ensures that the EnvelopeTo field process is working.
     * @test
     * @covers \SpoolRun::__construct()
     * @covers \SpoolRun::setData()
     * @covers \SpoolRun::runHandleEnvelopeTo()
     * @covers \SpoolRun::handleEnvelopeTo()
     * @covers \SpoolRun::setConfig()
     * @covers \SpoolRun::sendMail()
     * @covers \SpoolRun::handleMail()
     */
    public function it_should_handle_envelope_to()
    {
        $appMsgUid = G::generateUniqueID();
        factory(AppMessage::class)->create([
            'APP_MSG_UID' => $appMsgUid
        ]);

        $emailServer = factory(EmailServerModel::class)->states('PHPMAILER')->make();

        $config = $emailServer->toArray();
        $config['SMTPSecure'] = 'ssl';

        $faker = Factory::create();
        $spoolRun = new SpoolRun();
        $spoolRun->setData(
                $appMsgUid,
                $faker->title,
                $faker->name . "<" . $faker->companyEmail . "," . $faker->companyEmail . ">",
                $faker->name . "<" . $faker->freeEmail . "," . $faker->freeEmail . ">",
                $faker->text(),
                $faker->dateTime()->format('Y-m-d H:i:s'),
                $faker->name . "<" . $faker->companyEmail . "," . $faker->companyEmail . ">",
                $faker->name . "<" . $faker->freeEmail . "," . $faker->freeEmail . ">"
        );
        $spoolRun->runHandleEnvelopeTo();
        $spoolRun->setConfig($config);

        $expected = $spoolRun->sendMail();

        $this->assertTrue($expected);
    }
}
