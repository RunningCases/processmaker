<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class CalendarTest
 *
 * @package Tests\BusinessModel
 */
class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCalendars()
    {
        $calendar = new \BusinessModel\Calendar();

        $arrayCalendar = $calendar->getCalendars();

        $this->assertNotEmpty($arrayCalendar);

        $arrayCalendar = $calendar->getCalendars(null, null, null, 0, 0);

        $this->assertEmpty($arrayCalendar);

        $arrayCalendar = $calendar->getCalendars(array("filter" => "Default"));

        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar[0]["CAL_UID"], "00000000000000000000000000000001");
        $this->assertEquals($arrayCalendar[0]["CAL_NAME"], "Default");
        $this->assertEquals($arrayCalendar[0]["CAL_DESCRIPTION"], "Default");
        $this->assertEquals($arrayCalendar[0]["CAL_STATUS"], "ACTIVE");
    }
}

