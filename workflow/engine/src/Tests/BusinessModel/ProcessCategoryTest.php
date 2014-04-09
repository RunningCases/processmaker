<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class ProcessCategoryTest
 *
 * @package Tests/ProcessMaker/BusinessModel
 */
class ProcessCategoryTest extends \PHPUnit_Framework_TestCase
{
    protected static $arrayUid = array();
    protected $oCategory;

    /**
     * Set class for test
     *
     * @coversNothing
     *
     * @copyright Colosa - Bolivia
     */
    public function setUp()
    {
        $this->oCategory = new \ProcessMaker\BusinessModel\ProcessCategory();
    }


    public static function tearDownAfterClass()
    {
        foreach (self::$arrayUid as $processCategoryUid) {
            if ($processCategoryUid != "") {
                $processCategory = new \ProcessCategory();

                $processCategory->setCategoryUid($processCategoryUid);
                $processCategory->delete();
            }
        }
    }

    /**
     * Test error for incorrect value of category name in array
     *
     * @covers \BusinessModel\ProcessCategory::addCategory
     * @expectedException        Exception
     * @expectedExceptionMessage cat_name. Process Category name can't be null
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCategoryErrorIncorrectValue()
    {
        $this->oCategory->addCategory('');
    }

    /**
     * Test add Category
     *
     * @covers \BusinessModel\ProcessCategory::addCategory
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCategory()
    {
        $response = $this->oCategory->addCategory('New Category Test');
        $this->assertTrue(is_object($response));
        $aResponse = json_decode(json_encode($response), true);
        return $aResponse;
    }

    /**
     * Test error for incorrect value of category name in array
     *
     * @covers \BusinessModel\ProcessCategory::addCategory
     * @expectedException        Exception
     * @expectedExceptionMessage cat_name. Duplicate Process Category name
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCategoryErrorDuplicateValue()
    {
        $this->oCategory->addCategory('New Category Test');
    }

    /**
     * Test error for incorrect value of category name in array
     *
     * @covers \BusinessModel\ProcessCategory::updateCategory
     * @expectedException        Exception
     * @expectedExceptionMessage cat_name. Duplicate Process Category name
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateCategoryErrorDuplicateValue()
    {
        $this->oCategory->addCategory('New Category Test');
    }

    /**
     * Test put Category
     *
     * @covers \BusinessModel\ProcessCategory::updateCategory
     * @depends testAddCategory
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateCategory(array $aResponse)
    {
        $response = $this->oCategory->updateCategory($aResponse["cat_uid"], 'Name Update Category Test');
        $this->assertTrue(is_object($response));
    }

    /**
     * Test error for incorrect value of category id
     *
     * @covers \BusinessModel\ProcessCategory::getCategory
     * @expectedException        Exception
     * @expectedExceptionMessage The Category with cat_uid: 12345678912345678912345678912345678 doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetErrorValue()
    {
        $this->oCategory->getCategory('12345678912345678912345678912345678');
    }

    /**
     * Test get Category
     *
     * @covers \BusinessModel\ProcessCategory::getCategory
     * @depends testAddCategory
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCategory(array $aResponse)
    {
        $response = $this->oCategory->getCategory($aResponse["cat_uid"]);
        $this->assertTrue(is_object($response));
    }

    /**
     * Test error for incorrect value of category id
     *
     * @covers \BusinessModel\ProcessCategory::deleteCategory
     * @expectedException        Exception
     * @expectedExceptionMessage The Category with cat_uid: 12345678912345678912345678912345678 doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testDeleteErrorValue()
    {
        $this->oCategory->deleteCategory('12345678912345678912345678912345678');
    }

    /**
     * Test delete Category
     *
     * @covers \BusinessModel\ProcessCategory::deleteCategory
     * @depends testAddCategory
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testDeleteCategory(array $aResponse)
    {
        $response = $this->oCategory->deleteCategory($aResponse["cat_uid"]);
        $this->assertTrue(empty($response));
    }

    public function testCreate()
    {
        try {
            $processCategory = new \ProcessCategory();

            $processCategoryUid = \G::GenerateUniqueID();

            $processCategory->setNew(true);
            $processCategory->setCategoryUid($processCategoryUid);
            $processCategory->setCategoryName("PHPUnit Category");
            $processCategory->save();
        } catch (\Exception $e) {
            $processCategoryUid = "";
        }

        self::$arrayUid[] = $processCategoryUid;

        $this->assertNotEmpty($processCategoryUid);
    }


    public function testGetCategories()
    {
        $processCategory = new \ProcessMaker\BusinessModel\ProcessCategory();

        $arrayProcessCategory = $processCategory->getCategories();

        $this->assertNotEmpty($arrayProcessCategory);

        $arrayProcessCategory = $processCategory->getCategories(null, null, null, 0, 0);

        $this->assertEmpty($arrayProcessCategory);

        $arrayProcessCategory = $processCategory->getCategories(array("filter" => "PHP"));

        $this->assertNotEmpty($arrayProcessCategory);

        $this->assertEquals($arrayProcessCategory[0]["CAT_NAME"], "PHPUnit Category");
        $this->assertEquals($arrayProcessCategory[0]["CAT_TOTAL_PROCESSES"], 0);
    }
}

