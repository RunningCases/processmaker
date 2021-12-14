<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\RbacRoles;
use ProcessMaker\Model\RbacUsers;
use Tests\TestCase;

/**
 * Class RbacUsersTest
 *
 * @coversDefaultClass \ProcessMaker\Model\SubProcess
 */
class RbacUsersTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the verifyUsernameExists() method
     * 
     * @test
     */
    public function it_should_test_the_verify_username_exists_method()
    {
        $rbacUser = factory(RbacUsers::class)->create([
            'USR_USERNAME' => 'test'
        ]);

        $res = RbacUsers::verifyUsernameExists('test');
        $this->assertTrue($res);

        $res = RbacUsers::verifyUsernameExists('test2');
        $this->assertFalse($res);
    }

    /**
     * It tests the createUser() method
     * 
     * @test
     */
    public function it_should_test_the_create_user_method()
    {
        $roles = factory(RbacRoles::class)->create();
        $data = [
            'USR_UID' => G::generateUniqueID(),
            'USR_USERNAME' => 'test',
            'USR_PASSWORD' => 'sample',
            'USR_FIRSTNAME' => 'test',
            'USR_LASTNAME' => 'test',
            'USR_EMAIL' => 'test@test.com',
            'USR_DUE_DATE' => '2021-01-01',
            'USR_CREATE_DATE' => '2021-01-01',
            'USR_UPDATE_DATE' => '2021-01-01',
            'USR_STATUS_ID' => 1,
            'USR_AUTH_TYPE' => '',
            'UID_AUTH_SOURCE' => '',
            'USR_AUTH_USER_DN' => '',
            'USR_AUTH_SUPERVISOR_DN' => '',
            'ROL_UID' => $roles['ROL_UID']
        ];
        $res = RbacUsers::createUser($data);
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the verifyUserExists() method
     * 
     * @test
     */
    public function it_should_test_the_verify_user_exists_method()
    {
        $rbacUser = factory(RbacUsers::class)->create();

        $res = RbacUsers::verifyUserExists($rbacUser['USR_UID']);
        $this->assertTrue($res);

        $res = RbacUsers::verifyUserExists('12345');
        $this->assertFalse($res);
    }
}
