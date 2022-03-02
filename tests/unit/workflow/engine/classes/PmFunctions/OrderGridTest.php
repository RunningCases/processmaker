<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the orderGrid() function
 * 
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#generateCode.28.29
 */
class OrderGridTest extends TestCase
{
    /**
     * This tests the "orderGrid"
     * @test
     */
    public function it_test_order_grid()
    {
        $grid = [];
        $grid[1]['NAME'] = 'Jhon';
        $grid[1]['AGE'] = 27;
        $grid[2]['NAME'] = 'Louis';
        $grid[2]['AGE'] = 5;
        $result = orderGrid($grid, 'AGE', 'ASC');
        $this->assertNotEmpty($result);
        $result = orderGrid($result, 'AGE', 'DESC');
        $this->assertNotEmpty($result);
    }
}
