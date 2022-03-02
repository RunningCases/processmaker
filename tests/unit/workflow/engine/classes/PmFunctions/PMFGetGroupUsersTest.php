<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Groupwf;
use Tests\TestCase;

/**
 * Test the PMFGetGroupUsers() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFGetGroupUsers.28.29
 */
class PMFGetGroupUsersTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetGroupUsers"
     * @test
     */
    public function it_return_list_of_groups()
    {
        // Create group
        $group = factory(Groupwf::class)->create();
        DB::commit();
        $result = PMFGetGroupUsers($group->GRP_UID);
        $this->assertEmpty($result);
    }
}
