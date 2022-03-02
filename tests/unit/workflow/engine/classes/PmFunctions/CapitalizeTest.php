<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the capitalize() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#capitalize.28.29
 */
class CapitalizeTest extends TestCase
{
    /**
     * This tests the "capitalize"
     * @test
     */
    public function it_get_lower_case()
    {
        $result = capitalize('test');
        $this->assertNotEmpty($result);
    }
}
