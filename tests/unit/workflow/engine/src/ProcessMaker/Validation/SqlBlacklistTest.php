<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Validation;

use Exception;
use ProcessMaker\Validation\SqlBlacklist;
use Tests\TestCase;

class SqlBlacklistTest extends TestCase
{
    /**
     * Property $sqlBlacklist
     * @var object
     */
    private $sqlBlacklist;

    /**
     * Property $content.
     * @var string
     */
    private $content;

    /**
     * Method setUp.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->content = "";
        $path = PATH_CONFIG . 'execute-query-blacklist.ini';
        if (file_exists($path)) {
            $this->content = file_get_contents($path);
        }
    }

    /**
     * Method tearDown.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $path = PATH_CONFIG . 'execute-query-blacklist.ini';
        if (file_exists($path)) {
            file_put_contents($path, $this->content);
        }
    }

    /**
     * This test the getConfigValues method.
     * @test
     * @covers \ProcessMaker\Validation\SqlBlacklist::getConfigValues()
     */
    public function it_should_test_getConfigValues_method()
    {
        $this->sqlBlacklist = new SqlBlacklist();
        $result = $this->sqlBlacklist->getConfigValues();

        //asserts
        $this->assertArrayHasKey('tables', $result);
        $this->assertArrayHasKey('statements', $result);
        $this->assertArrayHasKey('pmtables', $result);
    }

    /**
     * This test the validate method when restricted system tables.
     * @test
     * @covers \ProcessMaker\Validation\SqlBlacklist::validate()
     */
    public function it_should_test_validate_method_when_restricted_system_tables()
    {
        //assert exception
        $this->expectException(Exception::class);

        $sql = "INSERT INTO APPLICATION (c1,c2,c3) values('', '', '')";
        $this->sqlBlacklist = new SqlBlacklist($sql);
        $this->sqlBlacklist->validate();
    }

    /**
     * This test the validate method when restricted queries.
     * @test
     * @covers \ProcessMaker\Validation\SqlBlacklist::validate()
     */
    public function it_should_test_validate_method_when_restricted_queries()
    {
        //assert exception
        $this->expectException(Exception::class);

        $path = PATH_CONFIG . 'execute-query-blacklist.ini';
        $content = ""
            . "queries  = \"INSERT|UPDATE|REPLACE|DELETE|SHOW\"\n\n"
            . "pmtables = \"PMT_TEST\"\n";
        file_put_contents($path, $content);

        $sql = "SHOW tables";
        $this->sqlBlacklist = new SqlBlacklist($sql);
        $this->sqlBlacklist->validate();
    }

    /**
     * This test the validate method when restricted pmtables.
     * @test
     * @covers \ProcessMaker\Validation\SqlBlacklist::validate()
     */
    public function it_should_test_validate_method_when_restricted_pmtables()
    {
        //assert exception
        $this->expectException(Exception::class);

        $path = PATH_CONFIG . 'execute-query-blacklist.ini';
        $content = ""
            . "queries  = \"INSERT|UPDATE|REPLACE|DELETE|SHOW\"\n\n"
            . "pmtables = \"PMT_TEST\"\n";
        file_put_contents($path, $content);

        $sql = "INSERT INTO PMT_TEST (c1,c2,c3) values('', '', '')";
        $this->sqlBlacklist = new SqlBlacklist($sql);
        $this->sqlBlacklist->validate();
    }
}
