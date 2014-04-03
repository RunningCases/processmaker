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
    private $calendar;
    private $numCalendar = 2;

    /**
     * Set class for test
     *
     * @coversNothing
     */
    protected function setUp()
    {
        $this->calendar = new \ProcessMaker\BusinessModel\Calendar();
    }

    /**
     * Test create calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @return array
     */
    public function testCreate()
    {
        $arrayRecord = array();

        //Create
        for ($i = 0; $i <= $this->numCalendar - 1; $i++) {
            $arrayData = array(
                "CAL_NAME"        => "PHPUnit Calendar$i",
                "CAL_DESCRIPTION" => "Description",
                "CAL_WORK_DAYS"   => array("MON", "TUE", "WED", "THU", "FRI"),
                "CAL_STATUS"      => "ACTIVE",
                "CAL_WORK_HOUR" => array(
                    array("DAY" => "ALL", "HOUR_START" => "00:00", "HOUR_END" => "00:00"),
                    array("DAY" => "MON", "HOUR_START" => "09:00", "HOUR_END" => "17:00")
                ),
                "CAL_HOLIDAY" => array(
                    array("NAME" => "holiday1", "DATE_START" => "2014-03-01", "DATE_END" => "2014-03-31"),
                    array("NAME" => "holiday2", "DATE_START" => "2014-03-01", "DATE_END" => "2014-03-31")
                )
            );

            $arrayCalendar = $this->calendar->create($arrayData);

            $this->assertTrue(is_array($arrayCalendar));
            $this->assertNotEmpty($arrayCalendar);

            $this->assertTrue(isset($arrayCalendar["CAL_UID"]));

            $arrayRecord[] = $arrayCalendar;
        }

        //Return
        return $arrayRecord;
    }

    /**
     * Test update calendars
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     */
    public function testUpdate($arrayRecord)
    {
        $arrayData = array("CAL_DESCRIPTION" => "Description...");

        $arrayCalendar = $this->calendar->update($arrayRecord[1]["CAL_UID"], $arrayData);

        $arrayCalendar = $this->calendar->getCalendar($arrayRecord[1]["CAL_UID"]);

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar["CAL_DESCRIPTION"], $arrayData["CAL_DESCRIPTION"]);
    }

    /**
     * Test get calendars
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::getCalendars
     */
    public function testGetCalendars($arrayRecord)
    {
        $arrayCalendar = $this->calendar->getCalendars();

        $this->assertNotEmpty($arrayCalendar);

        $arrayCalendar = $this->calendar->getCalendars(null, null, null, 0, 0);

        $this->assertEmpty($arrayCalendar);

        $arrayCalendar = $this->calendar->getCalendars(array("filter" => "PHPUnit"));

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar[0]["CAL_UID"], $arrayRecord[0]["CAL_UID"]);
        $this->assertEquals($arrayCalendar[0]["CAL_NAME"], $arrayRecord[0]["CAL_NAME"]);
        $this->assertEquals($arrayCalendar[0]["CAL_DESCRIPTION"], $arrayRecord[0]["CAL_DESCRIPTION"]);
        $this->assertEquals($arrayCalendar[0]["CAL_STATUS"], $arrayRecord[0]["CAL_STATUS"]);
    }

    /**
     * Test get calendar
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::getCalendar
     */
    public function testGetCalendar($arrayRecord)
    {
        $arrayCalendar = $this->calendar->getCalendar($arrayRecord[0]["CAL_UID"]);

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar["CAL_UID"], $arrayRecord[0]["CAL_UID"]);
        $this->assertEquals($arrayCalendar["CAL_NAME"], $arrayRecord[0]["CAL_NAME"]);
        $this->assertEquals($arrayCalendar["CAL_DESCRIPTION"], $arrayRecord[0]["CAL_DESCRIPTION"]);
        $this->assertEquals($arrayCalendar["CAL_STATUS"], $arrayRecord[0]["CAL_STATUS"]);
    }

    /**
     * Test delete calendars
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::delete
     */
    public function testDelete($arrayRecord)
    {
        foreach ($arrayRecord as $value) {
            $this->calendar->delete($value["CAL_UID"]);
        }

        $arrayCalendar = $this->calendar->getCalendars(array("filter" => "PHPUnit"));

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertEmpty($arrayCalendar);
    }
}

