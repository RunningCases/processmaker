<?php

namespace ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\Lists;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Lists Tests
 */
class ListsTest extends TestCase
{
    /**
     * It tests the construct of the Lists class
     * 
     * @covers \ProcessMaker\BusinessModel\Lists
     * @test
     */
    public function it_should_test_the_lists_construct()
    {
        $user = factory(User::class)->create();

        $list = new Lists();
        $res = $list->getList('inbox', ['userId' => $user->USR_UID]);

        $this->assertEmpty($res['data']);
        $this->assertEquals(0, $res['totalCount']);
    }
}
