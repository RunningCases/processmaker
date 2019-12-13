<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel;

use Exception;
use Faker\Factory;
use G;
use ProcessMaker\BusinessModel\EmailServer;
use ProcessMaker\Model\EmailServerModel;
use Tests\TestCase;

class EmailServerTest extends TestCase
{
    private $emailServer;
    private $faker;

    /**
     * set up function.
     */
    public function setUp()
    {
        parent::setUp();
        $this->emailServer = new EmailServer();
        $this->faker = Factory::create();
    }

    /**
     * Get structure for registry the EMAIL_SERVER.
     * @return array
     */
    private function getDataForEmailServerRegistry(): array
    {
        $faker = $this->faker;
        return [
            'MESS_ENGINE' => 'PHPMAILER',
            'MESS_SERVER' => 'smtp.' . $faker->domainName,
            'MESS_PORT' => $faker->numberBetween(400, 500),
            'MESS_INCOMING_SERVER' => '',
            'MESS_INCOMING_PORT' => $faker->numberBetween(400, 500),
            'MESS_RAUTH' => 1,
            'MESS_ACCOUNT' => $faker->email,
            'MESS_PASSWORD' => $faker->password,
            'MESS_FROM_MAIL' => $faker->email,
            'MESS_FROM_NAME' => $faker->name,
            'SMTPSECURE' => 'ssl',
            'MESS_TRY_SEND_INMEDIATLY' => 1,
            'MAIL_TO' => $faker->email,
            'MESS_DEFAULT' => 1,
            'OAUTH_CLIENT_ID' => '',
            'OAUTH_CLIENT_SECRET' => '',
            'OAUTH_REFRESH_TOKEN' => '',
        ];
    }

    /**
     * This creates a record in the EMAIL_SERVER table.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::create()
     */
    public function it_should_create()
    {
        $faker = $this->faker;
        $expected = $this->getDataForEmailServerRegistry();
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);
        $actual = $this->emailServer->create($expected);

        $this->assertTrue(isset($actual['MESS_UID']));
        $this->assertTrue(is_string($actual['MESS_UID']));
        $this->assertEquals($expected['MESS_ENGINE'], $actual['MESS_ENGINE']);
        $this->assertEquals($expected['MESS_ACCOUNT'], $actual['MESS_ACCOUNT']);

        $expected['MESS_PASSWORD'] = G::encrypt('hash:' . $faker->password, 'EMAILENCRYPT');
        $actual = $this->emailServer->create($expected);

        $expected['MESS_PASSWORD'] = G::encrypt('hash:' . $faker->password . 'hash:', 'EMAILENCRYPT');
        $actual = $this->emailServer->create($expected);

        $this->expectException(Exception::class);
        $this->emailServer->create([]);
    }

    /**
     * This updates a record in the EMAIL_SERVER table.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::update()
     */
    public function it_should_update()
    {
        $faker = $this->faker;
        $emailServer = factory(EmailServerModel::class)->create($this->getDataForEmailServerRegistry());
        $data = $emailServer->toArray();

        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);

        $expected = [
            'MESS_ENGINE' => 'PHPMAILER',
            'MESS_SERVER' => 'smtp.' . $faker->domainName,
            'MESS_PORT' => $faker->numberBetween(400, 500),
            'MESS_INCOMING_SERVER' => '',
            'MESS_INCOMING_PORT' => $faker->numberBetween(400, 500),
            'MESS_RAUTH' => 1,
            'MESS_ACCOUNT' => $faker->email,
            'MESS_PASSWORD' => $faker->password,
            'MESS_FROM_MAIL' => $faker->email,
            'MESS_FROM_NAME' => $faker->name,
            'SMTPSECURE' => 'ssl',
            'MESS_TRY_SEND_INMEDIATLY' => 1,
            'MAIL_TO' => $faker->email,
            'MESS_DEFAULT' => 1,
        ];
        $actual = $this->emailServer->update($data['MESS_UID'], $expected);

        $this->assertEquals($expected['MESS_ENGINE'], $actual['MESS_ENGINE']);
        $this->assertEquals($expected['MESS_ACCOUNT'], $actual['MESS_ACCOUNT']);

        $expected['MESS_PASSWORD'] = G::encrypt('hash:' . $faker->password, 'EMAILENCRYPT');
        $actual = $this->emailServer->update($data['MESS_UID'], $expected);

        $expected['MESS_PASSWORD'] = G::encrypt('hash:' . $faker->password . 'hash:', 'EMAILENCRYPT');
        $actual = $this->emailServer->update($data['MESS_UID'], $expected);

        $this->emailServer->setFormatFieldNameInUppercase(false);
        $this->expectException(Exception::class);
        $actual = $this->emailServer->update($data['MESS_UID'], $expected);
    }

    /**
     * Get data of a from a record EMAIL_SERVER.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::getEmailServerDataFromRecord()
     */
    public function it_should_get_email_server_data_from_record()
    {
        $faker = $this->faker;
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);
        $expected = $this->getDataForEmailServerRegistry();
        $expected['MESS_UID'] = $faker->regexify("/[a-zA-Z]{32}/");

        $actual = $this->emailServer->getEmailServerDataFromRecord($expected);

        $this->assertEquals($expected['MESS_ENGINE'], $actual['MESS_ENGINE']);
        $this->assertEquals($expected['MESS_ACCOUNT'], $actual['MESS_ACCOUNT']);

        unset($expected['MESS_ENGINE']);
        $this->expectException(Exception::class);
        $actual = $this->emailServer->getEmailServerDataFromRecord($expected);
    }

    /**
     * This test obtains the configuration record that is marked by default.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::getEmailServerDefault()
     */
    public function it_should_get_email_server_default()
    {
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);
        $actual = $this->emailServer->getEmailServerDefault();
        $this->assertNotEmpty($actual);
    }

    /**
     * This test gets the records from the "EMAIL_SERVER" table.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::getEmailServers()
     */
    public function it_should_get_email_servers()
    {
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);
        $actual = $this->emailServer->getEmailServers();
        $this->assertNotEmpty($actual);
    }

    /**
     * This test gets the records from the "EMAIL_SERVER" table with parameters.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::getEmailServers()
     */
    public function it_should_get_email_servers_with_parameters()
    {
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);

        $actual = $this->emailServer->getEmailServers(null, null, null, null, 0);
        $this->assertEmpty($actual);

        $faker = $this->faker;
        $actual = $this->emailServer->getEmailServers(['filter' => $faker->text]);
        $this->assertNotEmpty($actual);

        $actual = $this->emailServer->getEmailServers(null, $faker->text);
        $this->assertNotEmpty($actual);

        $actual = $this->emailServer->getEmailServers(null, "MESS_SERVER");
        $this->assertNotEmpty($actual);

        $actual = $this->emailServer->getEmailServers(null, "MESS_SERVER", "DESC");
        $this->assertNotEmpty($actual);

        $actual = $this->emailServer->getEmailServers(null, "MESS_SERVER", "DESC", 0);
        $this->assertNotEmpty($actual);

        $actual = $this->emailServer->getEmailServers(null, "MESS_SERVER", "DESC", 0, 10);
        $this->assertNotEmpty($actual);

        $this->expectException(Exception::class);
        $actual = $this->emailServer->getEmailServers(null, "MESS_SERVER", "DESC", -1, -10);
    }

    /**
     * This test gets a record of the EMAIL_SERVER table.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::getEmailServer()
     */
    public function it_should_get_email_server()
    {
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);
        $emailServer = factory(EmailServerModel::class)->create($this->getDataForEmailServerRegistry());
        $emailServerUid = $emailServer->MESS_UID;
        $actual = $this->emailServer->getEmailServer($emailServerUid);
        $this->assertNotEmpty($actual);
    }

    /**
     * This test should throw an exception when a record is not found.
     * @test
     * @covers \ProcessMaker\BusinessModel\EmailServer::getEmailServer()
     */
    public function it_should_get_email_server_when_not_exist_registry()
    {
        $faker = $this->faker;
        $this->emailServer->setContextLog([
            'workspace' => 'workflow'
        ]);
        $emailServer = factory(EmailServerModel::class)->create($this->getDataForEmailServerRegistry());
        $emailServerUid = $faker->regexify("/[a-zA-Z]{32}/");

        $this->expectException(Exception::class);
        $actual = $this->emailServer->getEmailServer($emailServerUid);
    }
}
