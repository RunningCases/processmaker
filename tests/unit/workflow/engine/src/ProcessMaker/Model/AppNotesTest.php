<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppNotes;
use Tests\TestCase;

/**
 * Class AppNotesTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AppNotes
 */
class AppNotesTest extends TestCase
{
	use DatabaseTransactions;

    /**
     * Review get cases notes related to the case
     * 
     * @test
     */
    public function it_test_get_case_notes()
    {
        $appNotes = factory(AppNotes::class)->states('foreign_keys')->create();
        $notes = new AppNotes();
        $res = $notes->getNotes($appNotes->APP_UID);
        $this->assertNotEmpty($res);
    }

    /**
     * Review get total cases notes by cases
     * 
     * @test
     */
    public function it_test_get_total_case_notes()
    {
        $application = factory(Application::class)->create();
        $appNotes = factory(AppNotes::class, 10)->states('foreign_keys')->create([
        	'APP_UID' => $application->APP_UID
        ]);
        $notes = new AppNotes();
        $total = $notes->getTotal($application->APP_UID);
        $this->assertEquals(10, $total);
    }
}