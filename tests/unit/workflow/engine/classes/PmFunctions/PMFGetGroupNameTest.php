<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Groupwf;
use Tests\TestCase;

/**
 * Test the PMFGetGroupName() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFGetGroupName.28.29
 */
class PMFGetGroupNameTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetGroupName"
     * @test
     */
    public function it_get_group_name()
    {
        // Create group
        $group = factory(Groupwf::class)->create();
        DB::commit();
        $result = PMFGetGroupName($group->GRP_TITLE, 'en');
        $this->assertFalse($result);
        // When is empty
        $result = PMFGetGroupName('');
        $this->assertFalse($result);
    }
}
