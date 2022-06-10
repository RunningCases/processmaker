<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\ProcessCategory;
use Tests\TestCase;

/**
 * Class ProcessCategoryTest
 *
 * @coversDefaultClass \ProcessMaker\Model\ProcessCategory
 */
class ProcessCategoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Call the setUp parent method
     */
    public function setUp(): void
    {
        parent::setUp();
        ProcessCategory::query()->delete();
    }

    /**
     * Tests get categories
     * 
     * @covers \ProcessMaker\Model\ProcessCategory::getCategories()
     * @test
     */
    public function it_tests_get_categories()
    {
        $processCategory = factory(ProcessCategory::class)->create();
        $result = ProcessCategory::getCategories();

        $this->assertNotEmpty($result);
    }

    /**
     * Tests the getProcessCategories method without paremeters
     * 
     * @covers \ProcessMaker\Model\ProcessCategory::getProcessCategories()
     * @covers \ProcessMaker\Model\ProcessCategory::scopeCategoryName()
     * @test
     */
    public function it_tests_get_process_categories_method_without_paremeters()
    {
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 1
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 2
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 3
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 4
        ]);
        $result = ProcessCategory::getProcessCategories();

        $this->assertCount(4, $result);
    }

    /**
     * Tests the getProcessCategories method filtered by name
     * 
     * @covers \ProcessMaker\Model\ProcessCategory::getProcessCategories()
     * @covers \ProcessMaker\Model\ProcessCategory::scopeCategoryName()
     * @test
     */
    public function it_tests_get_process_categories_method_filter_by_name()
    {
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 1,
            'CATEGORY_NAME' => 'Category1'
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 2,
            'CATEGORY_NAME' => 'Category2'
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 3,
            'CATEGORY_NAME' => 'Category3'
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 4,
            'CATEGORY_NAME' => 'Category4'
        ]);
        $result = ProcessCategory::getProcessCategories('1');

        $this->assertCount(1, $result);
    }

    /**
     * Tests the getProcessCategories method with start and limit parameters
     * 
     * @covers \ProcessMaker\Model\ProcessCategory::getProcessCategories()
     * @covers \ProcessMaker\Model\ProcessCategory::scopeCategoryName()
     * @test
     */
    public function it_tests_get_process_categories_method_with_start_limit()
    {
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 1,
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 2,
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 3,
        ]);
        factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => 4,
        ]);
        $result = ProcessCategory::getProcessCategories(null, 1, 3);

        $this->assertCount(3, $result);
    }

    /**
     * Tests the getCategoryId method
     * 
     * @covers \ProcessMaker\Model\ProcessCategory::getCategoryId()
     * @test
     */
    public function it_tests_get_category_id_method()
    {
        $processCategory = factory(ProcessCategory::class)->create();
        $result = ProcessCategory::getCategoryId($processCategory->CATEGORY_UID);

        $this->assertEquals($processCategory->CATEGORY_ID, $result);
    }

    /**
     * Tests get category
     * 
     * @covers \ProcessMaker\Model\ProcessCategory::getCategory()
     * @covers \ProcessMaker\Model\ProcessCategory::scopeCategory()
     * @test
     */
    public function it_tests_get_category()
    {
        $processCategory = factory(ProcessCategory::class)->create();
        $result = ProcessCategory::getCategory($processCategory->CATEGORY_ID);

        $this->assertNotEmpty($result);
    }
}
