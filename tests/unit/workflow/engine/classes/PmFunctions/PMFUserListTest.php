<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFUserList() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/User_Functions#PMFUserList.28.29
 */
class PMFUserListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFUserList"
     * @test
     */
    public function it_return_list_of_users()
    {
        // Create user
        $user = factory(User::class)->create();
        $result = PMFUserList();
        $this->assertNotEmpty($result);
    }
}
