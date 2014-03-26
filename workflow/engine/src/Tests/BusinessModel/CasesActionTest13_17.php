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
class CasesAction13_17Test extends \PHPUnit_Framework_TestCase
{
    protected $oCases;
    protected $nowCountTodo = 0;
    protected $nowCountDraft = 0;
    protected $nowCountPaused = 0;
    protected $idCaseToDo = '';
    protected $idCaseDraft = '';

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
        \G::loadClass('pmFunctions');

        $usrUid = '00000000000000000000000000000001';
        $proUid = '2317283235320c1a36972b2028131767';
        $tasUid = '7983935495320c1a75e1df6068322280';
        $idCaseToDo = PMFNewCase($proUid, $usrUid, $tasUid, array());
        PMFDerivateCase($idCaseToDo, 1);
        $this->idCaseToDo = $idCaseToDo;

        $idCaseDraft = PMFNewCase($proUid, $usrUid, $tasUid, array());
        $this->idCaseDraft = $idCaseDraft;

        $this->oCases = new \BusinessModel\Cases();
        $listToDo = $this->oCases->getList(array('userId' => '00000000000000000000000000000001'));
        $this->nowCountTodo = $listToDo['total'];

        $listDraft = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'draft'));
        $this->nowCountDraft = $listDraft['total'];

        $listPaused = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'paused'));
        $this->nowCountPaused = $listPaused['total'];
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::putCancelCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$app_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutCancelCaseErrorAppUidArray()
    {
        $this->oCases->putCancelCase(array(), '00000000000000000000000000000001');
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::putCancelCase
     * @expectedException        Exception
     * @expectedExceptionMessage The application with $app_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutCancelCaseErrorAppUidIncorrect()
    {
        $this->oCases->putCancelCase('IdDoesNotExists', '00000000000000000000000000000001');
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Cases::putCancelCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$usr_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutCancelCaseErrorUsrUidArray()
    {
        $this->oCases->putCancelCase($this->idCaseDraft, array());
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Cases::putCancelCase
     * @expectedException        Exception
     * @expectedExceptionMessage The user with $usr_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutCancelCaseErrorUsrUidIncorrect()
    {
        $this->oCases->putCancelCase($this->idCaseDraft, 'IdDoesNotExists');
    }

    /**
     * Test error for type in third field the function
     *
     * @covers \BusinessModel\Cases::putCancelCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$del_index' it must be a integer.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutCancelCaseErrorDelIndexIncorrect()
    {
        $this->oCases->putCancelCase($this->idCaseDraft, '00000000000000000000000000000001', 'string');
    }

    /**
     * Test for cancel case
     *
     * @covers \BusinessModel\Cases::putCancelCase
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutCancelCase()
    {
        $this->oCases->putCancelCase($this->idCaseDraft, '00000000000000000000000000000001');
        $this->oCases = new \BusinessModel\Cases();
        $listDraft = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'draft'));
        $this->assertNotEquals($this->nowCountDraft, $listDraft['total']);
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::putPauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$app_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCaseErrorAppUidArray()
    {
        $this->oCases->putPauseCase(array(), '00000000000000000000000000000001');
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::putPauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage The application with $app_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCaseErrorAppUidIncorrect()
    {
        $this->oCases->putPauseCase('IdDoesNotExists', '00000000000000000000000000000001');
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Cases::putPauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$usr_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCaseErrorUsrUidArray()
    {
        $this->oCases->putPauseCase($this->idCaseDraft, array());
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Cases::putPauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage The user with $usr_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCaseErrorUsrUidIncorrect()
    {
        $this->oCases->putPauseCase($this->idCaseDraft, 'IdDoesNotExists');
    }

    /**
     * Test error for type in third field the function
     *
     * @covers \BusinessModel\Cases::putPauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$del_index' it must be a integer.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCaseErrorDelIndexIncorrect()
    {
        $this->oCases->putPauseCase($this->idCaseDraft, '00000000000000000000000000000001', 'string');
    }

    /**
     * Test error for type in fourth field the function
     *
     * @covers \BusinessModel\Cases::putPauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage The value '2014-44-44' is not a valid date for the format 'Y-m-d'.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCaseErrorDateIncorrect()
    {
        $this->oCases->putPauseCase($this->idCaseDraft, '00000000000000000000000000000001', false, '2014-44-44');
    }

    /**
     * Test for cancel case
     *
     * @covers \BusinessModel\Cases::putPauseCase
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutPauseCase()
    {
        $this->oCases->putPauseCase($this->idCaseToDo, '00000000000000000000000000000001');
        $this->oCases = new \BusinessModel\Cases();
        $listPaused = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'paused'));
        $this->assertNotEquals($this->nowCountPaused, $listPaused['total']);
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::putUnpauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$app_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutUnpauseCaseErrorAppUidArray()
    {
        $this->oCases->putUnpauseCase(array(), '00000000000000000000000000000001');
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::putUnpauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage The application with $app_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutUnpauseCaseErrorAppUidIncorrect()
    {
        $this->oCases->putUnpauseCase('IdDoesNotExists', '00000000000000000000000000000001');
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Cases::putUnpauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$usr_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutUnpauseCaseErrorUsrUidArray()
    {
        $this->oCases->putUnpauseCase($this->idCaseDraft, array());
    }

    /**
     * Test error for type in second field the function
     *
     * @covers \BusinessModel\Cases::putUnpauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage The user with $usr_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutUnpauseCaseErrorUsrUidIncorrect()
    {
        $this->oCases->putUnpauseCase($this->idCaseDraft, 'IdDoesNotExists');
    }

    /**
     * Test error for type in third field the function
     *
     * @covers \BusinessModel\Cases::putUnpauseCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$del_index' it must be a integer.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutUnpauseCaseErrorDelIndexIncorrect()
    {
        $this->oCases->putUnpauseCase($this->idCaseDraft, '00000000000000000000000000000001', 'string');
    }

    /**
     * Test for cancel case
     *
     * @covers \BusinessModel\Cases::putUnpauseCase
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testPutUnpauseCase()
    {
        $this->oCases->putUnpauseCase($this->idCaseToDo, '00000000000000000000000000000001');
        $this->oCases = new \BusinessModel\Cases();
        $listPaused = $this->oCases->getList(array('userId' => '00000000000000000000000000000001', 'action' => 'paused'));
        $this->assertEquals($this->nowCountPaused, $listPaused['total']);
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::deleteCase
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for '$app_uid' it must be a string.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testDeleteCaseErrorAppUidArray()
    {
        $this->oCases->deleteCase(array());
    }

    /**
     * Test error for type in first field the function
     *
     * @covers \BusinessModel\Cases::deleteCase
     * @expectedException        Exception
     * @expectedExceptionMessage The application with $app_uid: 'IdDoesNotExists' does not exist.
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testDeleteCaseErrorAppUidIncorrect()
    {
        $this->oCases->deleteCase('IdDoesNotExists');
    }

    /**
     * Test for cancel case
     *
     * @covers \BusinessModel\Cases::deleteCase
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function testDeleteCase()
    {
        $this->oCases->deleteCase($this->idCaseToDo);
        $this->oCases = new \BusinessModel\Cases();
        $listToDo = $this->oCases->getList(array('userId' => '00000000000000000000000000000001'));
        $this->assertNotEquals($this->nowCountTodo, $listToDo['total']);
    }
}