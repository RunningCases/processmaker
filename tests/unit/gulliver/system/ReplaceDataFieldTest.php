<?php

use Tests\TestCase;

class ReplaceDataFieldTest extends TestCase
{
    /**
     * This checks that strings with HTML reserved characters are replaced with entities
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_replace_entities()
    {
        // Initializing Faker instance
        $faker = Faker\Factory::create();

        // Initializing variables to use that will not change
        $stringWithVariablesToReplace = 'Hello @@var1 the @#var2 is @=var3 not @&var4->value';
        $dbEngine = 'mysql'; // This only affects the way to escape the variables with "@@" prefix
        $applyEntities = true; // If a value to replace is a not valid HTML and have HTML reserved characters, entities should be applied

        // Initializing variables to test the assertions, entities should be applied in variable with @@
        $var4 = new stdClass();
        $var4->value = $faker->words(1, true);
        $valuesToReplace = [
            'var1' => 'Java < PHP & Python',
            'var2' => $faker->words(1, true),
            'var3' => $faker->words(1, true),
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/&lt;/', $stringToCheck);
        $this->assertRegExp('/&amp;/', $stringToCheck);

        // Initializing variables to test the assertions, entities should be applied in variable with @#
        $var4 = new stdClass();
        $var4->value = $faker->words(1, true);
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'var2' => 'Java < PHP & Python',
            'var3' => $faker->words(1, true),
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/&lt;/', $stringToCheck);
        $this->assertRegExp('/&amp;/', $stringToCheck);

        // Initializing variables to test the assertions, entities should be applied in variable with @=
        $var4 = new stdClass();
        $var4->value = $faker->words(1, true);
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'var2' => $faker->words(1, true),
            'var3' => 'Java < PHP & Python',
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/&lt;/', $stringToCheck);
        $this->assertRegExp('/&amp;/', $stringToCheck);

        // Initializing variables to test the assertions, entities should be applied in variable with @&
        $var4 = new stdClass();
        $var4->value = 'Java < PHP & Python';
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'var2' => $faker->words(1, true),
            'var3' => $faker->words(1, true),
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/&lt;/', $stringToCheck);
        $this->assertRegExp('/&amp;/', $stringToCheck);
    }

    /**
     * This checks that strings with HTML reserved characters are NOT replaced with entities
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_no_replace_entities()
    {
        // Initializing Faker instance
        $faker = Faker\Factory::create();

        // Initializing variables to use that will not change
        $stringWithVariablesToReplace = 'Hello @@var1 the @#var2 is @=var3 not @&var4->value';
        $dbEngine = 'mysql'; // This only affects the way to escape the variables with "@@" prefix
        $applyEntities = false; // The values should not be replaced with entities

        // Initializing variables to test the assertions, entities should be applied in variable with @@
        $var4 = new stdClass();
        $var4->value = $faker->words(1, true);
        $valuesToReplace = [
            'var1' => 'Java < PHP & Python',
            'var2' => $faker->words(1, true),
            'var3' => $faker->words(1, true),
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/</', $stringToCheck);
        $this->assertRegExp('/&/', $stringToCheck);

        // Initializing variables to test the assertions, entities should be applied in variable with @#
        $var4 = new stdClass();
        $var4->value = $faker->words(1, true);
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'var2' => 'Java < PHP & Python',
            'var3' => $faker->words(1, true),
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/</', $stringToCheck);
        $this->assertRegExp('/&/', $stringToCheck);

        // Initializing variables to test the assertions, entities should be applied in variable with @=
        $var4 = new stdClass();
        $var4->value = $faker->words(1, true);
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'var2' => $faker->words(1, true),
            'var3' => 'Java < PHP & Python',
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/</', $stringToCheck);
        $this->assertRegExp('/&/', $stringToCheck);

        // Initializing variables to test the assertions, entities should be applied in variable with @&
        $var4 = new stdClass();
        $var4->value = 'Java < PHP & Python';
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'var2' => $faker->words(1, true),
            'var3' => $faker->words(1, true),
            'var4' => $var4
        ];

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/</', $stringToCheck);
        $this->assertRegExp('/&/', $stringToCheck);
    }

    /**
     * This checks that strings with HTML reserved characters are NOT replaced with entities if is a valid HTML, because
     * PS team sometimes build a HTML string to insert in templates (output documents or emails), Ex.- A table to list
     * users or results from a query
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_no_replace_entities_if_exists_valid_html()
    {
        // Initializing Faker instance
        $faker = Faker\Factory::create();

        // Initializing variables to use
        $stringWithVariablesToReplace = 'bla @#var1 bla @=listHtml bla @@var2 bla';
        $valuesToReplace = [
            'var1' => $faker->words(1, true),
            'listHtml' => '<table>
                             <tr>
                               <th>t1</th> 
                               <th>t2</th>
                               <th>t3</th>
                               <th>t4</th>
                               <th>t5</th>
                               <th>t6</th>
                             </tr>
                             <tr>
                               <td>c1</td>
                               <td>c2</td>
                               <td>c3</td>
                               <td>c4</td>
                               <td>c5</td>
                               <td>c6</td>
                             </tr>
                           </table>',
            'var2' => $faker->words(1, true)
        ];
        $dbEngine = 'mysql'; // This only affects the way to escape the variables with "@@" prefix
        $applyEntities = true; // Is true because the string will b used in a output document or a email template

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithVariablesToReplace, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp('/<table>/', $stringToCheck);
        $this->assertRegExp('/<tr>/', $stringToCheck);
        $this->assertRegExp('/<th>/', $stringToCheck);
        $this->assertRegExp('/<td>/', $stringToCheck);
    }

    /**
     * This checks that strings with tag <br /> should not be replaced, because is a valid tag
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_no_replace_tag_br()
    {
        // Initializing variables to use
        $stringWithTagBr = nl2br("prospection auprès d'entreprises de CA < 10 M euros
test
<a
>a
&a
\"a
'a
¢a
£a
¥a
€a
©a
®a
test");
        $valuesToReplace = [];
        $dbEngine = 'mysql'; // This only affects the way to escape the variables with "@@" prefix
        $applyEntities = true; // Is true because the string will be used in a output document or a email template

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($stringWithTagBr, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp("/<br \/>/", $stringToCheck);
    }

    /**
     * Check that the value for the System variable "__ABE__" should not be replaced never
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_no_replace_entities_for_var_abe()
    {
        // Initializing variables to use
        $string = "bla @#__ABE__ bla @#anotherVar bla";
        $valuesToReplace = [// Add a value for reserved system variable "__ABE__" used in Actions By Email feature
            '__ABE__' => 'Java < PHP', // The value for System variable "__ABE__" shouldn't be changed never
            'anotherVar' => '.NET < Java' // The value for another variables should be validated/replaced normally
        ];
        $dbEngine = 'mysql'; // This only affects the way to escape the variables with "@@" prefix
        $applyEntities = true; // Is true because the string will be used in a output document or a email template

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($string, $valuesToReplace, $dbEngine, $applyEntities);

        // Assertions
        $this->assertRegExp("/Java < PHP/", $stringToCheck);
        $this->assertRegExp("/.NET &lt; Java/", $stringToCheck);
    }
}
