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
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFGetGroupUsers.28.29
 */
class PMFRemoveUsersFromGroupTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFRemoveUsersFromGroup"
     * @test
     */
    public function it_remove_user_group()
    {
        // Create group
        $user = factory(User::class)->create();
        $group = factory(Groupwf::class)->create();
        $groupUser = factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' =>$user->USR_UID
        ]);
        DB::commit();
        $result = PMFRemoveUsersFromGroup($groupUser->GRP_UID, [$groupUser->USR_UID]);
        $this->assertNotEmpty($result);
    }
}
