<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFGetGroupUsers() function
 *
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFRemoveUsersToGroup.28.29
 */
class PMFRemoveUsersToGroupTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFRemoveUsersToGroup"
     * @test
     */
    public function it_remove_user_group()
    {
        // Create group
        $user = User::factory()->create();
        $group = Groupwf::factory()->create();
        $groupUser = GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' =>$user->USR_UID
        ]);
        DB::commit();
        $result = PMFRemoveUsersToGroup($groupUser->GRP_UID, [$groupUser->USR_UID]);
        $this->assertNotEmpty($result);
    }
}
