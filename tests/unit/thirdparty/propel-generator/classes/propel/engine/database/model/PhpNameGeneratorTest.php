<?php

use Tests\TestCase;

class PhpNameGeneratorTest extends TestCase
{
    /**
     * @var PhpNameGenerator
     */
    var $phpNameGenerator;

    /**
     * Set up the test class
     */
    public function setUp()
    {
        // Call the setUp parent method
        parent::setUp();

        // Include class "PhpNameGenerator" from "thirdparty" folder, this class is not loaded with composer
        require_once PATH_THIRDPARTY . 'propel-generator/classes/propel/engine/database/model/PhpNameGenerator.php';

        // Instance class PhpNameGenerator
        $this->phpNameGenerator = new PhpNameGenerator();
    }

    /**
     * Test "underscoreMethod" using texts with and without underscores
     *
     * @test
     * @covers PhpNameGenerator::underscoreMethod()
     */
    public function it_should_test_underscore_method_through_generate_name()
    {
        // To force to use the protected method "underscoreMethod"
        $method = PhpNameGenerator::CONV_METHOD_UNDERSCORE;

        // Assert for bug PMC-1341
        $string = 'q_10_0_0';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('Q1000', $convertedString);

        // Assert for a text in lowercase without underscores
        $string = 'example';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('Example', $convertedString);

        // Assert for a text in uppercase without underscores
        $string = 'EXAMPLE';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('Example', $convertedString);

        // Assert for a capitalized text without underscores, should be return the same value
        $string = 'Example';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals($string, $convertedString);

        // Assert for a text in lowercase with underscore
        $string = 'first_name';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('FirstName', $convertedString);

        // Assert for a text in lowercase with underscores
        $string = 'this_is_my_text';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('ThisIsMyText', $convertedString);

        // Assert for a mixed text with underscores
        $string = 'this_Is_the_Number_1_to_check';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('ThisIsTheNumber1ToCheck', $convertedString);
    }

    /**
     * Test "phpnameMethod" using texts with and without underscores
     *
     * @test
     * @covers PhpNameGenerator::phpnameMethod()
     */
    public function it_should_test_php_name_method_through_generate_name()
    {
        // To force to use the protected method "underscoreMethod"
        $method = PhpNameGenerator::CONV_METHOD_UNDERSCORE;

        // Assert for bug PMC-1341
        $string = 'q_10_0_0';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('Q1000', $convertedString);

        // Assert for a text in lowercase without underscores
        $string = 'example';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('Example', $convertedString);

        // Assert for a text in uppercase without underscores
        $string = 'EXAMPLE';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('Example', $convertedString);

        // Assert for a capitalized text without underscores, should be return the same value
        $string = 'Example';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals($string, $convertedString);

        // Assert for a text in lowercase with underscore
        $string = 'first_name';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('FirstName', $convertedString);

        // Assert for a text in lowercase with underscores
        $string = 'this_is_my_text';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('ThisIsMyText', $convertedString);

        // Assert for a mixed text with underscores
        $string = 'this_Is_the_Number_1_to_check';
        $convertedString = $this->phpNameGenerator->generateName([$string, $method]);
        $this->assertEquals('ThisIsTheNumber1ToCheck', $convertedString);
    }
}
