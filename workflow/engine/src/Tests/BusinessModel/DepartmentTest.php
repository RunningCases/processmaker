<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class Department Test
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 * @package Tests\BusinessModel
 */
class DepartmentTest extends \PHPUnit_Framework_TestCase
{
    protected $oDepartment;

    /**
     * Set class for test
     *
     * @coversNothing
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function setUp()
    {
        $this->oDepartment = new \BusinessModel\Department();
        return true;
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$dep_data' it must be an array.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArray()
    {
        $this->oDepartment->saveDepartment(true);
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$create' it must be a boolean.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorBoolean()
    {
        $this->oDepartment->saveDepartment(array('1'),array());
    }

    /**
     * Test error for empty array in first field the function
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The field '$dep_data' is empty.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArrayEmpty()
    {
        $this->oDepartment->saveDepartment(array());
    }

    /**
     * Test error for create department with nonexistent dep_parent
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The departament with dep_parent: 'testUidDepartment' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArrayDepParentExist()
    {
        $data = array('dep_parent' => 'testUidDepartment');
        $this->oDepartment->saveDepartment($data);
    }

    /**
     * Test error for create department with nonexistent dep_manager
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The user with dep_manager: 'testUidUser' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArrayDepManagerExist()
    {
        $data = array('dep_manager' => 'testUidUser');
        $this->oDepartment->saveDepartment($data);
    }

    /**
     * Test error for create department with incorrect dep_status
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The departament with dep_status: 'SUPER ACTIVO' is incorrect.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArrayDepStatus()
    {
        $data = array('dep_status' => 'SUPER ACTIVO');
        $this->oDepartment->saveDepartment($data);
    }

    /**
     * Test error for create department untitled
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The field dep_title is required.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArrayDepTitleEmpty()
    {
        $data = array('dep_status' => 'ACTIVE');
        $this->oDepartment->saveDepartment($data);
    }

    /**
     * Save department parent
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentParent()
    {
        $data = array('dep_title' => 'Departamento Padre');
        $dep_data = $this->oDepartment->saveDepartment($data);
        $this->assertTrue(is_array($dep_data));
        $this->assertTrue(isset($dep_data['dep_uid']));
        $this->assertEquals($dep_data['dep_parent'], '');
        $this->assertEquals($dep_data['dep_title'], 'Departamento Padre');
        $this->assertEquals($dep_data['dep_status'], 'ACTIVE');
        $this->assertEquals($dep_data['dep_manager'], '');
        $this->assertEquals($dep_data['has_children'], 0);
        return $dep_data;
    }

    /**
     * Test error for create department with title exist
     *
     * @depends testCreateDepartmentParent
     * @param array $dep_data, Data for parent department
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The departament with dep_title: 'Departamento Padre' already exists.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentErrorArrayDepTitleRepeated(array $dep_data)
    {
        $data = array('dep_title' => 'Departamento Padre');
        $this->oDepartment->saveDepartment($data);
    }

    /**
     * Test error for create department untitled
     *
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The departament with dep_uid: 'testUidDepartment' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testUpdateDepartmentErrorArrayDepUidExist()
    {
        $data = array('dep_uid' => 'testUidDepartment');
        $this->oDepartment->saveDepartment($data);
    }

    /**
     * Save department child
     *
     * @depends testCreateDepartmentParent
     * @param array $dep_data, Data for parent department
     * @covers \BusinessModel\Department::saveDepartment
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testCreateDepartmentChild(array $dep_data)
    {
        $data = array(
            'dep_title' => 'Departamento Child',
            'dep_parent' => $dep_data['dep_uid'],
            'dep_status' => 'INACTIVE',
            'dep_manager' => '00000000000000000000000000000001'
        );
        $child_data = $this->oDepartment->saveDepartment($data);
        $this->assertTrue(is_array($child_data));
        $this->assertTrue(isset($child_data['dep_uid']));
        $this->assertEquals($child_data['dep_parent'], $dep_data['dep_uid']);
        $this->assertEquals($child_data['dep_title'], 'Departamento Child');
        $this->assertEquals($child_data['dep_status'], 'INACTIVE');
        $this->assertEquals($child_data['dep_manager'], '00000000000000000000000000000001');
        $this->assertEquals($child_data['has_children'], 0);
        return $child_data;
    }

    /**
     * Test error for update department with title exist
     *
     * @depends testCreateDepartmentParent
     * @depends testCreateDepartmentChild
     * @param array $dep_data, Data for parent department
     * @param array $child_data, Data for child department
     * @covers \BusinessModel\Department::saveDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage The departament with dep_title: 'Departamento Padre' already exists.
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testUpdateDepartmentErrorArrayDepTitleRepeated(array $dep_data, array $child_data)
    {
        $dataUpdate = array (
            'dep_uid' => $child_data['dep_uid'],
            'dep_title' => 'Departamento Padre'
        );
        $this->oDepartment->saveDepartment($dataUpdate, false);
    }

    /**
     * Test get departments array
     *
     * @depends testCreateDepartmentParent
     * @depends testCreateDepartmentChild
     * @param array $dep_data, Data for parent department
     * @param array $child_data, Data for child department
     * @covers \BusinessModel\Department::getDepartments
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetDepartments(array $dep_data, array $child_data)
    {
        $arrayDepartments = $this->oDepartment->getDepartments();
        $this->assertTrue(is_array($arrayDepartments));
        $this->assertEquals(count($arrayDepartments), 1);
        $this->assertTrue(is_array($arrayDepartments[0]['dep_children']));
        $this->assertEquals(count($arrayDepartments[0]['dep_children']), 1);

        $dataParent = $arrayDepartments[0];
        $this->assertEquals($dep_data['dep_parent'], $dataParent['dep_parent']);
        $this->assertEquals($dep_data['dep_title'], $dataParent['dep_title']);
        $this->assertEquals($dep_data['dep_status'], $dataParent['dep_status']);
        $this->assertEquals($dep_data['dep_manager'], $dataParent['dep_manager']);

        $dataChild = $arrayDepartments[0]['dep_children'][0];
        $this->assertEquals($child_data['dep_parent'], $dataChild['dep_parent']);
        $this->assertEquals($child_data['dep_title'], $dataChild['dep_title']);
        $this->assertEquals($child_data['dep_status'], $dataChild['dep_status']);
        $this->assertEquals($child_data['dep_manager'], $dataChild['dep_manager']);
    }

    /**
     * Test get department array
     *
     * @depends testCreateDepartmentParent
     * @param array $dep_data, Data for parent department
     * @covers \BusinessModel\Department::getDepartment
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetDepartment(array $dep_data)
    {
        $dataParent = $this->oDepartment->getDepartment($dep_data['dep_uid']);
        $this->assertTrue(is_array($dataParent));

        $this->assertEquals($dep_data['dep_parent'], $dataParent['dep_parent']);
        $this->assertEquals($dep_data['dep_title'], $dataParent['dep_title']);
        $this->assertEquals($dep_data['dep_status'], $dataParent['dep_status']);
        $this->assertEquals($dep_data['dep_manager'], $dataParent['dep_manager']);
    }

    // TODO: Assigned Users to department
    public function testDeleteDepartmentErrorUsersSelections()
    {

    }

    /**
     * Test error for delete department with children
     *
     * @depends testCreateDepartmentParent
     * @depends testCreateDepartmentChild
     * @param array $dep_data, Data for parent department
     * @param array $child_data, Data for child department
     * @covers \BusinessModel\Department::deleteDepartment
     * @expectedException        Exception
     * @expectedExceptionMessage Can not delete the department, it has a children department.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testDeleteDepartmentErrorDepartmentParent(array $dep_data, array $child_data)
    {
        $this->oDepartment->deleteDepartment($dep_data['dep_uid']);
    }

    /**
     * Test get departments array
     *
     * @depends testCreateDepartmentParent
     * @depends testCreateDepartmentChild
     * @param array $dep_data, Data for parent department
     * @param array $child_data, Data for child department
     * @covers \BusinessModel\Department::deleteDepartment
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testDeleteDepartments(array $dep_data, array $child_data)
    {
        $this->oDepartment->deleteDepartment($child_data['dep_uid']);
        $this->oDepartment->deleteDepartment($dep_data['dep_uid']);
        $dataParent = $this->oDepartment->getDepartments();
        $this->assertTrue(empty($dataParent));
    }
}

