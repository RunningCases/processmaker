<?php

namespace Tests\unit\workflow\engine\classes\model;

use IsoLocation;
use Tests\TestCase;

class IsoLocationTest extends TestCase
{
    /**
     * It tests the search for an iso location
     *
     * @test
     */
    public function it_should_search_for_an_iso_location()
    {
        //Call the findById method
        $res = IsoLocation::findById('BO', 'C', 'CBB');
        //Assert the result is the expected
        $this->assertEquals('Cochabamba', $res['IL_NAME']);

        //Call the findById method
        $res = IsoLocation::findById('DE', 'NW', 'DUN');
        //Assert the result is the expected
        $this->assertEquals('Dulmen', $res['IL_NAME']);

    }

    /**
     * It tests the result is null if the location does not exist
     *
     * @test
     */
    public function it_should_return_null_if_the_location_does_not_exist()
    {
        //Call the findById method
        $res = IsoLocation::findById('ZZ', 'ZZ', 'ZZ');
        //Assert the result is null
        $this->assertNull($res);
    }
}