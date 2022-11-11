<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use Tests\TestCase;

/**
 * Class GroupwfTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Groupwf
 */
class GroupwfTest extends TestCase
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
     * Method set up.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test belongs to GRP_ID
     *
     * @covers \ProcessMaker\Model\Groupwf::groupUsers()
     * @test
     */
    public function it_belong_group()
    {
        $table = Groupwf::factory()->create([
            'GRP_ID' => function () {
                return GroupUser::factory()->create()->GRP_ID;
            }
        ]);
        $this->assertInstanceOf(GroupUser::class, $table->groupUsers);
    }

    /**
     * This test scopeActive
     *
     * @covers \ProcessMaker\Model\Groupwf::scopeActive()
     * @test
     */
    public function it_return_scope_active()
    {
        $table = Groupwf::factory()->create();
        $this->assertNotEmpty($table->active()->get());
    }

    /**
     * It tests the verifyGroupExists() method
     * 
     * @test
     */
    public function it_should_test_the_verify_group_exists_method()
    {
        $groupWf = Groupwf::factory()->create();

        $res = Groupwf::verifyGroupExists($groupWf['GRP_UID']);
        $this->assertTrue($res);

        $res = Groupwf::verifyGroupExists('12345');
        $this->assertFalse($res);
    }

    /**
     * It tests the getGroupId() method
     * 
     * @test
     */
    public function it_should_test_the_get_group_id_method()
    {
        $groupWf = Groupwf::factory()->create();

        $res = Groupwf::getGroupId($groupWf['GRP_UID']);
        $this->assertNotEmpty($res);
        $this->assertEquals($res['GRP_ID'], $groupWf['GRP_ID']);
    }
}
