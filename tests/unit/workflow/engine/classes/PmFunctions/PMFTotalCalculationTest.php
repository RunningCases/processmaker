<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Faker\Factory;
use Tests\TestCase;

/**
 * Test the PMFTotalCalculation() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#executeQuery.28.29
 */
class PMFTotalCalculationTest extends TestCase
{
	/**
     * This tests if the "PMFTotalCalculation" execute correctly the sum
     * @test
     */
    public function it_must_return_the_sum_of_the_method()
    {
        $grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 2
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 5
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 3
            ]
        ];
        $field = "field2";
        $this->assertEquals(10, PMFTotalCalculation($grid, $field, 'sum'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the average
     * @test
     */
    public function it_must_return_the_average_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 2
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 5
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 3
            ]
        ];
        $this->assertEquals(3.3333333333, PMFTotalCalculation($grid, 'field2', 'average'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the median
     * @test
     */
    public function it_must_return_the_median_of_the_method()
    {
    	$grid1 = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 2
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 5
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 3
            ]
        ];
        $grid2 = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 2
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 5
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 3
            ],
            '4' => [ 
                "field1" => "Value 3",
                "field2" => 8
            ]
        ];
        $this->assertEquals(3, PMFTotalCalculation($grid1, 'field2', 'median'));
        $this->assertEquals(4, PMFTotalCalculation($grid2, 'field2', 'median'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the minimum
     * @test
     */
    public function it_must_return_the_minimum_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 5
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 2
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 3
            ]
        ];
        $this->assertEquals(2, PMFTotalCalculation($grid, 'field2', 'minimum'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the maximum
     * @test
     */
    public function it_must_return_the_maximum_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 2
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 5
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 3
            ]
        ];
        $this->assertEquals(5, PMFTotalCalculation($grid, 'field2', 'maximum'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the standardDeviation
     * @test
     */
    public function it_must_return_the_standardDeviation_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 25
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 40
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 10
            ]
        ];
        $this->assertEquals(12.2474487139, PMFTotalCalculation($grid, 'field2', 'standardDeviation'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the variance
     * @test
     */
    public function it_must_return_the_variance_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 25
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 40
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 10
            ]
        ];
        $this->assertEquals(150, PMFTotalCalculation($grid, 'field2', 'variance'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the percentile
     * @test
     */
    public function it_must_return_the_percentile_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 10
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 35
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 5
            ]
        ];
        $expectedArray = [
            "1" => 20,
            "2" => 70,
            "3" => 10,
        ];
        $this->assertEquals($expectedArray, PMFTotalCalculation($grid, 'field2', 'percentile'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the count
     * @test
     */
    public function it_must_return_the_count_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 25
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 40
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 10
            ]
        ];
        $this->assertEquals(3, PMFTotalCalculation($grid, 'field2', 'count'));
    }
    /**
     * This tests if the "PMFTotalCalculation" execute correctly the count distinct
     * @test
     */
    public function it_must_return_the_count_distinct_of_the_method()
    {
    	$grid = [
            '1' => [ 
                "field1" => "Value 1",
                "field2" => 20
            ],
            '2' => [ 
                "field1" => "Value 2",
                "field2" => 20
            ],
            '3' => [ 
                "field1" => "Value 3",
                "field2" => 10
            ]
        ];
        $this->assertEquals(2, PMFTotalCalculation($grid, 'field2', 'countDistinct'));
    }
}