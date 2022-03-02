<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the generateCode() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#generateCode.28.29
 */
class GenerateCodeTest extends TestCase
{
    /**
     * This tests the "generateCode"
     * @test
     */
    public function it_get_code()
    {
        $result = generateCode();
        $this->assertNotEmpty($result);
    }
}
