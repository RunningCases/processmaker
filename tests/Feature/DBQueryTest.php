<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\DbSource;
use ProcessMaker\Model\Process;
use Tests\TestCase;
use Propel;
use DbConnections;

class DBQueryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic cache example.
     *
     * @return void
     */
    public function testStandardExecuteQuery()
    {
        $results = executeQuery("SELECT * FROM USERS WHERE USR_UID = '00000000000000000000000000000001'");
        $this->assertCount(1, $results);
        // Note, we check index 1 because results from executeQuery are 1 indexed, not 0 indexed.
        $this->assertArraySubset([
            'USR_UID' => '00000000000000000000000000000001',
            'USR_USERNAME' => 'admin'
        ], $results[1]);
    }

    public function testDBFacadeQuery()
    {
        $record = DB::table('USERS')->where([
            'USR_UID' => '00000000000000000000000000000001'
        ])->first();
        $this->assertEquals('admin', $record->USR_USERNAME);
    }

    public function testStandardExecuteQueryWithExternalMySQLDatabase()
    {
        // Our test external database is created in our tests/bootstrap.php file
        // We'll use our factories to create our process and database
        $process = factory(Process::class)->create();
        // Let's create an external DB to ourselves
        $externalDB = factory(DbSource::class)->create([
            'DBS_SERVER' => 'localhost',
            'DBS_PORT' => '3306',
            'DBS_USERNAME' => env('DB_USERNAME'),
            // Remember, we have to do some encryption here @see DbSourceFactory.php
            'DBS_PASSWORD' => \G::encrypt( env('DB_PASSWORD'), 'testexternal') . "_2NnV3ujj3w",
            'DBS_DATABASE_NAME' => 'testexternal',
            'PRO_UID' => $process->PRO_UID
        ]);

        // Now set our process ID
        /**
         * @todo Migrate to non session based process store
         */
        $_SESSION['PROCESS'] = $process->PRO_UID;
        // Perform test
        $results = executeQuery('SELECT * FROM test', $externalDB->DBS_UID);
        $this->assertCount(1, $results);
        $this->assertEquals('testvalue', $results[1]['value']);
    }

    public function testStandardExecuteQueryWithExternalMSSqlDatabase()
    {
        if(!env('RUN_MSSQL_TESTS')) {
            $this->markTestSkipped('MSSQL Related Test Skipped');
        }
        // Our test external database is created in our tests/bootstrap.php file
        // We'll use our factories to create our process and database
        $process = factory(Process::class)->create();
        // Let's create an external DB to ourselves
        $externalDB = factory(DbSource::class)->create([
            'DBS_SERVER' => env('MSSQL_HOST'),
            'DBS_PORT' => env('MSSQL_PORT'),
            'DBS_TYPE' => 'mssql',
            'DBS_USERNAME' => env('MSSQL_USERNAME'),
            // Remember, we have to do some encryption here @see DbSourceFactory.php
            'DBS_PASSWORD' => \G::encrypt( env('MSSQL_PASSWORD'), 'testexternal') . "_2NnV3ujj3w",
            'DBS_DATABASE_NAME' => 'testexternal',
            'PRO_UID' => $process->PRO_UID
        ]);
        // Now set our process ID
        /**
         * @todo Migrate to non session based process store
         */
        $_SESSION['PROCESS'] = $process->PRO_UID;
        // Perform test
        $results = executeQuery('SELECT * FROM test', $externalDB->DBS_UID);
        $this->assertCount(1, $results);
        $this->assertEquals('testvalue', $results[1]['value']);
    }
}