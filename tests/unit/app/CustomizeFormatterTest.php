<?php

namespace Tests\unit\app;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CustomizeFormatterTest extends TestCase
{
    private static $directory;

    /**
     * This is executed for each test.
     */
    protected function setUp()
    {
        parent::setUp();
        self::$directory = PATH_TRUNK . '/storage/logs/';
    }

    /**
     * This is done before the first test.
     */
    public static function setUpBeforeClass()
    {
        $file = new Filesystem();
        $file->cleanDirectory(self::$directory);
    }

    /**
     * This is done after the last test.
     */
    public static function tearDownAfterClass()
    {
        $file = new Filesystem();
        $file->cleanDirectory(self::$directory);
    }

    /**
     * Return all of the log levels defined in the RFC 5424 specification.
     * @return array
     */
    public function levelProviders()
    {
        return [
            ['emergency', 'production.EMERGENCY'],
            ['alert', 'production.ALERT'],
            ['critical', 'production.CRITICAL'],
            ['error', 'production.ERROR'],
            ['warning', 'production.WARNING'],
            ['notice', 'production.NOTICE'],
            ['info', 'production.INFO'],
            ['debug', 'production.DEBUG'],
        ];
    }

    /**
     * This check the creation of a record with the emergency level.
     * @test
     * @dataProvider levelProviders
     */
    public function it_should_create_log_file_levels($level, $message)
    {
        Log::{$level}($level);
        $files = File::allFiles(self::$directory);
        $this->assertCount(1, $files);

        $string = File::get($files[0]);
        $this->assertRegExp("/{$message}/", $string);
    }
}
