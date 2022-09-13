<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\CreateTestSite;
use Tests\TestCase;

class ReportTablesTest extends TestCase
{
    use CreateTestSite;

    /**
     * Sets up the unit tests.
     */
    public function setUp(): void
    {
        parent::setUp();
        $_SERVER["REQUEST_URI"] = "";
        config(['queue.default' => 'sync']);
        config(["system.workspace" => "test"]);
        $workspace = config("system.workspace");
        $this->createDBFile($workspace);
        $this->createConstantsOfConnection();
    }

    /**
     * Tear down the unit tests.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Create constants of connection to databases.
     */
    private function createConstantsOfConnection()
    {
        $constants = [
            'DB_ADAPTER' => env('mysql'),
            'DB_HOST' => env('DB_HOST'),
            'DB_NAME' => env('DB_DATABASE'),
            'DB_USER' => env('DB_USERNAME'),
            'DB_PASS' => env('DB_PASSWORD'),
            'DB_RBAC_HOST' => env('DB_HOST'),
            'DB_RBAC_NAME' => env('DB_DATABASE'),
            'DB_RBAC_USER' => env('DB_USERNAME'),
            'DB_RBAC_PASS' => env('DB_PASSWORD'),
            'DB_REPORT_HOST' => env('DB_HOST'),
            'DB_REPORT_NAME' => env('DB_DATABASE'),
            'DB_REPORT_USER' => env('DB_USERNAME'),
            'DB_REPORT_PASS' => env('DB_PASSWORD'),
        ];
        foreach ($constants as $key => $value) {
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }

    /**
     * Check if the "populateTable" function returns an array value if entered all parameters.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_populating_data_with_all_parameters()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = '';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the "populateTable" function returns an array value if entered all 
     * parameters and type and grid are correct values.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_populating_data_with_all_parameters_with_type_is_grid()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName, true);
        $connectionShortName = 'wf';
        $type = 'GRID';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = 'var_Grid1';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $indexRow = 1;
        $expected = $result->appData[$grid];
        foreach ($expected as &$row) {
            $row['APP_UID'] = $result->applicationUid;
            $row['APP_NUMBER'] = $result->applicationNumber;
            $row['ROW'] = (string) ($indexRow++);
        }
        $expected = array_values($expected);

        $actual = DB::table($tableName)
                ->select()
                ->get();
        $actual->transform(function ($item, $key) {
            return (array) $item;
        });
        $actual = $actual->toArray();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the "populateTable" function returns an array value if entered all 
     * parameters and type and grid are incorrect values.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_populating_data_with_all_parameters_with_type_is_grid_null()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName, true);
        $connectionShortName = 'wf';
        $type = 'GRID';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = null;

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $actual = DB::table($tableName)
                ->select()
                ->get();
        $actual->transform(function ($item, $key) {
            return (array) $item;
        });
        $actual = $actual->toArray();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if only the 
     * name of the report table has been entered.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_only_with_the_mandatory_parameter_tableName()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable and the name of the 
     * connection.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_connectionShortName_parameter()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable and the name of the 
     * connection is null.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_connectionShortName_parameter_is_null()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable and the name of the 
     * connection is incorrect value.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_connectionShortName_parameter_is_incorrect_value()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = G::generateUniqueID();

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection and type.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection and type is grid.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_is_grid()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'GRID';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection and type is null.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_is_null()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = '';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type and fields.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type and fields is null.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_is_null()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = [];

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type and fields is empty array.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_is_empty_array()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = [];

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type and fields is incorrect value.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_is_incorrect_value()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = [];

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an array value if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type, the fields and process identifier.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_proUid()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the "populateTable" function returns an empty array if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type, the fields and process identifier is null.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_proUid_is_null()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = '';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals([], $actual);
    }

    /**
     * Check if the "populateTable" function returns an array value if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type, the fields, the process identifier and grid name.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_proUid_grid()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = '';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the "populateTable" function returns an array value if you have 
     * entered the name of the table of the reportTable, the name of the 
     * connection, the type, the fields, the process identifier and grid name if null.
     * @test
     * @covers ReportTables::populateTable
     */
    public function this_should_populate_the_reports_table_with_the_parameters_connectionShortName_type_fields_proUid_grid_if_null()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = '';

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Prepare data initial for test, the grid parameter is optional if you want 
     * to create a grid type field.
     * 
     * @param string $tableName
     * @param integer $applicationNumber
     * @param boolean $grid
     * @return object
     */
    private function prepareData($tableName, $grid = null, $structure = [])
    {
        $applicationNumber = Application::max('APP_NUMBER');
        if (is_null($applicationNumber)) {
            $applicationNumber = 0;
        }
        $applicationNumber = $applicationNumber + 1;

        $faker = Faker\Factory::create();
        $date = $faker->dateTime();

        $userUid = G::generateUniqueID();
        $processUid = G::generateUniqueID();
        $taskUid = G::generateUniqueID();
        $applicationUid = G::generateUniqueID();

        if (empty($structure)) {
            $structure = $this->getDataFromFile('structureReportTable.json');
        }

        $fields = $structure['mapping'];
        $dataFields = $structure['data'];
        $appData = [
            'SYS_LANG' => 'en',
            'SYS_SKIN' => 'neoclassic',
            'SYS_SYS' => 'workflow',
            'APPLICATION' => G::generateUniqueID(),
            'PROCESS' => G::generateUniqueID(),
            'TASK' => '',
            'INDEX' => 2,
            'USER_LOGGED' => $userUid,
            'USR_USERNAME' => 'admin',
            'APP_NUMBER' => $applicationNumber,
            'PIN' => '97ZN'
        ];
        $appData = array_merge($appData, $dataFields);
        if ($grid === true) {
            $gridFields = [
                'var_Grid1' => [
                    '1' => $dataFields,
                    '2' => $dataFields,
                ]
            ];
            $appData = array_merge($appData, $gridFields);
        }

        $user = User::factory()->create([
            'USR_UID' => $userUid
        ]);

        $process = Process::factory()->create([
            'PRO_UID' => $processUid
        ]);

        $task = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_UID' => $applicationUid,
            'APP_NUMBER' => $applicationNumber,
            'APP_DATA' => serialize($appData)
        ]);

        Schema::dropIfExists($tableName);
        Schema::create($tableName, function ($table) use ($dataFields, $grid) {
            $table->string('APP_UID');
            $table->string('APP_NUMBER');
            if ($grid === true) {
                $table->string('ROW');
            }
            foreach ($dataFields as $key => $value) {
                $table->string($key);
            }
        });
        $result = new stdClass();
        $result->userUid = $userUid;
        $result->processUid = $processUid;
        $result->taskUid = $taskUid;
        $result->applicationUid = $applicationUid;
        $result->applicationNumber = $applicationNumber;
        $result->fields = $fields;
        $result->dataFields = $dataFields;
        $result->appData = $appData;
        $result->user = $user;
        $result->process = $process;
        $result->task = $task;
        $result->application = $application;
        return $result;
    }

    /**
     * Check if the "populateTable" method is it filling with missing values into app_data.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_populating_data_with_fields_missing_in_to_app_data()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = '';

        $app = Application::where('APP_UID', '=', $result->applicationUid)->first();
        $appData = unserialize($app->APP_DATA);
        unset($appData['var_Textarea1']);
        $appData = serialize($appData);
        Application::where('APP_UID', '=', $result->applicationUid)->update(['APP_DATA' => $appData]);

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;
        $expected['var_Textarea1'] = '';

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the "populateTable" method is it filling with arrays values.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_populating_data_with_arrays_values()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = '';

        $app = Application::where('APP_UID', '=', $result->applicationUid)->first();
        $appData = unserialize($app->APP_DATA);
        $appData['var_Textarea1'] = [];
        $appData = serialize($appData);
        Application::where('APP_UID', '=', $result->applicationUid)->update(['APP_DATA' => $appData]);

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;
        $expected['var_Textarea1'] = '';

        $actual = (array) DB::table($tableName)
                        ->select()
                        ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the "populateTable" method is it filling with missing values into app_data for grids control.
     * parameters and type and grid are correct values.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_populating_data_with_all_parameters_with_type_is_grid_fields_missing_in_to_app_data()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName, true);
        $connectionShortName = 'wf';
        $type = 'GRID';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = 'var_Grid1';

        $app = Application::where('APP_UID', '=', $result->applicationUid)->first();
        $appData = unserialize($app->APP_DATA);
        unset($appData['var_Grid1'][1]['var_Textarea1']);
        $appData = serialize($appData);
        Application::where('APP_UID', '=', $result->applicationUid)->update(['APP_DATA' => $appData]);

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid);

        $indexRow = 1;
        $expected = $result->appData[$grid];
        foreach ($expected as &$row) {
            $row['APP_UID'] = $result->applicationUid;
            $row['APP_NUMBER'] = $result->applicationNumber;
            $row['ROW'] = (string) ($indexRow++);
        }
        $expected = array_values($expected);
        $expected[0]['var_Textarea1'] = '';

        $actual = DB::table($tableName)
                ->select()
                ->get();
        $actual->transform(function ($item, $key) {
            return (array) $item;
        });
        $actual = $actual->toArray();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check an exception if the input parameters are wrong.
     * @test
     * @covers ReportTables::populateTable
     */
    public function it_should_catch_an_exception()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName, true);
        $connectionShortName = 'wf';
        $type = 'GRID';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = 'var_Grid1';

        //assert exception
        $this->expectException(TypeError::class);

        $reportTables = new ReportTables();
        $reportTables->populateTable($tableName, $connectionShortName, $type, null, $proUid, $grid);
    }

    /**
     * This gets data from a json file.
     * @param string $pathData
     * @return array
     */
    private function getDataFromFile(string $pathData): array
    {
        $pathData = PATH_TRUNK . "tests/resources/{$pathData}";
        $data = file_get_contents($pathData);
        $result = json_decode($data, JSON_OBJECT_AS_ARRAY);
        return $result;
    }

    /**
     * @test
     * @covers ReportTables::generateOldReportTable
     * @covers ReportTables::buildAndExecuteQuery
     * @covers ReportTables::buildFieldsSection
     * @covers ReportTables::buildValuesSection
     */
    public function it_should_test_generateOldReportTable_for_normal()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName);
        $connectionShortName = 'wf';
        $type = 'NORMAL';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = '';

        $start = 0;
        $limit = 100;

        $reportTables = new ReportTables();
        DB::delete("TRUNCATE TABLE `{$tableName}` ");

        $reportTables->generateOldReportTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid, $start, $limit);

        $expected = $result->dataFields;
        $expected['APP_UID'] = $result->applicationUid;
        $expected['APP_NUMBER'] = $result->applicationNumber;

        $actual = (array) DB::table($tableName)
                ->select()
                ->first();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @covers ReportTables::generateOldReportTable
     * @covers ReportTables::buildAndExecuteQuery
     * @covers ReportTables::buildFieldsSection
     * @covers ReportTables::buildValuesSection
     */
    public function it_should_test_generateOldReportTable_for_grid()
    {
        $tableName = 'TestReportTable';
        $result = $this->prepareData($tableName, true);
        $connectionShortName = 'wf';
        $type = 'GRID';
        $fields = $result->fields;
        $proUid = $result->processUid;
        $grid = 'var_Grid1';

        $app = Application::where('APP_UID', '=', $result->applicationUid)->first();
        $appData = unserialize($app->APP_DATA);
        $appData['var_Textarea1'] = [];
        $appData = serialize($appData);
        Application::where('APP_UID', '=', $result->applicationUid)->update(['APP_DATA' => $appData]);

        $start = 0;
        $limit = 100;

        $reportTables = new ReportTables();
        DB::delete("TRUNCATE TABLE `{$tableName}` ");

        $reportTables->generateOldReportTable($tableName, $connectionShortName, $type, $fields, $proUid, $grid, $start, $limit);

        $indexRow = 1;
        $expected = $result->appData[$grid];
        foreach ($expected as &$row) {
            $row['APP_UID'] = $result->applicationUid;
            $row['APP_NUMBER'] = $result->applicationNumber;
            $row['ROW'] = (string) ($indexRow++);
        }
        $expected = array_values($expected);

        $actual = DB::table($tableName)
            ->select()
            ->get();
        $actual->transform(function ($item, $key) {
            return (array) $item;
        });
        $actual = $actual->toArray();

        $this->assertEquals($expected, $actual);
    }

}
