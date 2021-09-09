<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use ProcessMaker\Model\GroupUser;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class PMFNewUserTest extends TestCase
{
    /**
     * Creates the setUp method
     */
    public function setUp()
    {
        parent::setup();

        if (!defined('PPP_NUMERICAL_CHARACTER_REQUIRED')) {
            define('PPP_NUMERICAL_CHARACTER_REQUIRED', 1);
        }
        if (!defined('PPP_UPPERCASE_CHARACTER_REQUIRED')) {
            define('PPP_UPPERCASE_CHARACTER_REQUIRED', 1);
        }
        if (!defined('PPP_SPECIAL_CHARACTER_REQUIRED')) {
            define('PPP_SPECIAL_CHARACTER_REQUIRED', 1);
        }
    }
    
    /**
     * Creates the tearDown method
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * It tests the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_the_pmfnewuser_function()
    {
        global $RBAC;
        $user = User::where('USR_ID', '=', 1)->get()->first();
        $_SESSION['USER_LOGGED'] = $user['USR_UID'];
        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);

        $group = factory(Groupwf::class)->create();

        $result = PMFNewUser("test", "Test123*", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, $group['GRP_UID']);

        $query = GroupUser::select();
        $r = $query->get()->values()->toArray();

        $this->assertEquals($r[0]['GRP_UID'], $result['groupUid']);
        $this->assertEquals($r[0]['USR_UID'], $result['userUid']);

        $query = RbacUsers::select()->where('USR_UID', $result['userUid']);
        $r = $query->get()->values()->toArray();

        $this->assertNotEmpty($r);
        $this->assertEquals($result['userUid'], $r[0]['USR_UID']);
        $this->assertEquals($result['username'], $r[0]['USR_USERNAME']);
    }

    /**
     * It tests the exception user is required in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_exception_user_required()
    {
        $this->expectExceptionMessage('**ID_USERNAME_REQUIRED**');
        PMFNewUser("", "test123", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the exception lastname is required in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_exception_lastname_required()
    {
        $this->expectExceptionMessage('**ID_MSG_ERROR_USR_LASTNAME**');
        PMFNewUser("test", "test123", "test", "", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the exception firstname is required in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_exception_firstname_required()
    {
        $this->expectExceptionMessage('**ID_MSG_ERROR_USR_FIRSTNAME**');
        PMFNewUser("test", "test123", "", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the exception password is required in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_exception_password_required()
    {
        $this->expectExceptionMessage('**ID_PASSWD_REQUIRED**');
        PMFNewUser("test", "", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the exception email is required in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_exception_email_required()
    {
        $this->expectExceptionMessage('**ID_EMAIL_IS_REQUIRED**');
        PMFNewUser("test", "test123", "test", "test", "", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the email format exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_email_format_exception()
    {
        $this->expectExceptionMessage('**ID_EMAIL_INVALID**');
        PMFNewUser("test2", "Test123*", "test", "test", "test@test", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the due date format exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_duedate_format_exception()
    {
        $this->expectExceptionMessage('**ID_INVALID_DATA**');
        PMFNewUser("test2", "test123", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", '121212', null, null);
    }

    /**
     * It tests the status exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_status_exception()
    {
        $this->expectExceptionMessage('**ID_INVALID_DATA**');
        PMFNewUser("test2", "test123", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, 'ACTI', null);
    }

    /**
     * It tests the rol exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_rol_exception()
    {
        $this->expectExceptionMessage('**ID_INVALID_ROLE**');
        PMFNewUser("test2", "test13", "test", "test", "test@test.com", "PROCESSMAKER_ADM", null, null, null);
    }

    /**
     * It tests the password surprases exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_password_surprases_exception()
    {
        $this->expectExceptionMessage('**ID_PASSWORD_SURPRASES**');
        PMFNewUser("test2", "123456789012345678901234567890", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the password numerical character required exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_password_numerical_exception()
    {
        $this->expectExceptionMessage('**ID_PPP_NUMERICAL_CHARACTER_REQUIRED**');
        PMFNewUser("test2", "TestA*", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the password uppercase character required exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_password_uppercase_exception()
    {
        $this->expectExceptionMessage('**ID_PPP_UPPERCASE_CHARACTER_REQUIRED**');
        PMFNewUser("test2", "test1*", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the password special character required exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_password_special_character_exception()
    {
        $this->expectExceptionMessage('**ID_PPP_SPECIAL_CHARACTER_REQUIRED**');
        PMFNewUser("test2", "Test1", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the password below exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_password_below_exception()
    {
        $this->expectExceptionMessage('**ID_PASSWORD_BELOW**');
        PMFNewUser("test2", "test", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the username exists exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_username_exists_exception()
    {
        $this->expectExceptionMessage('**ID_USERNAME_ALREADY_EXISTS**');
        PMFNewUser("test", "Test12345*", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the email is invalid exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_email_invalid_exception()
    {
        $this->expectExceptionMessage('**ID_EMAIL_INVALID**');
        PMFNewUser("test3", "Test12345*", "test", "test", "test@test", "PROCESSMAKER_ADMIN", null, null, null);
    }

    /**
     * It tests the group does not exists exception in the PMFNewUser() function
     * 
     * @test
     */
    public function it_should_test_group_doesnot_exists_exception()
    {
        $this->expectExceptionMessage('**ID_GROUP_DOESNT_EXIST**');
        PMFNewUser("test3", "Test12345*", "test", "test", "test@test.com", "PROCESSMAKER_ADMIN", null, null, '1234');
    }
}
