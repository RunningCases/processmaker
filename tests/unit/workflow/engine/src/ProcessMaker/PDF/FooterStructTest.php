<?php

namespace ProcessMaker\PDF;

use Faker\Factory;
use Tests\TestCase;

/**
 * @covers ProcessMaker\PDF\FooterStruct
 * @test
 */
class FooterStructTest extends TestCase
{
    /**
     * FooterStruct object.
     * @var FooterStruct
     */
    protected $object;

    /**
     * Object faker.
     * @var object
     */
    private $faker;

    /**
     * setUp method.
     */
    public function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->object = new FooterStruct();
    }

    /**
     * tearDown method.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This test the getLogo method.
     * @covers ProcessMaker\PDF\BasicStruct::getLogo()
     * @test
     */
    public function it_should_test_the_method_getLogo()
    {
        $this->assertTrue(is_string($this->object->getLogo()));
    }

    /**
     * This test the getLogoWidth method.
     * @covers ProcessMaker\PDF\BasicStruct::getLogoWidth()
     * @test
     */
    public function it_should_test_the_method_getLogoWidth()
    {
        $this->assertTrue(is_float($this->object->getLogoWidth()));
    }

    /**
     * This test the getLogoPositionX method.
     * @covers ProcessMaker\PDF\BasicStruct::getLogoPositionX()
     * @test
     */
    public function it_should_test_the_method_getLogoPositionX()
    {
        $this->assertTrue(is_float($this->object->getLogoPositionX()));
    }

    /**
     * This test the getLogoPositionY method.
     * @covers ProcessMaker\PDF\BasicStruct::getLogoPositionY()
     * @test
     */
    public function it_should_test_the_method_getLogoPositionY()
    {
        $this->assertTrue(is_float($this->object->getLogoPositionY()));
    }

    /**
     * This test the getTitle method.
     * @covers ProcessMaker\PDF\BasicStruct::getTitle()
     * @test
     */
    public function it_should_test_the_method_getTitle()
    {
        $this->assertTrue(is_string($this->object->getTitle()));
    }

    /**
     * This test the getTitleFontSize method.
     * @covers ProcessMaker\PDF\BasicStruct::getTitleFontSize()
     * @test
     */
    public function it_should_test_the_method_getTitleFontSize()
    {
        $this->assertTrue(is_float($this->object->getTitleFontSize()));
    }

    /**
     * This test the getTitleFontPositionX method.
     * @covers ProcessMaker\PDF\BasicStruct::getTitleFontPositionX()
     * @test
     */
    public function it_should_test_the_method_getTitleFontPositionX()
    {
        $this->assertTrue(is_float($this->object->getTitleFontPositionX()));
    }

    /**
     * This test the getTitleFontPositionY method.
     * @covers ProcessMaker\PDF\BasicStruct::getTitleFontPositionY()
     * @test
     */
    public function it_should_test_the_method_getTitleFontPositionY()
    {
        $this->assertTrue(is_float($this->object->getTitleFontPositionY()));
    }

    /**
     * This test the getPageNumber method.
     * @covers ProcessMaker\PDF\BasicStruct::getPageNumber()
     * @test
     */
    public function it_should_test_the_method_getPageNumber()
    {
        $this->assertTrue(is_bool($this->object->getPageNumber()));
    }

    /**
     * This test the getPageNumberTitle method.
     * @covers ProcessMaker\PDF\BasicStruct::getPageNumberTitle()
     * @test
     */
    public function it_should_test_the_method_getPageNumberTitle()
    {
        $this->assertTrue(is_string($this->object->getPageNumberTitle()));
    }

    /**
     * This test the getPageNumberTotal method.
     * @covers ProcessMaker\PDF\BasicStruct::getPageNumberTotal()
     * @test
     */
    public function it_should_test_the_method_getPageNumberTotal()
    {
        $this->assertTrue(is_bool($this->object->getPageNumberTotal()));
    }

    /**
     * This test the getPageNumberPositionX method.
     * @covers ProcessMaker\PDF\BasicStruct::getPageNumberPositionX()
     * @test
     */
    public function it_should_test_the_method_getPageNumberPositionX()
    {
        $this->assertTrue(is_float($this->object->getPageNumberPositionX()));
    }

    /**
     * This test the getPageNumberPositionY method.
     * @covers ProcessMaker\PDF\BasicStruct::getPageNumberPositionY()
     * @test
     */
    public function it_should_test_the_method_getPageNumberPositionY()
    {
        $this->assertTrue(is_float($this->object->getPageNumberPositionY()));
    }

    /**
     * This test the setTitle method.
     * @covers ProcessMaker\PDF\BasicStruct::setTitle()
     * @test
     */
    public function it_should_test_the_method_setTitle()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setTitle($this->faker->title));
    }

    /**
     * This test the setTitleFontSize method.
     * @covers ProcessMaker\PDF\BasicStruct::setTitleFontSize()
     * @test
     */
    public function it_should_test_the_method_setTitleFontSize()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setTitleFontSize($this->faker->randomFloat()));
    }

    /**
     * This test the setTitleFontPositionX method.
     * @covers ProcessMaker\PDF\BasicStruct::setTitleFontPositionX()
     * @test
     */
    public function it_should_test_the_method_setTitleFontPositionX()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setTitleFontPositionX($this->faker->randomFloat()));
    }

    /**
     * This test the setTitleFontPositionY method.
     * @covers ProcessMaker\PDF\BasicStruct::setTitleFontPositionY()
     * @test
     */
    public function it_should_test_the_method_setTitleFontPositionY()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setTitleFontPositionY($this->faker->randomFloat()));
    }

    /**
     * This test the setLogo method.
     * @covers ProcessMaker\PDF\BasicStruct::setLogo()
     * @test
     */
    public function it_should_test_the_method_setLogo()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setLogo($this->faker->word));
    }

    /**
     * This test the setLogoWidth method.
     * @covers ProcessMaker\PDF\BasicStruct::setLogoWidth()
     * @test
     */
    public function it_should_test_the_method_setLogoWidth()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setLogoWidth($this->faker->randomFloat()));
    }

    /**
     * This test the setLogoPositionX method.
     * @covers ProcessMaker\PDF\BasicStruct::setLogoPositionX()
     * @test
     */
    public function it_should_test_the_method_setLogoPositionX()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setLogoPositionX($this->faker->randomFloat()));
    }

    /**
     * This test the setLogoPositionY method.
     * @covers ProcessMaker\PDF\BasicStruct::setLogoPositionY()
     * @test
     */
    public function it_should_test_the_method_setLogoPositionY()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setLogoPositionY($this->faker->randomFloat()));
    }

    /**
     * This test the setPageNumber method.
     * @covers ProcessMaker\PDF\BasicStruct::setPageNumber()
     * @test
     */
    public function it_should_test_the_method_setPageNumber()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setPageNumber($this->faker->boolean));
    }

    /**
     * This test the setPageNumberTitle method.
     * @covers ProcessMaker\PDF\BasicStruct::setPageNumberTitle()
     * @test
     */
    public function it_should_test_the_method_setPageNumberTitle()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setPageNumberTitle($this->faker->title));
    }

    /**
     * This test the setPageNumberTotal method.
     * @covers ProcessMaker\PDF\BasicStruct::setPageNumberTotal()
     * @test
     */
    public function it_should_test_the_method_setPageNumberTotal()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setPageNumberTotal($this->faker->boolean));
    }

    /**
     * This test the setPageNumberPositionX method.
     * @covers ProcessMaker\PDF\BasicStruct::setPageNumberPositionX()
     * @test
     */
    public function it_should_test_the_method_setPageNumberPositionX()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setPageNumberPositionX($this->faker->randomFloat()));
    }

    /**
     * This test the setPageNumberPositionY method.
     * @covers ProcessMaker\PDF\BasicStruct::setPageNumberPositionY()
     * @test
     */
    public function it_should_test_the_method_setPageNumberPositionY()
    {
        $this->faker->numberBetween(400, 500);
        $this->assertEmpty($this->object->setPageNumberPositionY($this->faker->randomFloat()));
    }

}
