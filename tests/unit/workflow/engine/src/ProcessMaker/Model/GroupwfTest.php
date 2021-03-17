<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Groupwf;
use Tests\TestCase;

/**
 * Class ProcessTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Groupwf
 */
class GroupwfTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the verifyGroupExists() method
     * 
     * @test
     */
    public function it_should_test_the_verify_group_exists_method()
    {
        $groupWf = factory(Groupwf::class)->create();

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
        $groupWf = factory(Groupwf::class)->create();

        $res = Groupwf::getGroupId($groupWf['GRP_UID']);
        $this->assertNotEmpty($res);
        $this->assertEquals($res['GRP_ID'], $groupWf['GRP_ID']);
    }
}
