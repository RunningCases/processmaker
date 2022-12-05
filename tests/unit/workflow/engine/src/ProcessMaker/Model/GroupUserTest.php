<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class GroupUserTest
 *
 * @coversDefaultClass \ProcessMaker\Model\GroupUser
 */
class GroupUserTest extends TestCase
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
     * Test belongs to USR_UID
     *
     * @covers \ProcessMaker\Model\GroupUser::user()
     * @test
     */
    public function it_belong_user()
    {
        $table = GroupUser::factory()->create([
            'USR_UID' => function () {
                return User::factory()->create()->USR_UID;
            }
        ]);
        $this->assertInstanceOf(User::class, $table->user);
    }

    /**
     * Test belongs to GRP_ID
     *
     * @covers \ProcessMaker\Model\GroupUser::groupsWf()
     * @test
     */
    public function it_belong_group()
    {
        $table = GroupUser::factory()->create([
            'GRP_ID' => function () {
                return Groupwf::factory()->create()->GRP_ID;
            }
        ]);
        $this->assertInstanceOf(Groupwf::class, $table->groupsWf);
    }

    /**
     * This test scopeUser
     *
     * @covers \ProcessMaker\Model\GroupUser::scopeUser()
     * @test
     */
    public function it_return_scope_user()
    {
        $table = GroupUser::factory()->foreign_keys()->create();
        $this->assertNotEmpty($table->user($table->USR_UID)->get());
    }

    /**
     * It tests the messages related assignUserToGroup() method
     * 
     * @covers \ProcessMaker\Model\GroupUser::assignUserToGroup()
     * @test
     */
    public function it_should_test_message()
    {
        // When the user does not exist
        $user = User::factory()->create();
        $group = Groupwf::factory()->create();
        $result = GroupUser::assignUserToGroup('', 0, '', 0);
        $this->assertNotEmpty($result);
        // When the group does not exist
        $rbacUser = RbacUsers::factory()->create();
        $user = User::factory()->create([
            'USR_UID' => $rbacUser['USR_UID']
        ]);
        $group = Groupwf::factory()->create();
        $result = GroupUser::assignUserToGroup($user['USR_UID'], 0, '', 0);
        $this->assertNotEmpty($result);
        // When the user already exist in a group
        $rbacUser = RbacUsers::factory()->create();
        $user = User::factory()->create([
            'USR_UID' => $rbacUser['USR_UID']
        ]);
        $group = Groupwf::factory()->create();
        GroupUser::assignUserToGroup($user['USR_UID'], $user['USR_ID'], $group['GRP_UID'], $group['GRP_ID']);
        $result = GroupUser::assignUserToGroup($user['USR_UID'], $user['USR_ID'], $group['GRP_UID'], $group['GRP_ID']);
        $this->assertNotEmpty($result);
    }

    /**
     * It tests the assignUserToGroup() method
     * 
     * @covers \ProcessMaker\Model\GroupUser::assignUserToGroup()
     * @test
     */
    public function it_should_test_the_assign_user_to_group_method()
    {
        $rbacUser = RbacUsers::factory()->create();
        $user = User::factory()->create([
            'USR_UID' => $rbacUser['USR_UID'],
            'USR_USERNAME' => $rbacUser['USR_USERNAME'],
            'USR_PASSWORD' => $rbacUser['USR_PASSWORD'],
            'USR_FIRSTNAME' => $rbacUser['USR_FIRSTNAME'],
            'USR_LASTNAME' => $rbacUser['USR_LASTNAME'],
            'USR_EMAIL' => $rbacUser['USR_EMAIL'],
        ]);
        $group = Groupwf::factory()->create();

        GroupUser::assignUserToGroup($rbacUser['USR_UID'], $user['USR_ID'], $group['GRP_UID'], $group['GRP_ID']);

        $query = GroupUser::select()->where('GRP_ID', $group['GRP_ID'])->where('USR_ID', $user['USR_ID']);
        $res = $query->get()->values()->toArray();
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the verifyUserIsInGroup() method
     * 
     * @covers \ProcessMaker\Model\GroupUser::verifyUserIsInGroup()
     * @covers \ProcessMaker\Model\GroupUser::assignUserToGroup()
     * @test
     */
    public function it_should_test_the_verify_user_is_in_group_method()
    {
        $rbacUser = RbacUsers::factory()->create();
        $user = User::factory()->create([
            'USR_UID' => $rbacUser['USR_UID'],
            'USR_USERNAME' => $rbacUser['USR_USERNAME'],
            'USR_PASSWORD' => $rbacUser['USR_PASSWORD'],
            'USR_FIRSTNAME' => $rbacUser['USR_FIRSTNAME'],
            'USR_LASTNAME' => $rbacUser['USR_LASTNAME'],
            'USR_EMAIL' => $rbacUser['USR_EMAIL'],
        ]);
        $group = Groupwf::factory()->create();

        $res = GroupUser::verifyUserIsInGroup($user['USR_ID'], $group['GRP_ID']);
        $this->assertFalse($res);

        GroupUser::assignUserToGroup($rbacUser['USR_UID'], $user['USR_ID'], $group['GRP_UID'], $group['GRP_ID']);

        $res = GroupUser::verifyUserIsInGroup($user['USR_ID'], $group['GRP_ID']);
        $this->assertTrue($res);
    }
}
