<?php

namespace Tests\unit\workflow\engine\methods\users;

use ProcessMaker\Model\Configuration;
use ProcessMaker\Model\RbacUsersRoles;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class UsersAjaxTest extends TestCase
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
     * Tests the user ajax file with the userData action
     * @test
     */
    public function it_tests_the_user_ajax_file()
    {
        //Declare the global variable
        global $RBAC;
        //Creates the user factory
        $user = factory(User::class)->create();
        $usrUid = $user['USR_UID'];
        //Creates the configuration factory
        factory(Configuration::class)->create([
            'CFG_UID' => 'USER_PREFERENCES',
            'OBJ_UID' => '',
            'CFG_VALUE' => 'a:3:{s:12:"DEFAULT_LANG";s:0:"";s:12:"DEFAULT_MENU";s:8:"PM_SETUP";s:18:"DEFAULT_CASES_MENU";s:0:"";}',
            'PRO_UID' => '',
            'USR_UID' => $usrUid,
            'APP_UID' => '',
        ]);

        //Sets the needed variables
        $_SESSION['USER_LOGGED'] = $usrUid;
        $_POST['action'] = 'userData';
        $_POST['USR_UID'] = $usrUid;
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        //Turn on output buffering
        ob_start();

        //Call the tested file
        require_once PATH_TRUNK . 'workflow/engine/methods/users/usersAjax.php';

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

        //Assert the call was success
        $this->assertTrue($res->success);
        //Assert the result corresponds to the user logged
        $this->assertEquals($usrUid, $res->user->USR_UID);
        //Assert the default menu is set
        $this->assertEquals(
            'PM_EDIT_USER_PROFILE_DEFAULT_MAIN_MENU_OPTIONS',
            $res->permission->PREF_DEFAULT_MENUSELECTED
        );
    }

    /**
     * Tests the user ajax file with the userData action
     * @test
     */
    public function it_tests_the_user_ajax_file_with_save_personal_info_action()
    {
        //Declare the global variable
        global $RBAC;
        //Creates the user factory
        $user2 = factory(User::class)->create(
            [
                'USR_ROLE' => 'PROCESSMAKER_ADMIN',
                'USR_EMAIL' => 'test@processmaker.com'
            ]
        );
        $usrUid = $user2['USR_UID'];
        //Creates the configuration factory
        factory(Configuration::class)->create([
            'CFG_UID' => 'USER_PREFERENCES',
            'OBJ_UID' => '',
            'CFG_VALUE' => 'a:3:{s:12:"DEFAULT_LANG";s:0:"";s:12:"DEFAULT_MENU";s:8:"PM_SETUP";s:18:"DEFAULT_CASES_MENU";s:0:"";}',
            'PRO_UID' => '',
            'USR_UID' => $usrUid,
            'APP_UID' => '',
        ]);

        //Creates the UsersRoles factory
        factory(RbacUsersRoles::class)->create(
            [
                'USR_UID' => $usrUid,
                'ROL_UID' => '00000000000000000000000000000002'
            ]
        );

        //Sets the needed variables
        $_SESSION['USER_LOGGED'] = $usrUid;
        $_POST['action'] = 'savePersonalInfo';
        $_POST['USR_UID'] = $usrUid;
        $_POST['USR_EMAIL'] = "andrea.Adamczyk@processmaker.com";
        $_POST['_token'] = 'b8sbHBMAcdwZ40W1Epf2A5leyJq3mArcnTjoToXU';
        $_SESSION['USR_CSRF_TOKEN'] = 'b8sbHBMAcdwZ40W1Epf2A5leyJq3mArcnTjoToXU';
        $_FILES['USR_PHOTO'] = ['name' => '', 'type' => '', 'tmp_name' => '', 'error' => 4, 'size' => 0];
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        //Turn on output buffering
        ob_start();

        //Call the tested file
        require PATH_TRUNK . 'workflow/engine/methods/users/usersAjax.php';

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

        //It asserts the result is success
        $this->assertFalse($res->success);

        //Get the edited user
        $resUser = User::where('USR_UID', '=', $usrUid)->get();

        //It asserts the user's email has been converted to lowercase
        $this->assertEquals($resUser[0]->USR_EMAIL, strtolower($_POST['USR_EMAIL']));
    }
}
