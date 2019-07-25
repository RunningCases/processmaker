<?php

namespace Tests\unit\workflow\engine\classes;

use Configurations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\User;
use Tests\TestCase;

class ConfigurationsTest extends TestCase
{
    use DatabaseTransactions;
    private $filters = [];

    /**
     * Define values of some parameters of the test
     */
    protected function setUp()
    {
        //Define filters
        $filters = [];
        $filters['category'] = ''; //Dropdown: Category id
        $filters['columnSearch'] = 'APP_TITLE'; //Dropdown: filter by value
        $filters['dateFrom'] = '2019-07-01'; //Date picker
        $filters['dateTo'] = '2020-07-01'; //Date picker
        $filters['dir'] = 'DESC';
        $filters['limit'] = 15;
        $filters['filterStatus'] = 3; //Dropdown: Status id
        $filters['process'] = ''; //Suggest: Process id
        $filters['process_label'] = ''; //Suggest: Process label
        $filters['search'] = ''; //Text search
        $filters['sort'] = 'APP_NUMBER';
        $filters['start'] = 0;
        $filters['user'] = ''; //Suggest: User id
        $filters['user_label'] = ''; //Suggest: User label

        $this->filters['advanced'] = $filters;
    }

    /**
     * Review the user preferences when the user does not save filters
     * @covers Configurations::getUserPreferences
     * @test
     */
    public function it_should_return_default_filters()
    {
        $user = factory(User::class)->create();
        $configuration = new Configurations();

        //Get the user preferences
        $response = $configuration->getUserPreferences('FILTERS', $user->USR_UID);

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
        //Define a user
        $user = factory(User::class)->create();

        //Save the configuration defined
        $configuration = new Configurations();
        $configuration->aConfig['FILTERS'] = $this->filters;
        $configuration->saveConfig('USER_PREFERENCES', '', '', $user->USR_UID);

        //Get the user preferences
        $response = $configuration->getUserPreferences('FILTERS', $user->USR_UID);

        //Compare filters
        $this->assertEquals($response, $this->filters);
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