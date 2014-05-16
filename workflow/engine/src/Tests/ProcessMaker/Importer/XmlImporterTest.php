<?php
namespace Tests\ProcessMaker\Importer;

if (!class_exists("Propel")) {
    include_once(__DIR__ . "/../../bootstrap.php");
}

/**
 * Class XmlImporterTest
 *
 * @package Tests\ProcessMaker\Project
 */
class XmlImporterTest extends \PHPUnit_Framework_TestCase
{
    protected static $importer;
    protected static $projectUid = "";
    protected static $filePmx = "";

    protected static $arrayPrjUid = array();

    /**
     * Set class for test
     *
     * @coversNothing
     */
    public static function setUpBeforeClass()
    {
        $json = "
        {
            \"prj_name\": \"" . \ProcessMaker\Util\Common::generateUID() . "\",
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
        self::$filePmx = PATH_DOCUMENT . "input" . PATH_SEP . self::$projectUid . ".pmx";

        $exporter = new \ProcessMaker\Exporter\XmlExporter(self::$projectUid);
        $exporter->saveExport(self::$filePmx);

        $bpmnWf = \ProcessMaker\Project\Adapter\BpmnWorkflow::load(self::$projectUid);
        $bpmnWf->remove();

        self::$importer = new \ProcessMaker\Importer\XmlImporter();
        self::$importer->setSourceFile(self::$filePmx);
    }

    /**
     * Delete projects
     *
     * @coversNothing
     */
    public static function tearDownAfterClass()
    {
        foreach (self::$arrayPrjUid as $value) {
            $prjUid = $value;

            $bpmnWf = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($prjUid);
            $bpmnWf->remove();
        }

        unlink(self::$filePmx);
    }

    /**
     * Test load
     *
     * @covers \ProcessMaker\Importer\XmlImporter::load
     */
    public function testLoad()
    {
        $arrayData = self::$importer->load();

        $this->assertTrue(is_array($arrayData));
        $this->assertNotEmpty($arrayData);

        $this->assertArrayHasKey("tables", $arrayData);
        $this->assertArrayHasKey("files", $arrayData);

        $this->assertEquals($arrayData["tables"]["bpmn"]["project"][0]["prj_uid"], self::$projectUid);
        $this->assertEquals($arrayData["tables"]["workflow"]["process"][0]["PRO_UID"], self::$projectUid);
    }

    /**
     * Test getTextNode
     *
     * @covers \ProcessMaker\Importer\XmlImporter::getTextNode
     */
    public function testGetTextNode()
    {
        //Is not implemented. Method getTextNode() is private
    }

    /**
     * Test import
     *
     * @covers \ProcessMaker\Importer\XmlImporter::import
     */
    public function testImport()
    {
        $prjUid = self::$importer->import();
        self::$arrayPrjUid[] = $prjUid;

        $this->assertNotNull(\BpmnProjectPeer::retrieveByPK($prjUid));
    }

    /**
     * Test importPostFile
     *
     * @covers \ProcessMaker\Importer\XmlImporter::importPostFile
     */
    public function testImportPostFile()
    {
        self::$importer->setSaveDir(PATH_DOCUMENT . "input");

        $arrayData = self::$importer->importPostFile(array("PROJECT_FILE" => self::$projectUid . ".pmx"), "KEEP");
        self::$arrayPrjUid[] = $arrayData["PRJ_UID"];

        $this->assertNotNull(\BpmnProjectPeer::retrieveByPK($arrayData["PRJ_UID"]));
    }

    /**
     * Test exception when the project exists
     *
     * @covers \ProcessMaker\Importer\XmlImporter::import
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Project already exists, you need set an action to continue. Available actions: [project.import.create_new|project.import.override|project.import.disable_and_create_new|project.import.keep_without_changing_and_create_new].
     */
    public function testImportExceptionProjectExists()
    {
        $prjUid = self::$importer->import();
    }

    /**
     * Test exception for empty data
     *
     * @covers \ProcessMaker\Importer\XmlImporter::importPostFile
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "$arrayData", it can not be empty.
     */
    public function testImportPostFileExceptionEmptyData()
    {
        $arrayData = self::$importer->importPostFile(array());
    }

    /**
     * Test exception for invalid extension
     *
     * @covers \ProcessMaker\Importer\XmlImporter::importPostFile
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The file extension not is "pmx"
     */
    public function testImportPostFileExceptionInvalidExtension()
    {
        $arrayData = self::$importer->importPostFile(array("PROJECT_FILE" => "file.pm"));
    }

    /**
     * Test exception for file does not exist
     *
     * @covers \ProcessMaker\Importer\XmlImporter::importPostFile
     *
     * @expectedException        Exception
     * @expectedExceptionMessage The file with PROJECT_FILE: "file.pmx" does not exist.
     */
    public function testImportPostFileExceptionFileNotExists()
    {
        $arrayData = self::$importer->importPostFile(array("PROJECT_FILE" => "file.pmx"));
    }

    /**
     * Test exception for invalid option
     *
     * @covers \ProcessMaker\Importer\XmlImporter::importPostFile
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid value for "OPTION", it only accepts values: "CREATE|OVERWRITE|DISABLE|KEEP".
     */
    public function testImportPostFileExceptionInvalidOption()
    {
        $arrayData = self::$importer->importPostFile(array("PROJECT_FILE" => "file.pmx"), "CREATED");
    }

    /**
     * Test exception when the project exists
     *
     * @covers \ProcessMaker\Importer\XmlImporter::importPostFile
     *
     * @expectedException        Exception
     * @expectedExceptionMessage Project already exists, you need set an action to continue. Available actions: [CREATE|OVERWRITE|DISABLE|KEEP].
     */
    public function testImportPostFileExceptionProjectExists()
    {
        self::$importer->setSaveDir(PATH_DOCUMENT . "input");

        $arrayData = self::$importer->importPostFile(array("PROJECT_FILE" => self::$projectUid . ".pmx"));
    }
}

