<?php

namespace Tests\unit\gulliver\system;

use Bootstrap;
use Faker\Factory;
use Tests\TestCase;

class BootstrapTest extends TestCase
{
    private $faker;

    /**
     * Set up method.
     */
    public function setUp()
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
        $this->assertRegexp("/{$filename}/", $result);
        $this->assertRegexp("/font-face/", $result);
        $this->assertRegexp("/font-family/", $result);

        $filename = "jscolors";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertRegexp("/{$filename}/", $result);

        $filename = "xmlcolors";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertRegexp("/{$filename}/", $result);

        $filename = "classic";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertRegexp("/{$filename}/", $result);
        $this->assertRegexp("/font-family/", $result);
        $this->assertRegexp("/ss_group_suit/", $result);

        $filename = "classic-extjs";
        $result = Bootstrap::streamCSSBigFile($filename);

        //add more assertions
        $this->assertRegexp("/{$filename}/", $result);
    }
}
