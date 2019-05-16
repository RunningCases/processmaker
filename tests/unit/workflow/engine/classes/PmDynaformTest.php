<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use Tests\TestCase;

class PmDynaformTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * Constructor of the class.
     */
    function __construct()
    {
        $_SERVER["REQUEST_URI"] = "";
    }

    /**
     * Check if the getDynaform() method returning null if current dynaform parameter not exist.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_current_dynaform_parameter_not_exist()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
            'DYN_ID' => 6,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $pmDynaform = new PmDynaform(['CURRENT_DYNAFORM' => G::generateUniqueID()]);
        $pmDynaform->getDynaform();

        $this->assertEquals(null, $pmDynaform->langs);
    }

    /**
     * Check if the getDynaform() method returning null if dynaform not exist in the process.
     * @covers PmDynaform::getDynaform
     * @test
     */
    public function it_should_return_null_if_dynaform_not_exist_in_the_process()
    {
        $arrayForm = $this->createArrayDynaform();

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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
        $process = factory(Process::class, 1)->create();

        $arrayForm = $this->createArrayDynaform();
        $dynaform = factory(Dynaform::class, 1)->create([
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
        $process = factory(Process::class, 1)->create();

        $arrayForm = $this->createArrayDynaform();
        $dynaform = factory(Dynaform::class, 1)->create([
            'DYN_ID' => 7,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $arrayForm2 = $this->createArrayDynaform();
        $dynaform2 = factory(Dynaform::class, 1)->create([
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
        $process = factory(Process::class, 1)->create();

        $arrayForm = $this->createArrayDynaform();
        $dynaform = factory(Dynaform::class, 1)->create([
            'DYN_ID' => 7,
            'DYN_UID' => $arrayForm['items'][0]['id'],
            'PRO_UID' => $process[0]->PRO_UID,
            'DYN_CONTENT' => G::json_encode($arrayForm)
        ]);

        $arrayForm2 = $this->createArrayDynaform();
        $dynaform2 = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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

        $process = factory(Process::class, 1)->create();

        $dynaform = factory(Dynaform::class, 1)->create([
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
}
