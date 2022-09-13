<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFAssignUserToGroup() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Group_Functions#PMFAssignUserToGroup.28.29
 */
class PMFAssignUserToGroupTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFAssignUserToGroup"
     * @test
     */
    public function it_assign_user_to_group()
    {
        // Create user
        global $RBAC;
        $user = User::factory()->create();
        RbacUsers::factory()->create([
            'USR_UID' => $user->USR_UID,
            'USR_USERNAME' => $user->USR_USERNAME,
            'USR_FIRSTNAME' => $user->USR_FIRSTNAME,
            'USR_LASTNAME' => $user->USR_LASTNAME
        ]);
        // Create group
        $group = Groupwf::factory()->create();
        DB::commit();
        $result = PMFAssignUserToGroup($user->USR_UID, $group->GRP_UID);
        $this->assertNotEmpty($result);
    }
}