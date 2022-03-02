<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Exception;
use Tests\TestCase;

/**
 * Test the literalDate() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#literalDate.28.29
 */
class LiteralDateTest extends TestCase
{
    /**
     * This tests the "literalDate"
     * @test
     */
    public function it_get_literal_date()
    {
        $result = literalDate(date('Y-m-d'), 'en');
        $this->assertNotEmpty($result);
        $result = literalDate(date('Y-m-d'), 'es');
        $this->assertNotEmpty($result);
    }

    /**
     * This tests the "literalDate"
     * @test
     */
    public function it_get_exceptions()
    {
        $this->expectException(Exception::class);
        $result = literalDate('');
    }
}
