<?php

namespace Tests\unit\workflow\engine\classes;

use DbConnections;
use G;
use ProcessMaker\Model\DbSource;
use ProcessMaker\Model\Process;
use Propel;
use Tests\TestCase;

class DbConnectionsTest extends TestCase
{
    private $dbConnections;

    /**
     * Setup method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->dbConnections = new DbConnections();
    }

    /**
     * This test verify loadAdditionalConnections method.
     * @test
     * @covers DbConnections::loadAdditionalConnections
     */
    public function it_should_test_loadAdditionalConnections_method()
    {
        $process = Process::factory()->create();

        $dbName = env('DB_DATABASE');
        $dbSource = DbSource::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DBS_TYPE' => 'mysql',
            'DBS_SERVER' => env('DB_HOST'),
            'DBS_DATABASE_NAME' => $dbName,
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), $dbName, false, false) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
        ]);

        $a = Propel::getConfiguration();


        $_SESSION['PROCESS'] = $process->PRO_UID;
        $this->dbConnections->loadAdditionalConnections();

        $actual = Propel::getConfiguration();

        $this->assertArrayHasKey($dbSource->DBS_UID, $actual['datasources']);
    }

    /**
     * This test verify loadAdditionalConnections method with option true.
     * @test
     * @covers DbConnections::loadAdditionalConnections
     */
    public function it_should_test_loadAdditionalConnections_method_with_force_option_true()
    {
        $process = Process::factory()->create();

        $dbName = env('DB_DATABASE');
        $dbSource = DbSource::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DBS_TYPE' => 'mysql',
            'DBS_SERVER' => env('DB_HOST'),
            'DBS_DATABASE_NAME' => $dbName,
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), $dbName, false, false) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
        ]);

        $_SESSION['PROCESS'] = $process->PRO_UID;
        $this->dbConnections->loadAdditionalConnections(true);

        $actual = Propel::getConfiguration();

        $this->assertArrayHasKey($dbSource->DBS_UID, $actual['datasources']);
    }

    /**
     * This test verify loadAdditionalConnections method with option false.
     * @test
     * @covers DbConnections::loadAdditionalConnections
     */
    public function it_should_test_loadAdditionalConnections_method_with_force_option_false()
    {
        $process = Process::factory()->create();

        $dbName = env('DB_DATABASE');
        $dbSource = DbSource::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'DBS_TYPE' => 'mysql',
            'DBS_SERVER' => env('DB_HOST'),
            'DBS_DATABASE_NAME' => $dbName,
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), $dbName, false, false) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
        ]);

        $_SESSION['PROCESS'] = $process->PRO_UID;
        $this->dbConnections->loadAdditionalConnections(false);

        $actual = Propel::getConfiguration();

        $this->assertArrayHasKey($dbSource->DBS_UID, $actual['datasources']);
    }
}
