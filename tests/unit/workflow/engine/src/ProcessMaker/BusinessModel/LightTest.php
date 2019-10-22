<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel;

use Faker\Factory;
use ProcessMaker\BusinessModel\Light;
use Tests\TestCase;

class LightTest extends TestCase
{

    /**
     * This verifies that the mobile_offline_tables_download_interval parameter 
     * is defined in the result returned by the getConfiguration() method.
     * 
     * @test
     * @covers \ProcessMaker\BusinessModel\Light::getConfiguration
     */
    public function it_should_return_mobile_offline_tables_download_interval_from_get_configuration_method()
    {
        $param = [
            'fileLimit' => true,
            'tz' => true,
        ];
        $light = new Light();

        /**
         * In the getConfiguration() method, the next section:
         * 
         * $postMaxSize = $this->return_bytes(ini_get('post_max_size'));
         * $uploadMaxFileSize = $this->return_bytes(ini_get('upload_max_filesize'));
         * if ($postMaxSize < $uploadMaxFileSize) {
         *     $uploadMaxFileSize = $postMaxSize;
         * }
         * 
         * It can only be tested if you change the values of "post_max_size" and "upload_max_filesize" 
         * in php.ini, you can't use the ini_set() function.
         * The mode change of these directives is "PHP_INI_PERDIR", where is entry can be 
         * set in php.ini, .htaccess, httpd.conf or .user.ini, see here: 
         * https://www.php.net/manual/es/ini.list.php
         * https://www.php.net/manual/en/configuration.changes.modes.php
         */
        $result = $light->getConfiguration($param);

        $this->assertArrayHasKey('mobile_offline_tables_download_interval', $result);
    }

    /**
     * This returns the value of mobile_offline_tables_download_interval
     * @test
     * @covers \ProcessMaker\BusinessModel\Light::getConfiguration
     */
    public function this_should_return_mobile_offline_tables_download_interval_inside_env()
    {
        $oldContent = "";
        $path = PATH_CONFIG . "env.ini";
        if (file_exists($path)) {
            $oldContent = file_get_contents($path);
        }

        $expected = 30;

        $content = "mobile_offline_tables_download_interval = {$expected};";
        file_put_contents($path, $content);

        $light = new Light();
        $result = $light->getConfiguration([]);
        $actual = $result['mobile_offline_tables_download_interval'];

        file_put_contents($path, $oldContent);

        $this->assertEquals($expected, $actual);
    }

    /**
     * This returns the default value of mobile_offline_tables_download_interval.
     * @test
     * @covers \ProcessMaker\BusinessModel\Light::getConfiguration
     */
    public function this_should_return_default_value_if_mobile_offline_tables_download_interval_inside_env_is_not_an_integer()
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

        $light = new Light();
        $result = $light->getConfiguration([]);
        $expected = (string) $result['mobile_offline_tables_download_interval'];

        file_put_contents($path, $oldContent);

        $this->assertTrue(ctype_digit($expected));
    }
}
