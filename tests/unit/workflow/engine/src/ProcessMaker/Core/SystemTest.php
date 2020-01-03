<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Core;

use G;
use Faker\Factory;
use ProcessMaker\Core\System;
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
     * It tests the initLaravel method
     *
     * @test
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
}
