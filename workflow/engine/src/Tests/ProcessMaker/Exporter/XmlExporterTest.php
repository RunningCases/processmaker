<?php
namespace Tests\ProcessMaker\Exporter;

if (!class_exists("Propel")) {
    include_once(__DIR__ . "/../../bootstrap.php");
}

/**
 * Class XmlExporterTest
 *
 * @package Tests\ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class XmlExporterTest extends \PHPUnit_Framework_TestCase
{
    protected static $exporter;
    protected static $projectUid = "";
    protected static $projectName = "";
    protected static $fileXml = "";

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        self::$projectName = \ProcessMaker\Util\Common::generateUID();

        $json = "
        {
            \"prj_name\": \"" . self::$projectName . "\",
            \"prj_author\": \"00000000000000000000000000000001\",
            \"diagrams\": [
                {
                    \"dia_uid\": \"\",
                    \"activities\": [],
                    \"events\": [],
                    \"gateways\": [],
                    \"flows\": [],
                    \"artifacts\": [],
                    \"laneset\": [],
                    \"lanes\": []
                }
            ]
        }
        ";

        $arrayResult = \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct(json_decode($json, true));

        self::$projectUid = $arrayResult[0]["new_uid"];
        self::$fileXml = PATH_DOCUMENT . "output" . PATH_SEP . self::$projectUid . ".xml";

        self::$exporter = new \ProcessMaker\Exporter\XmlExporter(self::$projectUid);
    }

    /**
     * Delete project
     *
     * @coversNothing
     */
    public static function tearDownAfterClass()
    {
        $bpmnWf = \ProcessMaker\Project\Adapter\BpmnWorkflow::load(self::$projectUid);

        $bpmnWf->remove();

        unlink(self::$fileXml);
    }

    /**
     * Test export
     *
     * @covers \ProcessMaker\Exporter\XmlExporter::export
     *
     * @return string
     */
    public function testExport()
    {
        $strXml = self::$exporter->export();

        $this->assertTrue(is_string($strXml));
        $this->assertNotEmpty($strXml);

        return $strXml;
    }

    /**
     * Test build
     *
     * @covers \ProcessMaker\Exporter\XmlExporter::build
     *
     * @depends testExport
     * @param   string $strXml Data xml
     */
    public function testBuild($strXml)
    {
        //DOMDocument
        $doc = new \DOMDocument();
        $doc->loadXML($strXml);

        $nodeRoot = $doc->getElementsByTagName("ProcessMaker-Project")->item(0);
        $uid = "";

        //Node meta
        $nodeMeta = $nodeRoot->getElementsByTagName("metadata")->item(0)->getElementsByTagName("meta");

        $this->assertNotEmpty($nodeMeta);

        foreach ($nodeMeta as $value) {
            $node = $value;

            if ($node->hasAttribute("key") && $node->getAttribute("key") == "uid") {
                $uid = $node->nodeValue;
                break;
            }
        }

        $this->assertEquals(self::$projectUid, $uid);

        //Node definition
        $nodeDefinition = $nodeRoot->getElementsByTagName("definition");

        $this->assertNotEmpty($nodeDefinition);

        foreach ($nodeDefinition as $value) {
            $node = $value;

            if ($node->hasAttribute("class")) {
                $this->assertTrue(in_array($node->getAttribute("class"), array("BPMN", "workflow")));
            }
        }
    }

    /**
     * Test saveExport
     *
     * @covers \ProcessMaker\Exporter\XmlExporter::saveExport
     */
    public function testSaveExport()
    {
        self::$exporter->saveExport(self::$fileXml);

        $this->assertTrue(file_exists(self::$fileXml));
    }

    /**
     * Test getTextNode
     *
     * @covers \ProcessMaker\Exporter\XmlExporter::getTextNode
     */
    public function testGetTextNode()
    {
        //Is not implemented. Method getTextNode() is private
    }

    /**
     * Test exception for invalid project uid
     *
     * @covers \ProcessMaker\Exporter\XmlExporter::__construct
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Project "ProcessMaker\Project\Bpmn" with UID: 0, does not exist.
     */
    public function test__constructExceptionInvalidProjectUid()
    {
        $exporter = new \ProcessMaker\Exporter\XmlExporter("0");
    }
}

