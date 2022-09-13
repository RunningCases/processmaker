<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Process;
use Tests\TestCase;

/**
 * Test the PMFProcessList() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Process_Functions#PMFProcessList.28.29
 */
class PMFProcessListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFProcessList"
     * @test
     */
    public function it_return_list_of_process()
    {
        // Create delegation
        Process::factory()->create();
        $result = PMFProcessList();
        $this->assertNotEmpty($result);
    }
}
