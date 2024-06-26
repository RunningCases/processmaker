<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel\Cases;

use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Cases\Home;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\AppDelay;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\CaseList;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Fields;
use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use Tests\TestCase;

/**
 * Class HomeTest
 * 
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Home
 */
class HomeTest extends TestCase
{
    /**
     * This method is called before the first test of this test class is run.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::truncateNonInitialModels();
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::truncateNonInitialModels();
    }

    /**
     * setUp method.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * This test the getDraft method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getUserId()
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getDraft()
     */
    public function it_should_test_getDraft()
    {
        $application = Application::factory()->draft()->create();
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getDraft();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * This test the getInbox method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getInbox()
     */
    public function it_should_test_getInbox()
    {
        $application = Application::factory()->todo()->create();
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getInbox();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * This test the getUnassigned method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getUnassigned()
     */
    public function it_should_test_getUnassigned()
    {
        $user = User::factory()->create();
        $group = Groupwf::factory()->create();
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        $process = Process::factory()->create();
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year"))
        ]);

        $home = new Home($user->USR_UID);
        $result = $home->getUnassigned();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * This test the getPaused method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getPaused()
     */
    public function it_should_test_getPaused()
    {
        $user = User::factory()->create();
        $process1 = Process::factory()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();
        $delegation1 = Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);

        $process2 = Process::factory()->create();
        $application2 = Application::factory()->create();
        $delegation2 = Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);

        $home = new Home($user->USR_UID);
        $result = $home->getPaused();

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * This test the buildCustomCaseList method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::buildCustomCaseList()
     */
    public function it_should_test_buildCustomCaseList()
    {
        $user = User::factory()->create();
        $additionalTables = AdditionalTables::factory()->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`)"
            . ")ENGINE=InnoDB  DEFAULT CHARSET='utf8'";
        DB::statement($query);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        //for inbox
        $type = 'inbox';
        $caseList = CaseList::factory()->create([
            'CAL_TYPE' => $type,
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'USR_ID' => $user->USR_ID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 15, 0, '', '', 'APP_NUMBER,DESC'];
        $defaultColumns = CaseList::formattingColumns($type, '', []);

        $home = new Home($user->USR_UID);
        $home->buildCustomCaseList($type, $caseList->CAL_ID, $arguments, $defaultColumns);

        $this->assertTrue(is_callable(array_pop($arguments)));
    }

    /**
     * This test the getCustomDraft method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getCustomDraft()
     */
    public function it_should_test_getCustomDraft()
    {
        $additionalTables = AdditionalTables::factory()->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`)"
            . ")ENGINE=InnoDB  DEFAULT CHARSET='utf8'";
        DB::statement($query);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $application = Application::factory()->draft()->create();
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        $caseList = CaseList::factory()->create([
            'CAL_TYPE' => 'draft',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 0, 15, 0, '', '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getCustomDraft(...$arguments);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('columns', $result);
    }

    /**
     * This test the getCustomInbox method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getCustomInbox()
     */
    public function it_should_test_getCustomInbox()
    {
        $additionalTables = AdditionalTables::factory()->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`)"
            . ")ENGINE=InnoDB  DEFAULT CHARSET='utf8'";
        DB::statement($query);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $application = Application::factory()->todo()->create();
        Delegation::factory()->foreign_keys()->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);

        $caseList = CaseList::factory()->create([
            'CAL_TYPE' => 'inbox',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 0, 15, 0, '', '', '', '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getCustomInbox(...$arguments);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('columns', $result);
    }

    /**
     * This test the getCustomUnassigned method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getCustomUnassigned()
     */
    public function it_should_test_getCustomUnassigned()
    {
        $additionalTables = AdditionalTables::factory()->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`)"
            . ")ENGINE=InnoDB  DEFAULT CHARSET='utf8'";
        DB::statement($query);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $user = User::factory()->create();
        $group = Groupwf::factory()->create();
        GroupUser::factory()->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        $process = Process::factory()->create();
        $application = Application::factory()->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        Delegation::factory()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:i:s', strtotime("-1 year"))
        ]);

        $caseList = CaseList::factory()->create([
            'CAL_TYPE' => 'unassigned',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 0, 15, 0, '', '', '', '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getCustomUnassigned(...$arguments);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('columns', $result);
    }

    /**
     * This test the getCustomPaused method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getCustomPaused()
     */
    public function it_should_test_getCustomPaused()
    {
        $additionalTables = AdditionalTables::factory()->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`)"
            . ")ENGINE=InnoDB  DEFAULT CHARSET='utf8'";
        DB::statement($query);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        Fields::factory()->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $user = User::factory()->create();
        $process1 = Process::factory()->create();
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = Application::factory()->create();
        $delegation1 = Delegation::factory()->create([
            'APP_NUMBER' => $application1->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'CLOSED',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process1->PRO_ID,
            'PRO_UID' => $process1->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);

        $process2 = Process::factory()->create();
        $application2 = Application::factory()->create();
        $delegation2 = Delegation::factory()->create([
            'APP_NUMBER' => $application2->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID,
            'PRO_ID' => $process2->PRO_ID,
            'PRO_UID' => $process2->PRO_UID,
            'DEL_PREVIOUS' => 1,
            'DEL_INDEX' => 2
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        AppDelay::factory(5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);

        $caseList = CaseList::factory()->create([
            'CAL_TYPE' => 'paused',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 0, 15, 0, '', '', '', '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application1->APP_INIT_USER);
        $result = $home->getCustomPaused(...$arguments);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('columns', $result);
    }
}
