<?php

use Tests\TestCase;

class ReplaceDataFieldTest extends TestCase
{
    /**
     * Check that the value of "@q" followed by a string is not being set as empty when using "@#" to identify a variable
     *
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_not_set_empty_when_calling_a_variable_with_hashtag_symbol()
    {
        $string = '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                    <html>
                    <head>
                    </head>
                    <body>
                    <p>THIS IS ONLY A TEST OF THE VARIABLE&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;@#var_supplierEmail&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                    </body>
                    </html>';

        $result = [
            'var_supplierEmail' => 'asa@qq.fds',
            'var_supplierEmail_label' => 'asa@qq.fds',
        ];

        $dbEngine = 'mysql';

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine);

        // Assert the @qq is not being set as an empty value
        $this->assertRegExp("/asa@qq.fds/", $stringToCheck);

        // Testing with a "@qstring" value
        $result = [
            'var_supplierEmail' => '@qstring',
            'var_supplierEmail_label' => '@qstring',
        ];

        $dbEngine = 'mysql';

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine);

        // Assert the @qstring is not being set as an empty value
        $this->assertRegExp("/@qstring/", $stringToCheck);
    }

    /**
     * Check that the value of "@q" followed by a string is not being set as empty when using "@=" to identify a variable
     *
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_not_set_empty_when_calling_a_variable_with_equals_symbol()
    {
        $string = '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                    <html>
                    <head>
                    </head>
                    <body>
                    <p>THIS IS ONLY A TEST OF THE VARIABLE&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;@=var_supplierEmail&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
                    </body>
                    </html>';

        $result = [
            'var_supplierEmail' => 'asa@qq.fds',
            'var_supplierEmail_label' => 'asa@qq.fds',
        ];

        $dbEngine = 'mysql';

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine);

        // Assert the @qq is not being set as an empty value
        $this->assertRegExp("/asa@qq.fds/", $stringToCheck);

        // Testing with a "@qstring" value
        $result = [
            'var_supplierEmail' => '@qstring',
            'var_supplierEmail_label' => '@qstring',
        ];

        $dbEngine = 'mysql';

        // Replace variables in the string
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine);

        // Assert the @qstring is not being set as an empty value
        $this->assertRegExp("/@qstring/", $stringToCheck);
    }
}
