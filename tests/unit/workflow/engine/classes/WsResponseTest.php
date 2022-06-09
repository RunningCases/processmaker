<?php

use Tests\TestCase;

class WsResponseTest extends TestCase
{
    private $wsResponse;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * This test get a extra param.
     * @test
     * @covers \WsResponse::__construct
     * @covers \WsResponse::getExtraParam
     */
    public function it_should_test_get_extra_param()
    {
        $this->wsResponse = new WsResponse(0, '');

        //assert
        $actual = $this->wsResponse->getExtraParam('');
        $this->assertEmpty($actual);

        //assert
        $actual = $this->wsResponse->getExtraParam('test');
        $this->assertEmpty($actual);

        //assert
        $expected = 'test';
        $this->wsResponse->addExtraParam('test', $expected);
        $actual = $this->wsResponse->getExtraParam('test');
        $this->assertEquals($expected, $actual);
    }

    /**
     * This test the add extra param.
     * @test
     * @covers \WsResponse::addExtraParam
     */
    public function it_should_test_add_extra_param()
    {
        $this->wsResponse = new WsResponse(0, '');

        //assert
        $expected = 'test';
        $this->wsResponse->addExtraParam('test', $expected);
        $actual = $this->wsResponse->getExtraParam('test');
        $this->assertEquals($expected, $actual);
    }

    /**
     * This test a get payload string.
     * @test
     * @covers \WsResponse::getPayloadString
     */
    public function it_should_test_get_payload_string()
    {
        $this->wsResponse = new WsResponse(0, '');

        //assert
        $actual = $this->wsResponse->getPayloadString('test');
        $this->assertContains('test', $actual);
    }

    /**
     * @test
     * @covers \WsResponse::getPayloadArray
     */
    public function it_should_test_payload_array()
    {
        $this->wsResponse = new WsResponse(0, '');

        //assert
        $actual = $this->wsResponse->getPayloadArray();
        $this->assertArrayHasKey('status_code', $actual);
        $this->assertArrayHasKey('message', $actual);
        $this->assertArrayHasKey('timestamp', $actual);
    }
}
