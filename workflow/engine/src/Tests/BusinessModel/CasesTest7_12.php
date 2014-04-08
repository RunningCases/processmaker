<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class Cases Test
 *
 * @copyright Colosa - Bolivia
 *
 * @protected
 * @package Tests\BusinessModel
 */
class CasesTest extends \PHPUnit_Framework_TestCase
{
    protected $oCases;
    protected $idCase = '';

    protected static $usrUid = "00000000000000000000000000000001";
    protected static $usrUid2 = "00000000000000000000000000000012";
    protected static $proUid = "00000000000000000000000000000003";
    protected static $tasUid = "00000000000000000000000000000004";
    protected static $tasUid2 = "00000000000000000000000000000005";
    protected static $tasUid3 = "00000000000000000000000000000006";

    public static function setUpBeforeClass()
    {
        $process = new \Process();
        $process->create(array("type"=>"classicProject", "PRO_TITLE"=> "NEW TEST PHP UNIT", "PRO_DESCRIPTION"=> "465",
            "PRO_CATEGORY"=> "", "PRO_CREATE_USER"=> "00000000000000000000000000000001",
            "PRO_UID"=> self::$proUid, "USR_UID"=> "00000000000000000000000000000001"), false);

        $task = new \Task();
        $task->create(array("TAS_START"=>"TRUE", "TAS_UID"=> self::$tasUid, "PRO_UID"=> self::$proUid, "TAS_TITLE" => "NEW TASK TEST PHP UNIT",
            "TAS_POSX"=> 581, "TAS_POSY"=> 47, "TAS_WIDTH"=> 165, "TAS_HEIGHT"=> 40), false);
        $task = new \Task();
        $task->create(array( "TAS_UID"=> self::$tasUid2, "PRO_UID"=> self::$proUid, "TAS_TITLE" => "NEW TASK ONE",
            "TAS_POSX"=> 481, "TAS_POSY"=> 127, "TAS_WIDTH"=> 165, "TAS_HEIGHT"=> 40), false);
        $task = new \Task();
        $task->create(array( "TAS_UID"=> self::$tasUid3, "PRO_UID"=> self::$proUid, "TAS_TITLE" => "NEW TASK TWO",
            "TAS_POSX"=> 681, "TAS_POSY"=> 127, "TAS_WIDTH"=> 165, "TAS_HEIGHT"=> 40), false);

        $nw = new \processMap();
        $nw->saveNewPattern(self::$proUid, self::$tasUid, self::$tasUid2, 'PARALLEL', true);
        $nw = new \processMap();
        $nw->saveNewPattern(self::$proUid, self::$tasUid, self::$tasUid3, 'PARALLEL', true);

        $user = new \Users();
        $user->create(array("USR_ROLE"=> "PROCESSMAKER_ADMIN","USR_UID"=> self::$usrUid2, "USR_USERNAME"=> "dummy",
            "USR_PASSWORD"=>"21232f297a57a5a743894a0e4a801fc3", "USR_FIRSTNAME"=>"dummy_firstname", "USR_LASTNAME"=>"dummy_lastname",
            "USR_EMAIL"=>"dummy@dummy.com", "USR_DUE_DATE"=>'2020-01-01', "USR_CREATE_DATE"=>"2014-01-01 12:00:00", "USR_UPDATE_DATE"=>"2014-01-01 12:00:00",
            "USR_STATUS"=>"ACTIVE", "USR_UX"=>"NORMAL"));

        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid, "USR_UID"=> self::$usrUid, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));
        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid, "USR_UID"=> self::$usrUid2, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));

        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid2, "USR_UID"=> self::$usrUid, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));
        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid2, "USR_UID"=> self::$usrUid2, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));

        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid3, "USR_UID"=> self::$usrUid, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));
        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid3, "USR_UID"=> self::$usrUid2, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));
    }


    /**
     * Set class for test
     *
     * @coversNothing
     *
     * @copyright Colosa - Bolivia
     */
    public function setUp()
    {
        $this->oCases = new \ProcessMaker\BusinessModel\Cases();
        return true;
    }

    /**
     * Test error for incorrect value of process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid process 12345678912345678912345678912345678
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseErrorIncorrectProcessValueArray()
    {
        $this->oCases->addCase('12345678912345678912345678912345678', self::$tasUid, self::$usrUid, array());
    }

    /**
     * Test error for incorrect value of task in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCase
     * @expectedException        Exception
     * @expectedExceptionMessage Task invalid or the user is not assigned to the task
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseErrorIncorrectTaskValueArray()
    {
        $this->oCases->addCase(self::$proUid, '12345678912345678912345678912345678', self::$usrUid, array());
    }

    /**
     * Test add Case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCase
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCase()
    {
        $response = $this->oCases->addCase(self::$proUid, self::$tasUid, self::$usrUid, array());
        $this->assertTrue(is_object($response));
        $aResponse = json_decode(json_encode($response), true);
        return $aResponse;
    }

    /**
     * Test error for incorrect value of case in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getTaskCase
     * @expectedException        Exception
     * @expectedExceptionMessage Incorrect or unavailable information about this case: 12345678912345678912345678912345678
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetTaskCaseErrorIncorrectCaseValueArray()
    {
        $this->oCases->getTaskCase('12345678912345678912345678912345678', self::$usrUid);
    }

    /**
     * Test get Task Case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getTaskCase
     * @depends testAddCase
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetTaskCase(array $aResponse)
    {
        $response = $this->oCases->getTaskCase($aResponse['app_uid'], self::$usrUid);
        $this->assertTrue(is_array($response));
    }

    /**
     * Test error for incorrect value of case in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getCaseInfo
     * @expectedException        Exception
     * @expectedExceptionMessage The Application row '12345678912345678912345678912345678' doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCaseInfoErrorIncorrectCaseValueArray()
    {
        $this->oCases->getCaseInfo('12345678912345678912345678912345678', self::$usrUid);
    }

    /**
     * Test get Case Info
     *
     * @covers \ProcessMaker\BusinessModel\Cases::getCaseInfo
     * @depends testAddCase
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCaseInfo(array $aResponse)
    {
        $response = $this->oCases->getCaseInfo($aResponse['app_uid'], self::$usrUid);
        $this->assertTrue(is_object($response));
    }

    /**
     * Test error for incorrect value of user delegation in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::updateReassignCase
     * @depends testAddCase
     * @param array $aResponse
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid Case Delegation index for this user
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateReassignCaseErrorIncorrectUserValueArray(array $aResponse)
    {
        $this->oCases->updateReassignCase($aResponse['app_uid'], self::$usrUid, null, self::$usrUid, self::$usrUid2);
    }

    /**
     * Test error for incorrect value of case in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::updateReassignCase
     * @expectedException        Exception
     * @expectedExceptionMessage The Application with app_uid: 12345678912345678912345678912345678 doesn't exist
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateReassignCaseErrorIncorrectCaseValueArray()
    {
        $this->oCases->updateReassignCase('12345678912345678912345678912345678', self::$usrUid, null, self::$usrUid2, self::$usrUid);
    }

    /**
     * Test put reassign case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::updateReassignCase
     * @depends testAddCase
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateReassignCase(array $aResponse)
    {
        $response = $this->oCases->updateReassignCase($aResponse['app_uid'], self::$usrUid, null, self::$usrUid2, self::$usrUid);
        $this->assertTrue(empty($response));
    }

    /**
     * Test add Case to test route case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCase
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseRouteCase()
    {
        $response = $this->oCases->addCase(self::$proUid, self::$tasUid, self::$usrUid, array());
        $this->assertTrue(is_object($response));
        $aResponseRouteCase = json_decode(json_encode($response), true);
        return $aResponseRouteCase;
    }

    /**
     * Test error for incorrect value of case in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::updateRouteCase
     * @expectedException        Exception
     * @expectedExceptionMessage The row '12345678912345678912345678912345678, ' in table AppDelegation doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateRouteCaseErrorIncorrectCaseValueArray()
    {
        $this->oCases->updateRouteCase('12345678912345678912345678912345678', self::$usrUid, null);
    }

    /**
     * Test put route case
     *
     * @covers \ProcessMaker\BusinessModel\Cases::updateRouteCase
     * @depends testAddCaseRouteCase
     * @param array $aResponseRouteCase
     *
     * @copyright Colosa - Bolivia
     */
    public function testUpdateRouteCase(array $aResponseRouteCase)
    {
        $response = $this->oCases->updateRouteCase($aResponseRouteCase['app_uid'], self::$usrUid, null);
        $this->assertTrue(empty($response));
    }

    /**
     * Test error for incorrect value of process in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid process 12345678912345678912345678912345678
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseImpersonateErrorIncorrectProcessValueArray()
    {
        $this->oCases->addCaseImpersonate('12345678912345678912345678912345678', self::$usrUid2, self::$tasUid, array());
    }

    /**
     * Test error for incorrect value of task in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCase
     * @expectedException        Exception
     * @expectedExceptionMessage User not registered! 12345678912345678912345678912345678!!
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseImpersonateErrorIncorrectTaskValueArray()
    {
        $this->oCases->addCaseImpersonate(self::$proUid, '12345678912345678912345678912345678', self::$tasUid, array());
    }


    /**
     * Test add Case impersonate
     *
     * @covers \ProcessMaker\BusinessModel\Cases::addCaseImpersonate
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCaseImpersonate()
    {
        $response = $this->oCases->addCaseImpersonate(self::$proUid, self::$usrUid2, self::$tasUid, array());
        $this->assertTrue(is_object($response));
    }

    public static function tearDownAfterClass()
    {
        $assign = new \TaskUser();
        $assign->remove(self::$tasUid, self::$usrUid, 1,1);

        $task = new \Task();
        $task->remove(self::$tasUid);

        $task = new \Task();
        $task->remove(self::$tasUid2);

        $task = new \Task();
        $task->remove(self::$tasUid3);

        $process = new \Process();
        $process->remove(self::$proUid);

        $criteria = new \Criteria("workflow");
        $criteria->addSelectColumn(\RoutePeer::PRO_UID);
        $criteria->add(\RoutePeer::PRO_UID, self::$proUid, \Criteria::EQUAL);
        \ProcessFilesPeer::doDelete($criteria);

        $user = new \Users();
        $user->remove(self::$usrUid2);

        $oConnection = \Propel::getConnection( \UsersPeer::DATABASE_NAME );
        try {
            $oUser = \UsersPeer::retrieveByPK( self::$usrUid2 );
            if (! is_null( $oUser )) {
                $oConnection->begin();
                $oUser->delete();
                $oConnection->commit();
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }
}
