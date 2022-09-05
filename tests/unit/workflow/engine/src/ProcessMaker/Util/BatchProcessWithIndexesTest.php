<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Util;

use ProcessMaker\Util\BatchProcessWithIndexes;
use Tests\TestCase;

class BatchProcessWithIndexesTest extends TestCase
{
    /**
     * Testing object.
     * @var BatchProcessWithIndexesTest
     */
    protected $batchProcessWithIndexes = null;

    /**
     * Set up method.
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear down method.
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * This test the setLimit() method.
     * @test
     * @covers \ProcessMaker\Util\BatchProcessWithIndexes::setLimit()
     */
    public function it_should_test_setLimit_method()
    {
        $this->batchProcessWithIndexes = new BatchProcessWithIndexes(1);
        $result = $this->batchProcessWithIndexes->setLimit(1);

        $this->assertInstanceOf(BatchProcessWithIndexes::class, $result);
    }

    /**
     * This test the process() method.
     * @test
     * @covers \ProcessMaker\Util\BatchProcessWithIndexes::process()
     */
    public function it_should_test_process_method()
    {
        //for 10 elements will be expect the next indexes
        $expected = [
            [0, 2], [2, 2], [4, 2], [6, 2], [8, 2]
        ];

        $size = 10;
        $limit = 2;
        $result = [];
        $this->batchProcessWithIndexes = new BatchProcessWithIndexes($size);
        $this->batchProcessWithIndexes->setLimit($limit);
        $this->batchProcessWithIndexes->process(function ($start, $limit) use (&$result) {
            $result[] = [$start, $limit];
        });

        $this->assertEquals(count($expected), count($result));
        $this->assertEquals(json_encode($expected), json_encode($result));

        //for 17 elements will be expect the next indexes
        $expected = [
            [0, 3], [3, 3], [6, 3], [9, 3], [12, 3], [15, 3]
        ];

        $size = 17;
        $limit = 3;
        $result = [];
        $this->batchProcessWithIndexes = new BatchProcessWithIndexes($size);
        $this->batchProcessWithIndexes->setLimit($limit);
        $this->batchProcessWithIndexes->process(function ($start, $limit) use (&$result) {
            $result[] = [$start, $limit];
        });

        $this->assertEquals(count($expected), count($result));
        $this->assertEquals(json_encode($expected), json_encode($result));
    }
}
