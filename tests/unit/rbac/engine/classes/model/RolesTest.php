<?php

namespace Tests\unit\rbac\engine\classes\model;

use ProcessMaker\Model\RbacRoles;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\RbacUsersRoles;
use Roles;
use Tests\TestCase;

class RolesTest extends TestCase
{
    /**
     * Test the method "numUsersWithRole" with users with different statuses
     *
     * @test
     *
     * @covers Roles::numUsersWithRole()
     */
    public function it_should_count_correctly_the_users_with_a_role_assigned()
    {
        // Instance the class with the method to test
        $rolesInstance = new Roles();

        // Create elements
        $role = RbacRoles::factory()->create();
        $deletedUser = RbacUsers::factory()->deleted()->create();
        $activeUser = RbacUsers::factory()->active()->create();
        $inactiveUser = RbacUsers::factory()->inactive()->create();

        // Assign the role to a deleted user
        RbacUsersRoles::factory()->create([
            'ROL_UID' => $role->ROL_UID,
            'USR_UID' => $deletedUser->USR_UID
        ]);
        // Should be 0, because a deleted user should not be considered
        $this->assertEquals(0, $rolesInstance->numUsersWithRole($role->ROL_UID));

        // Assign the role to an active user
        RbacUsersRoles::factory()->create([
            'ROL_UID' => $role->ROL_UID,
            'USR_UID' => $activeUser->USR_UID
        ]);
        // Should be 1, because only the active user should be considered
        $this->assertEquals(1, $rolesInstance->numUsersWithRole($role->ROL_UID));

        // Assign the role to an inactive user
        RbacUsersRoles::factory()->create([
            'ROL_UID' => $role->ROL_UID,
            'USR_UID' => $inactiveUser->USR_UID
        ]);
        // Should be 2, because only the active and the inactive users should be considered
        $this->assertEquals(2, $rolesInstance->numUsersWithRole($role->ROL_UID));
    }
}
