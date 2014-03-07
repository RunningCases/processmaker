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
    public function testSaveDepartment()
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
    }

    public function testGetDepartments()
    {
        $oDepartment = new \BusinessModel\Department();
        $arrayDepartments = $oDepartment->getDepartments();
        $this->assertTrue(is_array($arrayDepartments));
        $this->assertEquals(count($arrayDepartments), 1);
        $this->assertTrue(is_array($arrayDepartments[0]['dep_children']));
        $this->assertEquals(count($arrayDepartments[0]['dep_children']), 1);
    }

    public function testGetDepartment()
    {
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
    }

    public function testDeleteDepartment()
    {
        $oDepartment = new \BusinessModel\Department();
        $arrayDepartments = $oDepartment->getDepartments();
        $dataDepChild = $arrayDepartments[0]['dep_children'];

        $oDepartment->deleteDepartment($dataDepChild[0]['dep_uid']);
        $oDepartment->deleteDepartment($arrayDepartments[0]['dep_uid']);

        $arrayDepartments = $oDepartment->getDepartments();
        $this->assertEquals(count($arrayDepartments), 0);
    }
}

