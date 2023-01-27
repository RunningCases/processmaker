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
        $appDoc =Documents::factory()->case_notes()->create();
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
        $appNote =AppNotes::factory()->create();
        $appDocument =Documents::factory()->create([
            'DOC_ID' => $appNote->NOTE_ID
        ]);

        $appUid = $appDocument->APP_UID;
        $result = Documents::getAttachedFilesFromTheCaseNote($appUid);

        $this->assertNotEmpty($result);
    }

    /**
     * This test get files
     * 
     * @test
     * @covers \ProcessMaker\Model\Documents::getFiles()
     * @covers \ProcessMaker\Model\Documents::scopeDocId()
     */
    public function it_should_test_get_files()
    {
        $appNote =AppNotes::factory()->create();
        $appDocument =Documents::factory()->create([
            'DOC_ID' => $appNote->NOTE_ID
        ]);
        $result = Documents::getFiles($appDocument->DOC_ID, $appDocument->APP_UID);

        $this->assertNotEmpty($result);
    }
}