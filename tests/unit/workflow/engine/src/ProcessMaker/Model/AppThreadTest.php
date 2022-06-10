<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AppThread;
use Tests\TestCase;

/**
 * Class AppThreadTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AppThread
 */
class AppThreadTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Set up function.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * This test scopeAppUid
     *
     * @covers \ProcessMaker\Model\AppThread::scopeAppUid()
     * @test
     */
    public function it_return_scope_app_uid()
    {
        $table = factory(AppThread::class)->create();
        $this->assertCount(1, $table->appUid($table->APP_UID)->get());
    }

    /**
     * This test scopeIndex
     *
     * @covers \ProcessMaker\Model\AppThread::scopeIndex()
     * @test
     */
    public function it_return_scope_index()
    {
        $table = factory(AppThread::class)->create();
        $this->assertCount(1, $table->index($table->DEL_INDEX)->get());
    }

    /**
     * This test getThread
     *
     * @covers \ProcessMaker\Model\AppThread::getThread()
     * @covers \ProcessMaker\Model\AppThread::scopeAppUid()
     * @covers \ProcessMaker\Model\AppThread::scopeIndex()
     * @test
     */
    public function it_return_thread()
    {
        $table = factory(AppThread::class)->create();
        $result = AppThread::getThread($table->APP_UID, $table->DEL_INDEX);
        $this->assertNotEmpty($result);
    }
}