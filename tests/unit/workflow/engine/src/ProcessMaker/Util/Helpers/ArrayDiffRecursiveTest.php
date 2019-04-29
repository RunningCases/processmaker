<?php
namespace Tests\unit\workflow\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class ArrayDiffRecursiveTest extends TestCase
{
    /**
     * Test to make sure a diff is an empty associative array when two input arrays have the same data
     * @test
     */
    public function it_should_return_an_empty_diff()
    {
        // Create two of the same associative arrays
        $change = [
            'a' => 'b',
        ];
        $source = [
            'a' => 'b',
        ];
        $this->assertEquals([], arrayDiffRecursive($change, $source));
    }

    /**
     * Test to make sure a diff contains nothing if the source has additional properties but the change does not have them
     * @test
     */
    public function it_should_return_empty_diff_with_source_having_extra_properties()
    {
        // Create two of the same associative arrays
        $change = [
            'a' => 'b',
        ];
        $source = [
            'a' => 'b',
            'c' => 'd',
        ];
        $this->assertEquals([], arrayDiffRecursive($change, $source));
    }

    /**
     * Test to make sure a diff contains an extra property if the change has it but source does not
     * @test
     */
    public function it_should_return_simple_diff_with_change_having_extra_property()
    {
        // Create two of the same associative arrays
        $change = [
            'a' => 'b',
            'c' => 'd',
        ];
        $source = [
            'a' => 'b',
        ];
        $expected = [
            'c' => 'd',
        ];
        $this->assertEquals($expected, arrayDiffRecursive($change, $source));
    }

    /**
     * Test to make sure the diff includes a property that is in a nested deep array property
     * @test
     */
    public function it_should_return_diff_with_nested_difference()
    {
        $change = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => 'Goodbye',
            ],
        ];

        $source = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => 'Hello',
            ],
        ];
        $expected = [
            'c' => [
                'f' => 'Goodbye',
            ],
        ];
        $this->assertEquals($expected, arrayDiffRecursive($change, $source));
    }

    /**
     * Test to make sure the diff includes changes in a nested array
     * @test
     */
    public function it_should_return_a_diff_with_array_changes()
    {
        $change = [
            'var1' => 'A', 
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'], 
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        $source = [
            'var1' => 'A', 
            'var2' => 'B', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B']
            ]
        ];
        $expected = [
            'var2' => 'X', 
            'grid1' => [
                // Note, the index of 1 is not present in the expected, because the record at index 1 did not change
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        $this->assertEquals($expected, arrayDiffRecursive($change, $source));
    }

    /**
     * Ensure that the diff provided can be merged with source array to show added rows with proper indexes
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_added_rows_to_array()
    {
        $change = [
            'var1' => 'A', 
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'], 
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        $source = [
            'var1' => 'A', 
            'var2' => 'B', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B']
            ]
        ];
        // Now, let's make sure that when we array replace recursive, it properly has all rows and changes 
        $expected = [
            'var1' => 'A',
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        $diff = arrayDiffRecursive($change, $source);
        $merged = array_replace_recursive($source, $diff);
        $this->assertEquals($expected, $merged);
    }

    /**
     * Test to make sure it provides a diff that can be used with replace_recursive to modify an array in place 
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_modifying_rows_in_array()
    {
        $change = [
            'var1' => 'A', 
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'], 
                2 => ['field1' => 'AA', 'field2' => 'CC'],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ]
        ];
        $source = [
            'var1' => 'A', 
            'var2' => 'B', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'field2' => 'BB'],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ]
        ];
        // Now, let's make sure that when we array replace recursive, it properly has all rows and changes 
        $expected = [
            'var1' => 'A',
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                // Note the changed record at 2
                2 => ['field1' => 'AA', 'field2' => 'CC'],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ]
        ];
        $diff = arrayDiffRecursive($change, $source);
        $merged = array_replace_recursive($source, $diff);
        $this->assertEquals($expected, $merged);
    }

    /**
     * Ensure that the diff can be applied to source to REMOVE records from the array
     * @note This will absolutely fail, because you can't apply the diff to remove something, it's not possible in this way
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_removed_rows_from_array()
    {
        $change = [
            'var1' => 'A', 
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'], 
            ]
        ];
        $source = [
            'var1' => 'A', 
            'var2' => 'B', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        // Now, let's make sure that when we array replace recursive, it properly has all rows and changes 
        $expected = [
            'var1' => 'A',
            'var2' => 'X', 
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B']
            ]
        ];
        $diff = arrayDiffRecursive($change, $source);
        $merged = array_replace_recursive($source, $diff);
        $this->assertEquals($expected, $merged);

    }

}


