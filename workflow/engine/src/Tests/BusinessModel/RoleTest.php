<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    require_once(__DIR__ . "/../bootstrap.php");
}

/**
 * Class RoleTest
 *
 * @package Tests\BusinessModel
 */
class RoleTest extends \PHPUnit_Framework_TestCase
{
    protected static $role;
    protected static $numRole = 2;

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        self::$role = new \ProcessMaker\BusinessModel\Role();
    }

    /**
     * Test create roles
     *
     * @covers \ProcessMaker\BusinessModel\Role::create
     *
     * @return array
     */
    public function testCreate()
    {
        $arrayRecord = array();

        //Create
        for ($i = 0; $i <= self::$numRole - 1; $i++) {
            $arrayData = array(
                "ROL_CODE" => "PHPUNIT_MY_ROLE_" . $i,
                "ROL_NAME" => "PHPUnit My Role " . $i
            );

            $arrayRole = self::$role->create($arrayData);

            $this->assertTrue(is_array($arrayRole));
            $this->assertNotEmpty($arrayRole);

            $this->assertTrue(isset($arrayRole["ROL_UID"]));

            $arrayRecord[] = $arrayRole;
        }

        //Create - Japanese characters
        $arrayData = array(
            "ROL_CODE" => "PHPUNIT_MY_ROLE_" . self::$numRole,
            "ROL_NAME" => "テスト（PHPUnitの）",
        );

        $arrayRole = self::$role->create($arrayData);

        $this->assertTrue(is_array($arrayRole));
        $this->assertNotEmpty($arrayRole);

        $this->assertTrue(isset($arrayRole["ROL_UID"]));

        $arrayRecord[] = $arrayRole;

        //Return
        return $arrayRecord;
    }

    /**
     * Test update roles
     *
     * @covers \ProcessMaker\BusinessModel\Role::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the roles
     */
    public function testUpdate(array $arrayRecord)
    {
        $arrayData = array("ROL_NAME" => "PHPUnit My Role ...");

        $arrayRole = self::$role->update($arrayRecord[1]["ROL_UID"], $arrayData);

        $arrayRole = self::$role->getRole($arrayRecord[1]["ROL_UID"]);

        $this->assertTrue(is_array($arrayRole));
        $this->assertNotEmpty($arrayRole);

        $this->assertEquals($arrayRole["ROL_NAME"], $arrayData["ROL_NAME"]);
    }

    /**
     * Test get roles
     *
     * @covers \ProcessMaker\BusinessModel\Role::getRoles
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the roles
     */
    public function testGetRoles(array $arrayRecord)
    {
        $arrayRole = self::$role->getRoles();

        $this->assertNotEmpty($arrayRole);

        $arrayRole = self::$role->getRoles(null, null, null, 0, 0);

        $this->assertEmpty($arrayRole);

        $arrayRole = self::$role->getRoles(array("filter" => "PHPUNIT"));

        $this->assertTrue(is_array($arrayRole));
        $this->assertNotEmpty($arrayRole);

        $this->assertEquals($arrayRole[0]["ROL_UID"],  $arrayRecord[0]["ROL_UID"]);
        $this->assertEquals($arrayRole[0]["ROL_CODE"], $arrayRecord[0]["ROL_CODE"]);
        $this->assertEquals($arrayRole[0]["ROL_NAME"], $arrayRecord[0]["ROL_NAME"]);
    }

    /**
     * Test get role
     *
     * @covers \ProcessMaker\BusinessModel\Role::getRole
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the roles
     */
    public function testGetRole(array $arrayRecord)
    {
        //Get
        $arrayRole = self::$role->getRole($arrayRecord[0]["ROL_UID"]);

        $this->assertTrue(is_array($arrayRole));
        $this->assertNotEmpty($arrayRole);

        $this->assertEquals($arrayRole["ROL_UID"],  $arrayRecord[0]["ROL_UID"]);
        $this->assertEquals($arrayRole["ROL_CODE"], $arrayRecord[0]["ROL_CODE"]);
        $this->assertEquals($arrayRole["ROL_NAME"], $arrayRecord[0]["ROL_NAME"]);

        //Get - Japanese characters
        $arrayRole = self::$role->getRole($arrayRecord[self::$numRole]["ROL_UID"]);

        $this->assertTrue(is_array($arrayRole));
        $this->assertNotEmpty($arrayRole);

        $this->assertEquals($arrayRole["ROL_UID"],  $arrayRecord[self::$numRole]["ROL_UID"]);
        $this->assertEquals($arrayRole["ROL_CODE"], "PHPUNIT_MY_ROLE_" . self::$numRole);
        $this->assertEquals($arrayRole["ROL_NAME"], "テスト（PHPUnitの）");
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\Role::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testCreateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayRole = self::$role->create($arrayData);
    }

    /**
     * Test exception for required data (ROL_CODE)
     *
     * @covers \ProcessMaker\BusinessModel\Role::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Undefined value for "ROL_CODE", it is required.
     */
    public function testCreateExceptionRequiredDataRolCode()
    {
        $arrayData = array(
            //"ROL_CODE" => "PHPUNIT_MY_ROLE_N",
            "ROL_NAME" => "PHPUnit My Role N"
        );

        $arrayRole = self::$role->create($arrayData);
    }

    /**
     * Test exception for invalid data (ROL_CODE)
     *
     * @covers \ProcessMaker\BusinessModel\Role::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "ROL_CODE", it can not be empty.
     */
    public function testCreateExceptionInvalidDataRolCode()
    {
        $arrayData = array(
            "ROL_CODE" => "",
            "ROL_NAME" => "PHPUnit My Role N"
        );

        $arrayRole = self::$role->create($arrayData);
    }

    /**
     * Test exception for role code existing
     *
     * @covers \ProcessMaker\BusinessModel\Role::create
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The role code with ROL_CODE: "PHPUNIT_MY_ROLE_0" already exists.
     */
    public function testCreateExceptionExistsRolCode()
    {
        $arrayData = array(
            "ROL_CODE" => "PHPUNIT_MY_ROLE_0",
            "ROL_NAME" => "PHPUnit My Role 0"
        );

        $arrayRole = self::$role->create($arrayData);
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\BusinessModel\Role::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testUpdateExceptionEmptyData()
    {
        $arrayData = array();

        $arrayRole = self::$role->update("", $arrayData);
    }

    /**
     * Test exception for invalid role UID
     *
     * @covers \ProcessMaker\BusinessModel\Role::update
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The role with ROL_UID: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx does not exist.
     */
    public function testUpdateExceptionInvalidRolUid()
    {
        $arrayData = array(
            "ROL_CODE" => "PHPUNIT_MY_ROLE_N",
            "ROL_NAME" => "PHPUnit My Role N"
        );

        $arrayRole = self::$role->update("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $arrayData);
    }

    /**
     * Test exception for invalid data (ROL_CODE)
     *
     * @covers \ProcessMaker\BusinessModel\Role::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the roles
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "ROL_CODE", it can not be empty.
     */
    public function testUpdateExceptionInvalidDataRolCode(array $arrayRecord)
    {
        $arrayData = array(
            "ROL_CODE" => "",
            "ROL_NAME" => "PHPUnit My Role 0"
        );

        $arrayRole = self::$role->update($arrayRecord[0]["ROL_UID"], $arrayData);
    }

    /**
     * Test exception for role code existing
     *
     * @covers \ProcessMaker\BusinessModel\Role::update
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the roles
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The role code with ROL_CODE: "PHPUNIT_MY_ROLE_1" already exists.
     */
    public function testUpdateExceptionExistsRolCode(array $arrayRecord)
    {
        $arrayData = $arrayRecord[1];

        $arrayRole = self::$role->update($arrayRecord[0]["ROL_UID"], $arrayData);
    }

    /**
     * Test delete roles
     *
     * @covers \ProcessMaker\BusinessModel\Role::delete
     *
     * @depends testCreate
     * @param   array $arrayRecord Data of the roles
     */
    public function testDelete(array $arrayRecord)
    {
        foreach ($arrayRecord as $value) {
            self::$role->delete($value["ROL_UID"]);
        }

        $arrayRole = self::$role->getRoles(array("filter" => "PHPUNIT"));

        $this->assertTrue(is_array($arrayRole));
        $this->assertEmpty($arrayRole);
    }

    /**
     * Test exception for role UID that cannot be deleted
     *
     * @covers \ProcessMaker\BusinessModel\Role::delete
     *
     * @expectedException        Exception
     * @expectedExceptionMessage This role cannot be deleted while it still has some assigned users.
     */
    public function testDeleteExceptionCannotDeleted()
    {
        self::$role->delete("00000000000000000000000000000002");
    }
}

