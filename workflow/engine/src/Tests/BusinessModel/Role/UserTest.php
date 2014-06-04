<?php
namespace Tests\BusinessModel\Role;

if (!class_exists("Propel")) {
    require_once(__DIR__ . "/../../bootstrap.php");
}

/**
 * Class UserTest
 *
 * @package Tests\BusinessModel\Role
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    protected static $user;
    protected static $roleUser;
    protected static $numUser = 2;
    protected static $roleUid = "00000000000000000000000000000002"; //PROCESSMAKER_ADMIN

    protected static $arrayUsrUid = array();

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        self::$user     = new \ProcessMaker\BusinessModel\User();
        self::$roleUser = new \ProcessMaker\BusinessModel\Role\User();
    }

    /**
     * Delete
     *
     * @coversNothing
     */
    public static function tearDownAfterClass()
    {
        foreach (self::$arrayUsrUid as $value) {
            $usrUid = $value;

            self::$user->delete($usrUid);
        }
    }

    /**
     * Test assign users to role
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::create
     *
     * @return array
     */
    public function testCreate()
    {
        $arrayRecord = array();

        //User
        $arrayAux = explode("-", date("Y-m-d"));
        $dueDate  = date("Y-m-d", mktime(0, 0, 0, $arrayAux[1], $arrayAux[2] + 5, $arrayAux[0]));

        for ($i = 0; $i <= self::$numUser - 1; $i++) {
            $arrayData = array(
                "USR_USERNAME"    => "userphpunit" . $i,
                "USR_FIRSTNAME"   => "userphpunit" . $i,
                "USR_LASTNAME"    => "userphpunit" . $i,
                "USR_EMAIL"       => "userphpunit@email.com" . $i,
                "USR_COUNTRY"     => "",
                "USR_ADDRESS"     => "",
                "USR_PHONE"       => "",
                "USR_ZIP_CODE"    => "",
                "USR_POSITION"    => "",
                "USR_REPLACED_BY" => "",
                "USR_DUE_DATE"    => $dueDate,
                "USR_ROLE"        => "PROCESSMAKER_OPERATOR",
                "USR_STATUS"      => "ACTIVE",
                "USR_NEW_PASS"    => "userphpunit" . $i,
                "USR_CNF_PASS"    => "userphpunit" . $i
            );

            $arrayUser = array_change_key_case(self::$user->create($arrayData), CASE_UPPER);

            self::$arrayUsrUid[] = $arrayUser["USR_UID"];
            $arrayRecord[] = $arrayUser;
        }

        //Role and User - Create
        foreach ($arrayRecord as $value) {
            $usrUid = $value["USR_UID"];

            $arrayRoleUser = self::$roleUser->create(self::$roleUid, array("USR_UID" => $usrUid));

            $this->assertTrue(is_array($arrayRoleUser));
            $this->assertNotEmpty($arrayRoleUser);

            $this->assertTrue(isset($arrayRoleUser["ROL_UID"]));
        }

        //Return
        return $arrayRecord;
    }

    /**
     * Test get assigned users to role
     * Test get available users to assign to role
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::getUsers
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the users
     */
    public function testGetUsers(array $arrayRecord)
    {
        //USERS
        $arrayUser = self::$roleUser->getUsers(self::$roleUid, "USERS");

        $this->assertNotEmpty($arrayUser);

        $arrayUser = self::$roleUser->getUsers(self::$roleUid, "USERS", null, null, null, 0, 0);

        $this->assertEmpty($arrayUser);

        $arrayUser = self::$roleUser->getUsers(self::$roleUid, "USERS", array("filter" => "userphpunit"));

        $this->assertTrue(is_array($arrayUser));
        $this->assertNotEmpty($arrayUser);

        $this->assertEquals($arrayUser[0]["USR_UID"],      $arrayRecord[0]["USR_UID"]);
        $this->assertEquals($arrayUser[0]["USR_USERNAME"], $arrayRecord[0]["USR_USERNAME"]);

        //AVAILABLE-USERS
        $arrayUser = self::$roleUser->getUsers(self::$roleUid, "AVAILABLE-USERS", null, null, null, 0, 0);

        $this->assertEmpty($arrayUser);

        $arrayUser = self::$roleUser->getUsers(self::$roleUid, "AVAILABLE-USERS", array("filter" => "userphpunit"));

        $this->assertEmpty($arrayUser);
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testCreateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayRoleUser = self::$roleUser->create(self::$roleUid, $arrayData);
    }

    /**
     * Test exception for invalid role UID
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The role with ROL_UID: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx does not exist.
     */
    public function testCreateExceptionInvalidRolUid()
    {
        $arrayData = array(
            "USR_UID" => "",
        );

        $arrayRoleUser = self::$roleUser->create("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $arrayData);
    }

    /**
     * Test exception for invalid data (USR_UID)
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "USR_UID", it can not be empty.
     */
    public function testCreateExceptionInvalidDataUsrUid()
    {
        $arrayData = array(
            "USR_UID" => "",
        );

        $arrayRoleUser = self::$roleUser->create(self::$roleUid, $arrayData);
    }

    /**
     * Test unassign users of the role
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::delete
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the users
     */
    public function testDelete(array $arrayRecord)
    {
        foreach ($arrayRecord as $value) {
            $usrUid = $value["USR_UID"];

            self::$roleUser->delete(self::$roleUid, $usrUid);
        }

        $arrayUser = self::$roleUser->getUsers(self::$roleUid, "USERS", array("filter" => "userphpunit"));

        $this->assertTrue(is_array($arrayUser));
        $this->assertEmpty($arrayUser);
    }

    /**
     * Test exception for administrator's role can't be changed
     *
     * @covers \ProcessMaker\BusinessModel\Role\User::delete
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The administrator's role can't be changed!
     */
    public function testDeleteExceptionAdminRoleCantChanged()
    {
        self::$roleUser->delete(self::$roleUid, "00000000000000000000000000000001");
    }
}

