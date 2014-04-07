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
        \G::loadClass('pmFunctions');
        $usrUid = '00000000000000000000000000000001';//an user id valid
        $proUid = '1265557095225ff5c688f46031700471';//a process id valid
        $tasUid = '46941969352af5be2ab3f39001216717';//a task id valid and related to the previous proUid
        $inpDocUid = '70158392952979dedd77fe0058957493';//a input document id valid and related to the previous task id
        $idCase = PMFNewCase($proUid, $usrUid, $tasUid, array());
        $case = new \Cases();
        $appDocUid = $case->addInputDocument($inpDocUid, $appDocUid = \G::generateUniqueID(), '', 'INPUT',
                                             'PHPUNIT TEST', '', $idCase, \AppDelegation::getCurrentIndex($idCase),
                                             $tasUid, $usrUid, "xmlform", '/home/user/desarrollo/test.txt', 0, '/home/user/desarrollo/test.txt');
        $aResponse = array();
        $aResponse = array_merge(array("idCase" => $idCase, "appDocUid" => $appDocUid, "inpDocUid" => $inpDocUid), $aResponse);
        return $aResponse;
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
        $response = $this->oInputDocument->getCasesInputDocuments($aResponse["idCase"], '00000000000000000000000000000001');
        $this->assertTrue(is_array($response));
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
        $response = $this->oInputDocument->getCasesInputDocument($aResponse["idCase"], '00000000000000000000000000000001', $aResponse["appDocUid"]);
        $this->assertTrue(is_object($response));
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