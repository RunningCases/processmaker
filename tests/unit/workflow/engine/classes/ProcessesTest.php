<?php

namespace Tests\unit\workflow\engine\classes;

use Faker\Factory;
use G;
use Processes;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\InputDocument;
use ProcessMaker\Model\OutputDocument;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessVariables;
use Tests\TestCase;

class ProcessesTest extends TestCase
{
    private $processes;

    /**
     * Constructor of the class.
     * 
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->processes = new Processes();
    }

    /**
     * Sort array of array by column.
     * @param array $data
     * @param string $columnName
     */
    public function sortArrayByColumn(&$data, $columnName)
    {
        usort($data, function($a, $b) use($columnName) {
            return strnatcmp($a[$columnName], $b[$columnName]);
        });
    }

    /**
     * This checks if the returned dynaforms are correct with the different parameters.
     * @test
     * @covers \Processes::getDynaformRows()
     */
    public function it_should_return_dynaforms()
    {
        $process = Process::factory()->create()->first();
        $proUid = $process->PRO_UID;

        $dynaforms = Dynaform::factory(6)
                ->create([
                    'PRO_UID' => $proUid
                ])
                ->sortBy('DYN_UID')
                ->values();

        //test with parameter false
        $expected = $dynaforms->toArray();

        $processes = new Processes();
        $actual = $processes->getDynaformRows($proUid, false);
        $this->sortArrayByColumn($actual, 'DYN_UID');

        $this->assertEquals($expected, $actual);

        //by default the method getDynaformRows removed DYN_ID column
        $dynaforms->transform(function($item, $key) {
            unset($item->DYN_ID);
            return $item;
        });

        //test with parameter default
        $expected = $dynaforms->toArray();

        $processes = new Processes();
        $actual = $processes->getDynaformRows($proUid);
        $this->sortArrayByColumn($actual, 'DYN_UID');

        $this->assertEquals($expected, $actual);

        //test with parameter true
        $expected = $dynaforms->toArray();

        $processes = new Processes();
        $actual = $processes->getDynaformRows($proUid, true);
        $this->sortArrayByColumn($actual, 'DYN_UID');

        $this->assertEquals($expected, $actual);
    }

    /**
     * This check if the returned input documents are correct with the different 
     * parameters.
     * @test
     * @covers \Processes::getInputRows()
     */
    public function it_should_return_input_documents()
    {
        $process = Process::factory()->create()->first();
        $proUid = $process->PRO_UID;

        $inputDocument = InputDocument::factory(6)
                ->create([
                    'PRO_UID' => $proUid
                ])
                ->sortBy('INP_DOC_UID')
                ->values();

        //test with parameter false
        $expected = $inputDocument->toArray();

        $processes = new Processes();
        $actual = $processes->getInputRows($proUid, false);
        $this->sortArrayByColumn($actual, 'INP_DOC_UID');

        $this->assertEquals($expected, $actual);

        //by default the mnethod getInputRows removed INP_DOC_ID column
        $inputDocument->transform(function($item, $key) {
            unset($item->INP_DOC_ID);
            return $item;
        });

        //test with parameter default
        $expected = $inputDocument->toArray();

        $processes = new Processes();
        $actual = $processes->getInputRows($proUid);
        $this->sortArrayByColumn($actual, 'INP_DOC_UID');

        $this->assertEquals($expected, $actual);

        //test with the parameter true
        $expected = $inputDocument->toArray();

        $processes = new Processes();
        $actual = $processes->getInputRows($proUid, true);
        $this->sortArrayByColumn($actual, 'INP_DOC_UID');

        $this->assertEquals($expected, $actual);
    }

    /**
     * This checks fi the returned output documents are correct with the differect 
     * parameters.
     * @test
     * @covers \Processes::getOutputRows()
     */
    public function it_should_return_output_documents()
    {
        $process = Process::factory()->create()->first();
        $proUid = $process->PRO_UID;

        $outputDocument = OutputDocument::factory(6)
                ->create([
                    'PRO_UID' => $proUid
                ])
                ->sortBy('OUT_DOC_UID')
                ->values();

        //test with parameter false
        $expected = $outputDocument->toArray();

        $processes = new Processes();
        $actual = $processes->getOutputRows($proUid, false);
        $this->sortArrayByColumn($actual, 'OUT_DOC_UID');

        $this->assertEquals($expected, $actual);

        //by default the method getOutoutRows removed OUT_DOC_ID column
        $outputDocument->transform(function($item, $key) {
            unset($item->OUT_DOC_ID);
            return $item;
        });

        //test with parameter default
        $expected = $outputDocument->toArray();

        $processes = new Processes();
        $actual = $processes->getOutputRows($proUid);
        $this->sortArrayByColumn($actual, 'OUT_DOC_UID');

        $this->assertEquals($expected, $actual);

        //test with parameter true
        $expected = $outputDocument->toArray();

        $processes = new Processes();
        $actual = $processes->getOutputRows($proUid, true);
        $this->sortArrayByColumn($actual, 'OUT_DOC_UID');

        $this->assertEquals($expected, $actual);
    }

    /**
     * This checks if the dynaforms structure is saved with the different parameters.
     * @test
     * @covers \Processes::createDynaformRows()
     */
    public function it_sholud_create_dynaform()
    {
        $faker = Factory::create();
        $date = $faker->datetime();
        $proUid = G::generateUniqueID();
        $expected = [
            [
                'DYN_ID' => $faker->unique()->numberBetween(1, 10000000),
                'DYN_UID' => G::generateUniqueID(),
                'DYN_TITLE' => $faker->sentence(2),
                'DYN_DESCRIPTION' => $faker->sentence(5),
                'PRO_UID' => $proUid,
                'DYN_TYPE' => 'xmlform',
                'DYN_FILENAME' => '',
                'DYN_CONTENT' => '',
                'DYN_LABEL' => '',
                'DYN_VERSION' => 2,
                'DYN_UPDATE_DATE' => $date->format('Y-m-d H:i:s'),
                '__DYN_ID_UPDATE__' => false,
            ],
            [
                'DYN_ID' => $faker->unique()->numberBetween(1, 10000000),
                'DYN_UID' => G::generateUniqueID(),
                'DYN_TITLE' => $faker->sentence(2),
                'DYN_DESCRIPTION' => $faker->sentence(5),
                'PRO_UID' => $proUid,
                'DYN_TYPE' => 'xmlform',
                'DYN_FILENAME' => '',
                'DYN_CONTENT' => '',
                'DYN_LABEL' => '',
                'DYN_VERSION' => 2,
                'DYN_UPDATE_DATE' => $date->format('Y-m-d H:i:s'),
                '__DYN_ID_UPDATE__' => false,
            ],
        ];
        $this->sortArrayByColumn($expected, 'DYN_UID');

        $processes = new Processes();
        $processes->createDynaformRows($expected);
        foreach ($expected as &$value) {
            ksort($value);
            unset($value['__DYN_ID_UPDATE__']);
        }

        $dynaforms = Dynaform::getByProUid($proUid)
                ->sortBy('DYN_UID')
                ->values();
        $dynaforms->transform(function($item, $key) {
            return (array) $item;
        });
        $actual = $dynaforms->toArray();
        foreach ($actual as $value) {
            ksort($value);
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * This checks if the input documents structure is saved with the different 
     * parameters.
     * @test
     * @covers \Processes::createInputRows()
     */
    public function it_should_create_input_document()
    {
        $faker = Factory::create();
        $date = $faker->datetime();
        $proUid = G::generateUniqueID();
        $expected = [
            [
                'INP_DOC_UID' => G::generateUniqueID(),
                'INP_DOC_ID' => $faker->unique()->numberBetween(1, 10000),
                'PRO_UID' => $proUid,
                'INP_DOC_TITLE' => $faker->sentence(2),
                'INP_DOC_DESCRIPTION' => $faker->sentence(10),
                'INP_DOC_FORM_NEEDED' => 'VIRTUAL',
                'INP_DOC_ORIGINAL' => 'ORIGINAL',
                'INP_DOC_PUBLISHED' => 'PRIVATE',
                'INP_DOC_VERSIONING' => 0,
                'INP_DOC_DESTINATION_PATH' => '',
                'INP_DOC_TAGS' => 'INPUT',
                'INP_DOC_TYPE_FILE' => '.*',
                'INP_DOC_MAX_FILESIZE' => 0,
                'INP_DOC_MAX_FILESIZE_UNIT' => 'KB',
                '__INP_DOC_ID_UPDATE__' => false,
            ],
            [
                'INP_DOC_UID' => G::generateUniqueID(),
                'INP_DOC_ID' => $faker->unique()->numberBetween(1, 10000),
                'PRO_UID' => $proUid,
                'INP_DOC_TITLE' => $faker->sentence(2),
                'INP_DOC_DESCRIPTION' => $faker->sentence(10),
                'INP_DOC_FORM_NEEDED' => 'VIRTUAL',
                'INP_DOC_ORIGINAL' => 'ORIGINAL',
                'INP_DOC_PUBLISHED' => 'PRIVATE',
                'INP_DOC_VERSIONING' => 0,
                'INP_DOC_DESTINATION_PATH' => '',
                'INP_DOC_TAGS' => 'INPUT',
                'INP_DOC_TYPE_FILE' => '.*',
                'INP_DOC_MAX_FILESIZE' => 0,
                'INP_DOC_MAX_FILESIZE_UNIT' => 'KB',
                '__INP_DOC_ID_UPDATE__' => false,
            ],
        ];
        $this->sortArrayByColumn($expected, 'INP_DOC_UID');

        $processes = new Processes();
        $processes->createInputRows($expected);
        foreach ($expected as &$value) {
            ksort($value);
            unset($value['__INP_DOC_ID_UPDATE__']);
        }

        $inputDocuments = InputDocument::getByProUid($proUid)
                ->sortBy('INP_DOC_UID')
                ->values();
        $inputDocuments->transform(function($item, $key) {
            return $item->attributesToArray();
        });
        $actual = $inputDocuments->toArray();
        foreach ($actual as &$value) {
            ksort($value);
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * This checks if the output documents structure is saved with the different 
     * parameters.
     * @test
     * @covers \Processes::createOutputRows()
     */
    public function it_should_create_output_document()
    {
        $faker = Factory::create();
        $date = $faker->datetime();
        $proUid = G::generateUniqueID();
        $expected = [
            [
                'OUT_DOC_UID' => G::generateUniqueID(),
                'OUT_DOC_ID' => $faker->unique()->numberBetween(1, 10000),
                'OUT_DOC_TITLE' => $faker->sentence(2),
                'OUT_DOC_DESCRIPTION' => $faker->sentence(10),
                'OUT_DOC_FILENAME' => $faker->sentence(2),
                'OUT_DOC_TEMPLATE' => '',
                'PRO_UID' => $proUid,
                'OUT_DOC_REPORT_GENERATOR' => 'TCPDF',
                'OUT_DOC_LANDSCAPE' => 0,
                'OUT_DOC_MEDIA' => 'Letter',
                'OUT_DOC_LEFT_MARGIN' => 20,
                'OUT_DOC_RIGHT_MARGIN' => 20,
                'OUT_DOC_TOP_MARGIN' => 20,
                'OUT_DOC_BOTTOM_MARGIN' => 20,
                'OUT_DOC_GENERATE' => 'BOTH',
                'OUT_DOC_TYPE' => 'HTML',
                'OUT_DOC_CURRENT_REVISION' => 0,
                'OUT_DOC_FIELD_MAPPING' => '',
                'OUT_DOC_VERSIONING' => 1,
                'OUT_DOC_DESTINATION_PATH' => '',
                'OUT_DOC_TAGS' => '',
                'OUT_DOC_PDF_SECURITY_ENABLED' => 0,
                'OUT_DOC_PDF_SECURITY_OPEN_PASSWORD' => '',
                'OUT_DOC_PDF_SECURITY_OWNER_PASSWORD' => '',
                'OUT_DOC_PDF_SECURITY_PERMISSIONS' => '',
                'OUT_DOC_OPEN_TYPE' => 1,
                '__OUT_DOC_ID_UPDATE__' => false,
                'OUT_DOC_FOOTER' => null,
                'OUT_DOC_HEADER' => null
            ],
            [
                'OUT_DOC_UID' => G::generateUniqueID(),
                'OUT_DOC_ID' => $faker->unique()->numberBetween(1, 10000),
                'OUT_DOC_TITLE' => $faker->sentence(2),
                'OUT_DOC_DESCRIPTION' => $faker->sentence(10),
                'OUT_DOC_FILENAME' => $faker->sentence(2),
                'OUT_DOC_TEMPLATE' => '',
                'PRO_UID' => $proUid,
                'OUT_DOC_REPORT_GENERATOR' => 'TCPDF',
                'OUT_DOC_LANDSCAPE' => 0,
                'OUT_DOC_MEDIA' => 'Letter',
                'OUT_DOC_LEFT_MARGIN' => 20,
                'OUT_DOC_RIGHT_MARGIN' => 20,
                'OUT_DOC_TOP_MARGIN' => 20,
                'OUT_DOC_BOTTOM_MARGIN' => 20,
                'OUT_DOC_GENERATE' => 'BOTH',
                'OUT_DOC_TYPE' => 'HTML',
                'OUT_DOC_CURRENT_REVISION' => 0,
                'OUT_DOC_FIELD_MAPPING' => '',
                'OUT_DOC_VERSIONING' => 1,
                'OUT_DOC_DESTINATION_PATH' => '',
                'OUT_DOC_TAGS' => '',
                'OUT_DOC_PDF_SECURITY_ENABLED' => 0,
                'OUT_DOC_PDF_SECURITY_OPEN_PASSWORD' => '',
                'OUT_DOC_PDF_SECURITY_OWNER_PASSWORD' => '',
                'OUT_DOC_PDF_SECURITY_PERMISSIONS' => '',
                'OUT_DOC_OPEN_TYPE' => 1,
                '__OUT_DOC_ID_UPDATE__' => false,
                'OUT_DOC_FOOTER' => null,
                'OUT_DOC_HEADER' => null
            ]
        ];
        $this->sortArrayByColumn($expected, 'OUT_DOC_UID');

        $processes = new Processes();
        $processes->createOutputRows($expected);
        foreach ($expected as &$value) {
            ksort($value);
            unset($value['__OUT_DOC_ID_UPDATE__']);
        }

        $outputDocuments = OutputDocument::getByProUid($proUid)
                ->sortBy('OUT_DOC_UID')
                ->values();
        $outputDocuments->transform(function($item, $key) {
            return $item->attributestoArray();
        });
        $actual = $outputDocuments->toArray();
        foreach ($actual as &$value) {
            ksort($value);
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * This gets the data structure of a project.
     * @test
     * @covers \Processes::getWorkflowData()
     */
    public function it_should_get_workflow_data()
    {
        $process = \ProcessMaker\Model\Process::factory()->create();
        $processes = new Processes();
        $result = $processes->getWorkflowData($process->PRO_UID);
        $this->assertNotNull($result);
    }

    /**
     * This test guarantees the replacement of new identifiers.
     * @test
     * @covers \Processes::renewAllDynaformGuid()
     */
    public function it_should_renew_all_dynaform_guid()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaformDataForRenewUids.json";
        $data = file_get_contents($pathData);
        $result = json_decode($data, JSON_OBJECT_AS_ARRAY);
        $result = (object) $result;
        $this->processes->renewAllDynaformGuid($result);
        foreach ($result as $key => $value) {
            $this->assertObjectHasAttribute($key, $result);
        }

        //without PRO_DYNAFORMS
        $result = json_decode($data, JSON_OBJECT_AS_ARRAY);
        $result = (object) $result;
        unset($result->process['PRO_DYNAFORMS']);
        $this->processes->renewAllDynaformGuid($result);
        foreach ($result as $key => $value) {
            $this->assertObjectHasAttribute($key, $result);
        }

        //for process inside PRO_DYNAFORMS
        $result = json_decode($data, JSON_OBJECT_AS_ARRAY);
        $result = (object) $result;
        $result->process['PRO_DYNAFORMS'] = [];
        $result->process['PRO_DYNAFORMS']['PROCESS'] = $result->dynaforms[0]['DYN_UID'];
        $this->processes->renewAllDynaformGuid($result);
        foreach ($result as $key => $value) {
            $this->assertObjectHasAttribute($key, $result);
        }
    }

    /**
     * Test it create a variable from old xml fields
     *
     * @covers \Processes::createProcessVariables()
     * @test
     */
    public function it_create_variables_from_import_old()
    {
        $process = \ProcessMaker\Model\Process::factory()->create();
        $attributes[] = [
            'VAR_UID' => G::generateUniqueID(),
            'PRJ_UID' => $process->PRO_UID,
            'VAR_NAME' => 'varTest',
            'VAR_FIELD_TYPE' => 'integer',
            'VAR_FIELD_SIZE' => 10,
            'VAR_LABEL' => 'string',
            'VAR_DBCONNECTION' => '',
            'VAR_SQL' => '',
            'VAR_NULL' => 0,
            'VAR_DEFAULT' => '',
            'VAR_ACCEPTED_VALUES' => '[]',
            'INP_DOC_UID' => ''
        ];
        $processes = new Processes();
        $processes->createProcessVariables($attributes);
        $result = ProcessVariables::getVariables($process->PRO_ID);
        $this->assertNotEmpty($result);
        $result = head($result);
        $this->assertArrayHasKey('PRO_ID', $result, "The result does not contains 'PRO_ID' as a key");
        $this->assertArrayHasKey('VAR_FIELD_TYPE_ID', $result, "The result does not contains 'VAR_FIELD_TYPE_ID' as a key");
        $this->assertEquals($result['VAR_FIELD_TYPE_ID'], 2);
    }

    /**
     * Test it create a variable from new xml fields
     *
     * @covers \Processes::createProcessVariables()
     * @test
     */
    public function it_create_variables_from_import_new()
    {
        $process = \ProcessMaker\Model\Process::factory()->create();
        $attributes[] = [
            'VAR_UID' => G::generateUniqueID(),
            'PRJ_UID' => $process->PRO_UID,
            'VAR_NAME' => 'varTest',
            'VAR_FIELD_TYPE' => 'string',
            'VAR_FIELD_TYPE_ID' => 1,
            'VAR_FIELD_SIZE' => 10,
            'VAR_LABEL' => 'string',
            'VAR_DBCONNECTION' => '',
            'VAR_SQL' => '',
            'VAR_NULL' => 0,
            'VAR_DEFAULT' => '',
            'VAR_ACCEPTED_VALUES' => '[]',
            'INP_DOC_UID' => ''
        ];
        $processes = new Processes();
        $processes->createProcessVariables($attributes);
        $result = ProcessVariables::getVariables($process->PRO_ID);
        $this->assertNotEmpty($result);
        $result = head($result);
        $this->assertArrayHasKey('PRO_ID', $result, "The result does not contains 'PRO_ID' as a key");
        $this->assertArrayHasKey('VAR_FIELD_TYPE_ID', $result, "The result does not contains 'VAR_FIELD_TYPE_ID' as a key");
    }
}
