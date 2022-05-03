<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the PMFRemoveMask() function
 */
class PMFRemoveMaskTest extends TestCase
{
    /**
     * This tests the "PMFRemoveMask"
     * @test
     */
    public function it_get_literal_date()
    {
        $result = PMFRemoveMask('35.5');
        $this->assertNotEmpty($result);
    }
}
