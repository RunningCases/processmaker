<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Test the PMFAddCaseNote() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Case_Notes_Functions#PMFGetCaseNotes.28.29
 */
class PMFAddCaseNoteTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFAddCaseNote"
     * @test
     */
    public function it_add_case_notes()
    {
        // Create notes
        $table = Delegation::factory()->foreign_keys()->create();
        // Force commit for propel
        DB::commit();
        $result = PMFAddCaseNote($table->APP_UID, $table->PRO_UID, $table->TAS_UID, $table->USR_UID, 'note');
        $this->assertTrue($result >= 0);
    }
}
