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
        $this->assertEquals('texttext _text_text', $res);
    }
}