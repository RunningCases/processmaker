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
        $this->oCases = new \ProcessMaker\BusinessModel\Cases();
        return true;
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
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
     * @covers \ProcessMaker\BusinessModel\Cases::getList
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
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The user with userId: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorNotExistsUserIdArray()
    {
        $this->oCases->getList(array('userId' => 'IdDoesNotExists'));
    }

    /**
     * Test error for incorrect value $action in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The value for $action is incorrect.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueActionArray()
    {
        $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'incorrect'));
    }

    /**
     * Test error for incorrect value $process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The process with $pro_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueProUidArray()
    {
        $this->oCases->getList(array(
            'userId' => '00000000000000000000000000000001',
            'process' => 'IdDoesNotExists'
        ));
    }

    /**
     * Test error for incorrect value $process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The category with $cat_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueCatUidArray()
    {
        $this->oCases->getList(array(
            'userId' => '00000000000000000000000000000001',
            'category' => 'IdDoesNotExists'
        ));
    }

    /**
     * Test error for incorrect value $process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The user with $usr_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueUserArray()
    {
        $this->oCases->getList(array(
            'userId' => '00000000000000000000000000000001',
            'user' => 'IdDoesNotExists'
        ));
    }

    /**
     * Test error for incorrect value $process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The value '2014-44-44' is not a valid date for the format 'Y-m-d'.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueDateToArray()
    {
        $this->oCases->getList(array(
            'userId' => '00000000000000000000000000000001',
            'dateTo' => '2014-44-44'
        ));
    }

    /**
     * Test error for incorrect value $process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     * @expectedException        Exception
     * @expectedExceptionMessage The value '2014-44-44' is not a valid date for the format 'Y-m-d'.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesErrorIncorrectValueDateFromArray()
    {
        $this->oCases->getList(array(
            'userId' => '00000000000000000000000000000001',
            'dateFrom' => '2014-44-44'
        ));
    }

    /**
     * Test get list to do not paged
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesToDoNotPaged()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'paged' => false));
        $this->assertTrue(is_array($response));
        $this->assertFalse(isset($response['data']));
        $this->assertFalse(isset($response['total']));
        $this->assertFalse(isset($response['start']));
        $this->assertFalse(isset($response['limit']));
        $this->assertFalse(isset($response['sort']));
        $this->assertFalse(isset($response['dir']));
        $this->assertFalse(isset($response['cat_uid']));
        $this->assertFalse(isset($response['pro_uid']));
        $this->assertFalse(isset($response['search']));
    }

    /**
     * Test get list to do
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesToDo()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_array($response['data']));
        $this->assertTrue(isset($response['total']));
        $this->assertTrue(isset($response['start']));
        $this->assertTrue(isset($response['limit']));
        $this->assertTrue(isset($response['sort']));
        $this->assertTrue(isset($response['dir']));
        $this->assertTrue(isset($response['cat_uid']));
        $this->assertTrue(isset($response['pro_uid']));
        $this->assertTrue(isset($response['search']));
    }

    /**
     * Test get list draft not paged
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesDraftNotPaged()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'draft', 'paged' => false));
        $this->assertTrue(is_array($response));
        $this->assertFalse(isset($response['data']));
        $this->assertFalse(isset($response['total']));
        $this->assertFalse(isset($response['start']));
        $this->assertFalse(isset($response['limit']));
        $this->assertFalse(isset($response['sort']));
        $this->assertFalse(isset($response['dir']));
        $this->assertFalse(isset($response['cat_uid']));
        $this->assertFalse(isset($response['pro_uid']));
        $this->assertFalse(isset($response['search']));
    }

    /**
     * Test get list draft
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesDraft()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'draft'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_array($response['data']));
        $this->assertTrue(isset($response['total']));
        $this->assertTrue(isset($response['start']));
        $this->assertTrue(isset($response['limit']));
        $this->assertTrue(isset($response['sort']));
        $this->assertTrue(isset($response['dir']));
        $this->assertTrue(isset($response['cat_uid']));
        $this->assertTrue(isset($response['pro_uid']));
        $this->assertTrue(isset($response['search']));
    }

    /**
     * Test get list participated not paged
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesParticipatedNotPaged()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'sent', 'paged' => false));
        $this->assertTrue(is_array($response));
        $this->assertFalse(isset($response['data']));
        $this->assertFalse(isset($response['total']));
        $this->assertFalse(isset($response['start']));
        $this->assertFalse(isset($response['limit']));
        $this->assertFalse(isset($response['sort']));
        $this->assertFalse(isset($response['dir']));
        $this->assertFalse(isset($response['cat_uid']));
        $this->assertFalse(isset($response['pro_uid']));
        $this->assertFalse(isset($response['search']));
    }

    /**
     * Test get list participated
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesParticipated()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'sent'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_array($response['data']));
        $this->assertTrue(isset($response['total']));
        $this->assertTrue(isset($response['start']));
        $this->assertTrue(isset($response['limit']));
        $this->assertTrue(isset($response['sort']));
        $this->assertTrue(isset($response['dir']));
        $this->assertTrue(isset($response['cat_uid']));
        $this->assertTrue(isset($response['pro_uid']));
        $this->assertTrue(isset($response['search']));
    }

    /**
     * Test get list unassigned not paged
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesUnassignedNotPaged()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'unassigned', 'paged' => false));
        $this->assertTrue(is_array($response));
        $this->assertFalse(isset($response['data']));
        $this->assertFalse(isset($response['total']));
        $this->assertFalse(isset($response['start']));
        $this->assertFalse(isset($response['limit']));
        $this->assertFalse(isset($response['sort']));
        $this->assertFalse(isset($response['dir']));
        $this->assertFalse(isset($response['cat_uid']));
        $this->assertFalse(isset($response['pro_uid']));
        $this->assertFalse(isset($response['search']));
    }

    /**
     * Test get list unassigned
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesUnassigned()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'unassigned'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_array($response['data']));
        $this->assertTrue(isset($response['total']));
        $this->assertTrue(isset($response['start']));
        $this->assertTrue(isset($response['limit']));
        $this->assertTrue(isset($response['sort']));
        $this->assertTrue(isset($response['dir']));
        $this->assertTrue(isset($response['cat_uid']));
        $this->assertTrue(isset($response['pro_uid']));
        $this->assertTrue(isset($response['search']));
    }

    /**
     * Test get list search not paged
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesSearchNotPaged()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'search', 'paged' => false));
        $this->assertTrue(is_array($response));
        $this->assertFalse(isset($response['data']));
        $this->assertFalse(isset($response['total']));
        $this->assertFalse(isset($response['start']));
        $this->assertFalse(isset($response['limit']));
        $this->assertFalse(isset($response['sort']));
        $this->assertFalse(isset($response['dir']));
        $this->assertFalse(isset($response['cat_uid']));
        $this->assertFalse(isset($response['pro_uid']));
        $this->assertFalse(isset($response['search']));
        $this->assertFalse(isset($response['app_status']));
        $this->assertFalse(isset($response['usr_uid']));
        $this->assertFalse(isset($response['date_from']));
        $this->assertFalse(isset($response['date_to']));
    }

    /**
     * Test get list search
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getList
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testGetListCasesSearch()
    {
        $response = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'search'));
        $this->assertTrue(is_array($response));
        $this->assertTrue(is_array($response['data']));
        $this->assertTrue(isset($response['total']));
        $this->assertTrue(isset($response['start']));
        $this->assertTrue(isset($response['limit']));
        $this->assertTrue(isset($response['sort']));
        $this->assertTrue(isset($response['dir']));
        $this->assertTrue(isset($response['cat_uid']));
        $this->assertTrue(isset($response['pro_uid']));
        $this->assertTrue(isset($response['search']));
        $this->assertTrue(isset($response['app_status']));
        $this->assertTrue(isset($response['usr_uid']));
        $this->assertTrue(isset($response['date_from']));
        $this->assertTrue(isset($response['date_to']));
    }
}

