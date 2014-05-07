<?php
namespace Tests\BusinessModel;

if (!class_exists("Propel")) {
    include_once (__DIR__ . "/../bootstrap.php");
}

/**
 * Class Documents Cases Test
 *
 * @copyright Colosa - Bolivia
 *
 * @protected
 * @package Tests\BusinessModel
 */
class InputDocumentsCasesTest extends \PHPUnit_Framework_TestCase
{
    protected $oInputDocument;
    protected $idCase = '';

    protected static $usrUid = "00000000000000000000000000000001";
    protected static $proUid = "00000000000000000000000000000002";
    protected static $tasUid = "00000000000000000000000000000003";
    protected static $inpUid = "00000000000000000000000000000004";
    protected static $steUid = "00000000000000000000000000000005";

    public static function setUpBeforeClass()
    {
        $process = new \Process();
        $process->create(array("type"=>"classicProject", "PRO_TITLE"=> "NEW TEST PHP UNIT", "PRO_DESCRIPTION"=> "465",
            "PRO_CATEGORY"=> "", "PRO_CREATE_USER"=> "00000000000000000000000000000001",
            "PRO_UID"=> self::$proUid, "USR_UID"=> "00000000000000000000000000000001"), false);

        $task = new \Task();
        $task->create(array("TAS_START"=>"TRUE", "TAS_UID"=> self::$tasUid, "PRO_UID"=> self::$proUid, "TAS_TITLE" => "NEW TASK TEST PHP UNIT",
            "TAS_POSX"=> 581, "TAS_POSY"=> 17, "TAS_WIDTH"=> 165, "TAS_HEIGHT"=> 40), false);

        $inputDocument = new \InputDocument();
        $inputDocument->create(array("INP_DOC_UID"=> self::$inpUid, "PRO_UID"=> self::$proUid, "INP_DOC_TITLE"=> "INPUTDOCUMENT TEST UNIT", "INP_DOC_FORM_NEEDED"=> "VIRTUAL",
                                     "INP_DOC_ORIGINAL"=> "ORIGINAL", "INP_DOC_DESCRIPTION"=> "", "INP_DOC_VERSIONING"=> "",
                                     "INP_DOC_DESTINATION_PATH"=> "", "INP_DOC_TAGS"=> "INPUT", "ACCEPT"=> "Save", "BTN_CANCEL"=>"Cancel"));

        $step = new \Step();
        $step->create(array( "PRO_UID"=> self::$proUid, "TAS_UID"=> self::$tasUid, "STEP_UID"=> self::$steUid, "STEP_TYPE_OBJ" => "INPUT_DOCUMENT", "STEP_UID_OBJ" =>self::$inpUid));

        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid, "USR_UID"=> self::$usrUid, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));
    }

    public static function tearDownAfterClass()
    {
        $assign = new \TaskUser();
        $assign->remove(self::$tasUid, self::$usrUid, 1,1);

        $step = new \Step();
        $step->remove(self::$steUid);

        $inputDocument = new \InputDocument();
        $inputDocument->remove(self::$inpUid);

        $task = new \Task();
        $task->remove(self::$tasUid);

        $process = new \Process();
        $process->remove(self::$proUid);
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
        $this->oInputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
    }

    /**
     * Test add a test InputDocument
     *
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddInputDocument()
    {
        \G::loadClass('pmFunctions');
        $idCase = PMFNewCase(self::$proUid, self::$usrUid, self::$tasUid, array());
        $case = new \Cases();
        $appDocUid = $case->addInputDocument(self::$inpUid, $appDocUid = \G::generateUniqueID(), '', 'INPUT',
                                             'PHPUNIT TEST', '', $idCase, \AppDelegation::getCurrentIndex($idCase),
                                             self::$tasUid, self::$usrUid, "xmlform", PATH_DATA_SITE.'db.php',
                                             0, PATH_DATA_SITE.'db.php');
        $aResponse = array();
        $aResponse = array_merge(array("idCase" => $idCase, "appDocUid" => $appDocUid, "inpDocUid" => self::$inpUid), $aResponse);
        return $aResponse;
     }

    /**
     * Test error for incorrect value of case in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::getCasesInputDocuments
     * @expectedException        Exception
     * @expectedExceptionMessage The Application row '12345678912345678912345678912345678' doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesInputDocumentsErrorIncorrectCaseValueArray()
    {
        $this->oInputDocument->getCasesInputDocuments('12345678912345678912345678912345678', self::$usrUid);
    }

    /**
     * Test get InputDocuments
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::getCasesInputDocuments
     * @depends testAddInputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesInputDocuments(array $aResponse)
    {
        $response = $this->oInputDocument->getCasesInputDocuments($aResponse["idCase"], self::$usrUid);
        $this->assertTrue(is_array($response));
    }

    /**
     * Test error for incorrect value of task in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::getCasesInputDocument
     * @depends testAddInputDocument
     * @param array $aResponse
     * @expectedException        Exception
     * @expectedExceptionMessage The Application row '12345678912345678912345678912345678' doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesInputDocumentErrorIncorrectCaseValueArray(array $aResponse)
    {
        $this->oInputDocument->getCasesInputDocument('12345678912345678912345678912345678', self::$usrUid, $aResponse["appDocUid"]);
    }

    /**
     * Test error for incorrect value of input document in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::getCasesInputDocument
     * @depends testAddInputDocument
     * @param array $aResponse
     * @expectedException        Exception
     * @expectedExceptionMessage This input document with id: 12345678912345678912345678912345678 doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesInputDocumentErrorIncorrectInputDocumentValueArray(array $aResponse)
    {
        $this->oInputDocument->getCasesInputDocument($aResponse["idCase"], self::$usrUid, '12345678912345678912345678912345678');
    }

    /**
     * Test get InputDocument
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::getCasesInputDocument
     * @depends testAddInputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesInputDocument(array $aResponse)
    {
        $response = $this->oInputDocument->getCasesInputDocument($aResponse["idCase"], self::$usrUid, $aResponse["appDocUid"]);
        $this->assertTrue(is_object($response));
    }

    /**
     * Test error for incorrect value of input document in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::removeInputDocument
     * @expectedException        Exception
     * @expectedExceptionMessage This input document with id: 12345678912345678912345678912345678 doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testRemoveInputDocumentErrorIncorrectApplicationValueArray()
    {
        $this->oInputDocument->removeInputDocument('12345678912345678912345678912345678');
    }

    /**
     * Test remove InputDocument
     *
     * @covers \ProcessMaker\BusinessModel\Cases\InputDocument::removeInputDocument
     * @depends testAddInputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testRemoveInputDocument(array $aResponse)
    {
        $response = $this->oInputDocument->removeInputDocument($aResponse["appDocUid"]);
        $this->assertTrue(empty($response));
    }
}