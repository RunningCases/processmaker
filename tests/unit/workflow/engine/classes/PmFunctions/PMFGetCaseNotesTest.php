<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\AppNotes;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Test the PMFGetCaseNotes() function
 *
 * @link https://wiki.processmaker.com/3.2/ProcessMaker_Functions/Case_Notes_Functions#PMFGetCaseNotes.28.29
 */
class PMFGetCaseNotesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * This tests if the "PMFGetCaseNotes"
     * @test
     */
    public function it_get_case_notes()
    {
        // Create notes
        $user = User::factory()->create();
        $table = AppNotes::factory()->create([
            'USR_UID' => $user->USR_UID
        ]);
        // Force commit for propel
        DB::commit();
        $result = PMFGetCaseNotes($table->APP_UID, 'array');
        $this->assertNotEmpty($result);
        $result = PMFGetCaseNotes($table->APP_UID, 'object');
        $this->assertNotEmpty($result);
        $result = PMFGetCaseNotes($table->APP_UID, 'string');
        $this->assertNotEmpty($result);
    }
}
