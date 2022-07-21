<?php

namespace Tests\unit\gulliver\system;

use Bootstrap;
use Faker\Factory;
use Illuminate\Support\Facades\File;
use ProcessMaker\Core\System;
use Tests\TestCase;

class BootstrapTest extends TestCase
{
    private $faker;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * This tests if the content of the css files is being generated correctly.
     * @test
     * @covers \Bootstrap::streamCSSBigFile()
     */
    public function it_should_test_the_generation_of_css_files()
    {
        $userAgent = $this->faker->userAgent;
        $_SERVER ['HTTP_USER_AGENT'] = $userAgent;
        $filename = "neoclassic";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertMatchesRegularExpression("/{$filename}/", $result);
        $this->assertMatchesRegularExpression("/font-face/", $result);
        $this->assertMatchesRegularExpression("/font-family/", $result);

        $filename = "jscolors";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertMatchesRegularExpression("/{$filename}/", $result);

        $filename = "xmlcolors";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertMatchesRegularExpression("/{$filename}/", $result);

        $filename = "classic";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertMatchesRegularExpression("/{$filename}/", $result);
        $this->assertMatchesRegularExpression("/font-family/", $result);
        $this->assertMatchesRegularExpression("/ss_group_suit/", $result);

        $filename = "classic-extjs";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertMatchesRegularExpression("/{$filename}/", $result);
    }

    /**
     * Return logging level code.
     */
    public function levelCode()
    {
        //the level record depends on env.ini, by default the records are shown 
        //starting from info (200) and the debug level (100) is excluded.
        return[
            [200],
            [250],
            [300],
            [400],
            [500],
            [550],
            [600]
        ];
    }

    /**
     * This test the registerMonolog method.
     * @test
     * @covers Bootstrap::registerMonolog()
     * @dataProvider levelCode
     */
    public function it_should_test_registerMonolog_method($level)
    {
        $channel = 'test';
        $message = 'test';
        $context = [];
        Bootstrap::registerMonolog($channel, $level, $message, $context);

        $result = '';
        $files = File::allFiles(PATH_DATA_SITE . '/log');
        foreach ($files as $value) {
            $result = $result . File::get($value->getPathname());
        }
        $this->assertMatchesRegularExpression("/{$channel}/", $result);
    }

    /**
     * This test the getDefaultContextLog method.
     * @test
     * @covers Bootstrap::getDefaultContextLog()
     */
    public function it_should_test_getDefaultContextLog_method()
    {
        $result = Bootstrap::getDefaultContextLog();
        $this->assertArrayHasKey('ip', $result);
        $this->assertArrayHasKey('workspace', $result);
        $this->assertArrayHasKey('timeZone', $result);
        $this->assertArrayHasKey('usrUid', $result);
    }

    /**
     * This test the registerMonologPhpUploadExecution method.
     * @test
     * @covers Bootstrap::registerMonologPhpUploadExecution()
     * @dataProvider levelCode
     */
    public function it_should_test_registerMonologPhpUploadExecution_method($level)
    {
        $channel = 'test';
        $message = 'test';
        $fileName = 'test';
        Bootstrap::registerMonologPhpUploadExecution($channel, $level, $message, $fileName);

        $result = '';
        $files = File::allFiles(PATH_DATA_SITE . '/log');
        foreach ($files as $value) {
            $result = $result . File::get($value->getPathname());
        }
        $this->assertMatchesRegularExpression("/{$channel}/", $result);
    }
}
