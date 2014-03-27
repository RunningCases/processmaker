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
        $usrUid = '00000000000000000000000000000001';
        $proUid = '1265557095225ff5c688f46031700471';
        $tasUid = '1352844695225ff5fe54de2005407079';
        $idCase = PMFNewCase($proUid, $usrUid, $tasUid, array());
        $response = $this->oOutputDocument->addCasesOutputDocument($idCase, '10401087752fa8bc6f0cab6048419434', '00000000000000000000000000000001');
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
        echo $aResponse["app_doc_uid"];
        $response = $this->oOutputDocument->removeOutputDocument($aResponse["app_doc_uid"]);
        $this->assertTrue(empty($response));
    }
}