<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Fields;
use Tests\TestCase;

class FieldsTest extends TestCase
{
    /**
     * Test belongs to ADD_TAB_UID
     *
     * @covers \ProcessMaker\Model\Fields::table()
     * @test
     */
    public function it_has_a_columns_defined()
    {
        $tableColumns = factory(Fields::class)->create([
            'ADD_TAB_UID' => function () {
                return factory(AdditionalTables::class)->create()->ADD_TAB_UID;
            }
        ]);
        $this->assertInstanceOf(AdditionalTables::class, $tableColumns->table);
    }

    /**
     * Test scope and the query with a specific ADD_TAB_UID
     *
     * @covers \ProcessMaker\Model\Fields::scopeTable()
     * @covers \ProcessMaker\Model\Fields::getFields()
     * @test
     */
    public function it_get_fields_from_specific_table()
    {
        $fields = factory(Fields::class)->create();
        $result = Fields::getFields($fields->ADD_TAB_UID);
        $this->assertNotEmpty($result);
    }
}
