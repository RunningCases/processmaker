<?php

namespace Tests\unit\workflow\engine\methods\groups;

use Faker\Factory;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class GroupsAjaxTest extends TestCase
{
    private $groups;

    /**
     * Set up function
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
        $this->settingUserLogged();
        $this->createGroups();
    }

    /**
     * Create records in the GROUPSWF table
     */
    private function createGroups()
    {
        $this->groups = Groupwf::factory(10)->create();
    }

    /**
     * This starts a valid user in session with the appropriate permissions.
     * @global object $RBAC
     */
    private function settingUserLogged()
    {
        global $RBAC;

        $user = User::where('USR_ID', '=', 1)
                ->get()
                ->first();

        $_SESSION['USER_LOGGED'] = $user['USR_UID'];

        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);
    }

    /**
     * This tests the answer of the option groupsList.
     * @test
     */
    public function it_should_return_option_groups_list()
    {
        global $RBAC;
        $_POST['action'] = 'groupsList';
        $_GET['action'] = 'groupsList';
        $_REQUEST["dir"] = "DESC";
        $_REQUEST["sort"] = "GRP_TITLE";

        $fileName = PATH_METHODS . 'groups/groups_Ajax.php';

        ob_start();
        require_once $fileName;
        $content = ob_get_clean();
        $content = json_decode($content, JSON_OBJECT_AS_ARRAY);

        $this->assertArrayHasKey("success", $content);
        $this->assertArrayHasKey("groups", $content);
        $this->assertTrue($content["success"]);
        $this->assertTrue(is_array($content["groups"]));
    }
}
