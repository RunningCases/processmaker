<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFUpdateUser() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFUpdateUser.28.29
 */
class PMFUpdateUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests the "PMFUpdateUser"
     * @test
     */
    public function it_update_user()
    {
        // Create User
        global $RBAC;
        $user = User::factory()->create();
        DB::commit();
        $result = PMFUpdateUser($user->USR_UID, $user->USR_USERNAME, 'John A.');
        $this->assertEquals(0, $result);
    }
}
