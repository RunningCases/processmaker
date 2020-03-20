<?php

namespace Tests\unit\workflow\engine\controllers;

use AdditionalTables;
use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use pmTablesProxy;
use ProcessMaker\BusinessModel\ReportTable;
use Tests\CreateTestSite;
use Tests\TestCase;

/**
 * @coversDefaultClass \pmTablesProxy
 */
class PmTablesProxyTest extends TestCase
{
    use CreateTestSite;
    use DatabaseTransactions;

    protected $preserveGlobalState = false;
    protected $runTestInSeparateProcess = true;

    private $repTableBigInt;
    private $repTableChar;
    private $repTableInteger;
    private $repTableSmallInt;
    private $repTableTinyInt;
    private $repTableVarChar;
    private $repTableBigIntUid;
    private $repTableCharUid;
    private $repTableIntegerUid;
    private $repTableSmallIntUid;
    private $repTableTinyIntUid;
    private $repTableVarCharUid;

    /**
     * It setup the variables for the unit tests
     */
    protected function setUp()
    {
        parent::setUp();

        config(["system.workspace" => SYS_SYS]);
        $workspace = config("system.workspace");
        $this->createDBFile($workspace);

        //Set the user logged as the admin
        $_SESSION['USER_LOGGED'] = "00000000000000000000000000000001";

        // The InputFilter class use deprecated code
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    }

    /**
     * It tests the PM Table with a bigInt ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_big_int_id()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a bigint id
        $httpDatavarBigInt = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_BIGINT',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_BIGINT',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "BIGINT",
                    "field_size" => "",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableBigInt = $reportTable->saveStructureOfTable($httpDatavarBigInt, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableBigIntUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataBigInt = [
            'id' => $this->repTableBigIntUid,
            'rows' => json_encode(["ID" => 986, "NAME" => "BigInt"])
        ];

        //This will add rows to the PM tables
        $obj->dataCreate($httpDataBigInt);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateBigInt = (object)[
            'id' => $this->repTableBigIntUid,
            'rows' => json_encode(["NAME" => "BigIntUpdated", "__index__" => G::encrypt(986, PMTABLE_KEY)]),
        ];

        //This method update the PM tables rows
        $resultDataUpdateBigInt = $obj->dataUpdate($httpDataUpdateBigInt);

        //Assert the values were updated
        $resUpdateBigInt = $obj->dataView((object)["id" => $this->repTableBigIntUid]);
        $this->assertEquals("BigIntUpdated", $resUpdateBigInt['rows'][0]['NAME']);


        //The variable that is sent to the update method
        $httpDataUpdatebigInt = (object)[
            'id' => $this->repTableBigIntUid,
            'rows' => json_encode(["ID" => 111234, "__index__" => G::encrypt(986, PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage('*');
        $obj->dataUpdate($httpDataUpdatebigInt);

        //Variables that will be sent to the destroy method
        $httpDataDestroyBigInt = (object)[
            'id' => $this->repTableBigIntUid,
            'rows' => G::encrypt(986, PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteBigInt = $obj->dataDestroy($httpDataDestroyBigInt);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteBigInt);

        //This method will return a specific row
        $resViewBigInt = $obj->dataView((object)["id" => $this->repTableBigIntUid]);
        //Assert the row was deleted, so the PM table does not have data
        $this->assertEquals(0, $resViewBigInt['count']);
    }

    /**
     * It tests the PM Table with a varChar ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_var_char_id()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a char id
        $httpDatavarChar = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_CHAR',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_CHAR',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "CHAR",
                    "field_size" => "20",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableChar = $reportTable->saveStructureOfTable($httpDatavarChar, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableCharUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataChar = [
            'id' => $this->repTableCharUid,
            'rows' => json_encode(["ID" => "009", "NAME" => "Char"])
        ];

        //This will add rows to the PM tables
        $obj->dataCreate($httpDataChar);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateChar = (object)[
            'id' => $this->repTableCharUid,
            'rows' => json_encode(["NAME" => "CharUpdated", "__index__" => G::encrypt("009", PMTABLE_KEY)]),
        ];

        //This method update the PM tables rows
        $resultDataUpdateChar = $obj->dataUpdate($httpDataUpdateChar);

        //Assert the values were updated
        $resUpdateChar = $obj->dataView((object)["id" => $this->repTableCharUid]);
        $this->assertEquals("CharUpdated", $resUpdateChar['rows'][0]['NAME']);


        //The variable that is sent to the update method
        $httpDataUpdateChar = (object)[
            'id' => $this->repTableCharUid,
            'rows' => json_encode(["ID" => "fwew", "__index__" => G::encrypt("009", PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage('*');
        $obj->dataUpdate($httpDataUpdateChar);

        //Variables that will be sent to the destroy method
        $httpDataDestroyChar = (object)[
            'id' => $this->repTableCharUid,
            'rows' => G::encrypt("009", PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteChar = $obj->dataDestroy($httpDataDestroyChar);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteChar);

        //This method will return a specific row
        $resViewChar = $obj->dataView((object)["id" => $this->repTableCharUid]);
        //Assert the row was deleted, so the PM table does not have data
        $this->assertEquals(0, $resViewChar['count']);
    }

    /**
     * It tests the PM Table with an integer ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_integer_id()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with an integer id
        $httpDatavarInteger = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_INTEGER',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_INTEGER',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "INTEGER",
                    "field_size" => "",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableInteger = $reportTable->saveStructureOfTable($httpDatavarInteger, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableIntegerUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataInteger = [
            'id' => $this->repTableIntegerUid,
            'rows' => json_encode(["ID" => 8, "NAME" => "Integer"])
        ];

        //This will add rows to the PM tables
        $res = $obj->dataCreate($httpDataInteger);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateInteger = (object)[
            'id' => $this->repTableIntegerUid,
            'rows' => json_encode(["NAME" => "IntegerUpdated", "__index__" => G::encrypt(8, PMTABLE_KEY)]),
        ];

        //This method update the PM tables rows
        $resultDataUpdateInteger = $obj->dataUpdate($httpDataUpdateInteger);

        //Assert the values were updated
        $resUpdateInteger = $obj->dataView((object)["id" => $this->repTableIntegerUid]);
        $this->assertEquals("IntegerUpdated", $resUpdateInteger['rows'][0]['NAME']);


        //The variable that is sent to the update method
        $httpDataUpdateInteger = (object)[
            'id' => $this->repTableIntegerUid,
            'rows' => json_encode(["ID" => 7655, "__index__" => G::encrypt(8, PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage('*');
        $obj->dataUpdate($httpDataUpdateInteger);

        //Variables that will be sent to the destroy method
        $httpDataDestroyInteger = (object)[
            'id' => $this->repTableIntegerUid,
            'rows' => G::encrypt(8, PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteInteger = $obj->dataDestroy($httpDataDestroyInteger);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteInteger);

        //This method will return a specific row
        $resViewInteger = $obj->dataView((object)["id" => $this->repTableIntegerUid]);
        //Assert the row was deleted, so the PM table does not have data
        $this->assertEquals(0, $resViewInteger['count']);
    }

    /**
     * It tests the PM Table with a smallInt ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_smallint_id()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a smallint id
        $httpDatavarSmallInt = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_SMALLINT',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_SMALLINT',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "SMALLINT",
                    "field_size" => "",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableSmallInt = $reportTable->saveStructureOfTable($httpDatavarSmallInt, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableSmallIntUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataSmallInt = [
            'id' => $this->repTableSmallIntUid,
            'rows' => json_encode(["ID" => 5, "NAME" => "SmallInt"])
        ];

        //This will add rows to the PM tables
        $obj->dataCreate($httpDataSmallInt);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateSmallInt = (object)[
            'id' => $this->repTableSmallIntUid,
            'rows' => json_encode(["NAME" => "SmallIntUpdated", "__index__" => G::encrypt(5, PMTABLE_KEY)]),
        ];

        //This method update the PM tables rows
        $resultDataUpdateSmallInt = $obj->dataUpdate($httpDataUpdateSmallInt);

        //Assert the values were updated
        $resUpdateSmallInt = $obj->dataView((object)["id" => $this->repTableSmallIntUid]);
        $this->assertEquals("SmallIntUpdated", $resUpdateSmallInt['rows'][0]['NAME']);

        //The variable that is sent to the update method
        $httpDataUpdateSmallInt = (object)[
            'id' => $this->repTableSmallIntUid,
            'rows' => json_encode(["ID" => 7, "__index__" => G::encrypt(5, PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage('*');
        $obj->dataUpdate($httpDataUpdateSmallInt);

        //Variables that will be sent to the destroy method
        $httpDataDestroySmallInt = (object)[
            'id' => $this->repTableSmallIntUid,
            'rows' => G::encrypt(5, PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteSmallInt = $obj->dataDestroy($httpDataDestroySmallInt);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteSmallInt);

        //This method will return a specific row
        $resViewSmallInt = $obj->dataView((object)["id" => $this->repTableSmallIntUid]);
        //Assert the row was deleted, so the PM table does not have data
        $this->assertEquals(0, $resViewSmallInt['count']);
    }

    /**
     * It tests the PM Table with a tinyInt ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_tinyint_id()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a tinyint id
        $httpDatavarTinyInt = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_TINYINT',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_TINYINT',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "TINYINT",
                    "field_size" => "",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableTinyInt = $reportTable->saveStructureOfTable($httpDatavarTinyInt, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableTinyIntUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataTinyInt = [
            'id' => $this->repTableTinyIntUid,
            'rows' => json_encode(["ID" => 1, "NAME" => "TinyInt"])
        ];

        //This will add rows to the PM tables
        $obj->dataCreate($httpDataTinyInt);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateTinyInt = (object)[
            'id' => $this->repTableTinyIntUid,
            'rows' => json_encode(["NAME" => "TinyIntUpdated", "__index__" => G::encrypt(1, PMTABLE_KEY)]),
        ];

        //This method update the PM tables rows
        $resultDataUpdateTinyInt = $obj->dataUpdate($httpDataUpdateTinyInt);

        //Assert the values were updated
        $resUpdateTinyInt = $obj->dataView((object)["id" => $this->repTableTinyIntUid]);
        $this->assertEquals("TinyIntUpdated", $resUpdateTinyInt['rows'][0]['NAME']);

        //The variable that is sent to the update method
        $httpDataUpdateTinyInt = (object)[
            'id' => $this->repTableTinyIntUid,
            'rows' => json_encode(["ID" => 2, "__index__" => G::encrypt(1, PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage('*');
        $obj->dataUpdate($httpDataUpdateTinyInt);

        //Variables that will be sent to the destroy method
        $httpDataDestroyTinyInt = (object)[
            'id' => $this->repTableTinyIntUid,
            'rows' => G::encrypt(1, PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteTinyInt = $obj->dataDestroy($httpDataDestroyTinyInt);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteTinyInt);

        //This method will return a specific row
        $resViewTinyInt = $obj->dataView((object)["id" => $this->repTableTinyIntUid]);
        //Assert the row was deleted, so the PM table does not have data
        $this->assertEquals(0, $resViewTinyInt['count']);
    }

    /**
     * It tests the PM Table with a varChar ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_varchar_id()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a varchar id
        $httpDatavarVarChar = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "VARCHAR",
                    "field_size" => "20",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableVarChar = $reportTable->saveStructureOfTable($httpDatavarVarChar, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableVarCharUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataVarChar = [
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode(["ID" => "076", "NAME" => "VarChar"])
        ];

        //This will add rows to the PM tables
        $obj->dataCreate($httpDataVarChar);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateVarChar = (object)[
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode(["NAME" => "VarCharUpdated", "__index__" => G::encrypt("076", PMTABLE_KEY)]),
        ];

        //This method update the PM tables rows
        $resultDataUpdateVarChar = $obj->dataUpdate($httpDataUpdateVarChar);

        //Assert the values were updated
        $resUpdateVarChar = $obj->dataView((object)["id" => $this->repTableVarCharUid]);
        $this->assertEquals("VarCharUpdated", $resUpdateVarChar['rows'][0]['NAME']);

        //The variable that is sent to the update method
        $httpDataUpdateVarChar = (object)[
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode(["ID" => "sdsggs", "__index__" => G::encrypt("076", PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage('*');
        $obj->dataUpdate($httpDataUpdateVarChar);

        //Variables that will be sent to the destroy method
        $httpDataDestroyVarChar = (object)[
            'id' => $this->repTableVarCharUid,
            'rows' => G::encrypt("076", PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteVarChar = $obj->dataDestroy($httpDataDestroyVarChar);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteVarChar);

        //This method will return a specific row
        $resViewVarChar = $obj->dataView((object)["id" => $this->repTableVarCharUid]);
        //Assert the row was deleted, so the PM table does not have data
        $this->assertEquals(0, $resViewVarChar['count']);
    }

    /**
     * It tests the PM Table with a varChar ID
     *
     * @covers ::dataCreate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_varchar_id_filter()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a varchar id
        $httpDatavarVarChar = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "VARCHAR",
                    "field_size" => "20",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableVarChar = $reportTable->saveStructureOfTable($httpDatavarVarChar, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableVarCharUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataVarChar = [
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode(["ID" => "076", "NAME" => "VarChar"])
        ];

        //This will add rows to the PM tables
        $obj->dataCreate($httpDataVarChar);

        //Assert the values were updated
        $res = $obj->dataView((object)["id" => $this->repTableVarCharUid, 'textFilter' => "a"]);
        $this->assertEquals(1, $res['count']);

    }

    /**
     * It tests the PM Table with a varChar ID
     *
     * @covers ::dataCreate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_varchar_id_exception()
    {
        $reportTable = new ReportTable();

        //PM table with a varchar id
        $httpDatavarVarChar = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "VARCHAR",
                    "field_size" => "20",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableVarChar = $reportTable->saveStructureOfTable($httpDatavarVarChar, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableVarCharUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataVarChar = [
            'id' => "fake id",
            'rows' => json_encode(["ID" => "076", "NAME" => "VarChar"])
        ];

        //This will add rows to the PM tables
        $res = $obj->dataCreate($httpDataVarChar);
        $this->assertFalse($res->success);

        //The variables that will be used to create rows in the PM tables
        $httpDataVarChar = [
            'id' => $this->repTableVarCharUid,
            'rows' => ""
        ];
        $res = $obj->dataCreate($httpDataVarChar);
        $this->assertFalse($res->success);
    }

    /**
     * It tests the PM Table with a varChar ID
     *
     * @covers ::dataCreate()
     * @covers ::dataUpdate()
     * @covers ::dataView()
     * @test
     */
    public function it_should_test_varchar_id_rows()
    {
        $this->markTestIncomplete();
        $reportTable = new ReportTable();

        //PM table with a varchar id
        $httpDatavarVarChar = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "VARCHAR",
                    "field_size" => "20",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableVarChar = $reportTable->saveStructureOfTable($httpDatavarVarChar, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableVarCharUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];

        $obj = new pmTablesProxy();

        //The variables that will be used to create rows in the PM tables
        $httpDataVarChar = [
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode(["ID" => "076", "NAME" => "VarChar"])
        ];

        //This will add rows to the PM tables
        $r = $obj->dataCreate($httpDataVarChar);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateVarChar = (object)[
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode([
                ["NAME" => "VarCharUpdated", "__index__" => G::encrypt("076", PMTABLE_KEY)],
                ["NAME" => "VarCharUpdated2", "__index__" => G::encrypt("077", PMTABLE_KEY)]
            ]),
        ];

        //This method update the PM tables rows
        $obj->dataUpdate($httpDataUpdateVarChar);

        //Assert the values were updated
        $resUpdateVarChar = $obj->dataView((object)["id" => $this->repTableVarCharUid]);
        $this->assertEquals("VarCharUpdated", $resUpdateVarChar['rows'][0]['NAME']);

        //The variables that will be used to update the rows in the PM tables
        $httpDataUpdateVarChar = (object)[
            'id' => "fakeID",
            'rows' => json_encode([
                ["NAME" => "VarCharUpdated", "__index__" => G::encrypt("076", PMTABLE_KEY)],
                ["NAME" => "VarCharUpdated2", "__index__" => G::encrypt("077", PMTABLE_KEY)]
            ]),
        ];

        $this->expectExceptionMessage("**ID_PMTABLE_CLASS_DOESNT_EXIST**");
        //This method update the PM tables rows
        $obj->dataUpdate($httpDataUpdateVarChar);
    }

    /**
     * It should test the exception message when the PM table does not exists
     *
     * @covers ::dataDestroy()
     * @test
     */
    public function it_should_test_destroy_method()
    {
        $obj = new pmTablesProxy();

        //Variable that is sent to the destroy method
        $httpDataDestroyTest = (object)[
            'id' => "fakeUID",
            'rows' => G::encrypt("076", PMTABLE_KEY),
        ];

        //Assert the exception message when the PM table does not exists
        $this->expectExceptionMessage('ID_PMTABLE_CLASS_DOESNT_EXIST');
        //This method deletes a specific row of a PM table
        $obj->dataDestroy($httpDataDestroyTest);
    }

    /**
     * It should test the exception message when the PM table does not exists
     *
     * @covers ::dataDestroy()
     * @test
     */
    public function it_should_test_destroy_method_success()
    {
        $reportTable = new ReportTable();

        //PM table with a varchar id
        $httpDatavarVarChar = [
            'REP_TAB_UID' => '',
            'PRO_UID' => '',
            'REP_TAB_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_NAME_OLD_NAME' => 'PMT_TEST_VARCHAR',
            'REP_TAB_DSC' => '',
            'REP_TAB_CONNECTION' => 'workflow',
            'REP_TAB_TYPE' => '',
            'REP_TAB_GRID' => '',
            'columns' => json_encode([
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "ID",
                    "field_label" => "id",
                    "field_type" => "VARCHAR",
                    "field_size" => "20",
                    "field_key" => true,
                    "field_index" => false,
                    "field_null" => false,
                    "field_autoincrement" => false
                ],
                [
                    "uid" => "",
                    "field_uid" => "",
                    "field_dyn" => "",
                    "field_name" => "NAME",
                    "field_label" => "NAME",
                    "field_type" => "VARCHAR",
                    "field_size" => "30",
                    "field_key" => false,
                    "field_index" => false,
                    "field_null" => true,
                    "field_autoincrement" => false
                ]
            ])
        ];

        //This create the report tables
        $this->repTableVarChar = $reportTable->saveStructureOfTable($httpDatavarVarChar, true);
        $pmTablesList = new AdditionalTables();
        $resuPmTableList = $pmTablesList->getAll();
        $this->repTableVarCharUid = $resuPmTableList['rows'][0]['ADD_TAB_UID'];
        //The variables that will be used to create rows in the PM tables
        $httpDataVarChar = [
            'id' => $this->repTableVarCharUid,
            'rows' => json_encode(["ID" => "077", "NAME" => "VarChar2"])
        ];

        $obj = new pmTablesProxy();
        //This will add rows to the PM tables
        $obj->dataCreate($httpDataVarChar);

        //Variable that is sent to the destroy method
        $httpDataDestroyTest = (object)[
            'id' => $this->repTableVarCharUid,
            'rows' => G::encrypt("077", PMTABLE_KEY),
        ];

        //This method deletes a specific row of a PM table
        $obj->dataDestroy($httpDataDestroyTest);
    }

    /**
     * Delete all the PM Tables created for the test
     */
    public function tearDown()
    {
        parent::tearDown();
        $obj = new pmTablesProxy();
        $httpDataBigInt = (object)['rows' => '[{"id":"' . $this->repTableBigIntUid . '","type":""}]'];
        $httpDataChar = (object)['rows' => '[{"id":"' . $this->repTableCharUid . '","type":""}]'];
        $httpDataSmallInt = (object)['rows' => '[{"id":"' . $this->repTableSmallIntUid . '","type":""}]'];
        $httpDataInteger = (object)['rows' => '[{"id":"' . $this->repTableIntegerUid . '","type":""}]'];
        $httpDataVarChar = (object)['rows' => '[{"id":"' . $this->repTableVarCharUid . '","type":""}]'];
        $httpDataTinyInt = (object)['rows' => '[{"id":"' . $this->repTableTinyIntUid . '","type":""}]'];
        $obj->delete($httpDataBigInt);
        $obj->delete($httpDataChar);
        $obj->delete($httpDataSmallInt);
        $obj->delete($httpDataInteger);
        $obj->delete($httpDataVarChar);
        $obj->delete($httpDataTinyInt);
    }
}
