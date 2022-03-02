<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Tests\TestCase;

/**
 * Test the pmSqlEscape() function
 */
class PmSqlEscapeTest extends TestCase
{
    /**
     * This tests the "pmSqlEscape"
     * @test
     */
    public function it_get_sql_escape()
    {
        $value = 'select uid from user';
        $result = pmSqlEscape($value);
        $this->assertEquals($result, $value);
    }
}
