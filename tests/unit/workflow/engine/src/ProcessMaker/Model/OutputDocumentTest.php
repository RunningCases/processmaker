<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\OutputDocument;
use ProcessMaker\Model\Process;
use Tests\TestCase;

/**
 * Class OutputDocumentTest
 *
 * @coversDefaultClass \ProcessMaker\Model\OutputDocument
 */
class OutputDocumentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test belongs to PRO_UID
     *
     * @covers \ProcessMaker\Model\OutputDocument::process()
     * @test
     */
    public function it_has_a_process_defined()
    {
        $table = factory(OutputDocument::class)->create([
            'PRO_UID' => function () {
                return factory(Process::class)->create()->PRO_UID;
            }
        ]);
        $this->assertInstanceOf(Process::class, $table->process);
    }

    /**
     * Test get output by process
     *
     * @covers \ProcessMaker\Model\OutputDocument::getByProUid()
     * @test
     */
    public function it_get_by_process()
    {
        $table = factory(OutputDocument::class)->create();
        $result = OutputDocument::getByProUid($table->PRO_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test get output by uid
     *
     * @covers \ProcessMaker\Model\OutputDocument::getByOutDocUid()
     * @test
     */
    public function it_get_by_uid()
    {
        $table = factory(OutputDocument::class)->create();
        $result = OutputDocument::getByOutDocUid($table->OUT_DOC_UID);
        $this->assertNotEmpty($result);
    }
}