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
class OutputDocumentsCasesTest extends \PHPUnit_Framework_TestCase
{
    protected $oOutputDocument;
    protected $idCase = '';

    protected static $usrUid = "00000000000000000000000000000001";
    protected static $proUid = "00000000000000000000000000000002";
    protected static $tasUid = "00000000000000000000000000000003";
    protected static $outUid = "00000000000000000000000000000004";
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

        $outputDocument = new \OutputDocument();
        $outputDocument->create(array("OUT_DOC_UID"=> self::$outUid, "PRO_UID"=> self::$proUid, "OUT_DOC_TITLE"=> "NEW OUPUT TEST", "OUT_DOC_FILENAME"=> "NEW_OUPUT_TEST",
                                      "OUT_DOC_DESCRIPTION"=> "", "OUT_DOC_REPORT_GENERATOR"=> "HTML2PDF", "OUT_DOC_REPORT_GENERATOR_label"=> "HTML2PDF (Old Version)",
                                      "OUT_DOC_LANDSCAPE"=> "", "OUT_DOC_LANDSCAPE_label"=> "Portrait", "OUT_DOC_GENERATE"=> "BOTH", "OUT_DOC_GENERATE_label"=> "BOTH",
                                      "OUT_DOC_VERSIONING"=> "", "OUT_DOC_VERSIONING_label"=> "NO", "OUT_DOC_MEDIA"=> "Letter", "OUT_DOC_MEDIA_label"=> "Letter",
                                      "OUT_DOC_LEFT_MARGIN"=> "", "OUT_DOC_RIGHT_MARGIN"=> "", "OUT_DOC_TOP_MARGIN"=> "", "OUT_DOC_BOTTOM_MARGIN"=> "",
                                      "OUT_DOC_DESTINATION_PATH"=> "", "OUT_DOC_TAGS"=> "", "OUT_DOC_PDF_SECURITY_ENABLED"=> "0", "OUT_DOC_PDF_SECURITY_ENABLED_label"=> "Disabled",
                                      "OUT_DOC_PDF_SECURITY_OPEN_PASSWORD"=>"", "OUT_DOC_PDF_SECURITY_OWNER_PASSWORD"=> "", "OUT_DOC_PDF_SECURITY_PERMISSIONS"=> "",
                                      "OUT_DOC_OPEN_TYPE"=> "0", "OUT_DOC_OPEN_TYPE_label"=> "Download the file", "BTN_CANCEL"=> "Cancel", "ACCEPT"=> "Save"));

        $step = new \Step();
        $step->create(array( "PRO_UID"=> self::$proUid, "TAS_UID"=> self::$tasUid, "STEP_UID"=> self::$steUid, "STEP_TYPE_OBJ" => "OUTPUT_DOCUMENT", "STEP_UID_OBJ" =>self::$outUid));

        $assign = new \TaskUser();
        $assign->create(array("TAS_UID"=> self::$tasUid, "USR_UID"=> self::$usrUid, "TU_TYPE"=>  "1", "TU_RELATION"=> 1));

    }

    public static function tearDownAfterClass()
    {
        $assign = new \TaskUser();
        $assign->remove(self::$tasUid, self::$usrUid, 1,1);

        $step = new \Step();
        $step->remove(self::$steUid);

        $outputDocument = new \OutputDocument();
        $outputDocument->remove(self::$outUid);

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
        $this->oOutputDocument = new \ProcessMaker\BusinessModel\Cases\OutputDocument();
    }

    /**
     * Test add OutputDocument
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::addCasesOutputDocument
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCasesOutputDocument()
    {
        \G::loadClass('pmFunctions');
        $idCase = PMFNewCase(self::$proUid, self::$usrUid, self::$tasUid, array());
        $response = $this->oOutputDocument->addCasesOutputDocument($idCase, self::$outUid, self::$usrUid);
        $this->assertTrue(is_object($response));
        $aResponse = json_decode(json_encode($response), true);
        $aResponse = array_merge(array("idCase" => $idCase), $aResponse);
        return $aResponse;
    }

    /**
     * Test error for incorrect value of application in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::getCasesOutputDocuments
     * @expectedException        Exception
     * @expectedExceptionMessage The Application row '12345678912345678912345678912345678' doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocumentsErrorIncorrectApplicationValueArray()
    {
        $this->oOutputDocument->getCasesOutputDocuments('12345678912345678912345678912345678', self::$usrUid);
    }

    /**
     * Test get OutputDocuments
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::getCasesOutputDocuments
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocuments(array $aResponse)
    {
        $response = $this->oOutputDocument->getCasesOutputDocuments($aResponse["idCase"], self::$usrUid);
        $this->assertTrue(is_array($response));
    }

    /**
     * Test error for incorrect value of application in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::getCasesOutputDocument
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     * @expectedException        Exception
     * @expectedExceptionMessage The Application row '12345678912345678912345678912345678' doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocumentErrorIncorrectApplicationValueArray(array $aResponse)
    {
        $this->oOutputDocument->getCasesOutputDocument('12345678912345678912345678912345678', self::$usrUid, $aResponse["app_doc_uid"]);
    }

    /**
     * Test error for incorrect value of output document in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::getCasesOutputDocument
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     * @expectedException        Exception
     * @expectedExceptionMessage This output document with id: 12345678912345678912345678912345678 doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocumentErrorIncorrectOutputDocumentValueArray(array $aResponse)
    {
        $this->oOutputDocument->getCasesOutputDocument($aResponse["idCase"], self::$usrUid, '12345678912345678912345678912345678');
    }

    /**
     * Test get OutputDocument
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::getCasesOutputDocument
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocument(array $aResponse)
    {
        $response = $this->oOutputDocument->getCasesOutputDocument($aResponse["idCase"], self::$usrUid, $aResponse["app_doc_uid"]);
        $this->assertTrue(is_object($response));
    }

    /**
     * Test error for incorrect value of output document in array
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::removeOutputDocument
     * @expectedException        Exception
     * @expectedExceptionMessage This output document with id: 12345678912345678912345678912345678 doesn't exist!
     *
     * @copyright Colosa - Bolivia
     */
    public function testRemoveOutputDocumentErrorIncorrectOutputDocumentValueArray()
    {
        $this->oOutputDocument->removeOutputDocument('12345678912345678912345678912345678');
    }

    /**
     * Test remove OutputDocument
     *
     * @covers \ProcessMaker\BusinessModel\Cases\OutputDocument::removeOutputDocument
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testRemoveOutputDocument(array $aResponse)
    {
        $response = $this->oOutputDocument->removeOutputDocument($aResponse["app_doc_uid"]);
        $this->assertTrue(empty($response));

        //remove Case
        $case = new \Cases();
        $case->removeCase( $aResponse["idCase"] );
    }
}