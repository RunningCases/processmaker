<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use G;
use Illuminate\Support\Facades\DB;
use ProcessMaker\Model\AppDelay;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Documents;
use ProcessMaker\Model\ListUnassigned;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Step;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\Triggers;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

/**
 * Class CasesTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases
 */
class CasesTest extends TestCase
{
    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
        User::where('USR_ID', '=', 1)
            ->where('USR_ID', '=', 2)
            ->delete();
    }

    /**
     * This checks the delete case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::deleteCase()
     *
     * @test
     * @expectedException Exception
     */
    public function it_should_not_delete_case_without_permission()
    {
        // Set the RBAC
        global $RBAC;
        $_SESSION['USER_LOGGED'] = G::generateUniqueID();
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();

        $application = Application::factory()->create(['APP_INIT_USER' => G::generateUniqueID()]);
        // Tried to delete case
        $case = new Cases();

        $this->expectException('Exception');
        $case->deleteCase($application->APP_UID, $_SESSION['USER_LOGGED']);
    }

    /**
     * This checks the delete case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::deleteCase()
     *
     * @test
     * @expectedException Exception
     */
    public function it_should_not_delete_case_in_todo_status()
    {
        // Set the RBAC
        global $RBAC;
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000001';
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();

        $application = Application::factory()->create(['APP_STATUS' => 'TO_DO']);
        // Tried to delete case
        $case = new Cases();

        $this->expectException('Exception');
        $case->deleteCase($application->APP_UID, $_SESSION['USER_LOGGED']);
    }

    /**
     * This checks the delete case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::deleteCase()
     *
     * @test
     * @expectedException Exception
     */
    public function it_should_not_delete_case_when_is_not_owner()
    {
        // Set the RBAC
        global $RBAC;
        $_SESSION['USER_LOGGED'] = G::generateUniqueID();
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();

        $application = Application::factory()->create(['APP_INIT_USER' => G::generateUniqueID()]);
        // Tried to delete case
        $case = new Cases();

        $this->expectException('Exception');
        $case->deleteCase($application->APP_UID, $_SESSION['USER_LOGGED']);
    }

    /**
     * Review the upload file related to the case notes
     *
     * @covers \ProcessMaker\BusinessModel\Cases::uploadFilesInCaseNotes()
     * 
     * @test
     */
    public function it_should_upload_files_related_case_note()
    {
        $application = Application::factory()->create();
        Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID
        ]);
        // File reference to upload
        $filesReferences = [
            'activityRename.gif' => PATH_TRUNK . 'tests' . PATH_SEP . 'resources' . PATH_SEP . 'images' . PATH_SEP . 'activity.gif',
        ];
        // Path of the case
        $pathCase = PATH_DB . config('system.workspace') . PATH_SEP . 'files' . PATH_SEP . $application->APP_UID . PATH_SEP;
        // Upload the file
        $case = new Cases();
        $result = $case->uploadFilesInCaseNotes('00000000000000000000000000000001', $application->APP_UID, $filesReferences);
        $this->assertNotEmpty($result['attachments']);
        $result = head($result['attachments']);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('APP_UID', $result);
        $this->assertEquals($application->APP_UID, $result['APP_UID']);
        $this->assertArrayHasKey('APP_DOC_TYPE', $result);
        $this->assertEquals(Documents::DOC_TYPE_CASE_NOTE, $result['APP_DOC_TYPE']);
        $this->assertArrayHasKey('APP_DOC_FILENAME', $result);
        $this->assertEquals('activityRename.gif', $result['APP_DOC_FILENAME']);

        // Remove the path created
        G::rm_dir($pathCase);
    }

    /**
     * It tests the uploadFiles method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases::uploadFiles()
     * @test
     */
    public function it_should_test_upload_files_method()
    {
        $user = User::factory()->create();
        $application = Application::factory()->create([
            'APP_CUR_USER' => $user->USR_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID
        ]);
        $varName = "/tmp/test.pdf";
        fopen($varName, "w");
        $_FILES = ["form" =>
        [
            "name" => ["test"],
            "type" => ["application/pdf"],
            "tmp_name" => ["/tmp/test.pdf"],
            "error" => [0],
            "size" => [0]
        ]];

        $case = new Cases();

        // Call the uploadFiles method, sending the delIndex
        $res = $case->uploadFiles($user->USR_UID, $application->APP_UID, $varName, -1, null, $delegation->DEL_INDEX);
        // Asserts the result is not empy
        $this->assertNotEmpty($res);

        //Call the uploadFiles method, without the delIndex
        $res = $case->uploadFiles($user->USR_UID, $application->APP_UID, $varName);
        // Asserts the result is not empy
        $this->assertNotEmpty($res);
    }

    /**
     * It tests the exception in the uploadFiles method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases::uploadFiles()
     * @test
     */
    public function it_should_test_exception_in_upload_files_method()
    {
        $user = User::factory()->create();
        $application = Application::factory()->create([
            'APP_CUR_USER' => $user->USR_UID
        ]);
        Delegation::factory()->create([
            'APP_UID' => $application->APP_UID
        ]);
        $varName = "/tmp/test.pdf";
        fopen($varName, "w");

        $_FILES = [];
        $case = new Cases();

        // Asserts an exception is expected
        $this->expectExceptionMessage("**ID_ERROR_UPLOAD_FILE_CONTACT_ADMINISTRATOR**");
        // Call the uploadFiles method
        $case->uploadFiles($user->USR_UID, $application->APP_UID, $varName);
    }

    /**
     * It tests the exception in uploadFiles method
     * 
     * @covers \ProcessMaker\BusinessModel\Cases::uploadFiles()
     * @test
     */
    public function it_should_test_the_exception_in_upload_files_method()
    {
        $user = User::factory()->create();
        $application = Application::factory()->create([
            'APP_CUR_USER' => $user->USR_UID
        ]);
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID
        ]);
        $varName = "/tmp/test.pdf";
        fopen($varName, "w");
        $_FILES = ["form" =>
        [
            "name" => ["test"],
            "type" => ["application/pdf"],
            "tmp_name" => ["/tmp/test.pdf"],
            "error" => [UPLOAD_ERR_INI_SIZE],
            "size" => [0]
        ]];

        $case = new Cases();

        // Asserts there is an exception for the file
        $this->expectExceptionMessage("The uploaded file exceeds the upload_max_filesize directive in php.ini");
        // Call the uploadFiles method
        $case->uploadFiles($user->USR_UID, $application->APP_UID, $varName, -1, null, $delegation->DEL_INDEX);
    }

    /**
     * This test the execution of trigger from cases related to the self services timeout
     *
     * @covers \ProcessMaker\BusinessModel\Cases::executeSelfServiceTimeout()
     * @test
     */
    public function it_execute_trigger_from_cases_with_self_service_timeout_every_time()
    {
        // Define the Execute Trigger = EVERY_TIME
        $application = Application::factory()->foreign_keys()->create();
        // Create a trigger
        $trigger = Triggers::factory()->create([
            'PRO_UID' => $application->PRO_UID,
            'TRI_WEBBOT' => 'echo(1);'
        ]);
        // Create a task with the configuration trigger execution
        $task = Task::factory()->sef_service_timeout()->create([
            'PRO_UID' => $application->PRO_UID,
            'TAS_SELFSERVICE_EXECUTION' => 'EVERY_TIME',
            'TAS_SELFSERVICE_TRIGGER_UID' => $trigger->TRI_UID
        ]);
        // Create a unassigned cases
        ListUnassigned::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'TAS_ID' => $task->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $application->PRO_UID
        ]);
        // Define the session
        $_SESSION["PROCESS"] = $application->PRO_UID;

        // todo: the function Cases::loadCase is using propel we need to change this
        DB::commit();
        $casesExecuted = Cases::executeSelfServiceTimeout();
        $this->assertTrue(is_array($casesExecuted));
    }

    /**
     * This test the execution of trigger from cases related to the self services timeout
     *
     * @covers \ProcessMaker\BusinessModel\Cases::executeSelfServiceTimeout()
     * @test
     */
    public function it_execute_trigger_from_cases_with_self_service_timeout_once()
    {
        // Define the Execute Trigger = ONCE
        $application = Application::factory()->foreign_keys()->create();
        // Create a trigger
        $trigger = Triggers::factory()->create([
            'PRO_UID' => $application->PRO_UID,
            'TRI_WEBBOT' => 'echo(1);'
        ]);
        // Create a task with the configuration trigger execution
        $task = Task::factory()->sef_service_timeout()->create([
            'PRO_UID' => $application->PRO_UID,
            'TAS_SELFSERVICE_EXECUTION' => 'ONCE',
            'TAS_SELFSERVICE_TRIGGER_UID' => $trigger->TRI_UID
        ]);
        // Create a unassigned cases
        ListUnassigned::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'TAS_ID' => $task->TAS_ID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID,
            'PRO_UID' => $application->PRO_UID
        ]);
        // Define the session
        $_SESSION["PROCESS"] = $application->PRO_UID;

        // todo: the function Cases::loadCase is using propel we need to change this
        DB::commit();
        $casesExecuted = Cases::executeSelfServiceTimeout();
        $this->assertTrue(is_array($casesExecuted));
    }

    /**
     * It test get assigned DynaForms as steps by application Uid
     *
     * @covers \ProcessMaker\BusinessModel\Cases::dynaFormsByApplication()
     * @test
     */
    public function it_should_test_get_dynaforms_by_application()
    {
        // Create a process
        $process = Process::factory()->create();

        // Create a task related to the process
        $task1 = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        // Created another task related to the process
        $task2 = Task::factory()->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        // Created a step related to the first task
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task1->TAS_UID,
            'STEP_TYPE_OBJ' => 'DYNAFORM',
            'STEP_UID_OBJ' => G::generateUniqueID(),
            'STEP_POSITION' => 1
        ]);

        // Created a step related to the second task and with a specific DynaForm Uid
        $dynUid = G::generateUniqueID();
        Step::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'TAS_UID' => $task2->TAS_UID,
            'STEP_TYPE_OBJ' => 'DYNAFORM',
            'STEP_UID_OBJ' => $dynUid,
            'STEP_POSITION' => 1
        ]);

        // Create an application related to the process in draft status
        $application = Application::factory()->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_STATUS' => 'DRAFT'
        ]);

        // Get all DynaForms assigned as steps
        self::assertCount(2, Cases::dynaFormsByApplication($application->APP_UID));

        // Get DynaForms assigned as steps for the first task
        self::assertCount(1, Cases::dynaFormsByApplication($application->APP_UID, $task1->TAS_UID));

        // Get DynaForms assigned as steps sending a specific DynaForm Uid
        self::assertCount(1, Cases::dynaFormsByApplication($application->APP_UID, '', $dynUid));

        // Get DynaForms assigned as steps for the second task when the application status is DRAFT
        self::assertCount(1, Cases::dynaFormsByApplication($application->APP_UID, $task2->TAS_UID, '', 'TO_DO'));

        // Get DynaForms assigned as steps for the second task when the application status is COMPLETED
        self::assertCount(1, Cases::dynaFormsByApplication($application->APP_UID, $task2->TAS_UID, '', 'COMPLETED'));
    }

    /**
     * It test the case info used in the PMFCaseLink
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getStatusInfo()
     * @covers \ProcessMaker\Model\AppDelay::getPaused()
     * @test
     */
    public function it_should_test_case_status_info()
    {
        // Get status info when the case is PAUSED
        $table = AppDelay::factory()->paused_foreign_keys()->create();
        $cases = new Cases();
        $result = $cases->getStatusInfo($table->APP_UID, $table->APP_DEL_INDEX, $table->APP_DELEGATION_USER);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('APP_STATUS', $result);
        $this->assertArrayHasKey('DEL_INDEX', $result);
        $this->assertArrayHasKey('PRO_UID', $result);
        // Get status info when the case is UNASSIGNED
        // Get status info when the case is TO_DO
        $table = Delegation::factory()->foreign_keys()->create();
        $cases = new Cases();
        $result = $cases->getStatusInfo($table->APP_UID, $table->DEL_INDEX, $table->USR_UID);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('APP_STATUS', $result);
        $this->assertArrayHasKey('DEL_INDEX', $result);
        $this->assertArrayHasKey('PRO_UID', $result);
        // Get status info when the case is COMPLETED
        $table = Application::factory()->completed()->create();
        $table = Delegation::factory()->foreign_keys()->create([
            'APP_NUMBER' => $table->APP_NUMBER,
            'APP_UID' => $table->APP_UID,
        ]);
        $cases = new Cases();
        $result = $cases->getStatusInfo($table->APP_UID, $table->DEL_INDEX, $table->USR_UID);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('APP_STATUS', $result);
        $this->assertArrayHasKey('DEL_INDEX', $result);
        $this->assertArrayHasKey('PRO_UID', $result);
    }

}
