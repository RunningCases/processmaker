<?php
namespace Tests\unit\workflow\src\ProcessMaker\Util\Helpers;

use stdClass;
use Tests\TestCase;

class GetDiffBetweenModifiedVariablesTest extends TestCase
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
        $this->assertEquals([], getDiffBetweenModifiedVariables($change, $source));
    }

    /**
     * Test to make sure a diff contains nothing if the source has additional properties but the change does not have them
     * Reviewing: string
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
        $this->assertEquals([], getDiffBetweenModifiedVariables($change, $source));
    }

    /**
     * Test to make sure a diff contains an extra property if the change has it but source does not
     * Reviewing: string
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
        $this->assertEquals($expected, getDiffBetweenModifiedVariables($change, $source));
    }

    /**
     * Test to make sure the diff includes a property that is in a nested deep array and object property
     * @test
     */
    public function it_should_return_diff_with_nested_difference()
    {
        $object1 = new stdClass();
        $object1->h = 'i';
        $object1->j = 'Goodbye';
        $change = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => 'Goodbye',
            ],
            'g' => $object1
        ];
        $object2 = new stdClass();
        $object2->h = 'i';
        $object2->j = 'Hello';
        $source = [
            'a' => 'b',
            'c' => [
                'd' => 'e',
                'f' => 'Hello',
            ],
            'g' => $object2
        ];
        $object1 = new stdClass();
        $object1->h = 'i';
        $object1->j = 'Goodbye';
        $expected = [
            'c' => [
                'd' => 'e',
                'f' => 'Goodbye',
            ],
            'g' => $object1
        ];
        $this->assertEquals($expected, getDiffBetweenModifiedVariables($change, $source));
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
                // Note, the entire array will be updated
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        $this->assertEquals($expected, getDiffBetweenModifiedVariables($change, $source));
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
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
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
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
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
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
        $this->assertEquals($expected, $merged);
    }

    /**
     * Utilize new behavior using laravel collections to map and filter to create the desired merged array
     * @test
     */
    public function it_should_utilize_laravel_collections_to_map_and_filter_for_desired_final_array()
    {
        $change = [
            'var1' => 'A',
            // Note the changed var 2
            'var2' => 'X',
            // Note the missing var3 element
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                // Note the missing record at index 2
            ]
        ];
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'var3' => 'C',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'field2' => 'BB']
            ]
        ];
        // Now, let's make sure that when we array replace recursive, it properly has all rows and changes 
        $expected = [
            'var1' => 'A',
            'var2' => 'X',
            // Note we don't have var3
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B']
                // And we should not have index 2
            ]
        ];
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $merged = array_replace_recursive($source, $diff);
        // Now collect and map
        $final = collect($merged)->filter(function ($value, $key) use ($change) {
            // Only accept properties that exist in the change
            return array_key_exists($key, $change);
        })->map(function ($item, $key) use ($change) {
            // We aren't recursively calling, but that's not needed for our situation, so we'll 
            // Check if it's an array, if so, create a collection, filter, then return the array
            if (is_array($item)) {
                return collect($item)->filter(function ($value, $subkey) use ($change, $key) {
                    return array_key_exists($subkey, $change[$key]);
                })->all();
            }

            // Otherwise, just return item
            return $item;
        })->all();
        $this->assertEquals($expected, $final);
    }

    /**
     * Test to make sure the diff includes changes in a nested array
     * @test
     */
    public function it_should_return_a_diff_with_object_changes()
    {
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $change = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person1
        ];

        $person2 = new stdClass();
        $person2->firstName = 'Corine';
        $person2->lastName = 'Erler';
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'varPerson' => $person2
        ];
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $expected = [
            'var2' => 'X',
            'varPerson' => $person1
        ];
        $this->assertEquals($expected, getDiffBetweenModifiedVariables($change, $source));
    }

    /**
     * Ensure that the diff provided can be merged with source object to show added rows with proper indexes
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_added_rows_to_object()
    {
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $person1->address = '2789 Parkview Drive';
        $change = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person1
        ];
        $person2 = new stdClass();
        $person2->firstName = 'Corine';
        $person2->lastName = 'Erler';
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'varPerson' => $person2
        ];
        // Now, let's make sure that when we array replace recursive, it properly has all rows and changes
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $person1->address = '2789 Parkview Drive';
        $expected = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person1
        ];
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
        $this->assertEquals($expected, $merged);
    }

    /**
     * Test to make sure it provides a diff that can be used with replace_recursive to modify an object in place
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_modifying_rows_in_object()
    {
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $person1->address = '2789 Parkview Drive';
        $change = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person1
        ];
        $person2 = new stdClass();
        $person2->firstName = 'Corine';
        $person2->lastName = 'Martell';
        $person2->address = '2789 Parkview Drive';
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'varPerson' => $person2
        ];
        // Now, let's make sure that when we array replace recursive, it properly has all rows and changes
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $person1->address = '2789 Parkview Drive';
        $expected = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person1
        ];
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
        $this->assertEquals($expected, $merged);
    }

    /**
     * Ensure that the diff can be applied to source to REMOVE records from the object
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_removed_rows_from_object()
    {
        $person1 = new stdClass();
        $person1->lastName = 'Gail';
        $person1->address = '3607 Sycamore Road';
        $change = [
            'var1' => 'A',
            'var2' => 'X',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'varPerson' => $person1],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ],
            'varPerson' => $person1
        ];
        $person2 = new stdClass();
        $person2->firstName = 'Corine';
        $person2->lastName = 'Martell';
        $person2->address = '2789 Parkview Drive';
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'varPerson' => $person2],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ],
            'varPerson' => $person2
        ];
        // Now, let's make sure that when we has object variable we need to replace with the last change over the object
        $person1 = new stdClass();
        $person1->lastName = 'Gail';
        $person1->address = '3607 Sycamore Road';
        $expected = [
            'var1' => 'A',
            'var2' => 'X',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'varPerson' => $person1],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ],
            'varPerson' => $person1
        ];

        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
        $this->assertEquals($expected, $merged);
    }

    /**
     * Ensure that the diff can be applied to source to REMOVE records from the object
     * @test
     */
    public function it_should_provide_diff_with_merge_in_the_same_object()
    {
        $person2 = new stdClass();
        $person2->firstName = 'Corine';
        $person2->lastName = 'Martell';
        $person2->address = '2789 Parkview Drive';
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'varPerson' => $person2
        ];

        $person2->lastName = 'Gail';
        $person2->email = 'corine.gail@email.com';
        $change = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person2
        ];

        // Now, let's make sure that when we has object variable we need to replace with the last change over the object
        $person1 = new stdClass();
        $person1->firstName = 'Corine';
        $person1->lastName = 'Martell';
        $person1->address = '2789 Parkview Drive';
        $person1->lastName = 'Gail';
        $person1->email = 'corine.gail@email.com';
        $expected = [
            'var1' => 'A',
            'var2' => 'X',
            'varPerson' => $person1
        ];
        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
        $this->assertEquals($expected, $merged);
    }

    /**
     * Ensure that the diff can be applied to source to REMOVE records from nested object
     * @test
     */
    public function it_should_provide_diff_with_merge_that_supports_rows_from_nested_object()
    {
        $person1 = new stdClass();
        $person1->firstName = 'Gail';
        $person1->lastName = 'Martell';
        $person1->address = '3607 Sycamore Road';
        $email1 = new stdClass();
        $email1->personal = 'gail@personal.com';
        $email1->coorporative = 'gail@coorporative.com';
        $person1->email = $email1;
        $change = [
            'var1' => 'A',
            'var2' => 'X',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'varPerson' => $person1],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ],
            'varPerson' => $person1
        ];
        $person2 = new stdClass();
        $person2->firstName = 'Corine';
        $person2->lastName = 'Martell';
        $email2 = new stdClass();
        $email2->new = 'GailJMartell@email.com';
        $person2->email = $email2;
        $source = [
            'var1' => 'A',
            'var2' => 'B',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'varPerson' => $person2],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ],
            'varPerson' => $person2
        ];
        // Now, let's make sure that when we has object variable we need to replace with the last change over the object
        $person1 = new stdClass();
        $person1->firstName = 'Gail';
        $person1->lastName = 'Martell';
        $person1->address = '3607 Sycamore Road';
        $email1 = new stdClass();
        $email1->personal = 'gail@personal.com';
        $email1->coorporative = 'gail@coorporative.com';
        $person1->email = $email1;
        $expected = [
            'var1' => 'A',
            'var2' => 'X',
            'grid1' => [
                1 => ['field1' => 'A', 'field2' => 'B'],
                2 => ['field1' => 'AA', 'varPerson' => $person1],
                3 => ['field1' => 'AAA', 'field2' => 'BBB']
            ],
            'varPerson' => $person1
        ];

        $diff = getDiffBetweenModifiedVariables($change, $source);
        $collection = collect($source);
        $merged = $collection->merge($diff);
        $merged = $merged->all();
        $this->assertEquals($expected, $merged);
    }
}

