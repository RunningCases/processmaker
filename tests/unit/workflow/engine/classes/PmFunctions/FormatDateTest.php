<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Exception;
use Tests\TestCase;

/**
 * Test the formatDate() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#formatDate.28.29
 */
class FormatDateTest extends TestCase
{
    /**
     * This tests the "formatDate"
     * @test
     */
    public function it_get_format_date()
    {
        $result = formatDate(date('Y-m-d'), 'yyyy-mm-dd');
        $this->assertNotEmpty($result);
    }

    /**
     * This tests the "formatDate"
     * @test
     */
    public function it_get_exceptions()
    {
        $this->expectException(Exception::class);
        $result = formatDate('');
    }
}
