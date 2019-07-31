<?php

namespace Tests\unit\workflow\engine\classes\model;

use IsoCountry;
use Tests\TestCase;

class IsoCountryTest extends TestCase
{
    /**
     * It tests the search for an isocountry
     *
     * @test
     */
    public function it_should_search_for_an_iso_country()
    {
        //Call the findById method
        $res = IsoCountry::findById('BO');
        //Assert the result is the expected
        $this->assertEquals('Bolivia', $res['IC_NAME']);

        //Call the findById method
        $res = IsoCountry::findById('DE');
        //Assert the result is the expected
        $this->assertEquals('Germany', $res['IC_NAME']);

    }

    /**
     * It tests the result is null if the country does not exist
     *
     * @test
     */
    public function it_should_return_null_if_the_country_does_not_exist()
    {
        //Call the findById method
        $res = IsoCountry::findById('ZZ');
        //Assert the result is null
        $this->assertNull($res);
    }
}