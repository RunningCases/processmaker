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

    /**
     * Set class for test
     *
     * @coversNothing
     *
     * @copyright Colosa - Bolivia
     */
    public function setUp()
    {
        $this->oOutputDocument = new \BusinessModel\Cases\OutputDocument();
    }

    /**
     * Test add OutputDocument
     *
     * @covers \BusinessModel\Cases\OutputDocument::addCasesOutputDocument
     *
     * @copyright Colosa - Bolivia
     */
    public function testAddCasesOutputDocument()
    {
        \G::loadClass('pmFunctions');
        \G::loadClass('pmFunctions');
        $usrUid = '00000000000000000000000000000001';//an user id valid
        $proUid = '1265557095225ff5c688f46031700471';//a process id valid
        $tasUid = '46941969352af5be2ab3f39001216717';//a task id valid and related to the previous proUid
        $outDocUid = '64016692453346d546d0ad1037377043';//a output document id valid and related to the previous task id
        $idCase = PMFNewCase($proUid, $usrUid, $tasUid, array());
        $response = $this->oOutputDocument->addCasesOutputDocument($idCase, $outDocUid, '00000000000000000000000000000001');
        $this->assertTrue(is_object($response));
        $aResponse = json_decode(json_encode($response), true);
        $aResponse = array_merge(array("idCase" => $idCase), $aResponse);
        return $aResponse;
    }

    /**
     * Test get OutputDocuments
     *
     * @covers \BusinessModel\Cases\OutputDocument::getCasesOutputDocuments
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocuments(array $aResponse)
    {
        $response = $this->oOutputDocument->getCasesOutputDocuments($aResponse["idCase"], '00000000000000000000000000000001');
        $this->assertTrue(is_array($response));
    }

    /**
     * Test get OutputDocument
     *
     * @covers \BusinessModel\Cases\OutputDocument::getCasesOutputDocument
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testGetCasesOutputDocument(array $aResponse)
    {
        $response = $this->oOutputDocument->getCasesOutputDocument($aResponse["idCase"], '00000000000000000000000000000001', $aResponse["app_doc_uid"]);
        $this->assertTrue(is_object($response));
    }

    /**
     * Test remove OutputDocument
     *
     * @covers \BusinessModel\Cases\OutputDocument::removeOutputDocument
     * @depends testAddCasesOutputDocument
     * @param array $aResponse
     *
     * @copyright Colosa - Bolivia
     */
    public function testRemoveOutputDocument(array $aResponse)
    {
        $response = $this->oOutputDocument->removeOutputDocument($aResponse["app_doc_uid"]);
        $this->assertTrue(empty($response));
    }
}