<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\RbacRoles;
use Tests\TestCase;

/**
 * Test the PMFRoleList() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Group_Functions#PMFRoleList.28.29
 */
class PMFRoleListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFRoleList"
     * @test
     */
    public function it_return_list_of_roles()
    {
        // Create roles
        global $RBAC;
        factory(RbacRoles::class)->create();
        DB::commit();
        $result = PMFRoleList();
        $this->assertNotEmpty($result);
    }
}
