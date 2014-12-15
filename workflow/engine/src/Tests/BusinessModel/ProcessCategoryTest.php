<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    require_once(__DIR__ . "/../bootstrap.php");
}

/**
 * Class ProcessCategoryTest
 *
 * @package Tests\BusinessModel
 */
class ProcessCategoryTest extends \PHPUnit_Framework_TestCase
{
    protected static $category;
    protected static $numCategory = 2;

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        self::$category = new \ProcessMaker\BusinessModel\ProcessCategory();
    }

    /**
     * Test create categories
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::create
     *
     * @return array
     */
    public function testCreate()
    {
        $arrayRecord = array();

        //Create
        for ($i = 0; $i <= self::$numCategory - 1; $i++) {
            $arrayData = array(
                "CAT_NAME" => "PHPUnit My Category " . $i
            );

            $arrayCategory = self::$category->create($arrayData);

            $this->assertTrue(is_array($arrayCategory));
            $this->assertNotEmpty($arrayCategory);

            $this->assertTrue(isset($arrayCategory["CAT_UID"]));

            $arrayRecord[] = $arrayCategory;
        }

        //Create - Japanese characters
        $arrayData = array(
            "CAT_NAME" => "テスト（PHPUnitの）",
        );

        $arrayCategory = self::$category->create($arrayData);

        $this->assertTrue(is_array($arrayCategory));
        $this->assertNotEmpty($arrayCategory);

        $this->assertTrue(isset($arrayCategory["CAT_UID"]));

        $arrayRecord[] = $arrayCategory;

        //Return
        return $arrayRecord;
    }

    /**
     * Test update categories
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the categories
     */
    public function testUpdate(array $arrayRecord)
    {
        $arrayData = array("CAT_NAME" => "PHPUnit My Category 1...");

        $arrayCategory = self::$category->update($arrayRecord[1]["CAT_UID"], $arrayData);

        $arrayCategory = self::$category->getCategory($arrayRecord[1]["CAT_UID"]);

        $this->assertTrue(is_array($arrayCategory));
        $this->assertNotEmpty($arrayCategory);

        $this->assertEquals($arrayCategory["CAT_NAME"], $arrayData["CAT_NAME"]);
    }

    /**
     * Test get categories
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::getCategories
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the categories
     */
    public function testGetCategories(array $arrayRecord)
    {
        $arrayCategory = self::$category->getCategories();

        $this->assertNotEmpty($arrayCategory);

        $arrayCategory = self::$category->getCategories(null, null, null, 0, 0);

        $this->assertEmpty($arrayCategory);

        $arrayCategory = self::$category->getCategories(array("filter" => "PHPUnit"));

        $this->assertTrue(is_array($arrayCategory));
        $this->assertNotEmpty($arrayCategory);

        $this->assertEquals($arrayCategory[0]["CAT_UID"],  $arrayRecord[0]["CAT_UID"]);
        $this->assertEquals($arrayCategory[0]["CAT_NAME"], $arrayRecord[0]["CAT_NAME"]);
    }

    /**
     * Test get category
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::getCategory
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the categories
     */
    public function testGetCategory(array $arrayRecord)
    {
        //Get
        $arrayCategory = self::$category->getCategory($arrayRecord[0]["CAT_UID"]);

        $this->assertTrue(is_array($arrayCategory));
        $this->assertNotEmpty($arrayCategory);

        $this->assertEquals($arrayCategory["CAT_UID"],  $arrayRecord[0]["CAT_UID"]);
        $this->assertEquals($arrayCategory["CAT_NAME"], $arrayRecord[0]["CAT_NAME"]);

        //Get - Japanese characters
        $arrayCategory = self::$category->getCategory($arrayRecord[self::$numCategory]["CAT_UID"]);

        $this->assertTrue(is_array($arrayCategory));
        $this->assertNotEmpty($arrayCategory);

        $this->assertEquals($arrayCategory["CAT_UID"],  $arrayRecord[self::$numCategory]["CAT_UID"]);
        $this->assertEquals($arrayCategory["CAT_NAME"], "テスト（PHPUnitの）");
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testCreateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayCategory = self::$category->create($arrayData);
    }

    /**
     * Test exception for required data (CAT_NAME)
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Undefined value for "CAT_NAME", it is required.
     */
    public function testCreateExceptionRequiredDataCatName()
    {
        $arrayData = array(
            "CAT_NAMEX" => "PHPUnit My Category N"
        );

        $arrayCategory = self::$category->create($arrayData);
    }

    /**
     * Test exception for invalid data (CAT_NAME)
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "CAT_NAME", it can not be empty.
     */
    public function testCreateExceptionInvalidDataCatName()
    {
        $arrayData = array(
            "CAT_NAME" => ""
        );

        $arrayCategory = self::$category->create($arrayData);
    }

    /**
     * Test exception for category name existing
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The category name with CAT_NAME: "PHPUnit My Category 0" already exists.
     */
    public function testCreateExceptionExistsCatName()
    {
        $arrayData = array(
            "CAT_NAME" => "PHPUnit My Category 0"
        );

        $arrayCategory = self::$category->create($arrayData);
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testUpdateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayCategory = self::$category->update("", $arrayData);
    }

    /**
     * Test exception for invalid category UID
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The category with CAT_UID: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' does not exist.
     */
    public function testUpdateExceptionInvalidCatUid()
    {
        $arrayData = array(
            "CAT_NAME" => "PHPUnit My Category N"
        );

        $arrayCategory = self::$category->update("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $arrayData);
    }

    /**
     * Test exception for invalid data (CAT_NAME)
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the categories
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "CAT_NAME", it can not be empty.
     */
    public function testUpdateExceptionInvalidDataCatName(array $arrayRecord)
    {
        $arrayData = array(
            "CAT_NAME" => ""
        );

        $arrayCategory = self::$category->update($arrayRecord[0]["CAT_UID"], $arrayData);
    }

    /**
     * Test exception for category name existing
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the categories
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The category name with CAT_NAME: "PHPUnit My Category 0" already exists.
     */
    public function testUpdateExceptionExistsCatName(array $arrayRecord)
    {
        $arrayData = $arrayRecord[0];

        $arrayCategory = self::$category->update($arrayRecord[1]["CAT_UID"], $arrayData);
    }

    /**
     * Test delete categories
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::delete
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the categories
     */
    public function testDelete(array $arrayRecord)
    {
        foreach ($arrayRecord as $value) {
            self::$category->delete($value["CAT_UID"]);
        }

        $arrayCategory = self::$category->getCategories(array("filter" => "PHPUnit"));

        $this->assertTrue(is_array($arrayCategory));
        $this->assertEmpty($arrayCategory);
    }

    /**
     * Test exception for invalid category UID
     *
     * @covers \ProcessMaker\BusinessModel\ProcessCategory::delete
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The category with CAT_UID: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' does not exist.
     */
    public function testDeleteExceptionInvalidCatUid()
    {
        self::$category->delete("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
    }
}

