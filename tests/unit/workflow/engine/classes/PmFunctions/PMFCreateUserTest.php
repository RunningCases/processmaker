<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

/**
 * Test the PMFCreateUser() function
 * 
 * @link https://wiki.processmaker.com/3.7/ProcessMaker_Functions/User_Functions#PMFCreateUser.28.29
 */
class PMFCreateUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests the "PMFCreateUser"
     * @test
     */
    public function it_create_user()
    {
        // Create User
        $user = User::factory()->create();
        RbacUsers::factory()->create([
            'USR_UID' => $user->USR_UID,
            'USR_USERNAME' => $user->USR_USERNAME,
            'USR_FIRSTNAME' => $user->USR_FIRSTNAME,
            'USR_LASTNAME' => $user->USR_LASTNAME
        ]);
        DB::commit();
        $result = PMFCreateUser('jsmith', 'PaSsWoRd', 'John', 'Smith', 'jsmith@company.com', 'PROCESSMAKER_OPERATOR');
        $this->assertNotEmpty($result);
    }
}
