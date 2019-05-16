<?php

namespace Tests\unit\workflow\engine\controllers;

use G;
use pmTablesProxy;
use ProcessMaker\BusinessModel\ReportTable;
use Tests\TestCase;


class PmTablesProxyTest extends TestCase
{
    private $repTableBigInt;
    private $repTableChar;
    private $repTableInteger;
    private $repTableSmallInt;
    private $repTableTinyInt;
    private $repTableVarChar;

    /**
     * It will create the report tables needed for the unittest
     */
    protected function setUp()
    {
        config(["system.workspace" => SYS_SYS]);
        //the ./thirdparty/phing/Phing.php use deprecated code
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        $reportTable = new ReportTable();

        //PMTable with a bigint id
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

        //PMTable with a char id
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

        //PMTable with an integer id
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

        //PMTable with a smallint id
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


        //PMTable with a tinyint id
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


        //PMTable with a varchar id
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
        $this->repTableBigInt = $reportTable->saveStructureOfTable($httpDatavarBigInt, true);
        $this->repTableChar = $reportTable->saveStructureOfTable($httpDatavarChar, true);
        $this->repTableInteger = $reportTable->saveStructureOfTable($httpDatavarInteger, true);
        $this->repTableSmallInt = $reportTable->saveStructureOfTable($httpDatavarSmallInt, true);
        $this->repTableTinyInt = $reportTable->saveStructureOfTable($httpDatavarTinyInt, true);
        $this->repTableVarChar = $reportTable->saveStructureOfTable($httpDatavarVarChar, true);
    }

    function __construct()
    {
        //The user logged is the admin user
        $_SESSION['USER_LOGGED'] = "00000000000000000000000000000001";
    }

    /**
     * It tests that the update method in the pmTable works property
     *
     * @test
     */
    public function it_should_update_a_pmTable_row()
    {
        $obj = new pmTablesProxy();
        define('_SESSION', 'admin');

        //The variables that will be used to create rows in the pmTables
        $httpDataBigInt = [
            'id' => "1949507365cdc15f894d4e5031794159",
            'rows' => json_encode(["ID" => 986, "NAME" => "BigInt"])
        ];

        $httpDataChar = [
            'id' => "1650070085cdc2161da8868092036636",
            'rows' => json_encode(["ID" => "009", "NAME" => "Char"])
        ];

        $httpDataInteger = [
            'id' => "6834749815cdc22a012a4e3001841836",
            'rows' => json_encode(["ID" => 8, "NAME" => "Integer"])
        ];

        $httpDataSmallInt = [
            'id' => "2021802955cdc23ac21f422092445656",
            'rows' => json_encode(["ID" => 5, "NAME" => "SmallInt"])
        ];

        $httpDataTinyInt = [
            'id' => "2397869895cdc240e3472e5056572559",
            'rows' => json_encode(["ID" => 1, "NAME" => "TinyInt"])
        ];

        $httpDataVarChar = [
            'id' => "6034139095cdc24749c27f8067730700",
            'rows' => json_encode(["ID" => "076", "NAME" => "VarChar"])
        ];

        //This will add rows to the pmTables
        $obj->dataCreate($httpDataBigInt);
        $obj->dataCreate($httpDataChar);
        $obj->dataCreate($httpDataInteger);
        $obj->dataCreate($httpDataSmallInt);
        $obj->dataCreate($httpDataTinyInt);
        $obj->dataCreate($httpDataVarChar);

        //The variables that will be used to update the rows in the pmTables
        $httpDataUpdateBigInt = (object)[
            'id' => '1949507365cdc15f894d4e5031794159',
            'rows' => json_encode(["NAME" => "BigIntUpdated", "__index__" => G::encrypt(986, PMTABLE_KEY)]),
        ];

        $httpDataUpdateChar = (object)[
            'id' => '1650070085cdc2161da8868092036636',
            'rows' => json_encode(["NAME" => "CharUpdated", "__index__" => G::encrypt("009", PMTABLE_KEY)]),
        ];

        $httpDataUpdateInteger = (object)[
            'id' => '6834749815cdc22a012a4e3001841836',
            'rows' => json_encode(["NAME" => "IntegerUpdated", "__index__" => G::encrypt(8, PMTABLE_KEY)]),
        ];

        $httpDataUpdateSmallInt = (object)[
            'id' => '2021802955cdc23ac21f422092445656',
            'rows' => json_encode(["NAME" => "SmallIntUpdated", "__index__" => G::encrypt(5, PMTABLE_KEY)]),
        ];

        $httpDataUpdateTinyInt = (object)[
            'id' => '2397869895cdc240e3472e5056572559',
            'rows' => json_encode(["NAME" => "TinyIntUpdated", "__index__" => G::encrypt(1, PMTABLE_KEY)]),
        ];

        $httpDataUpdateVarChar = (object)[
            'id' => '6034139095cdc24749c27f8067730700',
            'rows' => json_encode(["NAME" => "VarCharUpdated", "__index__" => G::encrypt("076", PMTABLE_KEY)]),
        ];

        //This method update the pmTables rows
        $resultDataUpdateBigInt = $obj->dataUpdate($httpDataUpdateBigInt);
        $resultDataUpdateChar = $obj->dataUpdate($httpDataUpdateChar);
        $resultDataUpdateInteger = $obj->dataUpdate($httpDataUpdateInteger);
        $resultDataUpdateSmallInt = $obj->dataUpdate($httpDataUpdateSmallInt);
        $resultDataUpdateTinyInt = $obj->dataUpdate($httpDataUpdateTinyInt);
        $resultDataUpdateVarChar = $obj->dataUpdate($httpDataUpdateVarChar);

        //Assert the result is null and no errors appear
        $this->assertNull($resultDataUpdateBigInt);
        $this->assertNull($resultDataUpdateChar);
        $this->assertNull($resultDataUpdateInteger);
        $this->assertNull($resultDataUpdateSmallInt);
        $this->assertNull($resultDataUpdateTinyInt);
        $this->assertNull($resultDataUpdateVarChar);

        //Assert the values were updated
        $resUpdateBigInt = $obj->dataView((object)["id" => "1949507365cdc15f894d4e5031794159"]);
        $this->assertEquals("BigIntUpdated", $resUpdateBigInt['rows'][0]['NAME']);

        $resUpdateChar = $obj->dataView((object)["id" => "1650070085cdc2161da8868092036636"]);
        $this->assertEquals("CharUpdated", $resUpdateChar['rows'][0]['NAME']);

        $resUpdateInteger = $obj->dataView((object)["id" => "6834749815cdc22a012a4e3001841836"]);
        $this->assertEquals("IntegerUpdated", $resUpdateInteger['rows'][0]['NAME']);

        $resUpdateSmallInt = $obj->dataView((object)["id" => "2021802955cdc23ac21f422092445656"]);
        $this->assertEquals("SmallIntUpdated", $resUpdateSmallInt['rows'][0]['NAME']);

        $resUpdateTinyInt = $obj->dataView((object)["id" => "2397869895cdc240e3472e5056572559"]);
        $this->assertEquals("TinyIntUpdated", $resUpdateTinyInt['rows'][0]['NAME']);

        $resUpdateVarChar = $obj->dataView((object)["id" => "6034139095cdc24749c27f8067730700"]);
        $this->assertEquals("VarCharUpdated", $resUpdateVarChar['rows'][0]['NAME']);
    }

    /**
     * It should throw an exception if trying to update the primary key value of a pmTable
     * @test
     */
    public function it_should_throw_an_exception_if_trying_to_update_pk_value()
    {
        config(["system.workspace" => SYS_SYS]);
        $obj = new pmTablesProxy();

        //The variable that is sent to the update method
        $httpDataUpdateTest = (object)[
            'id' => '6034139095cdc24749c27f8067730700',
            'rows' => json_encode(["ID" => "00768", "__index__" => G::encrypt("076", PMTABLE_KEY)]),
        ];

        //Asserts an exception is thrown when the user tries to modify a primary key value
        $this->expectExceptionMessage("**ID_DONT_MODIFY_PK_VALUE**");
        $obj->dataUpdate($httpDataUpdateTest);
    }

    /**
     * Tests the delete of a pmTable row
     * @test
     */
    public function it_should_delete_a_pmTable_row()
    {
        config(["system.workspace" => SYS_SYS]);
        $obj = new pmTablesProxy();

        //Variables that will be sent to the destroy method
        $httpDataDestroyBigInt = (object)[
            'id' => '1949507365cdc15f894d4e5031794159',
            'rows' => G::encrypt(986, PMTABLE_KEY),
        ];

        $httpDataDestroyChar = (object)[
            'id' => '1650070085cdc2161da8868092036636',
            'rows' => G::encrypt("009", PMTABLE_KEY),
        ];

        $httpDataDestroyInteger = (object)[
            'id' => '6834749815cdc22a012a4e3001841836',
            'rows' => G::encrypt(8, PMTABLE_KEY),
        ];

        $httpDataDestroySmallInt = (object)[
            'id' => '2021802955cdc23ac21f422092445656',
            'rows' => G::encrypt(5, PMTABLE_KEY),
        ];

        $httpDataDestroyTinyInt = (object)[
            'id' => '2397869895cdc240e3472e5056572559',
            'rows' => G::encrypt(1, PMTABLE_KEY),
        ];

        $httpDataDestroyVarChar = (object)[
            'id' => '6034139095cdc24749c27f8067730700',
            'rows' => G::encrypt("076", PMTABLE_KEY),
        ];

        //This method will delete a specific row
        $resDeleteBigInt = $obj->dataDestroy($httpDataDestroyBigInt);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteBigInt);

        //This method will delete a specific row
        $resDeleteChar = $obj->dataDestroy($httpDataDestroyChar);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteChar);

        //This method will delete a specific row
        $resDeleteInteger = $obj->dataDestroy($httpDataDestroyInteger);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteInteger);

        //This method will delete a specific row
        $resDeleteSmallInt = $obj->dataDestroy($httpDataDestroySmallInt);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteSmallInt);

        //This method will delete a specific row
        $resDeleteTinyInt = $obj->dataDestroy($httpDataDestroyTinyInt);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteTinyInt);

        //This method will delete a specific row
        $resDeleteVarChar = $obj->dataDestroy($httpDataDestroyVarChar);
        //Assert the result is null, so no errors were thrown
        $this->assertNull($resDeleteVarChar);

        //This method will return a specific row
        $resViewBigInt = $obj->dataView((object)["id" => "1949507365cdc15f894d4e5031794159"]);
        //Assert the row was deleted, so the pmTable does not have data
        $this->assertEquals(0, $resViewBigInt['count']);

        //This method will return a specific row
        $resViewChar = $obj->dataView((object)["id" => "1650070085cdc2161da8868092036636"]);
        //Assert the row was deleted, so the pmTable does not have data
        $this->assertEquals(0, $resViewChar['count']);

        //This method will return a specific row
        $resViewInteger = $obj->dataView((object)["id" => "6834749815cdc22a012a4e3001841836"]);
        //Assert the row was deleted, so the pmTable does not have data
        $this->assertEquals(0, $resViewInteger['count']);

        //This method will return a specific row
        $resViewSmallInt = $obj->dataView((object)["id" => "2021802955cdc23ac21f422092445656"]);
        //Assert the row was deleted, so the pmTable does not have data
        $this->assertEquals(0, $resViewSmallInt['count']);

        //This method will return a specific row
        $resViewTinyInt = $obj->dataView((object)["id" => "2397869895cdc240e3472e5056572559"]);
        //Assert the row was deleted, so the pmTable does not have data
        $this->assertEquals(0, $resViewTinyInt['count']);

        //This method will return a specific row
        $resViewVarChar = $obj->dataView((object)["id" => "6034139095cdc24749c27f8067730700"]);
        //Assert the row was deleted, so the pmTable does not have data
        $this->assertEquals(0, $resViewVarChar['count']);
    }

    /**
     * It tests the exception if the pmTable does not exist when deleted a row
     * @test
     */
    public function it_should_throw_an_exception_if_the_pmTable_does_not_exist_when_deleted()
    {
        config(["system.workspace" => SYS_SYS]);
        $obj = new pmTablesProxy();

        //Variable that is sent to the destroy method
        $httpDataDestroyTest = (object)[
            'id' => '6034139095cdc24749c27f830700',
            'rows' => G::encrypt("6", PMTABLE_KEY),
        ];

        //Asser the exception message when the pmTable does not exists
        $this->expectExceptionMessage("Destroy:: **ID_PMTABLE_CLASS_DOESNT_EXIST**");
        //This method deletes a specific row of a pmTable
        $obj->dataDestroy($httpDataDestroyTest);
    }
}