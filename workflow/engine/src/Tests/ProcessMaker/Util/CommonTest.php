<?php
namespace Tests\ProcessMaker\Util;


use ProcessMaker\Util;

/**
 * Class XmlExporterTest
 *
 * @package Tests\ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class XmlExporterTest extends \PHPUnit_Framework_TestCase
{
    function testGetLastVersion()
    {
        $lastVer = Util\Common::getLastVersion(__DIR__."/../../fixtures/files_struct/first/sample-*.txt");
        $this->assertEquals(3, $lastVer);
    }

    function testGetLastVersionSec()
    {
        $lastVer = Util\Common::getLastVersion(__DIR__."/../../fixtures/files_struct/second/sample-*.txt");

        $this->assertEquals(5, $lastVer);
    }

    function testGetLastVersionThr()
    {
        $lastVer = Util\Common::getLastVersion(__DIR__."/../../fixtures/files_struct/third/sample-*.txt");

        $this->assertEquals("3.1.9", $lastVer);
    }

    /**
     * Negative test, no matched files found
     */
    function testGetLastVersionOther()
    {
        $lastVer = Util\Common::getLastVersion(sys_get_temp_dir()."/sample-*.txt");

        $this->assertEquals(0, $lastVer);
    }
}