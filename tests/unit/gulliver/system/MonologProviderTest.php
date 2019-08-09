<?php

namespace Tests\unit\gulliver\system;

use MonologProvider;
use Tests\TestCase;

/**
 * @coversDefaultClass \MonologProvider
 */
class MonologProviderTest extends TestCase
{
    /**
     * It tests an undefined level
     *
     * @covers ::setLevelDebug
     * @test
     */
    public function it_check_log_when_logging_level_is_undefined()
    {
        $log = MonologProvider::getSingleton('Channel Test', 'processmaker.log', true);
        // Define the logging_level = UNDEFINED value
        $log->setLevelDebug('UNDEFINED');
        $this->assertEmpty($log->getLevelDebug());
        // Register a log debug
        $res = $log->addLog(100, 'This test can not be registered', []);
        // Check that the DEBUG was not registered
        $this->assertFalse($res);
        // Register a log info
        $res = $log->addLog(200, 'This test can not be registered', []);
        // Check that the INFO was not registered
        $this->assertFalse($res);
        // Register a log warning
        $res = $log->addLog(300, 'This test can not be registered', []);
        // Check that the WARNING was not registered
        $this->assertFalse($res);
        // Register a log error
        $res = $log->addLog(400, 'This test can not be registered', []);
        // Check that the ERROR was not registered
        $this->assertFalse($res);
        // Register a log critical
        $res = $log->addLog(500, 'This test can not be registered', []);
        // Check that the CRITICAL was not registered
        $this->assertFalse($res);
    }

    /**
     * It tests that the log register from NONE, it to turn off the log
     *
     * @covers ::addLog
     * @test
     */
    public function it_check_log_when_logging_level_is_turn_off()
    {
        $log = MonologProvider::getSingleton('Channel Test', 'processmaker.log', true);
        // Define the logging_level = NONE
        $log->setLevelDebug('NONE');
        $this->assertEmpty($log->getLevelDebug());
        // Register a log debug
        $res = $log->addLog(100, 'This test can not be registered', []);
        // Check that the DEBUG was not registered
        $this->assertFalse($res);
        // Register a log info
        $res = $log->addLog(200, 'This test can not be registered', []);
        // Check that the INFO was not registered
        $this->assertFalse($res);
        // Register a log warning
        $res = $log->addLog(300, 'This test can not be registered', []);
        // Check that the WARNING was not registered
        $this->assertFalse($res);
        // Register a log error
        $res = $log->addLog(400, 'This test can not be registered', []);
        // Check that the ERROR was not registered
        $this->assertFalse($res);
        // Register a log critical
        $res = $log->addLog(500, 'This test can not be registered', []);
        // Check that the CRITICAL was not registered
        $this->assertFalse($res);
    }

    /**
     * It tests that the log register from INFO
     *
     * @covers ::addLog
     * @test
     */
    public function it_check_log_when_logging_level_is_info()
    {
        $log = MonologProvider::getSingleton('Channel Test', 'processmaker.log', true);
        // Define the logging_level = INFO (200)
        $log->setLevelDebug('INFO');
        $this->assertEquals($log->getLevelDebug(), 200);
        // Register a log debug
        $res = $log->addLog(100, 'This test can not be registered', []);
        // Check that the DEBUG was not registered
        $this->assertFalse($res);
        // Register a log info
        $res = $log->addLog(200, 'Test', []);
        // Check that the INFO was registered
        $this->assertTrue($res);
        // Register a log warning
        $res = $log->addLog(300, 'Test', []);
        // Check that the WARNING was registered
        $this->assertTrue($res);
        // Register a log error
        $res = $log->addLog(400, 'Test', []);
        // Check that the ERROR was registered
        $this->assertTrue($res);
        // Register a log critical
        $res = $log->addLog(500, 'Test', []);
        // Check that the CRITICAL was registered
        $this->assertTrue($res);
    }

    /**
     * It tests that the log register from WARNING
     *
     * @covers ::addLog
     * @test
     */
    public function it_check_log_when_logging_level_is_warning()
    {
        $log = MonologProvider::getSingleton('Channel Test', 'processmaker.log', true);
        // Define the logging_level WARNING (300)
        $log->setLevelDebug('WARNING');
        $this->assertEquals($log->getLevelDebug(), 300);
        // Register a log debug
        $res = $log->addLog(100, 'This test can not be registered', []);
        // Check that the DEBUG was not registered
        $this->assertFalse($res);
        // Register a log info
        $res = $log->addLog(200, 'This test can not be registered', []);
        // Check that the INFO was not registered
        $this->assertFalse($res);
        // Register a log warning
        $res = $log->addLog(300, 'Test', []);
        // Check that the WARNING was registered
        $this->assertTrue($res);
        // Register a log error
        $res = $log->addLog(400, 'Test', []);
        // Check that the ERROR was registered
        $this->assertTrue($res);
        // Register a log critical
        $res = $log->addLog(500, 'Test', []);
        // Check that the CRITICAL was registered
        $this->assertTrue($res);
    }

    /**
     * It tests that the log register from ERROR
     *
     * @covers ::addLog
     * @test
     */
    public function it_check_log_when_logging_level_is_error()
    {
        $log = MonologProvider::getSingleton('Channel Test', 'processmaker.log', true);
        // Define the logging_level ERROR (400)
        $log->setLevelDebug('ERROR');
        $this->assertEquals($log->getLevelDebug(), 400);
        // Register a log debug
        $res = $log->addLog(100, 'This test can not be registered', []);
        // Check that the DEBUG was not registered
        $this->assertFalse($res);
        // Register a log info
        $res = $log->addLog(200, 'This test can not be registered', []);
        // Check that the INFO was not registered
        $this->assertFalse($res);
        // Register a log warning
        $res = $log->addLog(300, 'This test can not be registered', []);
        // Check that the WARNING was not registered
        $this->assertFalse($res);
        // Register a log error
        $res = $log->addLog(400, 'Test', []);
        // Check that the ERROR was registered
        $this->assertTrue($res);
        // Register a log critical
        $res = $log->addLog(500, 'Test', []);
        // Check that the CRITICAL was registered
        $this->assertTrue($res);
    }

    /**
     * It tests that the log register from CRITICAL
     *
     * @covers ::addLog
     * @test
     */
    public function it_check_log_when_logging_level_is_critical()
    {
        $log = MonologProvider::getSingleton('Channel Test', 'processmaker.log', true);
        // Define the logging_level CRITICAL (500)
        $log->setLevelDebug('CRITICAL');
        $this->assertEquals($log->getLevelDebug(), 500);
        // Register a log debug
        $res = $log->addLog(100, 'This test can not be registered', []);
        // Check that the DEBUG was not registered
        $this->assertFalse($res);
        // Register a log info
        $res = $log->addLog(200, 'This test can not be registered', []);
        // Check that the INFO was not registered
        $this->assertFalse($res);
        // Register a log warning
        $res = $log->addLog(300, 'This test can not be registered', []);
        // Check that the WARNING was not registered
        $this->assertFalse($res);
        // Register a log error
        $res = $log->addLog(400, 'This test can not be registered', []);
        // Check that the ERROR was not registered
        $this->assertFalse($res);
        // Register a log critical
        $res = $log->addLog(500, 'Test', []);
        // Check that the CRITICAL was registered
        $this->assertTrue($res);
    }
}
