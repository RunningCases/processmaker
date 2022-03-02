<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the pmToFloat() function
 */
class PmToFloatTest extends TestCase
{
    /**
     * This tests the "pmToFloat"
     * @test
     */
    public function it_get_float()
    {
        $value = '1.2';
        $result = pmToFloat($value);
        $this->assertEquals($result, 1.2);
    }
}
