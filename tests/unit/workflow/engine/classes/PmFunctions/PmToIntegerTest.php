<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the pmToInteger() function
 */
class PmToIntegerTest extends TestCase
{
    /**
     * This tests the "pmToInteger"
     * @test
     */
    public function it_get_int()
    {
        $value = '1';
        $result = pmToInteger($value);
        $this->assertEquals($result, 1);
    }
}
