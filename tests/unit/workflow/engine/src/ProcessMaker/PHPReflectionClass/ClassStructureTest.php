<?php

namespace ProcessMaker\PHPReflectionClass;

use ProcessMaker\PHPReflectionClass\MethodStructure;
use Tests\TestCase;

class ClassStructureTest extends TestCase
{
    /**
     * Instance of ClassStructure.
     * @var ClassStructure
     */
    protected $object;

    /**
     * This setUp method.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new ClassStructure();
    }

    /**
     * This test the method deleteInfo.
     * @covers ProcessMaker\PHPReflectionClass\ClassStructure::deleteInfo
     * @test
     */
    public function testDeleteInfo()
    {
        //assert false
        $result1 = $this->object->deleteInfo("test1");
        $this->assertFalse($result1);

        //assert true
        $this->object->info["test2"] = [];
        $result2 = $this->object->deleteInfo("test2");
        $this->assertTrue($result2);
    }

    /**
     * This test the method getInfo.
     * @covers ProcessMaker\PHPReflectionClass\ClassStructure::getInfo
     * @test
     */
    public function testGetInfo()
    {
        //assert false
        $result1 = $this->object->getInfo("test1");
        $this->assertFalse($result1);

        //assert true
        $this->object->info["test2"] = [];
        $result2 = $this->object->getInfo("test2");
        $this->assertEquals([], $result2);
    }

    /**
     * This test the method parseFromFile.
     * @covers ProcessMaker\PHPReflectionClass\ClassStructure::parseFromFile
     * @test
     */
    public function testParseFromFile()
    {
        //assert false
        $result1 = $this->object->parseFromFile("invalidPath");
        $this->assertFalse($result1);

        $filename = PATH_TRUNK . "tests/resources/ContentPHPSourceCode.txt";
        $result2 = $this->object->parseFromFile($filename);
        $this->assertTrue($result2);
    }

    /**
     * This test the method setInfo.
     * @covers ProcessMaker\PHPReflectionClass\ClassStructure::setInfo
     * @test
     */
    public function testSetInfo()
    {
        $this->object->setInfo("test1", "testing");
        $this->assertEquals($this->object->info["test1"], "testing");
    }

    /**
     * This test the method setMethod.
     * @covers ProcessMaker\PHPReflectionClass\ClassStructure::setMethod
     * @test
     */
    public function testSetMethod()
    {
        //assert false
        $result1 = $this->object->setMethod(null);
        $this->assertFalse($result1);

        //assert true
        $method = new MethodStructure("testing");
        $result2 = $this->object->setMethod($method);
        $this->assertTrue($result2);
    }
}
