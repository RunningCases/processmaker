<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\InputDocument;
use ProcessMaker\Model\Process;
use Tests\TestCase;

/**
 * Class InputDocumentTest
 *
 * @coversDefaultClass \ProcessMaker\Model\InputDocument
 */
class InputDocumentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test belongs to PRO_UID
     *
     * @covers \ProcessMaker\Model\InputDocument::process()
     * @test
     */
    public function it_has_a_process_defined()
    {
        $table = InputDocument::factory()->create([
            'PRO_UID' => function () {
                return Process::factory()->create()->PRO_UID;
            }
        ]);
        $this->assertInstanceOf(Process::class, $table->process);
    }

    /**
     * Test get input by process
     *
     * @covers \ProcessMaker\Model\InputDocument::getByProUid()
     * @test
     */
    public function it_get_by_process()
    {
        $table = InputDocument::factory()->create();
        $result = InputDocument::getByProUid($table->PRO_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test get input by uid
     *
     * @covers \ProcessMaker\Model\InputDocument::getByInpDocUid()
     * @test
     */
    public function it_get_by_uid()
    {
        $table = InputDocument::factory()->create();
        $result = InputDocument::getByInpDocUid($table->INP_DOC_UID);
        $this->assertNotEmpty($result);
    }
}