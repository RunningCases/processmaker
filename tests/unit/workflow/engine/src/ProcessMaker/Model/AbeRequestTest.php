<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\AbeConfiguration;
use ProcessMaker\Model\AbeRequest;
use ProcessMaker\Model\Application;
use Tests\TestCase;

/**
 * Class AbeRequestTest
 *
 * @coversDefaultClass \ProcessMaker\Model\AbeRequest
 */
class AbeRequestTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test has one to APP_UID
     *
     * @covers \ProcessMaker\Model\AbeRequest::application()
     * @test
     */
    public function it_has_one_application()
    {
        $table = factory(AbeRequest::class)->create([
            'APP_UID' => function () {
                return factory(Application::class)->create()->APP_UID;
            }
        ]);
        $this->assertInstanceOf(Application::class, $table->application);
    }

    /**
     * Test has one to ABE_UID
     *
     * @covers \ProcessMaker\Model\AbeRequest::abeConfiguration()
     * @test
     */
    public function it_has_one_abe_configuration()
    {
        $table = factory(AbeRequest::class)->create([
            'ABE_UID' => function () {
                return factory(AbeConfiguration::class)->create()->ABE_UID;
            }
        ]);
        $this->assertInstanceOf(AbeConfiguration::class, $table->abeConfiguration);
    }
}