<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AppNotes;
use ProcessMaker\Model\Documents;
use Tests\TestCase;

/**
 * Class DocumentsTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Documents
 */
class DocumentsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Review get app files related to the case notes
     * 
     * @test
     */
    public function it_test_get_case_note_files()
    {
        $appDoc = factory(Documents::class)->states('case_notes')->create();
        $doc = new Documents();
        $res = $doc->getAppFiles($appDoc->APP_UID, Documents::DOC_TYPE_CASE_NOTE);
        $this->assertNotEmpty($res);
    }
    
    /**
     * This test verify if exists attachment files.
     * 
     * @test
     * @covers \ProcessMaker\Model\Documents::getAttachedFilesFromTheCaseNote()
     */
    public function it_should_test_get_attached_files_from_the_casenote()
    {
        $appNote = factory(AppNotes::class)->create();
        $appDocument = factory(Documents::class)->create([
            'DOC_ID' => $appNote->NOTE_ID
        ]);

        $appUid = $appDocument->APP_UID;
        $result = Documents::getAttachedFilesFromTheCaseNote($appUid);

        $this->assertNotEmpty($result);
    }
}