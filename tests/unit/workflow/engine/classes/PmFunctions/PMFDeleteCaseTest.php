<?php
namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Triggers;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Test the PMFDeleteCase() function
 *
 * @link https://wiki.processmaker.com/3.7/ProcessMaker_Functions/Case_Functions#PMFDeleteCase.28.29
 */
class PMFDeleteCaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the PMFDeleteCaseTest() function with the default parameters
     * 
     * @test
     */
    public function it_should_test_this_pmfunction_default_parameters()
    {
        $this->expectException(Exception::class);
        $table = Delegation::factory()->foreign_keys()->create();
        Triggers::factory()->create([
            'PRO_UID' => $table->PRO_UID
        ]);
        // Force commit for propel
        DB::commit();
        $result = PMFDeleteCase($table->APP_UID);
        $this->assertEquals(0, $result);
    }
}