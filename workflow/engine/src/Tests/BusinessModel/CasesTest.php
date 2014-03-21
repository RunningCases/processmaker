<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class Cases Test
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 * @package Tests\BusinessModel
 */
class CasesTest extends \PHPUnit_Framework_TestCase
{
    protected $oCases;

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
        $this->oCases = new \BusinessModel\Cases();
        return true;
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$dataList' it must be an array.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorArray()
    {
        $this->oCases->getList(true);
    }

    /**
     * Test error for empty userId in array
     *
     * @covers \BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The user with userId: '' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorUserIdArray()
    {
        $this->oCases->getList(array());
    }

    /**
     * Test error for not exists userId in array
     *
     * @covers \BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The user with userId: 'UidInexistente' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorNotExistsUserIdArray()
    {
        $this->oCases->getList(array('userId' => 'UidInexistente'));
    }

    /**
     * Test error for incorrect value $action in array
     *
     * @covers \BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The value for $action is incorrect.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueArray()
    {
        $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'incorrect'));
    }

    /**
     * Test get list to do
     *
     * @covers \BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesToDo()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_numeric($response['totalCount']));
        $this->assertTrue(is_array($response['data']));
    }

    /**
     * Test get list draft
     *
     * @covers \BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesDraft()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'draft'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_numeric($response['totalCount']));
        $this->assertTrue(is_array($response['data']));
    }

    /**
     * Test get list participated
     *
     * @covers \BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesParticipated()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'sent'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_numeric($response['totalCount']));
        $this->assertTrue(is_array($response['data']));
    }

    /**
     * Test get list unassigned
     *
     * @covers \BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesUnassigned()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'unassigned'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_numeric($response['totalCount']));
        $this->assertTrue(is_array($response['data']));
    }

    /**
     * Test get list search
     *
     * @covers \BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesSearch()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'search'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_numeric($response['totalCount']));
        $this->assertTrue(is_array($response['data']));
    }

    /**
     * Test add Case
     *
     * @covers \BusinessModel\Cases::addCase
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCase()
    {
        $response = $this->oCases->addCase('1265557095225ff5c688f46031700471', '46941969352af5be2ab3f39001216717', '00000000000000000000000000000001', array('name' => 'John', 'lastname' => 'Petersson'));
        $this->assertTrue(is_object($response));
        $aResponse = json_decode(json_encode($response), true);
        return $aResponse;
    }

    /**
     * Test get Task Case
     *
     * @covers \BusinessModel\Cases::getTaskCase
     * @depends testAddCase
     * @param array $aResponse, Data for parent department
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetTaskCase(array $aResponse)
    {
        $response = $this->oCases->getTaskCase($aResponse['app_uid'], '00000000000000000000000000000001');
        $this->assertTrue(is_array($response));
    }

    /**
     * Test get Case Info
     *
     * @covers \BusinessModel\Cases::getCaseInfo
     * @depends testAddCase
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCaseInfo(array $aResponse)
    {
        $response = $this->oCases->getCaseInfo($aResponse['app_uid'], '00000000000000000000000000000001');
        $this->assertTrue(is_object($response));
    }

    /**
     * Test put reassign case
     *
     * @covers \BusinessModel\Cases::getCaseInfo
     * @depends testAddCase
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateReassignCase(array $aResponse)
    {
        $response = $this->oCases->updateReassignCase($aResponse['app_uid'], '00000000000000000000000000000001', null, '00000000000000000000000000000001', '23063198853206b666e2bd0085065170');
        $this->assertTrue(empty($response));
    }

    /**
     * Test add Case to test route case
     *
     * @covers \BusinessModel\Cases::addCase
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseRouteCase()
    {
        $response = $this->oCases->addCase('1265557095225ff5c688f46031700471', '46941969352af5be2ab3f39001216717', '00000000000000000000000000000001', array('name' => 'John', 'lastname' => 'Petersson'));
        $this->assertTrue(is_object($response));
        $aResponseRouteCase = json_decode(json_encode($response), true);
        return $aResponseRouteCase;
    }

    /**
     * Test put route case
     *
     * @covers \BusinessModel\Cases::updateRouteCase
     * @depends testAddCaseRouteCase
     * @param array $aResponseRouteCase
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateRouteCase(array $aResponseRouteCase)
    {
        $response = $this->oCases->updateRouteCase($aResponseRouteCase['app_uid'], '00000000000000000000000000000001', null);
        $this->assertTrue(empty($response));
    }

    /**
     * Test add Case impersonate to test route case
     *
     * @covers \BusinessModel\Cases::addCaseImpersonate
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseImpersonate()
    {
        $response = $this->oCases->addCaseImpersonate('1265557095225ff5c688f46031700471', '23063198853206b666e2bd0085065170', '46941969352af5be2ab3f39001216717', array('name' => 'John', 'lastname' => 'Petersson'));
        $this->assertTrue(is_object($response));
    }

}

