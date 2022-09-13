<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFGetUserEmailAddress() function
 */
class PMFGetUserEmailAddressTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests the "PMFGetUserEmailAddress"
     * @test
     */
    public function it_get_user_mail()
    {
        // Create User
        global $RBAC;
        $user = User::factory()->create();
        DB::commit();
        $result = PMFGetUserEmailAddress([$user->USR_UID], null);
        $this->assertNotEmpty($result);
    }
}
