<?php

namespace Tests\unit\workflow\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class GetMysqlVersionTest extends TestCase
{
    /**
     * Test get the mysql version
     *
     * @test
     */
    public function it_should_get_mysql_version()
    {
        $this->assertNotEmpty(getMysqlVersion());
    }
}