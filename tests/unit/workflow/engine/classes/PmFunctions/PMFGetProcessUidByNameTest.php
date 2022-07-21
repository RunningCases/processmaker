<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Process;
use Tests\TestCase;

/**
 * Test the PMFGetProcessUidByName() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Process_Functions#PMFGetProcessUidByName.28.29
 */
class PMFGetProcessUidByNameTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetProcessUidByName"
     * @test
     */
    public function it_return_process()
    {
        // Create process
        $table = Process::factory()->create();
        DB::commit();
        $result = PMFGetProcessUidByName($table->PRO_TITLE);
        $this->assertNotEmpty($result);
        // When a process was defined in the session
        $result = PMFGetProcessUidByName('');
        $this->assertNotEmpty($result);
        // When does not defined the session
        $_SESSION['PROCESS'] = '';
        $result = PMFGetProcessUidByName('');
        $this->assertEmpty($result);
    }
}
