<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class ProcessTest
 *
 * @coversDefaultClass \ProcessMaker\Model\GroupUser
 */
class GroupUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the assignUserToGroup() method
     * 
     * @test
     */
    public function it_should_test_the_assign_user_to_group_method()
    {
        $rbacUser = factory(RbacUsers::class)->create();
        $user = factory(User::class)->create([
            'USR_UID' => $rbacUser['USR_UID'],
            'USR_USERNAME' => $rbacUser['USR_USERNAME'],
            'USR_PASSWORD' => $rbacUser['USR_PASSWORD'],
            'USR_FIRSTNAME' => $rbacUser['USR_FIRSTNAME'],
            'USR_LASTNAME' => $rbacUser['USR_LASTNAME'],
            'USR_EMAIL' => $rbacUser['USR_EMAIL'],
        ]);
        $group = factory(Groupwf::class)->create();

        GroupUser::assignUserToGroup($rbacUser['USR_UID'], $user['USR_ID'], $group['GRP_UID'], $group['GRP_ID']);

        $query = GroupUser::select()->where('GRP_ID', $group['GRP_ID'])->where('USR_ID', $user['USR_ID']);
        $res = $query->get()->values()->toArray();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the verifyUserIsInGroup() method
     * 
     * @test
     */
    public function it_should_test_the_verify_user_is_in_group_method()
    {
        $rbacUser = factory(RbacUsers::class)->create();
        $user = factory(User::class)->create([
            'USR_UID' => $rbacUser['USR_UID'],
            'USR_USERNAME' => $rbacUser['USR_USERNAME'],
            'USR_PASSWORD' => $rbacUser['USR_PASSWORD'],
            'USR_FIRSTNAME' => $rbacUser['USR_FIRSTNAME'],
            'USR_LASTNAME' => $rbacUser['USR_LASTNAME'],
            'USR_EMAIL' => $rbacUser['USR_EMAIL'],
        ]);
        $group = factory(Groupwf::class)->create();

        $res = GroupUser::verifyUserIsInGroup($user['USR_ID'], $group['GRP_ID']);
        $this->assertFalse($res);

        GroupUser::assignUserToGroup($rbacUser['USR_UID'], $user['USR_ID'], $group['GRP_UID'], $group['GRP_ID']);

        $res = GroupUser::verifyUserIsInGroup($user['USR_ID'], $group['GRP_ID']);
        $this->assertTrue($res);
    }
}
