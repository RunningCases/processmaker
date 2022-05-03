<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the lowerCase() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#lowerCase.28.29
 */
class LowerCaseTest extends TestCase
{
    /**
     * This tests the "lowerCase"
     * @test
     */
    public function it_get_lower_case()
    {
        $result = lowerCase('TEST');
        $this->assertNotEmpty($result);
    }
}
