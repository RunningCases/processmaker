<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class ProcessCategoryTest
 *
 * @package Tests\BusinessModel
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
        $this->oCategory = new \BusinessModel\ProcessCategory();
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
        $processCategory = new \BusinessModel\ProcessCategory();

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

