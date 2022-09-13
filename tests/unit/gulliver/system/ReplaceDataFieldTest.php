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
        $recursive = false;

        // Replace variables in the string, $recursive is false because is don't needed replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine, $recursive);

        // Assert the @qq is not being set as an empty value
        $this->assertMatchesRegularExpression("/asa@qq.fds/", $stringToCheck);

        // Testing with a "@qstring" value
        $result = [
            'var_supplierEmail' => '@qstring',
            'var_supplierEmail_label' => '@qstring',
        ];

        $dbEngine = 'mysql';
        $recursive = false;

        // Replace variables in the string, $recursive is false because is don't needed replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine, $recursive);

        // Assert the @qstring is not being set as an empty value
        $this->assertMatchesRegularExpression("/@qstring/", $stringToCheck);
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
        $recursive = false;

        // Replace variables in the string, $recursive is false because is don't needed replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine, $recursive);

        // Assert the @qq is not being set as an empty value
        $this->assertMatchesRegularExpression("/asa@qq.fds/", $stringToCheck);

        // Testing with a "@qstring" value
        $result = [
            'var_supplierEmail' => '@qstring',
            'var_supplierEmail_label' => '@qstring',
        ];

        $dbEngine = 'mysql';
        $recursive = false;

        // Replace variables in the string, $recursive is false because is don't needed replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $result, $dbEngine, $recursive);

        // Assert the @qstring is not being set as an empty value
        $this->assertMatchesRegularExpression("/@qstring/", $stringToCheck);
    }

    /**
     * Check that the variable using "@#" will be replaced recursively or not according to the parameters sent
     *
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_replace_recursively_a_variable_inside_another_variable_with_hashtag_symbol()
    {
        // Initialize variables
        $string = '@#upload_New';
        $variables = ['upload_New' => "javascript:uploadInputDocument('@#DOC_UID');",
            'DOC_UID' => '1988828025cc89aba0cd2b8079038028'];

        // Set parameters to test the method
        $dbEngine = 'mysql';
        $recursive = false;

        // Replace variables in the string, $recursive is false because is don't needed replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $variables, $dbEngine, $recursive);

        // The variable @#DOC_UID inside in the variable "@#upload_New" shouldn't be replaced
        $this->assertMatchesRegularExpression("/@#DOC_UID/", $stringToCheck);

        // Set parameters to test the method
        $dbEngine = 'mysql';
        $recursive = true;

        // Replace variables in the string, $recursive is true because is required replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $variables, $dbEngine, $recursive);

        // The variable @#DOC_UID inside in the variable "@#upload_New" should be replaced correctly
        $this->assertMatchesRegularExpression("/1988828025cc89aba0cd2b8079038028/", $stringToCheck);
    }

    /**
     * Check that the variable using "@=" will be replaced recursively or not according to the parameters sent
     *
     * @test
     * @covers G::replaceDataField
     */
    public function it_should_replace_recursively_a_variable_inside_another_variable_with_equals_symbol()
    {
        // Initialize variables
        $string = '@=upload_New';
        $variables = ['upload_New' => "javascript:uploadInputDocument('@=DOC_UID');",
            'DOC_UID' => '1988828025cc89aba0cd2b8079038028'];

        // Set parameters to test the method
        $dbEngine = 'mysql';
        $recursive = false;

        // Replace variables in the string, $recursive is false because is don't needed replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $variables, $dbEngine, $recursive);

        // The variable @=DOC_UID inside in the variable "@=upload_New" shouldn't be replaced
        $this->assertMatchesRegularExpression("/@=DOC_UID/", $stringToCheck);

        // Set parameters to test the method
        $dbEngine = 'mysql';
        $recursive = true;

        // Replace variables in the string, $recursive is true because is required replace recursively the same value
        $stringToCheck = G::replaceDataField($string, $variables, $dbEngine, $recursive);

        // The variable @=DOC_UID inside in the variable "@=upload_New" should be replaced correctly
        $this->assertMatchesRegularExpression("/1988828025cc89aba0cd2b8079038028/", $stringToCheck);
    }
}
