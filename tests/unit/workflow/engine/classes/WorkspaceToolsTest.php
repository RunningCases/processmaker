<?php

use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use Tests\CreateTestSite;
use Tests\TestCase;

class WorkspaceToolsTest extends TestCase
{
    use CreateTestSite;
    public $workspace;

    /**
     * Set up method.
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();

        config(["system.workspace" => "new_site"]);
        $this->workspace = config("system.workspace");
        $this->createDBFile($this->workspace);
    }

    /**
     * Tests the updateIsoCountry method
     * 
     * @covers \WorkspaceTools::updateIsoCountry
     * @test
     */
    public function it_should_test_upgrade_Iso_Country_method()
    {
        $workspaceTools = new WorkspaceTools($this->workspace);
        $workspaceTools->updateIsoCountry();

        $result = ob_get_contents();
        $this->assertMatchesRegularExpression("/-> Update table ISO_COUNTRY Done/", $result);

        $res = IsoCountry::findById('CI');
        // Assert the result is the expected
        $this->assertEquals('Côte d\'Ivoire', $res['IC_NAME']);
    }

    /**
     * Tests the migrateCaseTitleToThreads method
     * 
     * @covers \WorkspaceTools::migrateCaseTitleToThreads
     * @test
     */
    public function it_should_test_migrate_case_title_to_threads_method()
    {
        $application1 = Application::factory()->create([
            'APP_STATUS' => 'TO_DO',
            'APP_STATUS_ID' => 2,
        ]);
        $application2 = Application::factory()->create([
            'APP_STATUS' => 'COMPLETED',
            'APP_STATUS_ID' => 3,
        ]);
        $application3 = Application::factory()->create([
            'APP_STATUS' => 'CANCELED',
            'APP_STATUS_ID' => 4,
        ]);

        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'DEL_TITLE' => $application1->APP_TITLE,
            'DEL_INDEX' => 1
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'DEL_TITLE' => $application1->APP_TITLE,
            'DEL_INDEX' => 2
        ]);
        $delegation1 = Delegation::factory()->create([
            'APP_UID' => $application1->APP_UID,
            'APP_NUMBER' => $application1->APP_NUMBER,
            'DEL_TITLE' => $application1->APP_TITLE,
            'DEL_INDEX' => 3,
        ]);

        Delegation::factory()->create([
            'APP_UID' => $application2->APP_UID,
            'APP_NUMBER' => $application2->APP_NUMBER,
            'DEL_TITLE' => $application2->APP_TITLE,
            'DEL_INDEX' => 1
        ]);
        $delegation2 = Delegation::factory()->create([
            'APP_UID' => $application2->APP_UID,
            'APP_NUMBER' => $application2->APP_NUMBER,
            'DEL_TITLE' => $application2->APP_TITLE,
            'DEL_INDEX' => 2,
            'DEL_LAST_INDEX' => 1
        ]);

        Delegation::factory()->create([
            'APP_UID' => $application3->APP_UID,
            'APP_NUMBER' => $application3->APP_NUMBER,
            'DEL_TITLE' => $application3->APP_TITLE,
            'DEL_INDEX' => 1
        ]);
        $delegation3 = Delegation::factory()->create([
            'APP_UID' => $application3->APP_UID,
            'APP_NUMBER' => $application3->APP_NUMBER,
            'DEL_TITLE' => $application3->APP_TITLE,
            'DEL_INDEX' => 2,
            'DEL_LAST_INDEX' => 1
        ]);

        if (!defined('DB_RBAC_USER')) {
            define('DB_RBAC_USER', DB_USER);
        }
        if (!defined('DB_RBAC_PASS')) {
            define('DB_RBAC_PASS', DB_PASS);
        }
        if (!defined('DB_RBAC_HOST')) {
            define('DB_RBAC_HOST', DB_HOST);
        }
        if (!defined('DB_RBAC_NAME')) {
            define('DB_RBAC_NAME', DB_NAME);
        }
        if (!defined('DB_REPORT_USER')) {
            define('DB_REPORT_USER', DB_USER);
        }
        if (!defined('DB_REPORT_PASS')) {
            define('DB_REPORT_PASS', DB_PASS);
        }
        if (!defined('DB_REPORT_HOST')) {
            define('DB_REPORT_HOST', DB_HOST);
        }
        if (!defined('DB_REPORT_NAME')) {
            define('DB_REPORT_NAME', DB_NAME);
        }
        ob_start();
        $workspaceTools = new WorkspaceTools('');
        $workspaceTools->migrateCaseTitleToThreads(['testexternal']);
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertMatchesRegularExpression("/The Case Title has been updated successfully in APP_DELEGATION table./", $result);

        $r = Delegation::select('DEL_TITLE')->where('DELEGATION_ID', $delegation1->DELEGATION_ID)->get()->values()->toArray();
        $this->assertEquals($r[0]['DEL_TITLE'], $application1->APP_TITLE);

        $r = Delegation::select('DEL_TITLE')->where('DELEGATION_ID', $delegation2->DELEGATION_ID)->get()->values()->toArray();
        $this->assertEquals($r[0]['DEL_TITLE'], $application2->APP_TITLE);

        $r = Delegation::select('DEL_TITLE')->where('DELEGATION_ID', $delegation3->DELEGATION_ID)->get()->values()->toArray();
        $this->assertEquals($r[0]['DEL_TITLE'], $application3->APP_TITLE);
    }
}
