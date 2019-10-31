<?php

namespace Tests\unit\workflow\engine\classes\model;

use AdditionalTables;
use Exception;
use G;
use ProcessMaker\Model\AdditionalTables as AdditionalTablesModel;
use Tests\TestCase;

class AdditionalTablesTest extends TestCase
{

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
}
