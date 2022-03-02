<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the getCurrentDate() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#getCurrentDate.28.29
 */
class GetCurrentDateTest extends TestCase
{
    /**
     * This tests the "getCurrentDate"
     * @test
     */
    public function it_get_current_date()
    {
        $result = getCurrentDate();
        $this->assertNotEmpty($result);
    }
}
