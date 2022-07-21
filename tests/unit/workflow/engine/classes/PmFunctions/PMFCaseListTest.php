<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\AppThread;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Test the PMFCaseList() function
 *
 * @link https://wiki.processmaker.com/3.7/ProcessMaker_Functions/Case_Functions#PMFCaseList.28.29
 */
class PMFCaseListTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFCaseList"
     * @test
     */
    public function it_return_list_of_cases()
    {
        // Create delegation
        $table = Delegation::factory()->foreign_keys()->create();
        AppThread::factory()->create([
            'APP_THREAD_STATUS' => 'OPEN',
            'APP_UID' => $table->APP_UID
        ]);
        DB::commit();
        $result = PMFCaseList($table->USR_UID);
        $this->assertNotEmpty($result);
    }
}
