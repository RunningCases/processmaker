<?php

namespace Tests\unit\workflow\engine\classes;

use Configurations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Configuration;
use Tests\TestCase;

class ConfigurationsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Review the user preferences when the user does not save filters
     * @covers Configurations::getUserPreferences
     * @test
     */
    public function it_should_return_empty_preferences()
    {
        //Define a user preferences empty
        $configuration = Configuration::factory()->userPreferencesEmpty()->create();

        //Get the user preferences
        $conf = new Configurations();
        $response = $conf->getUserPreferences('FILTERS', $configuration->USR_UID);

        //Compare filters
        $this->assertEquals($response, ['advanced' => []]);
    }

    /**
     * Review the user preferences when the user save filters
     * @covers Configurations::getUserPreferences
     * @test
     */
    public function it_should_return_filters_saved()
    {
        //Define a user preferences related to the advanced search
        $conf = new Configurations();
        $filter = [];
        $filter['category'] = ''; //Dropdown: Category id
        $filter['columnSearch'] = 'APP_TITLE'; //Dropdown: filter by value
        $filter['dateFrom'] = '2019-07-01'; //Date picker
        $filter['dateTo'] = '2020-07-01'; //Date picker
        $filter['dir'] = 'DESC';
        $filter['limit'] = 15;
        $filter['filterStatus'] = 3; //Dropdown: Status id
        $filter['process'] = ''; //Suggest: Process id
        $filter['process_label'] = ''; //Suggest: Process label
        $filter['search'] = ''; //Text search
        $filter['sort'] = 'APP_NUMBER';
        $filter['start'] = 0;
        $filter['user'] = ''; //Suggest: User id
        $filter['user_label'] = ''; //Suggest: User label
        $filters['advanced'] = $filter;

        //Save the user preferences
        $conf->aConfig['FILTERS']['advanced'] = $filter;
        $conf->saveConfig('USER_PREFERENCES', '', '', '00000000000000000000000000000001');
        $response = $conf->getUserPreferences('FILTERS', '00000000000000000000000000000001');

        //Compare filters
        $this->assertEquals($response, $filters);
        //Review if some keys exist
        $this->assertArrayHasKey('category', $response['advanced']);
        $this->assertArrayHasKey('columnSearch', $response['advanced']);
        $this->assertArrayHasKey('dateFrom', $response['advanced']);
        $this->assertArrayHasKey('dateTo', $response['advanced']);
        $this->assertArrayHasKey('dir', $response['advanced']);
        $this->assertArrayHasKey('limit', $response['advanced']);
        $this->assertArrayHasKey('filterStatus', $response['advanced']);
        $this->assertArrayHasKey('process', $response['advanced']);
        $this->assertArrayHasKey('process_label', $response['advanced']);
        $this->assertArrayHasKey('search', $response['advanced']);
        $this->assertArrayHasKey('sort', $response['advanced']);
        $this->assertArrayHasKey('start', $response['advanced']);
        $this->assertArrayHasKey('user', $response['advanced']);
        $this->assertArrayHasKey('user_label', $response['advanced']);
    }
}
