<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use ProcessMaker\Model\AppDelay;
use Tests\TestCase;

/**
 * Class AppDelayTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AppDelay
 */
class AppDelayTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * Set up function.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * This test scopeType
     *
     * @covers \ProcessMaker\Model\AppDelay::scopeType()
     * @test
     */
    public function it_return_scope_type()
    {
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $this->assertCount(1, $table->type('PAUSE')->get());
    }

    /**
     * This test scopeNotDisabled
     *
     * @covers \ProcessMaker\Model\AppDelay::scopeNotDisabled()
     * @test
     */
    public function it_return_scope_not_action_disable()
    {
        AppDelay::truncate();
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $this->assertCount(1, $table->notDisabled()->get());
    }

    /**
     * This test scopeCase
     *
     * @covers \ProcessMaker\Model\AppDelay::scopeCase()
     * @test
     */
    public function it_return_scope_case()
    {
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $this->assertCount(1, $table->case($table->APP_NUMBER)->get());
    }

    /**
     * This test scopeIndex
     *
     * @covers \ProcessMaker\Model\AppDelay::scopeIndex()
     * @test
     */
    public function it_return_scope_index()
    {
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $this->assertCount(1, $table->index($table->APP_DEL_INDEX)->get());
    }

    /**
     * This test scopeDelegateUser
     *
     * @covers \ProcessMaker\Model\AppDelay::scopeDelegateUser()
     * @test
     */
    public function it_return_scope_delegate_user()
    {
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $this->assertCount(1, $table->delegateUser($table->APP_DELEGATION_USER)->get());
    }

    /**
     * This test getPaused
     *
     * @covers \ProcessMaker\Model\AppDelay::getPaused()
     * @covers \ProcessMaker\Model\AppDelay::scopeCase()
     * @covers \ProcessMaker\Model\AppDelay::scopeIndex()
     * @covers \ProcessMaker\Model\AppDelay::scopeDelegateUser()
     * @test
     */
    public function it_return_paused_threads()
    {
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $result = AppDelay::getPaused($table->APP_NUMBER, $table->APP_DEL_INDEX, $table->APP_DELEGATION_USER);
        $this->assertNotEmpty($result);
    }
}