<?php
namespace Tests\unit\workflow\engine\classes\PmFunctions;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Test the PMFNewCase() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFNewCase.28.29
 */
class PMFNewCaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the PMFNewCaseTest() function with the default parameters
     * 
     * @test
     */
    public function it_should_test_this_pmfunction_default_parameters()
    {
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        // Force commit for propel
        DB::commit();
        $result = PMFNewCase($table->PRO_UID, $table->USR_UID, $table->TAS_UID, [], null);
        $this->assertEquals(0, $result);
    }
}