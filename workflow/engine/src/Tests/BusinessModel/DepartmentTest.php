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
    public function testCreateDepartmentErrorArray()
    {
        try {
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment(true);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The field '". '$dep_data' . "' is not an array.");
        }
    }

    public function testCreateDepartmentErrorBoolean()
    {
        try {
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment(array('1'),array());
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The field '". '$create' . "' is not a boolean.");
        }
    }

    public function testCreateDepartmentErrorArrayEmpty()
    {
        try {
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment(array());
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The field '". '$dep_data' . "' is empty.");
        }
    }

    public function testCreateDepartmentErrorArrayDepUidExist()
    {
        try {
            $data = array('dep_uid' => 'testUidDepartment');
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment($data);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The departament with dep_uid: 'testUidDepartment' does not exist.");
        }
    }

    public function testCreateDepartmentErrorArrayDepParentExist()
    {
        try {
            $data = array('dep_parent' => 'testUidDepartment');
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment($data);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The departament with dep_parent: 'testUidDepartment' does not exist.");
        }
    }

    public function testCreateDepartmentErrorArrayDepManagerExist()
    {
        try {
            $data = array('dep_manager' => 'testUidUser');
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment($data);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The user with dep_manager: 'testUidUser' does not exist.");
        }
    }

    public function testCreateDepartmentErrorArrayDepStatus()
    {
        try {
            $data = array('dep_status' => 'SUPER ACTIVO');
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment($data);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The departament with dep_status: 'SUPER ACTIVO' is incorrect.");
        }
    }

    public function testCreateDepartmentErrorArrayDepTitleEmpty()
    {
        try {
            $data = array('dep_status' => 'ACTIVE');
            $oDepartment = new \BusinessModel\Department();
            $oDepartment->saveDepartment($data);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The field dep_title is required.");
        }
    }

    public function testCreateDepartmentErrorArrayDepTitleRepeated()
    {
        $oDepartment = new \BusinessModel\Department();

        ////////// Create department parent
        $dep1 = array (
            'dep_title' => 'departamento padre'
        );
        $arrayDepartments = $oDepartment->saveDepartment($dep1);
        $this->assertTrue(isset($arrayDepartments['dep_uid']));

        try {
            $arrayDepartments = $oDepartment->saveDepartment($dep1);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The departament with dep_title: 'departamento padre' exist.");
        }

        $oDepartment->deleteDepartment($arrayDepartments['dep_uid']);
    }

    public function testCreateDepartmentNormal()
    {
        $oDepartment = new \BusinessModel\Department();

        ////////// Create department parent
        $dep1 = array (
            'dep_title' => 'departamento padre'
        );
        $arrayDepartments = $oDepartment->saveDepartment($dep1);
        $this->assertTrue(isset($arrayDepartments['dep_uid']));
        $this->assertEquals($arrayDepartments['dep_parent'], '');
        $this->assertEquals($arrayDepartments['dep_title'], 'departamento padre');
        $this->assertEquals($arrayDepartments['dep_status'], 'ACTIVE');
        $this->assertEquals($arrayDepartments['dep_manager'], '');
        $this->assertEquals($arrayDepartments['has_children'], 0);


        $oDepartment->deleteDepartment($arrayDepartments['dep_uid']);
    }

    public function testUpdateDepartmentErrorArrayDepTitleRepeated()
    {
        $oDepartment = new \BusinessModel\Department();

        ////////// Create department parent
        $dep1 = array (
            'dep_title' => 'dep1'
        );
        $dep2 = array (
            'dep_title' => 'dep2'
        );
        $dataDep1 = $oDepartment->saveDepartment($dep1);
        $this->assertTrue(isset($dataDep1['dep_uid']));
        $dataDep2 = $oDepartment->saveDepartment($dep2);
        $this->assertTrue(isset($dataDep2['dep_uid']));

        $dep2Update = array (
            'dep_uid' => $dataDep2['dep_uid'],
            'dep_title' => 'dep1'
        );
        try {
            $oDepartment->saveDepartment($dep2Update, false);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "The departament with dep_title: 'dep1' exist.");
        }

        $oDepartment->deleteDepartment($dataDep1['dep_uid']);
        $oDepartment->deleteDepartment($dataDep2['dep_uid']);
    }

    public function testGetDepartments()
    {
        $oDepartment = new \BusinessModel\Department();

        ////////// Create department parent
        $dep1 = array (
            'dep_title' => 'departamento padre'
        );
        $arrayDepartments = $oDepartment->saveDepartment($dep1);
        $this->assertTrue(isset($arrayDepartments['dep_uid']));
        $this->assertEquals($arrayDepartments['dep_parent'], '');
        $this->assertEquals($arrayDepartments['dep_title'], 'departamento padre');
        $this->assertEquals($arrayDepartments['dep_status'], 'ACTIVE');
        $this->assertEquals($arrayDepartments['dep_manager'], '');
        $this->assertEquals($arrayDepartments['has_children'], 0);

        ////////// Create department child
        $dep1Uid = $arrayDepartments['dep_uid'];
        $dep2    = array (
            'dep_parent' => $dep1Uid,
            'dep_manager' => '00000000000000000000000000000001',
            'dep_title' => 'departamento hijo1',
            'dep_status' => 'INACTIVE'
        );
        $arrayDepartments2 = $oDepartment->saveDepartment($dep2);
        $this->assertTrue(isset($arrayDepartments2['dep_uid']));
        $this->assertEquals($arrayDepartments2['dep_parent'], $dep1Uid);
        $this->assertEquals($arrayDepartments2['dep_title'], 'departamento hijo1');
        $this->assertEquals($arrayDepartments2['dep_status'], 'INACTIVE');
        $this->assertEquals($arrayDepartments2['dep_manager'], '00000000000000000000000000000001');
        $this->assertEquals($arrayDepartments2['has_children'], 0);

        ////////// Update department parent
        $depUp1 = array (
            'dep_uid' => $dep1Uid,
            'dep_title' => 'DepPadre',
            'dep_manager' => '00000000000000000000000000000001'
        );
        $oDepartment->saveDepartment($depUp1, false);

        $dep2Uid = $arrayDepartments2['dep_uid'];
        $depUp2 = array (
            'dep_uid' => $dep2Uid,
            'dep_title' => 'DepHijo',
            'dep_manager' => '',
        );
        $oDepartment->saveDepartment($depUp2, false);

        $oDepartment = new \BusinessModel\Department();
        $arrayDepartments = $oDepartment->getDepartments();
        $this->assertTrue(is_array($arrayDepartments));
        $this->assertEquals(count($arrayDepartments), 1);
        $this->assertTrue(is_array($arrayDepartments[0]['dep_children']));
        $this->assertEquals(count($arrayDepartments[0]['dep_children']), 1);


        $oDepartment = new \BusinessModel\Department();
        $arrayDepartments = $oDepartment->getDepartments();
        $depIdPadre = $arrayDepartments[0]['dep_uid'];
        $depIdChild = $arrayDepartments[0]['dep_children'][0]['dep_uid'];
        $oDepartment = new \BusinessModel\Department();
        $dataPadre = $oDepartment->getDepartment($depIdPadre);
        $dataChild = $oDepartment->getDepartment($depIdChild);

        $this->assertTrue(is_array($dataPadre));
        $this->assertEquals($dataPadre['dep_title'], 'DepPadre');
        $this->assertEquals($dataPadre['dep_manager'], '00000000000000000000000000000001');
        $this->assertTrue(is_array($dataChild));
        $this->assertEquals($dataChild['dep_title'], 'DepHijo');
        $this->assertEquals($dataChild['dep_manager'], '');

        $oDepartment->deleteDepartment($depIdChild);
        $oDepartment->deleteDepartment($depIdPadre);
    }

    // TODO: Assigned Users to department
    public function testDeleteDepartmentErrorUsersSelections()
    {

    }

    public function testDeleteDepartmentErrorDepartmentParent()
    {
        $oDepartment = new \BusinessModel\Department();

        ////////// Create department parent
        $dep1 = array (
            'dep_title' => 'dep1'
        );
        $dataDep1 = $oDepartment->saveDepartment($dep1);
        $this->assertTrue(isset($dataDep1['dep_uid']));
        $dep2 = array (
            'dep_title' => 'dep2',
            'dep_parent' => $dataDep1['dep_uid']
        );
        $dataDep2 = $oDepartment->saveDepartment($dep2);
        $this->assertTrue(isset($dataDep2['dep_uid']));

        try {
            $oDepartment->deleteDepartment($dataDep1['dep_uid']);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            $this->assertEquals($res, "Can not delete the department. The department has children");
        }

        $oDepartment->deleteDepartment($dataDep2['dep_uid']);
        $oDepartment->deleteDepartment($dataDep1['dep_uid']);
    }
}

