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
    protected static $calendar;
    protected static $numCalendar = 2;

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        self::$calendar = new \ProcessMaker\BusinessModel\Calendar();
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
        for ($i = 0; $i <= self::$numCalendar - 1; $i++) {
            $arrayData = array(
                "CAL_NAME"        => "PHPUnit Calendar$i",
                "CAL_DESCRIPTION" => "Description",
                "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5),
                "CAL_WORK_HOUR" => array(
                    array("DAY" => 0, "HOUR_START" => "00:00", "HOUR_END" => "00:00"),
                    array("DAY" => 1, "HOUR_START" => "09:00", "HOUR_END" => "17:00")
                ),
                "CAL_HOLIDAY" => array(
                    array("NAME" => "holiday1", "DATE_START" => "2014-03-01", "DATE_END" => "2014-03-31"),
                    array("NAME" => "holiday2", "DATE_START" => "2014-03-01", "DATE_END" => "2014-03-31")
                )
            );

            $arrayCalendar = self::$calendar->create($arrayData);

            $this->assertTrue(is_array($arrayCalendar));
            $this->assertNotEmpty($arrayCalendar);

            $this->assertTrue(isset($arrayCalendar["CAL_UID"]));

            $arrayRecord[] = $arrayCalendar;
        }

        //Create - Japanese characters
        $arrayData = array(
            "CAL_NAME"        => "私の名前（PHPUnitの）",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5)
        );

        $arrayCalendar = self::$calendar->create($arrayData);

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertTrue(isset($arrayCalendar["CAL_UID"]));

        $arrayRecord[] = $arrayCalendar;

        //Return
        return $arrayRecord;
    }

    /**
     * Test update calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     */
    public function testUpdate($arrayRecord)
    {
        $arrayData = array("CAL_DESCRIPTION" => "Description...");

        $arrayCalendar = self::$calendar->update($arrayRecord[1]["CAL_UID"], $arrayData);

        $arrayCalendar = self::$calendar->getCalendar($arrayRecord[1]["CAL_UID"]);

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar["CAL_DESCRIPTION"], $arrayData["CAL_DESCRIPTION"]);
    }

    /**
     * Test get calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::getCalendars
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     */
    public function testGetCalendars($arrayRecord)
    {
        $arrayCalendar = self::$calendar->getCalendars();

        $this->assertNotEmpty($arrayCalendar);

        $arrayCalendar = self::$calendar->getCalendars(null, null, null, 0, 0);

        $this->assertEmpty($arrayCalendar);

        $arrayCalendar = self::$calendar->getCalendars(array("filter" => "PHPUnit"));

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar[0]["CAL_UID"], $arrayRecord[0]["CAL_UID"]);
        $this->assertEquals($arrayCalendar[0]["CAL_NAME"], $arrayRecord[0]["CAL_NAME"]);
        $this->assertEquals($arrayCalendar[0]["CAL_DESCRIPTION"], $arrayRecord[0]["CAL_DESCRIPTION"]);
    }

    /**
     * Test get calendar
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::getCalendar
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     */
    public function testGetCalendar($arrayRecord)
    {
        //Get
        $arrayCalendar = self::$calendar->getCalendar($arrayRecord[0]["CAL_UID"]);

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar["CAL_UID"], $arrayRecord[0]["CAL_UID"]);
        $this->assertEquals($arrayCalendar["CAL_NAME"], $arrayRecord[0]["CAL_NAME"]);
        $this->assertEquals($arrayCalendar["CAL_DESCRIPTION"], $arrayRecord[0]["CAL_DESCRIPTION"]);

        //Get - Japanese characters
        $arrayCalendar = self::$calendar->getCalendar($arrayRecord[self::$numCalendar]["CAL_UID"]);

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertNotEmpty($arrayCalendar);

        $this->assertEquals($arrayCalendar["CAL_UID"], $arrayRecord[self::$numCalendar]["CAL_UID"]);
        $this->assertEquals($arrayCalendar["CAL_NAME"], "私の名前（PHPUnitの）");
        $this->assertEquals($arrayCalendar["CAL_DESCRIPTION"], "Description");
    }

    /**
     * Test exception when data not is array
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", this value must be an array.
     */
    public function testCreateExceptionNoIsArrayData()
    {
        $arrayData = 0;

        $arrayCalendar = self::$calendar->create($arrayData);
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testCreateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayCalendar = self::$calendar->create($arrayData);
    }

    /**
     * Test exception for required data (CAL_NAME)
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Undefined value for "CAL_NAME", it is required.
     */
    public function testCreateExceptionRequiredDataCalName()
    {
        $arrayData = array(
            //"CAL_NAME"        => "PHPUnit Calendar",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5)
        );

        $arrayCalendar = self::$calendar->create($arrayData);
    }

    /**
     * Test exception for invalid data (CAL_NAME)
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "CAL_NAME", it can not be empty.
     */
    public function testCreateExceptionInvalidDataCalName()
    {
        $arrayData = array(
            "CAL_NAME"        => "",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5)
        );

        $arrayCalendar = self::$calendar->create($arrayData);
    }

    /**
     * Test exception for invalid data (CAL_WORK_DAYS)
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "CAL_WORK_DAYS", it only accepts values: "1|2|3|4|5|6|7".
     */
    public function testCreateExceptionInvalidDataCalWorkDays()
    {
        $arrayData = array(
            "CAL_NAME"        => "PHPUnit Calendar",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(10, 2, 3, 4, 5)
        );

        $arrayCalendar = self::$calendar->create($arrayData);
    }

    /**
     * Test exception for calendar name existing
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The calendar name with CAL_NAME: "PHPUnit Calendar0" already exists.
     */
    public function testCreateExceptionExistsCalName()
    {
        $arrayData = array(
            "CAL_NAME"        => "PHPUnit Calendar0",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5),
        );

        $arrayCalendar = self::$calendar->create($arrayData);
    }

    /**
     * Test exception when data not is array
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", this value must be an array.
     */
    public function testUpdateExceptionNoIsArrayData()
    {
        $arrayData = 0;

        $arrayCalendar = self::$calendar->update("", $arrayData);
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testUpdateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayCalendar = self::$calendar->update("", $arrayData);
    }

    /**
     * Test exception for invalid calendar UID
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The calendar with CAL_UID: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx does not exist.
     */
    public function testUpdateExceptionInvalidCalUid()
    {
        $arrayData = array(
            "CAL_NAME"        => "PHPUnit Calendar",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5),
        );

        $arrayCalendar = self::$calendar->update("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $arrayData);
    }

    /**
     * Test exception for invalid data (CAL_NAME)
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "CAL_NAME", it can not be empty.
     */
    public function testUpdateExceptionInvalidDataCalName($arrayRecord)
    {
        $arrayData = array(
            "CAL_NAME"        => "",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(1, 2, 3, 4, 5),
        );

        $arrayCalendar = self::$calendar->update($arrayRecord[0]["CAL_UID"], $arrayData);
    }

    /**
     * Test exception for invalid data (CAL_WORK_DAYS)
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "CAL_WORK_DAYS", it only accepts values: "1|2|3|4|5|6|7".
     */
    public function testUpdateExceptionInvalidDataCalWorkDays($arrayRecord)
    {
        $arrayData = array(
            "CAL_NAME"        => "PHPUnit Calendar",
            "CAL_DESCRIPTION" => "Description",
            "CAL_WORK_DAYS"   => array(10, 2, 3, 4, 5),
        );

        $arrayCalendar = self::$calendar->update($arrayRecord[0]["CAL_UID"], $arrayData);
    }

    /**
     * Test exception for calendar name existing
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The calendar name with CAL_NAME: "PHPUnit Calendar1" already exists.
     */
    public function testUpdateExceptionExistsCalName($arrayRecord)
    {
        $arrayData = $arrayRecord[1];

        $arrayCalendar = self::$calendar->update($arrayRecord[0]["CAL_UID"], $arrayData);
    }

    /**
     * Test delete calendars
     *
     * @covers \ProcessMaker\BusinessModel\Calendar::delete
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the calendars
     */
    public function testDelete($arrayRecord)
    {
        foreach ($arrayRecord as $value) {
            self::$calendar->delete($value["CAL_UID"]);
        }

        $arrayCalendar = self::$calendar->getCalendars(array("filter" => "PHPUnit"));

        $this->assertTrue(is_array($arrayCalendar));
        $this->assertEmpty($arrayCalendar);
    }
}

