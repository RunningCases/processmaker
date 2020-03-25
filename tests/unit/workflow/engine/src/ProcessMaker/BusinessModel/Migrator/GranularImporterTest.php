<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel;

use ProcessMaker\BusinessModel\Migrator\GranularImporter;
use Tests\TestCase;

class GranularImporterTest extends TestCase
{

    /**
     * This returns a set of data that is read from a json file.
     */
    public function importDataObject()
    {
        $filename = PATH_TRUNK . "tests/resources/GranularImporterTest.json";
        $json = file_get_contents($filename);
        $data = json_decode($json, true);
        return $data;
    }

    /**
     * It should return data from addObjectData() method.
     * @test
     * @covers \ProcessMaker\BusinessModel\Migrator\GranularImporter::addObjectData()
     * @dataProvider importDataObject
     */
    public function it_should_return_data_from_add_object_data_method($name, $data)
    {
        $granularImporter = new GranularImporter();
        $result = $granularImporter->addObjectData($name, $data);
        $this->assertArrayHasKey($name, $result);
    }
}
