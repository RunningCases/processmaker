<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Fields;
use Tests\TestCase;

/**
 * Class AdditionalTablesTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AdditionalTables
 */
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
        $table = AdditionalTables::factory()->create([
            'ADD_TAB_UID' => function () {
                return Fields::factory()->create()->ADD_TAB_UID;
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
        AdditionalTables::factory()->create(['ADD_TAB_OFFLINE' => 0]);
        $table = AdditionalTables::factory()->create([
            'ADD_TAB_OFFLINE' => 1
        ]);
        $this->assertCount(1, $table->offline([$table->ADD_TAB_OFFLINE])->get());
    }

    /**
     * Test scope query to get the offline tables
     *
     * @covers \ProcessMaker\Model\AdditionalTables::scopeProcess()
     * @test
     */
    public function it_filter_process()
    {
        $table = AdditionalTables::factory()->create();
        $this->assertCount(1, $table->process($table->PRO_UID)->get());
    }

    /**
     * Test scope query to get tables related to the process
     *
     * @covers \ProcessMaker\Model\AdditionalTables::getTables()
     * @test
     */
    public function it_get_tables_related_to_process()
    {
        $table = AdditionalTables::factory()->create();
        $result = AdditionalTables::getTables($table->PRO_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test get the structure of offline tables
     *
     * @covers \ProcessMaker\Model\AdditionalTables::getTablesOfflineStructure()
     * @test
     */
    public function it_get_structure_from_offline_tables()
    {
        Fields::factory()->foreign_keys()->create();
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
        Fields::factory()->foreign_keys()->create();
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

    /**
     * Test update the offline property
     *
     * @covers \ProcessMaker\Model\AdditionalTables::updatePropertyOffline()
     * @test
     */
    public function it_update_property_offline()
    {
        $pmTable = AdditionalTables::factory()->create(['ADD_TAB_OFFLINE' => 0]);
        $results = AdditionalTables::updatePropertyOffline([$pmTable->ADD_TAB_UID], 1);
        // Check the update
        $pmTableQuery = AdditionalTables::query()->select(['ADD_TAB_OFFLINE']);
        $pmTableQuery->where('ADD_TAB_UID', $pmTable->ADD_TAB_UID);
        $result = $pmTableQuery->get()->values()->toArray();

        // Assert, the update was executed
        $this->assertEquals($result[0]['ADD_TAB_OFFLINE'], 1);
    }
}
