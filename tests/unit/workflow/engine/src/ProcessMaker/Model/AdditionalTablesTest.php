<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Fields;
use Tests\TestCase;

class AdditionalTablesTest extends TestCase
{
    /**
     * Test belongs to ADD_TAB_UID
     *
     * @covers \ProcessMaker\Model\AdditionalTables::columns()
     * @test
     */
    public function it_has_a_columns_defined()
    {
        $table = factory(AdditionalTables::class)->create([
            'ADD_TAB_UID' => function () {
                return factory(Fields::class)->create()->ADD_TAB_UID;
            }
        ]);
        $this->assertInstanceOf(Fields::class, $table->columns);
    }

    /**
     * Test scope query to get the offline tables
     *
     * @covers \ProcessMaker\Model\AdditionalTables::scopeOffline()
     * @test
     */
    public function it_filter_offline_table()
    {
        factory(AdditionalTables::class)->create(['ADD_TAB_OFFLINE' => 0]);
        $table = factory(AdditionalTables::class)->create([
            'ADD_TAB_OFFLINE' => 1
        ]);
        $this->assertCount(1, $table->offline([$table->ADD_TAB_OFFLINE])->get());
    }

    /**
     * Test get the structure of offline tables
     *
     * @covers \ProcessMaker\Model\AdditionalTables::getTablesOfflineStructure()
     * @test
     */
    public function it_get_structure_from_offline_tables()
    {
        factory(Fields::class)->states('foreign_keys')->create();
        $results = AdditionalTables::getTablesOfflineStructure();
        $this->assertNotEmpty($results);
        foreach ($results as $row) {
            $this->assertArrayHasKey('add_tab_uid', $row);
            $this->assertArrayHasKey('add_tab_name', $row);
            $this->assertArrayHasKey('add_tab_description', $row);
            $this->assertArrayHasKey('add_tab_class_name', $row);
            $this->assertArrayHasKey('fields', $row);
        }
    }

    /**
     * Test get the data of offline tables
     *
     * @covers \ProcessMaker\Model\AdditionalTables::getTablesOfflineData()
     * @test
     */
    public function it_get_data_from_offline_tables()
    {
        factory(Fields::class)->states('foreign_keys')->create();
        $results = AdditionalTables::getTablesOfflineData();
        $this->assertNotEmpty($results);
        foreach ($results as $row) {
            $this->assertArrayHasKey('add_tab_uid', $row);
            $this->assertArrayHasKey('add_tab_name', $row);
            $this->assertArrayHasKey('add_tab_description', $row);
            $this->assertArrayHasKey('add_tab_class_name', $row);
            $this->assertArrayHasKey('rows', $row);
        }
    }
}
