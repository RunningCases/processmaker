<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the getCurrentTime() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#getCurrentTime.28.29
 */
class GetCurrentTimeTest extends TestCase
{
    /**
     * This tests the "getCurrentTime"
     * @test
     */
    public function it_get_current_date()
    {
        $result = getCurrentTime();
        $this->assertNotEmpty($result);
    }
}
