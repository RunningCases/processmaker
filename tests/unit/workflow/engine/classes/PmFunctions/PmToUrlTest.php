<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the pmToUrl() function
 */
class PmToUrlTest extends TestCase
{
    /**
     * This tests the "pmToUrl"
     * @test
     */
    public function it_get_url()
    {
        $value = 'home';
        $result = pmToUrl($value);
        $this->assertEquals($result, $value);
    }
}
