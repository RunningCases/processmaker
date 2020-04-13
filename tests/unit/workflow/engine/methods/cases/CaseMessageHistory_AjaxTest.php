<?php

namespace Tests\unit\workflow\engine\methods\cases;

use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppMessage;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\ObjectPermission;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\User;
use Tests\TestCase;

use function GuzzleHttp\json_decode;

class CaseMessageHistory_AjaxTest extends TestCase
{
    /**
     * This method calls the parent setUp
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * It tests the message history grid messageHistoryGridList_JXP action with no permissions configured
     * 
     * @test
     */
    public function it_shoud_test_the_message_history_grid_list_jxp_action_with_no_permissions()
    {
        $user = factory(USER::class)->create();
        $process = factory(PROCESS::class)->create();

        $application = factory(APPLICATION::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'APP_INIT_USER' => $user['USR_UID'],
            'APP_CUR_USER' => $user['USR_UID']
        ]);

        $task = factory(TASK::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'TAS_USER' => $user['USR_UID']
        ]);

        $appmessage1 = factory(APPMESSAGE::class)->create([
            'PRO_ID' => $process['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER']
        ]);

        $appmessage2 = factory(APPMESSAGE::class)->create([
            'APP_MSG_TYPE' => 'PM_FUNCTION',
            'PRO_ID' => $process['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER']
        ]);

        $_SESSION['PROCESS'] = $process['PRO_UID'];
        $_SESSION['APPLICATION'] = $application['APP_UID'];
        $_SESSION['TASK'] = $task['TAS_UID'];
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];

        $_REQUEST['actionAjax'] = "messageHistoryGridList_JXP";

        $_POST['sort'] = 'MSG_UID';

        //Turn on output buffering
        ob_start();

        require (PATH_CORE. "methods/cases/caseMessageHistory_Ajax.php");

        $result = ob_get_contents();
        ob_end_clean();
        $this->assertEquals(json_decode($result)->totalCount, 2);
    }

    /**
     * It tests the message history grid messageHistoryGridList_JXP action with view permissions configured
     * 
     * @test
     */
    public function it_shoud_test_the_message_history_grid_list_jxp_action_with_view_permission()
    {
        $user = factory(USER::class)->create();

        $processView = factory(PROCESS::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID']
        ]);

        $application = factory(APPLICATION::class)->create([
            'PRO_UID' => $processView['PRO_UID'],
            'APP_INIT_USER' => $user['USR_UID'],
            'APP_CUR_USER' => $user['USR_UID']
        ]);

        $task = factory(TASK::class)->create([
            'PRO_UID' => $processView['PRO_UID'],
            'TAS_USER' => $user['USR_UID']
        ]);

        $delegation = factory(DELEGATION::class)->create([
            'APP_UID' => $application['APP_UID'],
            'DEL_INDEX' => 0,
            'DEL_PREVIOUS' => 0,
            'PRO_UID' => $processView['PRO_UID'],
            'TAS_UID' => $task['TAS_ID'],
            'USR_UID' => $user['USR_UID'],
            'DEL_TYPE' => 'NORMAL',
            'DEL_THREAD' => 1,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PRIORITY' => 3,
        ]);

        $appmessage1 = factory(APPMESSAGE::class)->create([
            'PRO_ID' => $processView['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER'],
            'DEL_INDEX' => 0
        ]);

        $appmessage2 = factory(APPMESSAGE::class)->create([
            'APP_MSG_TYPE' => 'PM_FUNCTION',
            'PRO_ID' => $processView['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER'],
            'DEL_INDEX' => 0
        ]);

        $objectPermission = factory(OBJECTPERMISSION::class)->create([
            'PRO_UID' => $processView['PRO_UID'],
            'TAS_UID' => $task['TAS_UID'],
            'USR_UID' => $user['USR_UID'],
            'OP_ACTION' => 'VIEW'
        ]);

        $_SESSION['PROCESS'] = $processView['PRO_UID'];
        $_SESSION['APPLICATION'] = $application['APP_UID'];
        $_SESSION['TASK'] = $task['TAS_UID'];
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];

        $_REQUEST['actionAjax'] = "messageHistoryGridList_JXP";

        //Turn on output buffering
        ob_start();

        require (PATH_CORE. "methods/cases/caseMessageHistory_Ajax.php");
        $result = ob_get_contents();
        //Clean the output buffer and turn off output buffering
        ob_end_clean();
        $this->assertEquals(json_decode($result)->totalCount, 2);
    }

     /**
     * It tests the message history grid messageHistoryGridList_JXP action with resend permissions configured
     * 
     * @test
     */
    public function it_shoud_test_the_message_history_grid_list_jxp_action_with_resend_permission()
    {
        $user = factory(USER::class)->create();

        $processView = factory(PROCESS::class)->create([
            'PRO_CREATE_USER' => $user['USR_UID']
        ]);

        $application = factory(APPLICATION::class)->create([
            'PRO_UID' => $processView['PRO_UID'],
            'APP_INIT_USER' => $user['USR_UID'],
            'APP_CUR_USER' => $user['USR_UID']
        ]);

        $task = factory(TASK::class)->create([
            'PRO_UID' => $processView['PRO_UID'],
            'TAS_USER' => $user['USR_UID']
        ]);

        $delegation = factory(DELEGATION::class)->create([
            'APP_UID' => $application['APP_UID'],
            'DEL_INDEX' => 0,
            'DEL_PREVIOUS' => 0,
            'PRO_UID' => $processView['PRO_UID'],
            'TAS_UID' => $task['TAS_ID'],
            'USR_UID' => $user['USR_UID'],
            'DEL_TYPE' => 'NORMAL',
            'DEL_THREAD' => 1,
            'DEL_THREAD_STATUS' => 'OPEN',
            'DEL_PRIORITY' => 3,
        ]);

        $appmessage1 = factory(APPMESSAGE::class)->create([
            'PRO_ID' => $processView['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER'],
            'DEL_INDEX' => 0
        ]);

        $appmessage2 = factory(APPMESSAGE::class)->create([
            'APP_MSG_TYPE' => 'PM_FUNCTION',
            'PRO_ID' => $processView['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER'],
            'DEL_INDEX' => 0
        ]);

        $objectPermission = factory(OBJECTPERMISSION::class)->create([
            'PRO_UID' => $processView['PRO_UID'],
            'TAS_UID' => $task['TAS_UID'],
            'USR_UID' => $user['USR_UID'],
            'OP_ACTION' => 'RESEND'
        ]);

        $_SESSION['PROCESS'] = $processView['PRO_UID'];
        $_SESSION['APPLICATION'] = $application['APP_UID'];
        $_SESSION['TASK'] = $task['TAS_UID'];
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];

        $_REQUEST['actionAjax'] = "messageHistoryGridList_JXP";

        //Turn on output buffering
        ob_start();

        require (PATH_CORE. "methods/cases/caseMessageHistory_Ajax.php");
        $result = ob_get_contents();
        //Clean the output buffer and turn off output buffering
        ob_end_clean();
        $this->assertEquals(json_decode($result)->totalCount, 2);
    }

    /**
     * It tests the message history grid messageHistoryGridList_JXP action with block permissions configured
     * 
     * @test
     */
    public function it_shoud_test_the_message_history_grid_list_jxp_action_with_block_permission()
    {
        $user = factory(USER::class)->create();

        $process = factory(PROCESS::class)->create();

        $application = factory(APPLICATION::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'APP_INIT_USER' => $user['USR_UID'],
            'APP_CUR_USER' => $user['USR_UID']
        ]);

        $task = factory(TASK::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'TAS_USER' => $user['USR_UID']
        ]);

        $appmessage1 = factory(APPMESSAGE::class)->create([
            'PRO_ID' => $process['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER']
        ]);

        $appmessage2 = factory(APPMESSAGE::class)->create([
            'APP_MSG_TYPE' => 'PM_FUNCTION',
            'PRO_ID' => $process['PRO_ID'],
            'TAS_ID' => $task['TAS_ID'],
            'APP_NUMBER' => $application['APP_NUMBER']
        ]);

        $objectPermission = factory(OBJECTPERMISSION::class)->create([
            'PRO_UID' => $process['PRO_UID'],
            'TAS_UID' => $task['TAS_UID'],
            'USR_UID' => $user['USR_UID'],
            'OP_ACTION' => 'BLOCK',
        ]);

        $_SESSION['PROCESS'] = $process['PRO_UID'];
        $_SESSION['APPLICATION'] = $application['APP_UID'];
        $_SESSION['TASK'] = $task['TAS_UID'];
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];


        $_REQUEST['actionAjax'] = "messageHistoryGridList_JXP";

        //Turn on output buffering
        ob_start();
        
        require (PATH_CORE. "methods/cases/caseMessageHistory_Ajax.php");
        $result = ob_get_contents();
        //Clean the output buffer and turn off output buffering
        ob_end_clean();
        $this->assertEmpty(json_decode($result)->data);
    }

    /**
     * This method calls the parent tearDown
     */
    public function tearDown()
    {
        parent::tearDown();
    }
}