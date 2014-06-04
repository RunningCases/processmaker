<?php
namespace Tests\BusinessModel\Role;

if (!class_exists("Propel")) {
    require_once(__DIR__ . "/../../bootstrap.php");
}

/**
 * Class PermissionTest
 *
 * @package Tests\BusinessModel\Role
 */
class PermissionTest extends \PHPUnit_Framework_TestCase
{
    protected static $role;
    protected static $roleUid = "";

    protected static $rolePermission;

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        //Role
        self::$role = new \ProcessMaker\BusinessModel\Role();

        $arrayData = array(
            "ROL_CODE" => "PHPUNIT_MY_ROLE_0",
            "ROL_NAME" => "PHPUnit My Role 0"
        );

        $arrayRole = self::$role->create($arrayData);

        self::$roleUid = $arrayRole["ROL_UID"];

        //Role and Permission
        self::$rolePermission = new \ProcessMaker\BusinessModel\Role\Permission();
    }

    /**
     * Delete
     *
     * @coversNothing
     */
    public static function tearDownAfterClass()
    {
        self::$role->delete(self::$roleUid);
    }

    /**
     * Test assign permissions to role
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::create
     *
     * @return array
     */
    public function testCreate()
    {
        $arrayRecord = array();

        //Permission
        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "AVAILABLE-PERMISSIONS", array("filter" => "V"));

        $this->assertNotEmpty($arrayPermission);

        //Role and Permission - Create
        foreach ($arrayPermission as $value) {
            $perUid = $value["PER_UID"];

            $arrayRolePermission = self::$rolePermission->create(self::$roleUid, array("PER_UID" => $perUid));

            $this->assertTrue(is_array($arrayRolePermission));
            $this->assertNotEmpty($arrayRolePermission);

            $this->assertTrue(isset($arrayRolePermission["ROL_UID"]));

            $arrayRecord[] = $arrayRolePermission;
        }

        //Return
        return $arrayRecord;
    }

    /**
     * Test get assigned permissions to role
     * Test get available permissions to assign to role
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::getPermissions
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the role-permission
     */
    public function testGetPermissions(array $arrayRecord)
    {
        //PERMISSIONS
        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "PERMISSIONS");

        $this->assertNotEmpty($arrayPermission);

        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "PERMISSIONS", null, null, null, 0, 0);

        $this->assertEmpty($arrayPermission);

        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "PERMISSIONS", array("filter" => "V"));

        $this->assertTrue(is_array($arrayPermission));
        $this->assertNotEmpty($arrayPermission);

        $this->assertEquals($arrayPermission[0]["PER_UID"], $arrayRecord[0]["PER_UID"]);

        //AVAILABLE-PERMISSIONS
        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "AVAILABLE-PERMISSIONS", null, null, null, 0, 0);

        $this->assertEmpty($arrayPermission);

        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "AVAILABLE-PERMISSIONS", array("filter" => "V"));

        $this->assertEmpty($arrayPermission);
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testCreateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayRolePermission = self::$rolePermission->create(self::$roleUid, $arrayData);
    }

    /**
     * Test exception for invalid role UID
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The role with ROL_UID: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx does not exist.
     */
    public function testCreateExceptionInvalidRolUid()
    {
        $arrayData = array(
            "USR_UID" => "",
        );

        $arrayRolePermission = self::$rolePermission->create("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $arrayData);
    }

    /**
     * Test exception for invalid data (PER_UID)
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "PER_UID", it can not be empty.
     */
    public function testCreateExceptionInvalidDataPerUid()
    {
        $arrayData = array(
            "PER_UID" => "",
        );

        $arrayRolePermission = self::$rolePermission->create(self::$roleUid, $arrayData);
    }

    /**
     * Test unassign permissions of the role
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::delete
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the role-permission
     */
    public function testDelete(array $arrayRecord)
    {
        foreach ($arrayRecord as $value) {
            $perUid = $value["PER_UID"];

            self::$rolePermission->delete(self::$roleUid, $perUid);
        }

        $arrayPermission = self::$rolePermission->getPermissions(self::$roleUid, "PERMISSIONS", array("filter" => "V"));

        $this->assertTrue(is_array($arrayPermission));
        $this->assertEmpty($arrayPermission);
    }

    /**
     * Test exception for invalid permission UID
     *
     * @covers \ProcessMaker\BusinessModel\Role\Permission::delete
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The permission with PER_UID: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx does not exist.
     */
    public function testDeleteExceptionInvalidPerUid()
    {
        self::$rolePermission->delete(self::$roleUid, "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
    }
}

