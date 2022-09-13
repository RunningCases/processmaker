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
     * Create notes
     *
     * @param int
     *
     * @return array
     */
    public function createCaseNotes($rows = 10)
    {
        $application = Application::factory()->create();
        $notes = AppNotes::factory($rows)->foreign_keys()->create([
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER
        ]);

        return $notes;
    }

    /**
     * Review get cases notes related to the case
     * 
     * @covers \ProcessMaker\Model\AppNotes::getNotes()
     * @test
     */
    public function it_test_get_case_notes()
    {
        // Create factories
        $cases = $this->createCaseNotes();
        // Create an instance
        $notes = new AppNotes();
        $res = $notes->getNotes($cases[0]['APP_UID']);
        $this->assertNotEmpty($res);
    }

    /**
     * Review get total cases notes by cases
     * 
     * @covers \ProcessMaker\Model\AppNotes::getTotal()
     * @covers \ProcessMaker\Model\AppNotes::scopeAppUid()
     * @test
     */
    public function it_test_get_total_case_notes()
    {
        // Create factories
        $cases = $this->createCaseNotes();
        // Create an instance
        $notes = new AppNotes();
        $total = $notes::getTotal($cases[0]['APP_UID']);
        $this->assertEquals(10, $total);
    }

    /**
     * Review get total cases notes by cases
     *
     * @covers \ProcessMaker\Model\AppNotes::total()
     * @covers \ProcessMaker\Model\AppNotes::scopeAppNumber()
     * @test
     */
    public function it_test_count_case_notes()
    {
        // Create factories
        $cases = $this->createCaseNotes();
        // Create an instance
        $notes = new AppNotes();
        $total = $notes::total($cases[0]['APP_NUMBER']);
        $this->assertEquals(10, $total);
    }
}