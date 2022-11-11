<?php

namespace ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\Table;
use ProcessMaker\Model\AdditionalTables;
use Tests\TestCase;

class TableTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * Method setUp.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Method tearDown.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * This test getTables() method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Table::getTables()
     */
    public function it_should_test_getTables_method()
    {
        $additionalTables = AdditionalTables::factory()
            ->create();

        $proUid = $additionalTables->PRO_UID;
        $search = $additionalTables->ADD_TAB_NAME;

        $table = new Table();
        $result = $table->getTables($proUid, true, false, $search);

        //assertions
        $this->assertNotEmpty($result);
        $this->assertEquals($additionalTables->ADD_TAB_NAME, $result[0]['rep_tab_name']);

        $search = '';
        $table = new Table();
        $result = $table->getTables($proUid, true, false, $search);

        //assertions
        $this->assertNotEmpty($result);
        $this->assertEquals($additionalTables->ADD_TAB_NAME, $result[0]['rep_tab_name']);
    }
}
