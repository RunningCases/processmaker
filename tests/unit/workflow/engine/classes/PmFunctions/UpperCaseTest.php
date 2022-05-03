<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the upperCase() function
 */
class UpperCaseTest extends TestCase
{
    /**
     * This tests the "upperCase"
     * @test
     */
    public function it_get_upper_case()
    {
        $result = upperCase('test');
        $this->assertNotEmpty($result);
    }
}
