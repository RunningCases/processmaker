<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Core;

use G;
use Faker\Factory;
use ProcessMaker\Core\System;
use ProcessMaker\Model\EmailServerModel;
use Tests\TestCase;

class SystemTest extends TestCase
{

    /**
     * Define the required variables
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * It tests the initLaravel method.
     * @test
     * @covers \ProcessMaker\Core\System::initLaravel()
     */
    public function it_should_init_laravel_configurations()
    {
        $this->markTestIncomplete("@todo: Please correct this unit test");

        $object = new System();
        $object->initLaravel();

        // Assert that the configurations were set successfully
        $this->assertEquals(DB_HOST, config('database.connections.workflow.host'));
        $this->assertEquals(DB_NAME, config('database.connections.workflow.database'));
        $this->assertEquals(DB_USER, config('database.connections.workflow.username'));
        $this->assertEquals(DB_PASS, config('database.connections.workflow.password'));
    }

    /**
     * This gets the settings for sending email.
     * @test
     * @covers \ProcessMaker\Core\System::getEmailConfiguration()
     */
    public function it_should_get_email_configuration()
    {
        $system = new System();

        //default values
        EmailServerModel::truncate();
        $actual = $system->getEmailConfiguration();
        $this->assertEmpty($actual);

        //new instance
        $emailServer = factory(EmailServerModel::class)->create([
            'MESS_DEFAULT' => 1
        ]);
        $actual = $system->getEmailConfiguration();
        $this->assertNotEmpty($actual);
        $this->assertArrayHasKey('MESS_ENGINE', $actual);
        $this->assertArrayHasKey('OAUTH_CLIENT_ID', $actual);
        $this->assertArrayHasKey('OAUTH_CLIENT_SECRET', $actual);
        $this->assertArrayHasKey('OAUTH_REFRESH_TOKEN', $actual);
    }

    /**
     * It should return default system configuration parameters.
     * @test
     * @covers \ProcessMaker\Core\System::getSystemConfiguration()
     */
    public function it_should_return_default_system_configuration_parameters()
    {
        $result = System::getSystemConfiguration();

        $this->assertArrayHasKey('server_hostname_requests_frontend', $result);
        $this->assertArrayHasKey('disable_php_upload_execution', $result);
        $this->assertArrayHasKey('mobile_offline_tables_download_interval', $result);
    }

    /**
     * It should return default system configuration parameters without workspace.
     * @test
     * @covers \ProcessMaker\Core\System::getSystemConfiguration()
     */
    public function it_should_return_default_system_configuration_parameters_without_workspace()
    {
        config(["system.workspace" => '']);
        putenv("REQUEST_URI=/sysworkflow");

        $result = System::getSystemConfiguration();

        $this->assertArrayHasKey('server_hostname_requests_frontend', $result);
        $this->assertArrayHasKey('disable_php_upload_execution', $result);
        $this->assertArrayHasKey('mobile_offline_tables_download_interval', $result);
    }

    /**
     * It should return system configuration parameters defined inside env file.
     * @test
     * @covers \ProcessMaker\Core\System::getSystemConfiguration()
     */
    public function it_should_return_system_configuration_parameters_defined_inside_env_file()
    {
        $oldContent = "";
        $path = PATH_CONFIG . "env.ini";
        if (file_exists($path)) {
            $oldContent = file_get_contents($path);
        }

        $expected = 30;

        $content = "mobile_offline_tables_download_interval = {$expected};";
        file_put_contents($path, $content);

        $result = System::getSystemConfiguration();
        $actual = $result['mobile_offline_tables_download_interval'];

        file_put_contents($path, $oldContent);

        $this->assertEquals($expected, $actual);
    }

    /**
     * It should return default system configuration parameters defined inside env file when is not integer.
     * @test
     * @covers \ProcessMaker\Core\System::getSystemConfiguration()
     */
    public function it_should_return_default_system_configuration_parameters_defined_inside_env_file_when_is_not_an_integer()
    {
        $oldContent = "";
        $path = PATH_CONFIG . "env.ini";
        if (file_exists($path)) {
            $oldContent = file_get_contents($path);
        }

        $faker = $faker = Factory::create();
        $alphanumeric = $faker->regexify('[A-Za-z0-9]{20}');
        $content = "mobile_offline_tables_download_interval = '{$alphanumeric}';";
        file_put_contents($path, $content);

        $result = System::getSystemConfiguration();

        $expected = (string) $result['mobile_offline_tables_download_interval'];

        file_put_contents($path, $oldContent);

        $this->assertTrue(is_numeric($expected));
    }

    /**
     * It should return proxy_pass defined inside env file.
     * @test
     * @covers \ProcessMaker\Core\System::getSystemConfiguration()
     */
    public function it_should_return_proxy_pass_defined_inside_env_file()
    {
        $oldContent = "";
        $path = PATH_CONFIG . "env.ini";
        if (file_exists($path)) {
            $oldContent = file_get_contents($path);
        }

        $faker = $faker = Factory::create();
        $content = "proxy_pass = '{$faker->password}';";
        file_put_contents($path, $content);

        $result = System::getSystemConfiguration();

        file_put_contents($path, $oldContent);

        $this->assertArrayHasKey("proxy_pass", $result);
    }
    
    /**
     * This represents a set of connection strings that make up the dsn string.
     * https://processmaker.atlassian.net/browse/PMCORE-574
     * @return array
     */
    public function dsnConections()
    {
        return [
            ["oci8", "user1", "das#4dba", "localhost", "1521", "testDatabase?encoding=utf8"],
            ["mssql", "user1", "Sample12345!@#", "localhost", "1433", "testDatabase?encoding=utf8"],
            ["mysqli", "user1", "123*/.abc-+", "localhost", "3306", "testDatabase?encoding=utf8"],
            ["mysqli", "user1", "123*/.abc-+", "localhost", "", "testDatabase?encoding=utf8"],
            ["sqlite", "user1", "das#4dba", "localhost", "", "testDatabase?encoding=utf8"],
            ["sybase", "user1", "123!das#4dba", "localhost", "1433", "testDatabase?encoding=utf8"],
            ["sybase", "user1", "123!das@#4dba", "localhost", "1433", "testDatabase?encoding=utf8"],
            ["sybase", "user1", "123!das@#4db@a", "localhost", "1433", "testDatabase?encoding=utf8"],
        ];
    }

    /**
     * This tests the parsing of the dsn string.
     * @test
     * @dataProvider dsnConections
     * @covers \Creole::parseDSN()
     * @covers \ProcessMaker\Core\System::parseUrlWithNotEncodedPassword()
     */
    public function it_should_return_parse_url_for_dsn_string_with_special_characters($scheme, $user, $password, $host, $port, $database)
    {
        $hostname = $host;
        if (!empty($port)) {
            $hostname = $host . ":" . $port;
        }
        $dsn = $scheme . "://" . $user . ":" . $password . "@" . $hostname . "/" . $database;
        $result = System::parseUrlWithNotEncodedPassword($dsn);
        $this->assertEquals($scheme, $result["scheme"]);
        $this->assertEquals($user, $result["user"]);
        $this->assertEquals($password, $result["pass"]);
        $this->assertEquals($host, $result["host"]);
        if (!empty($port)) {
            $this->assertEquals($port, $result["port"]);
        }

        $dsn = $scheme;
        $result = System::parseUrlWithNotEncodedPassword($dsn);
        $this->assertEmpty($result["scheme"]);
        $this->assertEmpty($result["user"]);
        $this->assertEmpty($result["pass"]);
        $this->assertEmpty($result["host"]);
    }
}
