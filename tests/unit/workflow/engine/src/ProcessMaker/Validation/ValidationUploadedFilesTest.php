<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Validation;

use ProcessMaker\Validation\ValidationUploadedFiles;
use Tests\TestCase;

/**
 * @coversDefaultClass \ProcessMaker\Validation\ValidationUploadedFiles
 */
class ValidationUploadedFilesTest extends TestCase
{

    /**
     * It copies the images for the test
     */
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        copy(PATH_HTML . 'images/1.png', PATH_DATA . '1.PNG');
        copy(PATH_HTML . 'images/1.png', PATH_DATA . '1.png');
        copy(PATH_HTML . 'images/1.png', PATH_DATA . '1.PnG');
    }

    /**
     * It deletes the images created
     */
    public function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        if (file_exists(PATH_DATA . '1.PNG')) {
            unlink(PATH_DATA . '1.PNG');
        }
        if (file_exists(PATH_DATA . '1.png')) {
            unlink(PATH_DATA . '1.png');
        }
        if (file_exists(PATH_DATA . '1.PnG')) {
            unlink(PATH_DATA . '1.PnG');
        }
    }

    /**
     * It tests the runRules method when the file extension is in upper case
     *
     * @covers ::runRules
     * @test
     */
    public function it_should_test_the_run_rules_method_in_upper_case()
    {
        // Create the file
        $file = [
            "filename" => "1.PNG",
            "path" => PATH_DATA . "1.PNG"
        ];

        // Create the ValidationUploadedFiles object
        $validation = new ValidationUploadedFiles();

        // Call the runRules method
        $result = $validation->runRules($file);

        // Asserts the validation did not fail
        $this->assertFalse($result->fails());

        // Asserts there is no a message
        $this->assertEmpty($result->getMessage());

        // Asserts the status is 0
        $this->assertEquals(0, $result->getStatus());
    }

    /**
     * It tests the runRules method when the file extension is in lower case
     *
     * @covers ::runRules
     * @test
     */
    public function it_should_test_the_run_rules_method_in_lower_case()
    {
        // Create the file
        $file = [
            "filename" => "1.png",
            "path" => PATH_DATA . "1.png"
        ];

        // Create the ValidationUploadedFiles object
        $validation = new ValidationUploadedFiles();

        // Call the runRules method
        $result = $validation->runRules($file);

        // Asserts the validation did not fail
        $this->assertFalse($result->fails());

        // Asserts there is no a message
        $this->assertEmpty($result->getMessage());

        // Asserts the status is 0
        $this->assertEquals(0, $result->getStatus());
    }

    /**
     * It tests the runRules method when the file extension is in upper and lower case
     *
     * @covers ::runRules
     * @test
     */
    public function it_should_test_the_run_rules_method_in_upper_and_lower_case()
    {
        // Create the file
        $file = [
            "filename" => "1.PnG",
            "path" => PATH_DATA . "1.PnG"
        ];

        // Create the ValidationUploadedFiles object
        $validation = new ValidationUploadedFiles();

        // Call the runRules method
        $result = $validation->runRules($file);

        // Asserts the validation did not fail
        $this->assertFalse($result->fails());

        // Asserts there is no a message
        $this->assertEmpty($result->getMessage());

        // Asserts the status is 0
        $this->assertEquals(0, $result->getStatus());
    }

    /**
     * This test verify validation rules for files post in cases notes.
     * @test
     * @covers ::runRulesForPostFilesOfNote
     */
    public function it_should_test_run_rules_for_post_files_of_note()
    {
        //assert for file has not exist
        $file = [
            'filename' => 'testDocument.pdf',
            'path' => "testDocument.pdf"
        ];
        $validation = new ValidationUploadedFiles();
        $result = $validation->runRulesForPostFilesOfNote($file);
        $this->assertTrue($result->fails());

        //assert for file has not valid extension
        $file = [
            'filename' => 'projectData.json',
            'path' => PATH_TRUNK . "tests/resources/projectData.json"
        ];
        $validation = new ValidationUploadedFiles();
        $result = $validation->runRulesForPostFilesOfNote($file);
        $this->assertTrue($result->fails());

        //assert the file exists and has valid extension
        $file = [
            'filename' => 'testDocument.pdf',
            'path' => PATH_TRUNK . "tests/resources/testDocument.pdf"
        ];
        $validation = new ValidationUploadedFiles();
        $result = $validation->runRulesForPostFilesOfNote($file);
        $this->assertFalse($result->fails());
        $this->assertEmpty($result->getMessage());
        $this->assertEquals(0, $result->getStatus());
    }
}
