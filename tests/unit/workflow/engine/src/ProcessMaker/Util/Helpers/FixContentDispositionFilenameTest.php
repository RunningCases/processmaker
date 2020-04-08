<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class FixContentDispositionFilenameTest extends TestCase
{
    /**
     * It tests that the special characters are being replaced
     * @test
     */
    public function it_should_test_the_special_characters_located_in_a_filename()
    {
        //The file name send to the function
        $fileName = "text\"text ?text/text";

        //Calling the fixContentDispositionFilename() function
        $res = fixContentDispositionFilename($fileName);

        //Assert the special characters where replaced with the correct values
        $this->assertEquals('text_text%20_text_text', $res);

        // Initialize the variables for the test related to PMCORE-487
        $fileName = "12/2-20@test,TEST#123$56%100^500&Version*Test(URL)+File-Files. Test Output\SmartProcess";
        $expected = "12_2-20%40test%2CTEST%23123%2456%25100%5E500%26Version_Test%28URL%29%2BFile-Files.%20Test%20Output_SmartProcess";

        // Calling the fixContentDispositionFilename() function
        $newFileName = fixContentDispositionFilename($fileName);

        // Assert the values
        $this->assertEquals($expected, $newFileName);
    }
}
