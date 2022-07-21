<?php

namespace Tests\unit\workflow\engine\methods\emailServer;

use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class EmailServerTest extends TestCase
{
    /**
     * This test ensures that the script output generates the javascript variable 
     * EMAILSERVER_LICENSED.
     * @test
     */
    public function it_should_verify_the_script_output_contains_javascript_variable()
    {
        global $RBAC;

        $user = User::where('USR_ID', '=', 1)
                ->get()
                ->first();

        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $_POST['USR_UID'] = $user['USR_UID'];

        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        ob_start();
        require_once PATH_METHODS . 'emailServer/emailServer.php';
        $content = ob_get_clean();

        $this->assertStringContainsString("EMAILSERVER_LICENSED", $content);
    }
}
