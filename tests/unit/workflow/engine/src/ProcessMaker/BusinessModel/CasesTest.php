<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Documents;
use RBAC;
use Tests\TestCase;

/**
 * Class DelegationTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\Cases
 */
class CasesTest extends TestCase
{
    /**
     * This checks the delete case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::deleteCase()
     * @test
     * @expectedException Exception
     */
    public function it_should_not_delete_case_without_permission()
    {
        // Set the RBAC
        global $RBAC;
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000002';
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();

        $application = factory(Application::class)->create();
        // Tried to delete case
        $case = new Cases();
        $case->deleteCase($application->APP_UID, $_SESSION['USER_LOGGED']);
    }

    /**
     * This checks the delete case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::deleteCase()
     * @test
     * @expectedException Exception
     */
    public function it_should_not_delete_case_in_todo_status()
    {
        // Set the RBAC
        global $RBAC;
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000001';
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();

        $application = factory(Application::class)->create(['APP_STATUS' => 'TO_DO']);
        // Tried to delete case
        $case = new Cases();
        $case->deleteCase($application->APP_UID, $_SESSION['USER_LOGGED']);
    }

    /**
     * This checks the delete case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::deleteCase()
     * @test
     * @expectedException Exception
     */
    public function it_should_not_delete_case_when_is_not_owner()
    {
        // Set the RBAC
        global $RBAC;
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000001';
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();

        $application = factory(Application::class)->create(['APP_INIT_USER' => '00000000000000000000000000000002']);
        // Tried to delete case
        $case = new Cases();
        $case->deleteCase($application->APP_UID, $_SESSION['USER_LOGGED']);
    }

    /**
     * Review the upload file related to the case notes, an return an exception when the array is empty 
     *
     * @covers \ProcessMaker\BusinessModel\Cases::uploadFilesInCaseNotes()
     * 
     * @test
     * @expectedException Exception
     */
    public function it_return_exception_in_upload_files_related_case_note()
    {
        $application = factory(Application::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
            'APP_NUMBER' => $application->APP_NUMBER,
            'APP_UID' => $application->APP_UID
        ]);
        // Upload the file
        $case = new Cases();
        // Return an exception because the files does not exist
        $case->uploadFilesInCaseNotes('00000000000000000000000000000001', $application->APP_UID, $filesReferences = []);
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
        $application = factory(Application::class)->create();
        factory(Delegation::class)->states('foreign_keys')->create([
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
        $result = head($result);
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
}
