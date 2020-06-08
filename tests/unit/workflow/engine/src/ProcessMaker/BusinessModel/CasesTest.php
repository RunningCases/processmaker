<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use G;
use ProcessMaker\Model\Application;
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
}
