<?php

namespace Tests\unit\workflow\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class ChangeAbbreviationOfDirectivesTest extends TestCase
{
    /**
     * Provider to define different types of configurations in the php.ini and the result expected
     */
    public function provider()
    {
        return [
            ['1024K','1024KB'],
            ['600M','600MB'],
            ['5G','5GB'],
            ['10T','10Bytes'],
            ['250','250Bytes']
        ];
    }

    /**
     * Check if the function is changed correctly the possibles directives defined in the php.ini
     *
     * @link https://www.php.net/manual/es/faq.using.php#faq.using.shorthandbytes
     *
     * @param string $configuration
     * @param string $expected
     *
     * @dataProvider provider
     * @test
     */
    public function it_should_change_abbreviation_of_directives($configuration, $expected)
    {
        $this->assertEquals($expected, changeAbbreviationOfDirectives($configuration));
    }
}