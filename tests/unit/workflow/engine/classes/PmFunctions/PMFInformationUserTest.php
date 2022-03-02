<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFInformationUser() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/User_Functions#PMFInformationUser.28.29
 */
class PMFInformationUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFInformationUser"
     * @test
     */
    public function it_return_list_of_process()
    {
        // Create User
        global $RBAC;
        $user = factory(User::class)->create();
        DB::commit();
        $result = PMFInformationUser($user->USR_UID);
        $this->assertNotEmpty($result);
    }
}
