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
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases\Home
 */
class HomeTest extends TestCase
{

    /**
     * setUp method.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * This test the getDraft method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getDraft()
     */
    public function it_should_test_getDraft()
    {
        $application = factory(Application::class)->states('draft')->create();
        factory(Delegation::class)->states('foreign_keys')->create([
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
        $application = factory(Application::class)->states('todo')->create();
        factory(Delegation::class)->states('foreign_keys')->create([
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
        $user = factory(User::class)->create();
        $group = factory(Groupwf::class)->create();
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        $process = factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-1 year"))
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
        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create();
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = factory(Application::class)->create();
        $delegation1 = factory(Delegation::class)->create([
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

        $process2 = factory(Process::class)->create();
        $application2 = factory(Application::class)->create();
        $delegation2 = factory(Delegation::class)->create([
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
        factory(AppDelay::class, 5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        factory(AppDelay::class, 5)->create([
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
        $user = factory(User::class)->create();
        $additionalTables = factory(AdditionalTables::class)->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`))";
        DB::statement($query);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        //for inbox
        $type = 'inbox';
        $caseList = factory(CaseList::class)->create([
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
        $additionalTables = factory(AdditionalTables::class)->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`))";
        DB::statement($query);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $application = factory(Application::class)->states('draft')->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 1,
            'USR_UID' => $application->APP_INIT_USER,
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
        ]);

        $caseList = factory(CaseList::class)->create([
            'CAL_TYPE' => 'draft',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 15, 0, '', '', 'APP_NUMBER,DESC'];

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
        $additionalTables = factory(AdditionalTables::class)->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`))";
        DB::statement($query);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $application = factory(Application::class)->states('todo')->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_INDEX' => 2
        ]);

        $caseList = factory(CaseList::class)->create([
            'CAL_TYPE' => 'inbox',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 15, 0, '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getCustomDraft(...$arguments);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('columns', $result);
    }

    /**
     * This test the getCustomUnassigned method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Cases\Home::getCustomUnassigned()
     */
    public function it_should_test_getCustomUnassignedt()
    {
        $additionalTables = factory(AdditionalTables::class)->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`))";
        DB::statement($query);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $user = factory(User::class)->create();
        $group = factory(Groupwf::class)->create();
        factory(GroupUser::class)->create([
            'GRP_UID' => $group->GRP_UID,
            'GRP_ID' => $group->GRP_ID,
            'USR_UID' => $user->USR_UID,
        ]);
        $process = factory(Process::class)->create();
        $application = factory(Application::class)->create([
            'APP_STATUS_ID' => 2
        ]);
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        factory(TaskUser::class)->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $user->USR_UID,
            'TU_RELATION' => 1,
            'TU_TYPE' => 1
        ]);
        factory(Delegation::class)->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'TAS_ID' => $task->TAS_ID,
            'PRO_ID' => $process->PRO_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0,
            'DEL_DELEGATE_DATE' => date('Y-m-d H:m:s', strtotime("-1 year"))
        ]);

        $caseList = factory(CaseList::class)->create([
            'CAL_TYPE' => 'unassigned',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 15, 0, '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application->APP_INIT_USER);
        $result = $home->getCustomDraft(...$arguments);

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
        $additionalTables = factory(AdditionalTables::class)->create();
        $query = ""
            . "CREATE TABLE IF NOT EXISTS `{$additionalTables->ADD_TAB_NAME}` ("
            . "`APP_UID` varchar(32) NOT NULL,"
            . "`APP_NUMBER` int(11) NOT NULL,"
            . "`APP_STATUS` varchar(10) NOT NULL,"
            . "`VAR1` varchar(255) DEFAULT NULL,"
            . "`VAR2` varchar(255) DEFAULT NULL,"
            . "`VAR3` varchar(255) DEFAULT NULL,"
            . "PRIMARY KEY (`APP_UID`),"
            . "KEY `indexTable` (`APP_UID`))";
        DB::statement($query);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR1'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR2'
        ]);
        factory(Fields::class)->create([
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID,
            'FLD_NAME' => 'VAR3'
        ]);

        $user = factory(User::class)->create();
        $process1 = factory(Process::class)->create();
        $task = factory(Task::class)->create([
            'TAS_ASSIGN_TYPE' => '',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process1->PRO_UID,
            'TAS_TYPE' => 'NORMAL'
        ]);

        $application1 = factory(Application::class)->create();
        $delegation1 = factory(Delegation::class)->create([
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

        $process2 = factory(Process::class)->create();
        $application2 = factory(Application::class)->create();
        $delegation2 = factory(Delegation::class)->create([
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
        factory(AppDelay::class, 5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation1->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation1->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);
        factory(AppDelay::class, 5)->create([
            'APP_DELEGATION_USER' => $user->USR_UID,
            'PRO_UID' => $process2->PRO_UID,
            'APP_NUMBER' => $delegation2->APP_NUMBER,
            'APP_DEL_INDEX' => $delegation2->DEL_INDEX,
            'APP_DISABLE_ACTION_USER' => 0,
            'APP_TYPE' => 'PAUSE'
        ]);

        $caseList = factory(CaseList::class)->create([
            'CAL_TYPE' => 'paused',
            'CAL_COLUMNS' => '[{"field":"case_number","enableFilter":false,"set":true},{"field":"case_title","enableFilter":false,"set":true},{"field":"process_name","enableFilter":false,"set":true},{"field":"task","enableFilter":false,"set":true},{"field":"send_by","enableFilter":false,"set":true},{"field":"due_date","enableFilter":false,"set":true},{"field":"delegation_date","enableFilter":false,"set":true},{"field":"priority","enableFilter":false,"set":true},{"field":"VAR1","enableFilter":false,"set":true},{"field":"VAR2","enableFilter":false,"set":true},{"field":"VAR3","enableFilter":false,"set":false}]',
            'ADD_TAB_UID' => $additionalTables->ADD_TAB_UID
        ]);
        $arguments = [$caseList->CAL_ID, 0, 0, 0, 15, 0, '', '', 'APP_NUMBER,DESC'];

        $home = new Home($application1->APP_INIT_USER);
        $result = $home->getCustomDraft(...$arguments);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('columns', $result);
    }
}
