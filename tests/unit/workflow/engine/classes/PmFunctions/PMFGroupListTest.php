<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Groupwf;
use Tests\TestCase;

/**
 * Test the PMFGroupList() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Group_Functions#PMFGroupList.28.29
 */
class PMFGroupListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGroupList"
     * @test
     */
    public function it_return_list_of_groups()
    {
        // Create group
        Groupwf::factory()->create();
        DB::commit();
        $result = PMFGroupList();
        $this->assertNotEmpty($result);
    }
}
