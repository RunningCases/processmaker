<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

class ReportTablesTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * Sets up the unit tests.
     */
    public function setUp()
    {
        parent::setUp();
        $_SERVER["REQUEST_URI"] = "";
    }

    /**
     * Tear down the unit tests.
     */
    public function tearDown()
    {
        parent::tearDown();
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
        $connectionShortName = null;

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
        $type = null;

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
        $fields = null;

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
        $fields = "";

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
        $proUid = null;

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
        $grid = null;

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
     * Get mapping fields supported by report table.
     * @return array
     */
    private function getMapFields()
    {
        return [
            [
                'sFieldName' => 'var_Text1',
                'sType' => 'char'
            ],
            [
                'sFieldName' => 'var_Textarea1',
                'sType' => 'text'
            ],
            [
                'sFieldName' => 'var_Dropdown1',
                'sType' => 'char'
            ],
            [
                'sFieldName' => 'var_Suggest1',
                'sType' => 'char'
            ],
            [
                'sFieldName' => 'var_DateTime1',
                'sType' => 'date'
            ],
            [
                'sFieldName' => 'var_String1',
                'sType' => 'char'
            ],
            [
                'sFieldName' => 'var_Integer1',
                'sType' => 'number'
            ],
            [
                'sFieldName' => 'var_Boolean1',
                'sType' => 'boolean'
            ],
            [
                'sFieldName' => 'var_Array1',
                'sType' => 'array'
            ]
        ];
    }

    /**
     * Create fields data by type supported.
     * @param array $types
     * @return array
     */
    private function createFieldsByType($types = [])
    {
        $fields = [];
        $mapping = [];
        $faker = Faker\Factory::create();
        $date = $faker->dateTime();
        $mapFields = $this->getMapFields();
        foreach ($mapFields as $key => $value) {
            if (!in_array($value['sType'], $types)) {
                continue;
            }
            switch ($value['sType']) {
                case 'number':
                    $mapping[] = $value;
                    $fields[$value['sFieldName']] = (string) random_int(0, 100);
                    break;
                case 'char':
                    $mapping[] = $value;
                    $fields[$value['sFieldName']] = G::generateUniqueID();
                    break;
                case 'text':
                    $mapping[] = $value;
                    $fields[$value['sFieldName']] = G::generateUniqueID();
                    break;
                case 'date':
                    $mapping[] = $value;
                    $fields[$value['sFieldName']] = $date->format('Y-m-d H:i:s');
                    break;
                case 'boolean':
                    $mapping[] = $value;
                    $fields[$value['sFieldName']] = ['0' => 0];
                    break;
            }
        }
        return [
            'data' => $fields,
            'mapping' => $mapping
        ];
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
    private function prepareData($tableName, $grid = null)
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

        $structure = $this->createFieldsByType(['number', 'char', 'text', 'date']);
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

        $user = factory(User::class)->create([
            'USR_UID' => $userUid
        ]);

        $process = factory(Process::class)->create([
            'PRO_UID' => $processUid
        ]);

        $task = factory(Task::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        $application = factory(Application::class)->create([
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
}
