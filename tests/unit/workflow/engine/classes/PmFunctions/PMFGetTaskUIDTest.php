<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Task;
use Tests\TestCase;

/**
 * Test the PMFGetTaskUID() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFGetTaskUID.28.29
 */
class PMFGetTaskUIDTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetTaskUID"
     * @test
     */
    public function it_return_task_uid()
    {
        // Create task
        $table = Task::factory()->foreign_keys()->create();
        DB::commit();
        $result = PMFGetTaskUID($table->TAS_TITLE);
        $this->assertFalse($result);
        // When is empty
        $result = PMFGetTaskUID($table->TAS_TITLE, $table->PRO_UID);
        $this->assertNotEmpty($result);
    }
}
