<?php
require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.fieldValidator.php");





/**
 * Unit test for class FieldValidator
 */
class classFieldValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FieldValidator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FieldValidator();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * This is the default method to test, if the class still having
     * the same number of methods.
     */
    public function testNumberOfMethodsInThisClass()
    {
        $methods = get_class_methods('FieldValidator');
        $this->assertTrue( count($methods) == 7);
    }

    /**
     * Test all methods structure
     */
    public function testAllMethodsStructure()
    {
        $methods = get_class_methods($this->object);

        //isInt
        $this->assertTrue( in_array('isInt', $methods ), 'exists method isInt' );
        $r = new ReflectionMethod('FieldValidator', 'isInt');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'num');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);

        //isReal
        $this->assertTrue( in_array('isReal', $methods ), 'exists method isReal' );
        $r = new ReflectionMethod('FieldValidator', 'isReal');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'num');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);

        //isBool
        $this->assertTrue( in_array('isBool', $methods ), 'exists method isBool' );
        $r = new ReflectionMethod('FieldValidator', 'isBool');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'bool');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);

        //isUrl
        $this->assertTrue( in_array('isUrl', $methods ), 'exists method isUrl' );
        $r = new ReflectionMethod('FieldValidator', 'isUrl');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'url');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);

        //isEmail
        $this->assertTrue( in_array('isEmail', $methods ), 'exists method isEmail' );
        $r = new ReflectionMethod('FieldValidator', 'isEmail');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'email');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);

        //isIp
        $this->assertTrue( in_array('isIp', $methods ), 'exists method isIp' );
        $r = new ReflectionMethod('FieldValidator', 'isIp');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'ip');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);

        //validate
        $this->assertTrue( in_array('validate', $methods ), 'exists method validate' );
        $r = new ReflectionMethod('FieldValidator', 'validate');
        $params = $r->getParameters();
        $this->assertTrue( $params[0]->getName() == 'arrayData');
        $this->assertTrue( $params[0]->isArray() == false);
        $this->assertTrue( $params[0]->isOptional () == false);
        $this->assertTrue( $params[1]->getName() == 'arrayDataValidators');
        $this->assertTrue( $params[1]->isArray() == false);
        $this->assertTrue( $params[1]->isOptional () == false);
    }

    /**
     * @covers FieldValidator::isInt
     */
    public function testIsInt()
    {
        $this->assertTrue($this->object->isInt(0));
        $this->assertTrue($this->object->isInt("0"));
        $this->assertTrue($this->object->isInt(+0));
        $this->assertTrue($this->object->isInt("+0"));
        $this->assertTrue($this->object->isInt(-0));
        $this->assertTrue($this->object->isInt("-0"));

        $this->assertTrue($this->object->isInt(55));
        $this->assertTrue($this->object->isInt("55"));
        $this->assertTrue($this->object->isInt(+55));
        $this->assertTrue($this->object->isInt("+55"));
        $this->assertTrue($this->object->isInt(-55));
        $this->assertTrue($this->object->isInt("-55"));

        $this->assertFalse($this->object->isInt(""));

        $this->assertFalse($this->object->isInt(55.5));
        $this->assertFalse($this->object->isInt("55.5"));
        $this->assertFalse($this->object->isInt(+55.5));
        $this->assertFalse($this->object->isInt("+55.5"));
        $this->assertFalse($this->object->isInt(-55.5));
        $this->assertFalse($this->object->isInt("-55.5"));
    }

    /**
     * @covers FieldValidator::isReal
     */
    public function testIsReal()
    {
        $this->assertTrue($this->object->isReal(0));
        $this->assertTrue($this->object->isReal("0"));
        $this->assertTrue($this->object->isReal(+0));
        $this->assertTrue($this->object->isReal("+0"));
        $this->assertTrue($this->object->isReal(-0));
        $this->assertTrue($this->object->isReal("-0"));

        $this->assertTrue($this->object->isReal(55));
        $this->assertTrue($this->object->isReal("55"));
        $this->assertTrue($this->object->isReal(+55));
        $this->assertTrue($this->object->isReal("+55"));
        $this->assertTrue($this->object->isReal(-55));
        $this->assertTrue($this->object->isReal("-55"));

        $this->assertTrue($this->object->isReal(55.5));
        $this->assertTrue($this->object->isReal("55.5"));
        $this->assertTrue($this->object->isReal(+55.5));
        $this->assertTrue($this->object->isReal("+55.5"));
        $this->assertTrue($this->object->isReal(-55.5));
        $this->assertTrue($this->object->isReal("-55.5"));

        $this->assertFalse($this->object->isReal(""));

        $this->assertFalse($this->object->isReal(".5"));
        $this->assertFalse($this->object->isReal("a"));
    }

    /**
     * @covers FieldValidator::isBool
     */
    public function testIsBool()
    {
        $this->assertTrue($this->object->isBool(true));
        $this->assertTrue($this->object->isBool(false));

        $this->assertTrue($this->object->isBool("true"));
        $this->assertTrue($this->object->isBool("false"));

        $this->assertFalse($this->object->isBool(""));

        $this->assertFalse($this->object->isBool(1));
        $this->assertFalse($this->object->isBool(0));
        $this->assertFalse($this->object->isBool("1"));
        $this->assertFalse($this->object->isBool("0"));
    }

    /**
     * @covers FieldValidator::isUrl
     */
    public function testIsUrl()
    {
        $this->assertTrue($this->object->isUrl("http://www.myweb.com"));
        $this->assertTrue($this->object->isUrl("http://www.myweb.com/"));
        $this->assertTrue($this->object->isUrl("https://www.myweb.com"));
        $this->assertTrue($this->object->isUrl("https://www.myweb.com/"));
        $this->assertTrue($this->object->isUrl("https://www.myweb.com.bo"));
        $this->assertTrue($this->object->isUrl("https://myweb.com"));
        $this->assertTrue($this->object->isUrl("https://myweb.com.bo"));
        $this->assertTrue($this->object->isUrl("http://192.168.10.58"));
        $this->assertTrue($this->object->isUrl("https://192.168.10.58"));
        $this->assertTrue($this->object->isUrl("http://192.168.10.58:8080"));
        $this->assertTrue($this->object->isUrl("https://192.168.10.58:8080/"));
        $this->assertTrue($this->object->isUrl("http://www.myweb.com/project/01/activity/01/steps"));
        $this->assertTrue($this->object->isUrl("http://www.myweb.com/project/01/activity/01/steps.php"));

        $this->assertFalse($this->object->isUrl(""));

        $this->assertFalse($this->object->isUrl("www.myweb.com"));
        $this->assertFalse($this->object->isUrl("www.myweb.com.bo"));
        $this->assertFalse($this->object->isUrl("http ://www.myweb.com/"));
        $this->assertFalse($this->object->isUrl("http://myweb"));
        $this->assertFalse($this->object->isUrl("myweb"));
        $this->assertFalse($this->object->isUrl("192.168.10.58"));
        $this->assertFalse($this->object->isUrl("xttp://192.168.10.58"));
        $this->assertFalse($this->object->isUrl("http:://192.168.10.58:8080/"));
        $this->assertFalse($this->object->isUrl("http://"));
        $this->assertFalse($this->object->isUrl("https://"));
    }

    /**
     * @covers FieldValidator::isEmail
     */
    public function testIsEmail()
    {
        $this->assertTrue($this->object->isEmail("my-email@myweb.com"));
        $this->assertTrue($this->object->isEmail("my.email@myweb.com.bo"));
        $this->assertTrue($this->object->isEmail("my-email.sample@myweb.com"));
        $this->assertTrue($this->object->isEmail("my-email@my-web.com"));
        $this->assertTrue($this->object->isEmail("my-2013-email@my-web.com"));
        $this->assertTrue($this->object->isEmail("2013-my-email@my-web.com"));

        $this->assertFalse($this->object->isEmail(""));

        $this->assertFalse($this->object->isEmail(" @myweb.com"));
        $this->assertFalse($this->object->isEmail("my@email@myweb.com.bo"));
        $this->assertFalse($this->object->isEmail("my-2013-email"));
        $this->assertFalse($this->object->isEmail("my-email@myweb"));
        $this->assertFalse($this->object->isEmail(".@myweb.com"));
        $this->assertFalse($this->object->isEmail("-@myweb.com"));
        $this->assertFalse($this->object->isEmail("@myweb.com"));
        $this->assertFalse($this->object->isEmail("sample@.com"));
        $this->assertFalse($this->object->isEmail("sample.com"));
    }

    /**
     * @covers FieldValidator::isIp
     */
    public function testIsIp()
    {
        $this->assertTrue($this->object->isIp("255.255.255.255"));
        $this->assertTrue($this->object->isIp("0.0.0.0"));
        $this->assertTrue($this->object->isIp("127.0.0.1"));

        $this->assertFalse($this->object->isIp(""));

        $this->assertFalse($this->object->isIp("127.o.0.1"));
        $this->assertFalse($this->object->isIp("127.0.0."));
        $this->assertFalse($this->object->isIp("127.0.0"));
        $this->assertFalse($this->object->isIp(".0.0.1"));
        $this->assertFalse($this->object->isIp("127..0.1"));
    }

    /**
     * @covers FieldValidator::validate
     */
    public function testValidate()
    {
        //Data OK
        $arrayField = array(
            "name" => "peter",
            "lastname" => "parker",
            "age" => 33,
            "weight" => 56.55,
            "website" => "http://www.myweb.com/",
            "email" => "spider-man@myweb.com",
            "alive" => true
        );

        $arrayValidators = array(
           "name" => array(
               "type" => "string",
               "required" => true,
               "min_size" => 5
           ),
           "lastname" => array(
               "type" => "string",
               "min_size" => 2
           ),
           "age" => array(
               "type" => "int",
               "required" => true
           ),
           "weight" => array(
               "type" => "real",
               "required" => true
           ),
           "website" => array(
               "validation" => "url"
           ),
           "email" => array(
               "type" => "string",
               "required" => false,
               "validation" => "email"
           ),
           "alive" => array(
               "type" => "boolean"
           ),
           "ip" => array(
               "validation" => "ip",
               "required" => false
           )
        );

        $result = $this->object->validate($arrayField, $arrayValidators);

        $expectedSuccess = true;
        $expectedErrors = array();

        $this->assertEquals($result["success"], $expectedSuccess);
        $this->assertEquals($result["errors"], $expectedErrors);

        //Data failed
        $arrayField = array(
            "name" => "Dan",
            "lastname" => "",
            "age" => "hello",
            "weight" => "",
            "website" => "www.myweb.com",
            "email" => "spider-man@myweb",
            "alive" => 1
        );

        $arrayValidators = array(
            "name" => array(
                "type" => "string",
                "required" => true,
                "min_size" => 4
            ),
            "lastname" => array(
                "type" => "string",
                "required" => true
            ),
            "age" => array(
                "type" => "int"
            ),
            "weight" => array(
                "type" => "real",
                "required" => true
            ),
            "website" => array(
                "validation" => "url"
            ),
            "email" => array(
                "type" => "string",
                "required" => false,
                "validation" => "email"
            ),
            "alive" => array(
                "type" => "boolean"
            ),
            "ip" => array(
                "validation" => "ip",
                "required" => false
            )
        );

        $result = $this->object->validate($arrayField, $arrayValidators);

        $expectedSuccess = false;
        $expectedErrors = array(
            "Field \"name\" should be min 4 chars, 3 given",
            "Field \"lastname\" is required",
            "Field \"age\" not is an integer number",
            "Field \"weight\" not is an real number",
            "Field \"weight\" is required",
            "Field \"website\" have not an valid URL format",
            "Field \"email\" have not an valid email format",
            "Field \"alive\" not is an boolean"
        );

        $this->assertEquals($result["success"], $expectedSuccess);
        $this->assertEquals($result["errors"], $expectedErrors);
    }

    /**
     *
     */
    public function dataProviderTestValidateExceptionCovers()
    {
        return array(
            array("", array(), false),
            array(array(), "", false),
            array(array(), array(1), false),
            array(array(1), array(), false)
        );
    }

    /**
     * @covers       FieldValidator::validate
     * @dataProvider dataProviderTestValidateExceptionCovers
     */
    public function testValidateExceptionCovers($arrayData, $arrayDataValidators, $success)
    {
        $result = $this->object->validate($arrayData, $arrayDataValidators);

        $this->assertEquals($result["success"], $success);
    }

    /**
     *
     */
    public function dataProviderTestValidateRemainingCovers()
    {
        return array(
            array(array("age"  => 55.5),     array("age"  => array("type" => "int")), false),
            array(array("pi"   => "3_1416"), array("pi"   => array("type" => "real")), false),
            array(array("flag" => 1),        array("flag" => array("type" => "bool")), false),
            array(array("name" => "peter"),  array("name" => array("type" => "string")), true),

            array(array("email" => "my--email@myweb.com"), array("email" => array("validation" => "email")), false),
            array(array("ip"    => "127.0.0"),             array("ip" => array("validation" => "ip")), false),

            array(array("name" => "peter"), array("name" => array("min_size" => 6)), false),

            array(array("name" => "peter"), array("firstname" => array("required" => true)), false)
        );
    }

    /**
     * @covers       FieldValidator::validate
     * @dataProvider dataProviderTestValidateRemainingCovers
     */
    public function testValidateRemainingCovers($arrayData, $arrayDataValidators, $success)
    {
        $result = $this->object->validate($arrayData, $arrayDataValidators);

        $this->assertEquals($result["success"], $success);
    }
}

