<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the pmToString() function
 */
class PmToStringTest extends TestCase
{
    /**
     * This tests the "pmToString"
     * @test
     */
    public function it_get_string()
    {
        $value = 1;
        $result = pmToString($value);
        $this->assertEquals($result, '1');
    }
}
