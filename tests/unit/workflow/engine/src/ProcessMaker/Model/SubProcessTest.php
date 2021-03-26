<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\SubProcess;
use Tests\TestCase;

/**
 * Class ProcessTest
 *
 * @coversDefaultClass \ProcessMaker\Model\SubProcess
 */
class SubProcessTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Call the setUp parent method
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * It should test the getProParents() method
     * 
     * @covers \ProcessMaker\Model\SubProcess::getProParents()
     * @test
     */
    public function it_should_test_the_get_pro_parents_method()
    {
        $process = factory(Process::class)->create();
        $processParent = factory(Process::class, 3)->create();
        factory(SubProcess::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'PRO_PARENT' => $processParent[0]['PRO_UID']
        ]);
        factory(SubProcess::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'PRO_PARENT' => $processParent[1]['PRO_UID']
        ]);
        factory(SubProcess::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'PRO_PARENT' => $processParent[2]['PRO_UID']
        ]);

        $res = SubProcess::getProParents($process['PRO_UID']);

        $res = array_map(function ($x) {
            return $x['PRO_PARENT'];
        }, $res);

        // Assert the subprocess has three parents
        $this->assertCount(3, $res);

        // Assert that the parents are the processes created
        $this->assertContains($processParent[0]['PRO_UID'], $res);
        $this->assertContains($processParent[1]['PRO_UID'], $res);
        $this->assertContains($processParent[2]['PRO_UID'], $res);
    }
}
