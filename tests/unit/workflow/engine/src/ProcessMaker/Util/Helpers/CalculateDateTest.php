<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class CalculateDateTest extends TestCase
{
    /**
     * It tests the addition of days
     *
     * @test
     */
    public function it_test_adding_days()
    {
        $iniDate = '2019-10-04 12:07:40';
        $newDate = calculateDate($iniDate, 'DAYS', 1);
        $this->assertEquals('2019-10-05 12:07:40', $newDate);
    }

    /**
     * It tests the addition of hours
     *
     * @test
     */
    public function it_test_adding_hours()
    {
        $iniDate = '2019-10-04 12:07:40';
        $newDate = calculateDate($iniDate, 'HOURS', 1);
        $this->assertEquals('2019-10-04 13:07:40', $newDate);
    }

    /**
     * It tests the addition of minutes
     *
     * @test
     */
    public function it_test_adding_minutes()
    {
        $iniDate = '2019-10-04 12:07:40';
        $newDate = calculateDate($iniDate, 'MINUTES', 10);
        $this->assertEquals('2019-10-04 12:17:40', $newDate);
    }
}
