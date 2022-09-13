<?php

namespace Tests\unit\workflow\engine\methods\users;

use ProcessMaker\Model\Configuration;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class Users_AjaxTest extends TestCase
{
    /**
     * Set up the deprecated errors
     */
    public function setUp(): void
    {
        parent::setUp();
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    }

    /**
     * Tests the users_ajax file with the privateProcesses action
     * @test
     */
    public function it_tests_the_users_ajax_file_private_processes_action()
    {
        $_POST = [];
        //Declare the global variable
        global $RBAC;

        //Creates the user factory
        $user = User::factory()->create();
        $usrUid = $user['USR_UID'];
        Process::factory()->create([
            'PRO_CREATE_USER' => $usrUid,
            'PRO_STATUS' => 'ACTIVE',
            'PRO_TYPE_PROCESS' => 'PRIVATE',
        ]);

        //Creates the configuration factory
        Configuration::factory()->create([
            'CFG_UID' => 'USER_PREFERENCES',
            'OBJ_UID' => '',
            'CFG_VALUE' => 'a:3:{s:12:"DEFAULT_LANG";s:0:"";s:12:"DEFAULT_MENU";s:8:"PM_SETUP";s:18:"DEFAULT_CASES_MENU";s:0:"";}',
            'PRO_UID' => '',
            'USR_UID' => $usrUid,
            'APP_UID' => '',
        ]);

        //Sets the needed variables
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000001';
        $_POST['action'] = 'privateProcesses';
        $_POST['USR_UID'] = $usrUid;
        $_REQUEST['function'] = 'privateProcesses';
        $_GET['function'] = 'privateProcesses';
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        //Turn on output buffering
        ob_start();

        //Call the tested file
        require_once PATH_TRUNK . 'workflow/engine/methods/users/users_Ajax.php';

        //Return the contents of the output buffer
        $outputBuffer = ob_get_contents();

        //Clean the output buffer and turn off output buffering
        ob_end_clean();

        //Removing the BOM (Byte Order Mark)
        if (0 === strpos(bin2hex($outputBuffer), 'efbbbf')) {
            //Decode the JSON string
            $res = json_decode(substr($outputBuffer, 3));
        } else {
            //Decode the JSON string
            $res = json_decode($outputBuffer);
        }
        
        //Assert the response contains a row
        $this->assertNotEmpty($res);
    }

    /**
     * Tests the users_ajax file with the deleteUser action
     * @test
     */
    public function it_tests_the_users_ajax_file_delete_user_action()
    {
        //Declare the global variable
        global $RBAC;

        //Creates the user factory
        $user = User::factory()->create();
        RbacUsers::factory()->create([
            'USR_UID' => $user['USR_UID'],
            'USR_USERNAME' => $user->USR_USERNAME,
            'USR_FIRSTNAME' => $user->USR_FIRSTNAME,
            'USR_LASTNAME' => $user->USR_LASTNAME
        ]);
        $usrUid = $user['USR_UID'];

        $process = Process::factory()->create([
            'PRO_CREATE_USER' => $usrUid,
            'PRO_STATUS' => 'ACTIVE',
            'PRO_TYPE_PROCESS' => 'PRIVATE',
        ]);

        //Sets the needed variables
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000001';
        $_POST['action'] = 'userData';
        $_POST['USR_UID'] = $usrUid;
        $_REQUEST['function'] = 'deleteUser';
        $_GET['function'] = 'deleteUser';
        $_POST['private_processes'] = Process::where('PRO_ID', $process->PRO_ID)->get();
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        //Turn on output buffering
        ob_start();

        //Call the tested file
        require_once PATH_TRUNK . 'workflow/engine/methods/users/users_Ajax.php';

        //Return the contents of the output buffer
        $outputBuffer = ob_get_contents();

        //Clean the output buffer and turn off output buffering
        ob_end_clean();

        //Removing the BOM (Byte Order Mark)
        if (0 === strpos(bin2hex($outputBuffer), 'efbbbf')) {
            //Decode the JSON string
            $res = json_decode(substr($outputBuffer, 3));
        } else {
            //Decode the JSON string
            $res = json_decode($outputBuffer);
        }

        //Asserts the result is null
        $this->assertNull($res);
    }
}