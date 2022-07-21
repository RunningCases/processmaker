<?php

namespace Tests\unit\workflow\engine\methods\cases;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class CasesMenuHighlightTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    /**
     * This sets the initial parameters for each test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->settingUserLogged();
    }

    /**
     * This starts a valid user in session with the appropriate permissions.
     *
     * @global object $RBAC
     */
    private function settingUserLogged()
    {
        global $RBAC;

        $this->user = User::where('USR_ID', '=', 1)->get()->first();

        $_SESSION['USER_LOGGED'] = $this->user['USR_UID'];

        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);
    }

    /**
     * Test if the file is returning a valid JSON object with the correct data for the current user
     *
     * @test
     */
    public function it_should_test_the_response_of_the_cases_menu_highlight_file()
    {
        // Create process
        $process = Process::factory()->create();

        // Create a task self service
        $task = Task::factory()->create([
            'TAS_ASSIGN_TYPE' => 'SELF_SERVICE',
            'TAS_GROUP_VARIABLE' => '',
            'PRO_UID' => $process->PRO_UID
        ]);

        // Assign the current user in the task
        TaskUser::factory()->create([
            'TAS_UID' => $task->TAS_UID,
            'USR_UID' => $this->user->USR_UID,
            'TU_RELATION' => 1, //Related to the user
            'TU_TYPE' => 1
        ]);

        // Create records in delegation relate to self-service
        Delegation::factory(10)->create([
            'TAS_ID' => $task->TAS_ID,
            'DEL_THREAD_STATUS' => 'OPEN',
            'USR_ID' => 0
        ]);

        // Turn on output buffering
        ob_start();

        // Call the tested file
        require_once PATH_METHODS . 'cases/casesMenuHighlight.php';

        // Return the contents of the output buffer
        $outputBuffer = ob_get_contents();

        // Clean the output buffer and turn off output buffering
        ob_end_clean();

        // Check if is a valid JSON
        $this->assertJson($outputBuffer);

        // Parse JSON
        $result = json_decode($outputBuffer, true);

        // Check if the object is valid
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('item', $result[7]);
        $this->assertArrayHasKey('highlight', $result[7]);
        $this->assertEquals('CASES_SELFSERVICE', $result[7]['item']);
        $this->assertEquals(true, $result[7]['highlight']);
    }
}
