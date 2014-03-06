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
    public function testGetDepartments()
    {
        $oDepartment = new \BusinessModel\Department();
        $arrayDepartments = $oDepartment->getDepartments();
    }
}

