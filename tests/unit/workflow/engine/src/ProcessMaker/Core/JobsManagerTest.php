<?php

namespace ProcessMaker\Core;

use App\Jobs\Email;
use Tests\TestCase;

class JobsManagerTest extends TestCase
{
    /**
     * @var JobsManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->object = new JobsManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * This should return the configured value of delay in env.ini
     * 
     * @test
     * @covers ProcessMaker\Core\JobsManager::getDelay
     */
    public function testGetDelay()
    {
        $this->object->init();
        $actual = $this->object->getDelay();

        $envs = System::getSystemConfiguration('', '', config("system.workspace"));

        $this->assertEquals($envs['delay'], $actual);
    }

    /**
     * This should return the configured value of tries in env.ini
     * 
     * @test
     * @covers ProcessMaker\Core\JobsManager::getTries
     */
    public function testGetTries()
    {
        $this->object->init();
        $actual = $this->object->getTries();

        $envs = System::getSystemConfiguration('', '', config("system.workspace"));

        $this->assertEquals($envs['tries'], $actual);
    }

    /**
     * This should return the configured value of retry_after in env.ini
     * 
     * @test
     * @covers ProcessMaker\Core\JobsManager::getRetryAfter
     */
    public function testGetRetryAfter()
    {
        $this->object->init();
        $actual = $this->object->getRetryAfter();

        $envs = System::getSystemConfiguration('', '', config("system.workspace"));

        $this->assertEquals($envs['retry_after'], $actual);
    }

    /**
     * This returns a single instance of the object (this is a singleton).
     * @test
     * @covers ProcessMaker\Core\JobsManager::getSingleton
     */
    public function testGetSingleton()
    {
        $object1 = $this->object->getSingleton();
        $this->assertEquals($this->object, $object1);

        $object2 = $this->object->getSingleton();
        $this->assertEquals($this->object, $object2);
    }

    /**
     * If the object was started correctly returns the instance of this object.
     * 
     * @test
     * @covers ProcessMaker\Core\JobsManager::init
     */
    public function testInit()
    {
        $actual = $this->object->init();

        $this->assertEquals($this->object, $actual);
    }

    /**
     * This must return the instance of the object that prepares the work for dispatch.
     * 
     * @test
     * @covers ProcessMaker\Core\JobsManager::dispatch
     */
    public function testDispatch()
    {
        $callback = function() {
        };

        $actual = $this->object->dispatch(\App\Jobs\Email::class, $callback);

        $this->assertInstanceOf(\Illuminate\Foundation\Bus\PendingDispatch::class, $actual);
    }

    /**
     * This gets the value of the option specified in the second parameter from an 
     * array that represents the arguments.
     * 
     * @test
     * @covers ProcessMaker\Core\JobsManager::getOptionValueFromArguments
     */
    public function testGetOptionValueFromArguments()
    {
        $optionName = "--workspace";
        $valueOption = "workflow";
        $allocationSeparator = "=";

        $parameter0 = "queue:work";
        $parameter1 = $optionName . $allocationSeparator . $valueOption;

        $arguments = [$parameter0, $parameter1];

        $actual = $this->object->getOptionValueFromArguments($arguments, $optionName);
        $this->assertEquals($valueOption, $actual);

        $actual = $this->object->getOptionValueFromArguments($arguments, $optionName, $allocationSeparator);
        $this->assertEquals($valueOption, $actual);

        $actual = $this->object->getOptionValueFromArguments($arguments, "missing");
        $this->assertEquals(false, $actual);
    }
}
