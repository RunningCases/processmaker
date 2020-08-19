<?php

namespace Tests\unit\gulliver\system;

use Form;
use G;
use Tests\TestCase;

class FormTest extends TestCase
{
    /**
     * Test the creation of the XML file if not exists or has no content
     *
     * @covers Form::createXMLFileIfNotExists()
     * @test
     */
    public function it_should_test_create_xml_file_if_not_exists()
    {
        // Build the file path
        $xmlForm = PATH_DYNAFORM . G::generateUniqueID() . '/' . G::generateUniqueID() . '.xml';

        // The file doesn't exists, so, should be created
        Form::createXMLFileIfNotExists($xmlForm);

        // File created?
        $this->assertFileExists($xmlForm);

        // File with content?
        $this->assertNotEmpty(file_get_contents($xmlForm));

        // Delete the file
        unlink($xmlForm);

        // Create another empty
        touch($xmlForm);

        // The file exists, but is empty, should be regenerated
        Form::createXMLFileIfNotExists($xmlForm);

        // File with content?
        $this->assertNotEmpty(file_get_contents($xmlForm));
    }
}
