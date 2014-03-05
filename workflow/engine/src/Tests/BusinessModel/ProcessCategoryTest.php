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

