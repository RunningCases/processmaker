<?php

namespace ProcessMaker\PHPReflectionClass;

use Tests\TestCase;

class MethodStructureTest extends TestCase
{
    /**
     * Instance of MethodStructure.
     * @var MethodStructure
     */
    protected $object;

    /**
     * This setUp method.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MethodStructure("test");
    }

    /**
     * This test the method getInfo.
     * @covers ProcessMaker\PHPReflectionClass\MethodStructure::getInfo
     * @test
     */
    public function testGetInfo()
    {
        //assert false
        $result1 = $this->object->getInfo("test");
        $this->assertFalse($result1);

        //assert true
        $this->object->info["test2"] = [];
        $result2 = $this->object->getInfo("test2");
        $this->assertEquals([], $result2);
    }

    /**
     * This test the method setInfo.
     * @covers ProcessMaker\PHPReflectionClass\MethodStructure::setInfo
     * @test
     */
    public function testSetInfo()
    {
        $this->object->setInfo("test1", "test1");
        $this->assertEquals("test1", $this->object->info["test1"]);
    }

    /**
     * This test the method setParam.
     * @covers ProcessMaker\PHPReflectionClass\MethodStructure::setParam
     * @test
     */
    public function testSetParam()
    {
        $this->object->setParam("test2", "test2");
        $this->assertEquals("test2", $this->object->params["test2"]);
    }
}
