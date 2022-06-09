<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Propel;

abstract class TestCase extends BaseTestCase
{
    /**
     *
     * @var object 
     */
    protected $currentConfig;

    /**
     *
     * @var string 
     */
    protected $currentArgv;

    /**
     * Create application
     */
    use CreatesApplication;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        /**
         * Method Tests\CreatesApplication::createApplication() restarts the application 
         * and the values loaded in bootstrap.php have been lost, for this reason 
         * it is necessary to save the following values.
         */
        $this->currentConfig = app('config');
        $this->currentArgv = $_SERVER['argv'];
        parent::__construct($name, $data, $dataName);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        /**
         * Lost argv are restored.
         */
        if (empty($_SERVER['argv'])) {
            $_SERVER['argv'] = $this->currentArgv;
        }
        parent::setUp();
        /**
         * Lost config are restored.
         */
        app()->instance('config', $this->currentConfig);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
