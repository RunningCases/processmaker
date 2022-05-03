<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the userInfo() function
 */
class UserInfoTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests the "userInfo"
     * @test
     */
    public function it_get_user_info()
    {
        // Create User
        global $RBAC;
        $user = factory(User::class)->create();
        DB::commit();
        $result = userInfo($user->USR_UID);
        $this->assertNotEmpty($result);
    }
}
