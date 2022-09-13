<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Groupwf;
use Tests\TestCase;

/**
 * Test the PMFGetGroupUID() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFGetGroupUID.28.29
 */
class PMFGetGroupUIDTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetGroupUID"
     * @test
     */
    public function it_group_uid()
    {
        // Create group
        $group = Groupwf::factory()->create();
        $result = PMFGetGroupUID($group->GRP_UID);
        $this->assertFalse($result);
    }
}
