<?php
namespace Tests\ProcessMaker\Exporter;

use \ProcessMaker\Project;
use \ProcessMaker\Exporter;

if (! class_exists("Propel")) {
    include_once __DIR__ . "/../../bootstrap.php";
}

/**
 * Class XmlExporterTest
 *
 * @package Tests\ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class XmlExporterTest extends \PHPUnit_Framework_TestCase
{
    function testExport()
    {
        $exporter = new Exporter\XmlExporter("4857540205310b25f3d51a5020772457");
        $exporter->build();
        $exporter->save("/home/erik/out.xml");
    }
}