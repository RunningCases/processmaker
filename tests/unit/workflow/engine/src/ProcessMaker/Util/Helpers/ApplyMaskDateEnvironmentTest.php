<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Util\Helpers;

use Tests\TestCase;

class ApplyMaskDateEnvironmentTest extends TestCase
{
    /**
     * Check if the mask was applied correctly
     *
     * @test
     */
    public function it_should_apply_mask_in_dates()
    {
        $date1 = date("2020-11-12 09:09:10");
        $expected = '2020/11/12';
        $this->assertEquals($expected, applyMaskDateEnvironment($date1, 'Y/m/d'));
    }
}