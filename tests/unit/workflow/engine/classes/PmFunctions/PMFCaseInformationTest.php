<?php
namespace Tests\unit\workflow\engine\classes\PmFunctions;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\TestCase;

/**
 * Test the PMFCaseInformation() function
 *
 * @link http://wiki.processmaker.com/index.php/ProcessMaker_Functions#PMFCaseInformation.28.29
 */
class PMFCaseInformation extends TestCase
{
    use DatabaseTransactions;

    /**
     * Method set up.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * It tests the PMFCaseInformation() function with the default parameters
     * 
     * @test
     */
    public function it_should_test_this_pmfunction_default_parameters()
    {
        $table = factory(Application::class)->states('foreign_keys')->create();
        // Force commit for propel
        DB::commit();
        // Call the funtion
        $result = PMFCaseInformation($table->APP_UID, 0, 0);
        $this->assertNotEmpty($result);
        // Colums to return with the default parameters
        $this->assertArrayHasKey('APP_UID', $result);
        $this->assertArrayHasKey('APP_NUMBER', $result);
        $this->assertArrayHasKey('APP_STATUS', $result);
        $this->assertArrayHasKey('APP_STATUS_ID', $result);
        $this->assertArrayHasKey('PRO_UID', $result);
        $this->assertArrayHasKey('APP_INIT_USER', $result);
        $this->assertArrayHasKey('APP_CUR_USER', $result);
        $this->assertArrayHasKey('APP_CREATE_DATE', $result);
        $this->assertArrayHasKey('APP_INIT_DATE', $result);
        $this->assertArrayHasKey('APP_FINISH_DATE', $result);
        $this->assertArrayHasKey('APP_UPDATE_DATE', $result);
        $this->assertArrayHasKey('PRO_ID', $result);
        $this->assertArrayHasKey('APP_INIT_USER_ID', $result);
        // When the index = 0 those values will not return
        $this->assertArrayNotHasKey('DEL_INDEX', $result);
        $this->assertArrayNotHasKey('DEL_PREVIOUS', $result);
        $this->assertArrayNotHasKey('DEL_TYPE', $result);
        $this->assertArrayNotHasKey('DEL_PRIORITY', $result);
        $this->assertArrayNotHasKey('DEL_THREAD_STATUS', $result);
        $this->assertArrayNotHasKey('DEL_THREAD', $result);
        $this->assertArrayNotHasKey('DEL_DELEGATE_DATE', $result);
        $this->assertArrayNotHasKey('DEL_INIT_DATE', $result);
        $this->assertArrayNotHasKey('DEL_TASK_DUE_DATE', $result);
        $this->assertArrayNotHasKey('DEL_FINISH_DATE', $result);
        // When the returnAppData = 0, false this value will not return
        $this->assertArrayNotHasKey('APP_DATA', $result);
    }

    /**
     * It tests the PMFCaseInformation() function with index parameter
     * 
     * @test
     */
    public function it_should_test_this_pmfunction_index_parameter()
    {
        $application = factory(Application::class)->states('todo')->create();
        $table = factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        // Force commit for propel
        DB::commit();
        // Call the funtion
        $result = PMFCaseInformation($table->APP_UID, $table->DEL_INDEX, 0);
        $this->assertNotEmpty($result);
        // When the index != 0 those values will return
        $this->assertArrayHasKey('DEL_INDEX', $result);
        $this->assertArrayHasKey('DEL_PREVIOUS', $result);
        $this->assertArrayHasKey('DEL_TYPE', $result);
        $this->assertArrayHasKey('DEL_PRIORITY', $result);
        $this->assertArrayHasKey('DEL_THREAD_STATUS', $result);
        $this->assertArrayHasKey('DEL_THREAD', $result);
        $this->assertArrayHasKey('DEL_DELEGATE_DATE', $result);
        $this->assertArrayHasKey('DEL_INIT_DATE', $result);
        $this->assertArrayHasKey('DEL_TASK_DUE_DATE', $result);
        $this->assertArrayHasKey('DEL_FINISH_DATE', $result);
    }

    /**
     * It tests the PMFCaseInformation() function with returnAppData parameter
     * 
     * @test
     */
    public function it_should_test_this_pmfunction_app_data_parameter()
    {
        $application = factory(Application::class)->states('todo')->create();
        $table = factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
        ]);
        // Force commit for propel
        DB::commit();
        // Call the funtion
        $result = PMFCaseInformation($table->APP_UID, 0, true);
        $this->assertNotEmpty($result);
        // When the returnAppData = true, the case data will return
        $this->assertArrayHasKey('APP_DATA', $result);
    }

    /**
     * It tests the exception caseUid is required in the PMFCaseInformation() function
     * 
     * @test
     */
    public function it_should_test_exception_user_required()
    {
        $this->expectExceptionMessage('**ID_REQUIRED_FIELD**');
        $result = PMFCaseInformation('', 0, 0);
    }
}