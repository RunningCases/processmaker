<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class GetDiffBetweenDatesTest extends TestCase
{
    /**
     * Check if get the differences between dates
     *
     * @test
     */
    public function it_should_get_difference_between_dates()
    {
        $date1 = date("2020-11-12 09:09:10");
        $date2 = date("2020-11-15 09:09:10");
        $this->assertNotEmpty(getDiffBetweenDates($date1, $date2));
    }
}