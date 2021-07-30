<?php

namespace Tests\unit\workflow\src\ProcessMaker\Model;

use G;
use ProcessMaker\Model\UserConfig;
use Tests\TestCase;

class UserConfigTest extends TestCase
{

    /**
     * Setup method,
     */
    public function setUp()
    {
        parent::setUp();
        UserConfig::truncate();
    }

    /**
     * Teardown method.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * It test the method getSetting.
     * @test
     * @covers \ProcessMaker\Model\UserConfig::getSetting()
     */
    public function it_should_test_getSetting()
    {
        $id = 1;
        $name = "test";
        $setting = ["test" => 1];
        $result = UserConfig::addSetting($id, $name, $setting);

        //assert get
        $result = UserConfig::getSetting($id, $name);
        $this->assertArrayHasKey("id", $result);
        $this->assertArrayHasKey("name", $result);
        $this->assertArrayHasKey("setting", $result);
        $this->assertEquals($result["id"], $id);
        $this->assertEquals($result["name"], $name);
        $this->assertEquals($result["setting"], (object) $setting);
    }

    /**
     * It test the method addSetting.
     * @test
     * @covers \ProcessMaker\Model\UserConfig::addSetting()
     */
    public function it_should_test_addSetting()
    {
        $id = 1;
        $name = "test";
        $setting = ["test" => 1];

        $result = UserConfig::addSetting($id, $name, $setting);
        $this->assertArrayHasKey("id", $result);
        $this->assertArrayHasKey("name", $result);
        $this->assertArrayHasKey("setting", $result);
        $this->assertEquals($result["id"], $id);
        $this->assertEquals($result["name"], $name);
        $this->assertEquals($result["setting"], (object) $setting);
    }

    /**
     * It test the method editSetting.
     * @test
     * @covers \ProcessMaker\Model\UserConfig::editSetting()
     */
    public function it_should_test_editSetting()
    {
        $id = 1;
        $name = "test";
        $setting = ["test" => 1];
        $result = UserConfig::addSetting($id, $name, $setting);

        //assert edit
        $setting = ["test" => 2, "test2" => 3];
        $result = UserConfig::editSetting($id, $name, $setting);
        $this->assertArrayHasKey("id", $result);
        $this->assertArrayHasKey("name", $result);
        $this->assertArrayHasKey("setting", $result);
        $this->assertEquals($result["id"], $id);
        $this->assertEquals($result["name"], $name);
        $this->assertEquals($result["setting"], (object) $setting);
    }

    /**
     * It test the method deleteSetting.
     * @test
     * @covers \ProcessMaker\Model\UserConfig::deleteSetting()
     */
    public function it_should_test_deleteSetting()
    {
        $id = 2;
        $name = "test2";
        $setting = ["test2" => 1];
        $result = UserConfig::addSetting($id, $name, $setting);

        //assert delete
        $result = UserConfig::deleteSetting($id, $name);
        $this->assertArrayHasKey("id", $result);
        $this->assertArrayHasKey("name", $result);
        $this->assertArrayHasKey("setting", $result);
        $this->assertEquals($result["id"], $id);
        $this->assertEquals($result["name"], $name);
    }
}
