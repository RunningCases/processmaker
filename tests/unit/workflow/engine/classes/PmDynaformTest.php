<?php

use Faker\Factory;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use Tests\TestCase;

/**
 * Class PmDynaformTest
 *
 * @coversDefaultClass PmDynaform
 */
class PmDynaformTest extends TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $_SERVER["REQUEST_URI"] = "";
        if (!defined("DB_ADAPTER")) {
            define("DB_ADAPTER", "mysql");
        }
        if (!defined("DB_HOST")) {
            define("DB_HOST", env('DB_HOST'));
        }
        if (!defined("DB_NAME")) {
            define("DB_NAME", env('DB_DATABASE'));
        }
        if (!defined("DB_USER")) {
            define("DB_USER", env('DB_USERNAME'));
        }
        if (!defined("DB_PASS")) {
            define("DB_PASS", env('DB_PASSWORD'));
        }
        $this->truncateNonInitialModels();
    }

    /**
     * Check if the getDynaform() method returning null if current dynaform parameter not exist.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_current_dynaform_parameter_not_exist()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 6,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform();
        $result = $pmDynaform->getDynaform();

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaform() method returning null if parameters is empty.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_parameters_is_empty()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 5,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform([]);
        $result = $pmDynaform->getDynaform();

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaform() method returning null if current dynaform not exist.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_current_dynaform_not_exist()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 5,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => G::generateUniqueID()]);
        $result = $pmDynaform->getDynaform();

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaform() method returning null if parameters is not empty.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_parameters_is_not_empty()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 5,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform(["APP_DATA" => []]);
        $result = $pmDynaform->getDynaform();

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaform() method returning null if parameter is a string.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_parameter_is_a_string()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 5,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform("");

        $result = $pmDynaform->getDynaform();

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaform() method returning null if parameter is a integer.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_parameter_is_a_integer()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 5,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform(1);

        $result = $pmDynaform->getDynaform();

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaform() method returning record property.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_record_property_if_record_is_not_null()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 4,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);
        $expected = (array) $dynaform->first()->toArray();
        unset($expected['id']); //This is removed because is aggregate from factory.
        //first execution in constructor
        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => $arrayForm['items'][0]['id']]);
        //second execution
        $pmDynaform->getDynaform();
        //third execution
        $pmDynaform->getDynaform();

        $this->assertEquals($expected, $pmDynaform->record);
    }

    /**
     * Check if the getDynaform() method setting langs property in null if current dynaform not exist.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_langs_property_in_null_if_current_dynaform_not_exist()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 6,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => G::generateUniqueID()]);
        $pmDynaform->getDynaform();

        $this->assertEquals(null, $pmDynaform->translations);
    }

    /**
     * Check if the getDynaform() method returning null if dynaform not exist in the process.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_dynaform_not_exist_in_the_process()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 5,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => G::generateUniqueID()]);
        $pmDynaform->getDynaform();

        $this->assertEquals(null, $pmDynaform->record);
    }

    /**
     * Check if the getDynaform() method returning fields.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_a_dynaform_in_array_format()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 3,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);
        $expected = (array) $dynaform->first()->toArray();
        unset($expected['id']); //This is removed because is aggregate from factory.

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => $arrayForm['items'][0]['id']]);
        $result = $pmDynaform->getDynaform();
        $this->assertEquals($expected, $result);
    }

    /**
     * Check if the getDynaforms() method returning null when not exist dynaform.
     * @covers PmDynaform::getDynaforms
     * @test
     */
    public function it_should_return_null_when_not_exist_dynaform()
    {
        $process = Process::factory(1)->create();

        $arrayForm = $this->createArrayDynaform();
        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 7,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform();
        $result = $pmDynaform->getDynaforms(['CURRENT_DYNAFORM' => G::generateUniqueID()]);

        $this->assertEquals(null, $result);
    }

    /**
     * Check if the getDynaforms() method returning null when record is null.
     * @covers PmDynaform::getDynaforms
     * @test
     */
    public function it_should_return_null_when_record_is_null()
    {
        $pmDynaform = new PmDynaform();
        $pmDynaform->getDynaforms();

        $this->assertEquals(null, $pmDynaform->record);
    }

    /**
     * Check if the getDynaforms() method returning record property.
     * @covers PmDynaform::getDynaforms
     * @test
     */
    public function it_should_return_array_dynaforms_except_current_dynaform_in_second_execution()
    {
        $process = Process::factory(1)->create();

        $arrayForm = $this->createArrayDynaform();
        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 7,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $arrayForm2 = $this->createArrayDynaform();
        $dynaform2 = Dynaform::factory(1)->create([
            'DYN_ID' => 9,
            'DYN_UID' => $arrayForm2['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm2)
        ]);

        $expected = (array) $dynaform2->first()->toArray();
        unset($expected['id']); //This is removed because is aggregate from factory.

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => $arrayForm['items'][0]['id']]);
        $pmDynaform->getDynaforms();

        $this->assertEquals([$expected], $pmDynaform->records);
    }

    /**
     * Check if the getDynaforms() method returning arrays dynaforms except current dynaform.
     * @covers PmDynaform::getDynaforms
     * @test
     */
    public function it_should_return_array_dynaforms_except_current_dynaform()
    {
        $process = Process::factory(1)->create();

        $arrayForm = $this->createArrayDynaform();
        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 7,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $arrayForm2 = $this->createArrayDynaform();
        $dynaform2 = Dynaform::factory(1)->create([
            'DYN_ID' => 9,
            'DYN_UID' => $arrayForm2['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm2)
        ]);

        $expected = (array) $dynaform2->first()->toArray();
        unset($expected['id']); //This is removed because is aggregate from factory.

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => $arrayForm['items'][0]['id']]);
        $result = $pmDynaform->getDynaforms();

        $this->assertEquals([$expected], $result);
    }

    /**
     * Check if the isUsed() method is returning false when not exist data related to process id.
     * @covers PmDynaform::isUsed
     * @test
     */
    public function it_should_return_false_when_not_exist_data_related_to_id_process()
    {
        $processId = G::generateUniqueID();

        $pmDynaform = new PmDynaform();
        $result = $pmDynaform->isUsed($processId, 'var1');

        $this->assertEquals(false, $result);
    }

    /**
     * Check if the isUsed() method is returning the ID of the dynaform in case 
     * the variable is part of the dynaform.
     * @covers PmDynaform::isUsed
     * @test
     */
    public function it_should_return_id_of_dynaform_when_is_used_variable()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 1,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform();
        $result = $pmDynaform->isUsed($process[0]->PRO_UID, $arrayForm['items'][0]['variables'][0]);

        $this->assertEquals($dynaform[0]->DYN_UID, $result);
    }

    /**
     * Check if the isUsed() method is returning false in case the variable is 
     * not part of the dynaform.
     * @covers PmDynaform::isUsed
     * @test
     */
    public function it_should_return_false_when_not_used_variable()
    {
        $arrayVariable = $this->createArrayVariable('var10');

        $arrayForm = $this->createArrayDynaform();

        $process = Process::factory(1)->create();

        $dynaform = Dynaform::factory(1)->create([
            'DYN_ID' => 2,
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform();
        $result = $pmDynaform->isUsed($process[0]->PRO_UID, $arrayVariable);

        $this->assertEquals(false, $result);
    }

    /**
     * Return an object that represents the structure of a process variable.
     * @return array
     */
    private function createArrayVariable($varName)
    {
        return [
            "var_uid" => G::generateUniqueID(),
            "prj_uid" => G::generateUniqueID(),
            "var_name" => $varName,
            "var_field_type" => "string",
            "var_field_size" => 10,
            "var_label" => "string",
            "var_dbconnection" => "workflow",
            "var_dbconnection_label" => "PM Database",
            "var_sql" => "",
            "var_null" => 0,
            "var_default" => "",
            "var_accepted_values" => "[]",
            "inp_doc_uid" => ""
        ];
    }

    /**
     * Returns an object that represents the structure of a control.
     * @return array
     */
    private function createArrayControl($varUid, $varName)
    {
        return [
            "type" => "textarea",
            "variable" => $varName,
            "var_uid" => $varUid,
            "dataType" => "string",
            "protectedValue" => false,
            "id" => "textareaVar001",
            "name" => "textareaVar001",
            "label" => "textarea_1",
            "defaultValue" => "",
            "placeholder" => "",
            "hint" => "",
            "required" => false,
            "requiredFieldErrorMessage" => "",
            "validate" => "",
            "validateMessage" => "",
            "mode" => "parent",
            "dbConnection" => "workflow",
            "dbConnectionLabel" => "PM Database",
            "sql" => "",
            "rows" => "5",
            "var_name" => "textareaVar001",
            "colSpan" => 12
        ];
    }

    /**
     * Returns an object that represents the structure of a dynaform. 
     * @return array
     */
    private function createArrayDynaform()
    {
        $var1 = $this->createArrayVariable('var1');
        $control1 = $this->createArrayControl($var1['var_uid'], $var1['var_name']);

        $var2 = $this->createArrayVariable('var2');
        $control2 = $this->createArrayControl($var2['var_uid'], $var2['var_name']);

        return [
            "name" => "subform",
            "description" => "",
            "items" => [
                [
                    "type" => "form",
                    "variable" => "",
                    "var_uid" => "",
                    "dataType" => "",
                    "id" => G::generateUniqueID(),
                    "name" => "subform",
                    "description" => "",
                    "mode" => "edit",
                    "script" => "",
                    "language" => "en",
                    "externalLibs" => "",
                    "printable" => false,
                    "items" => [
                        [$control1],
                        [$control2]
                    ],
                    "variables" => [
                        $var1,
                        $var2
                    ]
                ]
            ]
        ];
    }

    /**
     * It tests that the json file is getting the defined values when the grid has one undefined control
     *
     * @test
     */
    public function it_should_add_the_correct_fields_with_a_single_undefined_control()
    {
        //Creates the PmDynaform object
        $pmDynaform = new PmDynaform();

        //A json that contains the text control data and columns
        $jsonData = (object)(
        [
            "data" => (object)([
                "1" => [
                    ["value" => "textControl1", "label" => "textControl1"],
                    ["value" => "textControl2", "label" => "textControl2"],
                    ["value" => "", "label" => ""]
                ]
            ]),

            "columns" => (object)([
                0 => (object)([
                    "id" => "text0000000001",
                    "name" => "text0000000001",
                ]),
                1 => (object)([
                    "id" => "textarea0000000001",
                    "name" => "textarea0000000001",
                ]),
                2 => (object)([
                    "id" => "text0000000002",
                    "name" => "text0000000002",
                ])
            ])
        ]
        );

        // An array that contains the variables stored on the App Data
        $appData = [
            "1" => [
                "text0000000001" => "",
                "text0000000001_label" => "",
                "textarea0000000001" => "",
                "textarea0000000001_label" => ""
            ]
        ];

        //Calls the setDataSchema method
        $resultText = $pmDynaform->setDataSchema($jsonData, $appData);

        //This assert the result is null
        $this->assertNull($resultText);

        //Assert the 'dataSchema' field was added
        $this->assertObjectHasAttribute('dataSchema', $jsonData);

        //It asserts the first control is defined
        $this->assertTrue($jsonData->dataSchema['1'][0]['defined']);

        //It asserts the second control is defined
        $this->assertTrue($jsonData->dataSchema['1'][1]['defined']);

        //It asserts the second control is undefined
        $this->assertFalse($jsonData->dataSchema['1'][2]['defined']);
    }

    /**
     * It tests that the json file is getting the defined values when the grid has more than one undefined control
     *
     * @test
     */
    public function it_should_add_the_correct_fields_with_more_than_one_undefined_control()
    {
        //Creates the PmDynaform object
        $pmDynaform = new PmDynaform();

        //A json that contains the text control data and columns
        $jsonData = (object)(
        [
            "data" => (object)([
                "1" => [
                    ["value" => "textControl1", "label" => "textControl1"],
                    ["value" => "textAreaControl2", "label" => "textAreaControl2"],
                    ["value" => "dropdowncontrol1", "label" => "dropdowncontrol1"],
                    ["value" => "", "label" => ""],
                    ["value" => "", "label" => ""]
                ]
            ]),
            "columns" => (object)([
                0 => (object)([
                    "id" => "text0000000001",
                    "name" => "text0000000001",
                ]),
                1 => (object)([
                    "id" => "textarea0000000001",
                    "name" => "textarea0000000001",
                ]),
                2 => (object)([
                    "id" => "dropdown0000000001",
                    "name" => "dropdown0000000001",
                ]),
                3 => (object)([
                    "id" => "text0000000002",
                    "name" => "text0000000002",
                ]),
                4 => (object)([
                    "id" => "text0000000003",
                    "name" => "text0000000003",
                ])
            ])
        ]
        );

        // An array that contains the variables stored on the App Data
        $appData = [
            "1" => [
                "text0000000001" => "",
                "text0000000001_label" => "",
                "textarea0000000001" => "",
                "textarea0000000001_label" => "",
                "dropdown0000000001" => "",
                "dropdown0000000001_label" => ""
            ]
        ];

        //Calls the setDataSchema method
        $resultText = $pmDynaform->setDataSchema($jsonData, $appData);

        //This assert the result is null
        $this->assertNull($resultText);

        //Assert the 'dataSchema' field was added
        $this->assertObjectHasAttribute('dataSchema', $jsonData);

        //It asserts the first control is defined
        $this->assertTrue($jsonData->dataSchema['1'][0]['defined']);

        //It asserts the second control is defined
        $this->assertTrue($jsonData->dataSchema['1'][1]['defined']);

        //It asserts the third control is defined
        $this->assertTrue($jsonData->dataSchema['1'][2]['defined']);

        //It asserts the fourth control is undefined
        $this->assertFalse($jsonData->dataSchema['1'][3]['defined']);

        //It asserts the fifth control is undefined
        $this->assertFalse($jsonData->dataSchema['1'][4]['defined']);
    }

    /**
     * It tests that the json file is getting the defined and undefined values when the grid has more than one row
     *
     * @test
     */
    public function it_should_add_the_correct_fields_with_more_than_one_rows()
    {
        //Creates the PmDynaform object
        $pmDynaform = new PmDynaform();

        //A json that contains the text control data and columns
        $jsonData = (object)(
        [
            "data" => (object)([
                "1" => [
                    ["value" => "textControl1", "label" => "textControl1"],
                    ["value" => "textAreaControl2", "label" => "textAreaControl2"],
                    ["value" => "dropdowncontrol1", "label" => "dropdowncontrol1"],
                    ["value" => "", "label" => ""],
                    ["value" => "", "label" => ""]
                ],
                "2" => [
                    ["value" => "textControl1", "label" => "textControl1"],
                    ["value" => "textAreaControl2", "label" => "textAreaControl2"],
                    ["value" => "dropdowncontrol1", "label" => "dropdowncontrol1"],
                    ["value" => "", "label" => ""],
                    ["value" => "", "label" => ""]
                ],
                "3" => [
                    ["value" => "textControl1", "label" => "textControl1"],
                    ["value" => "textAreaControl2", "label" => "textAreaControl2"],
                    ["value" => "dropdowncontrol1", "label" => "dropdowncontrol1"],
                    ["value" => "", "label" => ""],
                    ["value" => "", "label" => ""]
                ]
            ]),
            "columns" => (object)([
                0 => (object)([
                    "id" => "text0000000001",
                    "name" => "text0000000001",
                ]),
                1 => (object)([
                    "id" => "textarea0000000001",
                    "name" => "textarea0000000001",
                ]),
                2 => (object)([
                    "id" => "dropdown0000000001",
                    "name" => "dropdown0000000001",
                ]),
                3 => (object)([
                    "id" => "text0000000002",
                    "name" => "text0000000002",
                ]),
                4 => (object)([
                    "id" => "text0000000003",
                    "name" => "text0000000003",
                ])
            ])
        ]
        );

        // An array that contains the variables stored on the App Data
        $appData = [
            "1" => [
                "text0000000001" => "",
                "text0000000001_label" => "",
                "textarea0000000001" => "",
                "textarea0000000001_label" => "",
                "dropdown0000000001" => "",
                "dropdown0000000001_label" => ""
            ],
            "2" => [
                "text0000000001" => "",
                "text0000000001_label" => "",
                "textarea0000000001" => "",
                "textarea0000000001_label" => "",
                "dropdown0000000001" => "",
                "dropdown0000000001_label" => ""
            ],
            "3" => [
                "text0000000001" => "",
                "text0000000001_label" => "",
                "textarea0000000001" => "",
                "textarea0000000001_label" => "",
                "dropdown0000000001" => "",
                "dropdown0000000001_label" => ""
            ]
        ];

        //Calls the setDataSchema method
        $resultText = $pmDynaform->setDataSchema($jsonData, $appData);

        //This assert the result is null
        $this->assertNull($resultText);

        //Assert the 'dataSchema' field was added
        $this->assertObjectHasAttribute('dataSchema', $jsonData);

        foreach ($jsonData->dataSchema as $key => $value) {
            //It asserts the first control is defined
            $this->assertTrue($jsonData->dataSchema[$key][0]['defined']);

            //It asserts the second control is defined
            $this->assertTrue($jsonData->dataSchema[$key][1]['defined']);

            //It asserts the third control is defined
            $this->assertTrue($jsonData->dataSchema[$key][2]['defined']);

            //It asserts the fourth control is undefined
            $this->assertFalse($jsonData->dataSchema[$key][3]['defined']);

            //It asserts the fifth control is undefined
            $this->assertFalse($jsonData->dataSchema[$key][4]['defined']);
        }
    }
    /**
     * Review if the set translations are working correctly
     * If the translation does not exit needs to return null
     *
     * @covers PmDynaform::setTranslations()
     * @test
     */
    public function it_should_set_the_translations_if_exist()
    {
        // Create a form without translations defined
        $arrayForm = $this->createArrayDynaform();
        $form = Dynaform::factory()->create([
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);
        $pmDynaform = new PmDynaform([]);
        $pmDynaform->setTranslations($form->DYN_UID);
        $this->assertNull($pmDynaform->translations);

        // Create a form with  translations defined
        $arrayForm = $this->createArrayDynaform();
        $form = Dynaform::factory()->translations()->create([
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);
        $pmDynaform = new PmDynaform([]);
        $pmDynaform->setTranslations($form->DYN_UID);
        $this->assertNotNull($pmDynaform->translations);
    }

    /**
     * Review if the get labels from a specific language is working
     * If the translation defined does not have the specific language will return null
     *
     * @covers PmDynaform::getLabelsPo()
     * @test
     */
    public function it_should_get_label_from_translation()
    {
        $arrayForm = $this->createArrayDynaform();
        // Create a translations related to ["es", "es-Es"]
        $form = Dynaform::factory()->translations()->create([
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);
        $pmDynaform = new PmDynaform([]);
        $pmDynaform->setTranslations($form->DYN_UID);
        $labelsPo = $pmDynaform->getLabelsPo('es');
        $this->assertNotNull($labelsPo);
        $labelsPo = $pmDynaform->getLabelsPo('es-Es');
        $this->assertNotNull($labelsPo);
        $faker = Factory::create();
        $labelsPo = $pmDynaform->getLabelsPo($faker->sentence(1));
        $this->assertNull($labelsPo);
    }

    /**
     * Review if the SQL that uses the SELECT statement is parsed correctly
     *
     * @covers PmDynaform::sqlParse()
     * @test
     */
    public function it_should_get_sql_parsed_select_statement()
    {
        // Note.- The following queries are used by running tests but none of them are valid
        $sqlOriginal1 = 'SELECT TOP 10 USERS.USR_UID, USERS.USR_ID, USERS.USR_USERNAME AS USERNAME, MAX(RBAC_USERS_ROLES.ROL_UID), 
            MIN(RBAC_USERS_ROLES.ROL_UID) AS THEMIN, (SELECT USR_FIRSTNAME FROM USERS), (SELECT USR_LASTNAME AS XXX) AS YYY, <>, 1000
            FROM USERS AS OFFSET INNER JOIN RBAC_USERS ON USERS.USR_UID = RBAC_USERS.USR_UID INNER JOIN RBAC_USERS_ROLES ON
            USERS.USR_UID = RBAC_USERS_ROLES.USR_UID WHERE USERS.USR_UID <> "" AND 1 AND OFFSET 1 GROUP BY USERS.USR_UID HAVING
            USERS.USR_UID <> "" ORDER BY USERS.USR_ID DESC LIMIT 1 OFFSET 10 FOR UPDATE';

        $sqlOriginal2 = 'SELECT TOP 10 USERS.USR_UID, USERS.USR_ID, USERS.USR_USERNAME AS USERNAME, MAX(RBAC_USERS_ROLES.ROL_UID),
            MIN(RBAC_USERS_ROLES.ROL_UID) AS THEMIN, (SELECT USR_FIRSTNAME FROM USERS), (SELECT USR_LASTNAME AS XXX) AS YYY, <>, 1000
            FROM USERS INNER JOIN RBAC_USERS ON USERS.USR_UID = RBAC_USERS.USR_UID INNER JOIN RBAC_USERS_ROLES ON
            USERS.USR_UID = RBAC_USERS_ROLES.USR_UID WHERE USERS.USR_UID <> "" AND 1 GROUP BY USERS.USR_UID HAVING
            USERS.USR_UID <> "" ORDER BY USERS.USR_ID DESC LIMIT 1, 10 FOR UPDATE';

        $sqlOriginal3 = 'DUMMY';

        $sqlOriginal4 = 'SELECT U.USR_UID, U.USR_USERNAME FROM PMT_CODES C INNER JOIN USERS U ON U.USR_USERNAME = C.USR_USERNAME';

        // Instance the class PmDynaform
        $pmDynaform = new PmDynaform([]);

        // Test bug PMC-1299
        $sqlParsed1 = $pmDynaform->sqlParse($sqlOriginal1);
        $this->assertFalse(strpos($sqlParsed1, 'INNER INNER'));

        // For now is only used for complete the coverture
        $sqlParsed2 = $pmDynaform->sqlParse($sqlOriginal2, 'dummy_function_for_this_unit_test');
        // To Do: Currently, there is a coverture of 100%, but is necessary to add more tests to verify
        // if the SQL string is parsed correctly in more scenarios

        // Test another string, shoul be return the same value
        $sqlParsed3 = $pmDynaform->sqlParse($sqlOriginal3);
        $this->assertEquals($sqlOriginal3, $sqlParsed3);

        // Test bug PMCORE-1049
        $sqlParsed4 = $pmDynaform->sqlParse($sqlOriginal4);
        $this->assertNotFalse(strpos($sqlParsed4, 'C.USR_USERNAME'));
    }

    /**
     * Review if the SQL that uses the CALL statement is parsed correctly
     *
     * @covers PmDynaform::sqlParse()
     * @test
     */
    public function it_should_get_sql_parsed_call_statement()
    {
        $sqlOriginal = 'CALL dummy_sp_for_this_unit_test()';

        $pmDynaform = new PmDynaform([]);
        $sqlParsed = $pmDynaform->sqlParse($sqlOriginal);

        $this->assertEquals(strlen($sqlOriginal), strlen($sqlParsed));
    }

    /**
     * Review if the SQL that uses the EXECUTE statement is parsed correctly
     *
     * @covers PmDynaform::sqlParse()
     * @test
     */
    public function it_should_get_sql_parsed_execute_statement()
    {
        $sqlOriginal = 'EXECUTE dummy_sp_for_this_unit_test()';

        $pmDynaform = new PmDynaform([]);
        $sqlParsed = $pmDynaform->sqlParse($sqlOriginal);

        $this->assertEquals(strlen($sqlOriginal), strlen($sqlParsed));
    }

    /**
     * Review if the title of a Dynaform is correct
     *
     * @covers PmDynaform::getDynaformTitle()
     * @test
     */
    public function it_should_get_dynaform_title()
    {
        // Create a Dynaform
        $dynaform = Dynaform::factory()->create([]);

        // Instance the class to test
        $pmDynaform = new PmDynaform();

        // Get the title of the Dynaform
        $dynaformTitle = $pmDynaform->getDynaformTitle($dynaform->DYN_UID);

        // Compare the values
        $this->assertEquals($dynaformTitle, $dynaform->DYN_TITLE);
    }
    
    /**
     * This test should verify the setDependentOptionsForDatetime() method, to 
     * add the dependentOptions property to the datetime control.
     * @test
     * @covers PmDynaform::jsonr()
     * @covers PmDynaform::setDependentOptionsForDatetime()
     */
    public function it_should_test_dependent_options_for_datetime_control()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaform1.json";
        $data = file_get_contents($pathData);
        $json = json_decode($data);

        //assert for not contain property: dependentOptions
        $result = json_decode(json_encode($json), JSON_OBJECT_AS_ARRAY);
        $fn = function($item) use(&$fn) {
            if (is_array($item)) {
                if (isset($item['type']) && $item['type'] === 'datetime') {
                    $this->assertArrayNotHasKey('dependentOptions', $item);
                }
                array_map($fn, $item);
            }
        };
        array_map($fn, $result);

        //assert new property: dependentOptions
        $dynaform = new PmDynaform();
        $dynaform->jsonr($json);
        $result = json_decode(json_encode($json), JSON_OBJECT_AS_ARRAY);

        $fn = function($item) use(&$fn) {
            if (is_array($item)) {
                if (isset($item['type']) && $item['type'] === 'datetime') {
                    $this->assertArrayHasKey('dependentOptions', $item);
                    $this->assertArrayHasKey('minDate', $item['dependentOptions']);
                    $this->assertArrayHasKey('maxDate', $item['dependentOptions']);
                    $this->assertArrayHasKey('defaultDate', $item['dependentOptions']);
                }
                array_map($fn, $item);
            }
        };
        array_map($fn, $result);

        $dynaform = new PmDynaform();
        $reflection = new ReflectionClass($dynaform);
        $reflectionMethod = $reflection->getMethod('setDependentOptionsForDatetime');
        $reflectionMethod->setAccessible(true);

        $a = new stdClass();
        $reflectionMethod->invokeArgs($dynaform, [&$a]);
        $this->assertInstanceOf('ReflectionMethod', $reflectionMethod);

        $a = new stdClass();
        $a->type = 'suggest';
        $reflectionMethod->invokeArgs($dynaform, [&$a]);
        $this->assertInstanceOf('ReflectionMethod', $reflectionMethod);
    }
    
    /**
     * This verify method getValuesDependentFields.
     * @test
     * @covers PmDynaform::jsonr()
     * @covers PmDynaform::getValuesDependentFields()
     */
    public function it_should_test_get_values_dependent_fields()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaform2.json";
        $data = file_get_contents($pathData);
        $json = json_decode($data);

        $pathData2 = PATH_TRUNK . "/tests/resources/fieldDynaform.json";
        $data2 = file_get_contents($pathData2);
        $json2 = json_decode($data2);

        $dynaform = new PmDynaform();
        $dynaform->record = [
            'DYN_CONTENT' => $data
        ];
        $dynaform->fields = [
            'APP_DATA' => [
                'stateDropdown' => 'stateDropdown'
            ]
        ];
        $reflection = new ReflectionClass($dynaform);
        $reflectionMethod = $reflection->getMethod('getValuesDependentFields');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($dynaform, [&$json2]);

        $this->assertArrayHasKey('countryDropdown', $result);
    }

    /**
     * This verify method searchField.
     * @test
     * @covers PmDynaform::jsonr()
     * @covers PmDynaform::searchField()
     */
    public function it_should_test_search_field()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaform2.json";
        $data = file_get_contents($pathData);
        $json = json_decode($data);

        $pathData2 = PATH_TRUNK . "/tests/resources/fieldDynaform.json";
        $data2 = file_get_contents($pathData2);
        $json2 = json_decode($data2);

        $dynaform = Dynaform::factory()->create([
            'DYN_CONTENT' => $data
        ]);
        Dynaform::factory()->create([
            'DYN_CONTENT' => $data,
            'PRO_UID' => $dynaform->PRO_UID
        ]);

        $dynUid = $dynaform->DYN_UID;
        $fieldId = 'stateDropdown';
        $proUid = '';
        $and = [];

        $dynaform = new PmDynaform();
        $result = $dynaform->searchField($dynUid, $fieldId, $proUid, $and);

        $this->assertObjectHasAttribute('id', $result);
        $this->assertEquals($result->id, 'stateDropdown');
    }

    /**
     * This verify method replaceDataField.
     * @test
     * @covers PmDynaform::jsonr()
     * @covers PmDynaform::replaceDataField()
     */
    public function it_should_test_replace_data_field()
    {
        $sql = "SELECT IS_UID, IS_NAME FROM ISO_SUBDIVISION WHERE IC_UID = '@?countryDropdown' ORDER BY IS_NAME";
        $data = [
            'countryDropdown' => 'BO'
        ];
        $dynaform = new PmDynaform();
        $reflection = new ReflectionClass($dynaform);
        $reflectionMethod = $reflection->getMethod('replaceDataField');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($dynaform, [&$sql, $data]);

        $expected = "SELECT IS_UID, IS_NAME FROM ISO_SUBDIVISION WHERE IC_UID = 'BO' ORDER BY IS_NAME";
        $this->assertEquals($expected, $result);
    }

    /**
     * This verify method completeAdditionalHelpInformationOnControls.
     * @test
     * @covers PmDynaform::jsonr()
     * @covers PmDynaform::completeAdditionalHelpInformationOnControls()
     */
    public function it_should_test_complete_additional_help_information_on_controls()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaform2.json";
        $data = file_get_contents($pathData);
        $json = json_decode($data);

        $dynaform = new PmDynaform();
        $reflection = new ReflectionClass($dynaform);
        $reflectionMethod = $reflection->getMethod('completeAdditionalHelpInformationOnControls');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invokeArgs($dynaform, [&$json]);
        $this->assertInstanceOf('ReflectionMethod', $reflectionMethod);
    }

    /**
     * This verify method jsonsf.
     * @test
     * @covers PmDynaform::jsonr()
     * @covers PmDynaform::jsonsf()
     */
    public function it_should_test_jsonsf()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaform2.json";
        $data = file_get_contents($pathData);
        $json = json_decode($data);

        $pathData2 = PATH_TRUNK . "/tests/resources/fieldDynaform.json";
        $data2 = file_get_contents($pathData2);
        $json2 = json_decode($data2);

        $dynaform = Dynaform::factory()->create([
            'DYN_CONTENT' => $data
        ]);
        Dynaform::factory()->create([
            'DYN_CONTENT' => $data,
            'PRO_UID' => $dynaform->PRO_UID
        ]);

        $id = 'stateDropdown';
        $for = 'id';
        $and = ['gridName' => 'gridVar003'];

        $dynaform = new PmDynaform();
        $reflection = new ReflectionClass($dynaform);

        $reflectionMethod = $reflection->getMethod('jsonsf');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($dynaform, [&$json, $id, $for, $and]);

        $this->assertObjectHasAttribute('id', $result);
        $this->assertEquals($result->id, 'stateDropdown');
    }

    /**
     * This check that the method getCredentials is destroying correctly the session variable "USER_LOGGED" for
     * not authenticated users
     * @test
     * @covers PmDynaform::getCredentials()
     */
    public function it_should_test_get_credentials_destroy_user_logged_if_not_authenticated_user()
    {
        // Set the request URI, this is required by the method "getCredentials"
        $_SERVER['REQUEST_URI'] = '/sysworkflow/en/neoclassic/tracker/tracker_Show';

        // Destroy variable for "USER_LOGGED" if exists
        unset($_SESSION['USER_LOGGED']);

        // Create a new instance of the class for the first time
        $pmDynaform = new PmDynaform();

        // Call method "getCredentials"
        $pmDynaform->getCredentials();

        // Session variable for "USER_LOGGED" should be empty
        $this->assertTrue(empty($_SESSION['USER_LOGGED']));

        // Create a new instance of the class for the second time
        $pmDynaform = new PmDynaform();

        // Session variable for "USER_LOGGED" should be empty
        $this->assertTrue(empty($_SESSION['USER_LOGGED']));
    }
}

// Dummy function used for the coverture
function dummy_function_for_this_unit_test()
{
}
