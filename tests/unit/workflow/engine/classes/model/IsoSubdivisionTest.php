<?php

namespace Tests\unit\workflow\engine\classes\model;

use IsoSubdivision;
use Tests\TestCase;

class IsoSubdivisionTest extends TestCase
{
    /**
     * It tests the search for an iso subdivision
     *
     * @test
     */
    public function it_should_search_for_an_iso_subdivision()
    {
        //Call the findById method
        $res = IsoSubdivision::findById('BO', 'L');
        //Assert the result is the expected
        $this->assertEquals('La Paz', $res['IS_NAME']);

        //Call the findById method
        $res = IsoSubdivision::findById('DE', 'BE');
        //Assert the result is the expected
        $this->assertEquals('Berlin', $res['IS_NAME']);

    }

    /**
     * It tests the result is null if the subdivision does not exist
     *
     * @test
     */
    public function it_should_return_null_if_the_subdivision_does_not_exist()
    {
        //Call the findById method
        $res = IsoSubdivision::findById('ZZ', 'ZZ');
        //Assert the result is null
        $this->assertNull($res);
    }
}