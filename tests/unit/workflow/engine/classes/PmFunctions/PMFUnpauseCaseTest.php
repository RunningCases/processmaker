<?php
namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Test the PMFUnpauseCase() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#PMFUnpauseCase.28.29
 */
class PMFUnpauseCaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * It tests the PMFUnpauseCaseTest() function with the default parameters
     * 
     * @test
     */
    public function it_should_test_this_pmfunction_default_parameters()
    {
        $this->expectException(Exception::class);
        $table = factory(Delegation::class)->states('foreign_keys')->create();
        // Force commit for propel
        DB::commit();
        $result = PMFUnpauseCase($table->APP_UID, $table->DEL_INDEX, $table->USR_UID);
        $this->assertEquals(0, $result);
    }
}