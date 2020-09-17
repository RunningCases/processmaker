<?php

namespace Tests\unit\workflow\engine\classes\model;

use AdditionalTables;
use App\Jobs\GenerateReportTable;
use Exception;
use G;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use ProcessMaker\BusinessModel\ReportTable;
use ProcessMaker\Model\AdditionalTables as AdditionalTablesModel;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\DbSource;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use Tests\TestCase;

class AdditionalTablesTest extends TestCase
{

    /**
     * Set up method.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * This tests the creation of a PMTable.
     * @test
     * @covers \AdditionalTables::create()
     */
    public function it_should_create()
    {
        $data = [
            "ADD_TAB_UID" => "",
            "ADD_TAB_NAME" => "PMT_TEST11",
            "ADD_TAB_CLASS_NAME" => "PmtTest11",
            "ADD_TAB_DESCRIPTION" => "",
            "ADD_TAB_PLG_UID" => "",
            "DBS_UID" => "workflow",
            "PRO_UID" => "",
            "ADD_TAB_TYPE" => "",
            "ADD_TAB_GRID" => "",
            "ADD_TAB_OFFLINE" => false,
            "ADD_TAB_UPDATE_DATE" => "2019-10-22 19:52:52"
        ];

        $additionalTables = new AdditionalTables();
        $result = $additionalTables->create($data);

        $additionalTablesModel = AdditionalTablesModel::where('ADD_TAB_UID', '=', $result)
                ->get()
                ->first();
        $actual = $additionalTablesModel->toArray();
        unset($data["ADD_TAB_UID"]);

        $this->assertArraySubset($data, $actual);
    }

    /**
     * This attempts to create a PMTable without correct data to cause an exception.
     * @test
     * @covers \AdditionalTables::create()
     */
    public function it_should_create_without_data()
    {
        $data = [
        ];
        $additionalTables = new AdditionalTables();
        $this->expectException(Exception::class);
        $additionalTables->create($data);
    }

    /**
     * This updates the data of a PMTable.
     * @test
     * @covers \AdditionalTables::update()
     */
    public function it_should_update()
    {
        $additionalTables = factory(AdditionalTablesModel::class)->create();

        $expected = [
            "ADD_TAB_UID" => $additionalTables->ADD_TAB_UID,
            "ADD_TAB_NAME" => "PMT_TEST11",
            "ADD_TAB_CLASS_NAME" => "PmtTest11",
            "DBS_UID" => "workflow",
            "ADD_TAB_OFFLINE" => false,
            "ADD_TAB_UPDATE_DATE" => "2019-10-22 19:53:11"
        ];
        $additionalTables = new AdditionalTables();
        $additionalTables->update($expected);

        $additionalTables = AdditionalTablesModel::where('ADD_TAB_UID', '=', $expected['ADD_TAB_UID'])
                ->get()
                ->first();

        $this->assertEquals($expected["ADD_TAB_NAME"], $additionalTables->ADD_TAB_NAME);
        $this->assertEquals($expected["ADD_TAB_CLASS_NAME"], $additionalTables->ADD_TAB_CLASS_NAME);
    }

    /**
     * It tries to update the data of a non-existent "PMTable".
     * @test
     * @covers \AdditionalTables::update()
     */
    public function it_should_update_if_registry_not_exist()
    {
        $expected = [
            "ADD_TAB_UID" => G::generateUniqueID(),
            "ADD_TAB_NAME" => "PMT_TEST11",
            "ADD_TAB_CLASS_NAME" => "PmtTest11",
            "DBS_UID" => "workflow",
            "ADD_TAB_OFFLINE" => false,
            "ADD_TAB_UPDATE_DATE" => "2019-10-22 19:53:11"
        ];

        $this->expectException(Exception::class);
        $additionalTables = new AdditionalTables();
        $additionalTables->update($expected);
    }

    /**
     * It tries to getAll() method.
     * @test
     * @covers \AdditionalTables::getAll()
     */
    public function it_should_get_all_registries()
    {
        $proUid = factory(\ProcessMaker\Model\Process::class)->create()->PRO_UID;

        //local connections
        $additionalTables = factory(AdditionalTablesModel::class, 3);
        $dbSource = factory(\ProcessMaker\Model\DbSource::class)->create([
            'PRO_UID' => $proUid,
            'DBS_SERVER' => env('DB_HOST'),
            'DBS_DATABASE_NAME' => env('DB_DATABASE'),
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), env('DB_DATABASE')) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
            'DBS_CONNECTION_TYPE' => 'NORMAL'
        ]);
        $additionalTable = factory(AdditionalTablesModel::class)->create([
            'PRO_UID' => $proUid,
            'DBS_UID' => $dbSource->DBS_UID,
        ]);
        $tableName = $additionalTable->ADD_TAB_NAME;
        $name = $additionalTable->ADD_TAB_CLASS_NAME;
        $this->createSchema($dbSource->DBS_DATABASE_NAME, $tableName, $name, $dbSource->DBS_UID);

        //external connection
        $dbSource = factory(\ProcessMaker\Model\DbSource::class)->create([
            'PRO_UID' => $proUid,
            'DBS_SERVER' => config('database.connections.testexternal.host'),
            'DBS_DATABASE_NAME' => config('database.connections.testexternal.database'),
            'DBS_USERNAME' => config('database.connections.testexternal.username'),
            'DBS_PASSWORD' => G::encrypt(config('database.connections.testexternal.password'), config('database.connections.testexternal.database')) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
            'DBS_CONNECTION_TYPE' => 'NORMAL'
        ]);
        $additionalTable = factory(AdditionalTablesModel::class)->create([
            'PRO_UID' => $proUid,
            'DBS_UID' => $dbSource->DBS_UID,
        ]);
        $tableName = $additionalTable->ADD_TAB_NAME;
        $name = $additionalTable->ADD_TAB_CLASS_NAME;
        $this->createSchema($dbSource->DBS_DATABASE_NAME, $tableName, $name, $dbSource->DBS_UID);

        //expected
        $expected = AdditionalTablesModel::select()
                ->get()
                ->toArray();
        $expected = array_column($expected, 'ADD_TAB_UID');

        //assertions
        $additionalTables = new AdditionalTables();

        $actual = $additionalTables->getAll();
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);

        $actual = $additionalTables->getAll(0, 20, 'a');
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);

        $actual = $additionalTables->getAll(0, 20, '', ['equal' => $proUid]);
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);

        $_POST['sort'] = 'ADD_TAB_NAME';
        $_POST['dir'] = 'ASC';
        $actual = $additionalTables->getAll(0, 20, '', ['notequal' => $proUid]);
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);

        $_POST['sort'] = 'NUM_ROWS';
        $_POST['dir'] = 'DESC';
        $actual = $additionalTables->getAll(0, 20, '', ['notequal' => $proUid]);
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);

        $actual = $additionalTables->getAll(0, 20, '', ['equal' => $proUid]);
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);

        $actual = $additionalTables->getAll(0, 20, $tableName);
        $actual = array_column($actual['rows'], 'ADD_TAB_UID');
        $this->assertContains($actual[0], $expected, false);
    }

    /**
     * Check if populate report table is added to job queue.
     * @test
     * @covers \AdditionalTables::populateReportTable
     */
    public function it_should_test_populate_report_table()
    {
        $proUid = factory(Process::class)->create()->PRO_UID;

        $task = factory(Task::class)->create([
            'PRO_UID' => $proUid
        ]);

        //local connections
        $dbSource = factory(DbSource::class)->create([
            'PRO_UID' => $proUid,
            'DBS_SERVER' => env('DB_HOST'),
            'DBS_DATABASE_NAME' => env('DB_DATABASE'),
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), env('DB_DATABASE')) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
            'DBS_CONNECTION_TYPE' => 'NORMAL'
        ]);
        $additionalTable = factory(AdditionalTablesModel::class)->create([
            'PRO_UID' => $proUid,
            'DBS_UID' => $dbSource->DBS_UID,
        ]);
        $tableName = $additionalTable->ADD_TAB_NAME;
        $name = $additionalTable->ADD_TAB_CLASS_NAME;
        $this->createSchema($dbSource->DBS_DATABASE_NAME, $tableName, $name, $dbSource->DBS_UID);

        //external connection
        $dbSource = factory(DbSource::class)->create([
            'PRO_UID' => $proUid,
            'DBS_SERVER' => config('database.connections.testexternal.host'),
            'DBS_DATABASE_NAME' => config('database.connections.testexternal.database'),
            'DBS_USERNAME' => config('database.connections.testexternal.username'),
            'DBS_PASSWORD' => G::encrypt(config('database.connections.testexternal.password'), config('database.connections.testexternal.database')) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
            'DBS_CONNECTION_TYPE' => 'NORMAL'
        ]);
        $additionalTable = factory(AdditionalTablesModel::class)->create([
            'PRO_UID' => $proUid,
            'DBS_UID' => $dbSource->DBS_UID,
        ]);
        $tableNameExternal = $additionalTable->ADD_TAB_NAME;
        $nameExternal = $additionalTable->ADD_TAB_CLASS_NAME;
        $this->createSchema($dbSource->DBS_DATABASE_NAME, $tableNameExternal, $nameExternal, $dbSource->DBS_UID);

        $application = factory(Application::class)->create([
            'PRO_UID' => $proUid
        ]);
        factory(Delegation::class)->create([
            'DEL_THREAD_STATUS' => 'CLOSED',
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_UID' => $task->TAS_UID,
        ]);

        //assertions
        Queue::fake();
        Queue::assertNothingPushed();

        $additionalTables = new AdditionalTables();
        $additionalTables->populateReportTable($tableName, 'workflow', 'NORMAL', $proUid, '', $additionalTable->ADD_TAB_UID);

        Queue::assertPushed(GenerateReportTable::class);
    }

    /**
     * It tests the validateParameter method
     * 
     * @covers \AdditionalTables::validateParameter()
     * @test
     */
    public function it_should_test_the_validate_parameter_method()
    {
        //Create the AdditionalTables object
        $additionalTables = new AdditionalTables();
        //Call validateParameter method
        $result = $additionalTables->validateParameter(8, 1, 8, 4);
        //Assert the number is in the rage
        $this->assertEquals(8, $result);
        //Call validateParameter method
        $result = $additionalTables->validateParameter(9, 1, 5, 4);
        //Assert the number has exceeded the max value
        $this->assertEquals(5, $result);
        //Call validateParameter method
        $result = $additionalTables->validateParameter(-3, 1, 5, 4);
        //Assert the number has exceeded the min value
        $this->assertEquals(1, $result);
        //Call validateParameter method
        $result = $additionalTables->validateParameter("$%&(%&(DGS=UJHGE32598", 1, 5, 4);
        //Assert the number has extrange characters
        $this->assertEquals(4, $result);
    }

    /**
     * This gets the content from template file.
     * @param string $pathData
     * @param string $tableName
     * @param string $tableName2
     * @param string $database
     * @return string
     */
    private function getTemplate(string $pathData, string $tableName, string $tableName2 = "", string $database = ""): string
    {
        $pathData = PATH_TRUNK . "/tests/resources/{$pathData}";
        $result = file_get_contents($pathData);
        $result = str_replace("{tableName}", $tableName, $result);
        if (!empty($tableName2)) {
            $result = str_replace("{tableName2}", $tableName2, $result);
        }
        if (!empty($database)) {
            $result = str_replace("{database}", $database, $result);
        }
        return $result;
    }

    /**
     * Create directory if not exist.
     * @param string $path
     * @return string
     */
    private function createDirectory(string $path): string
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
        return $path;
    }

    /**
     * Create the schema of the table.
     * @param string $connection
     * @param string $tableName
     * @param string $className
     * @param string $dbsUid
     */
    private function createSchema(string $connection, string $tableName, string $className, string $dbsUid = 'workflow')
    {
        $query = ""
                . "CREATE TABLE IF NOT EXISTS `{$tableName}` ("
                . "`APP_UID` varchar(32) NOT NULL,"
                . "`APP_NUMBER` int(11) NOT NULL,"
                . "`APP_STATUS` varchar(10) NOT NULL,"
                . "`VAR1` varchar(255) DEFAULT NULL,"
                . "`VAR2` varchar(255) DEFAULT NULL,"
                . "`VAR3` varchar(255) DEFAULT NULL,"
                . "PRIMARY KEY (`APP_UID`),"
                . "KEY `indexTable` (`APP_UID`))";
        if (!empty(config("database.connections.{$connection}"))) {
            DB::connection($connection)->statement($query);
        } else {
            DB::statement($query);
        }

        $this->createDirectory(PATH_DB);
        $this->createDirectory(PATH_DB . env('MAIN_SYS_SYS'));

        $pathClasses = PATH_DB . env('MAIN_SYS_SYS') . "/classes";
        $this->createDirectory($pathClasses);
        $this->createDirectory("{$pathClasses}/om");
        $this->createDirectory("{$pathClasses}/map");

        $template1 = $this->getTemplate("PmtTableName.tpl", $className);
        $template2 = $this->getTemplate("PmtTableNamePeer.tpl", $className);
        $template3 = $this->getTemplate("BasePmtTableName.tpl", $className);
        $template4 = $this->getTemplate("BasePmtTableNamePeer.tpl", $className, $tableName, $dbsUid);
        $template5 = $this->getTemplate("PmtTableNameMapBuilder.tpl", $className);

        file_put_contents("{$pathClasses}/{$className}.php", $template1);
        file_put_contents("{$pathClasses}/{$className}Peer.php", $template2);
        file_put_contents("{$pathClasses}/om/Base{$className}.php", $template3);
        file_put_contents("{$pathClasses}/om/Base{$className}Peer.php", $template4);
        file_put_contents("{$pathClasses}/map/{$className}MapBuilder.php", $template5);
    }
}
