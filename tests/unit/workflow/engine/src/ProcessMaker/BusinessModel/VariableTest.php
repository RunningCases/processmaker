<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use G;
use ProcessMaker\BusinessModel\Variable;
use ProcessMaker\Model\AdditionalTables;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Fields;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessVariables;
use Tests\TestCase;

/**
 * @coversDefaultClass ProcessMaker\BusinessModel\Variables
 */
class VariableTest extends TestCase
{
    /**
     * Test it create variables related to the process
     *
     * @covers \ProcessMaker\BusinessModel\Variable::create()
     * @test
     */
    public function it_create_variable_by_process()
    {
        $process = factory(Process::class)->create();

        factory(ProcessVariables::class)->create([
            'PRJ_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        $properties = [
            'VAR_UID' => G::generateUniqueID(),
            'VAR_NAME' => 'var_test',
            'VAR_FIELD_TYPE' => 'string',
            'VAR_FIELD_SIZE' => 10,
            'VAR_LABEL' => 'string',
            'VAR_DBCONNECTION' => '',
            'VAR_SQL' => '',
            'VAR_NULL' => 0,
            'VAR_DEFAULT' => '',
            'VAR_ACCEPTED_VALUES' => '[]',
            'INP_DOC_UID' => ''
        ];

        $variable = new Variable();
        $res = $variable->create($process->PRO_UID, $properties);
        $this->assertNotEmpty($res);
        $this->assertArrayHasKey('var_uid', $res, "The result does not contains 'var_uid' as key");
        $this->assertArrayHasKey('prj_uid', $res, "The result does not contains 'prj_uid' as key");
        $this->assertArrayHasKey('var_name', $res, "The result does not contains 'var_name' as key");
        $this->assertArrayHasKey('var_field_type', $res, "The result does not contains 'var_field_type' as key");
        $this->assertArrayHasKey('var_field_size', $res, "The result does not contains 'var_field_size' as key");
        $this->assertArrayHasKey('var_label', $res, "The result does not contains 'var_label' as key");
        $this->assertArrayHasKey('var_dbconnection', $res, "The result does not contains 'var_dbconnection' as key");
        $this->assertArrayHasKey('var_dbconnection_label', $res, "The result does not contains 'var_dbconnection_label' as key");
        $this->assertArrayHasKey('var_sql', $res, "The result does not contains 'var_sql' as key");
        $this->assertArrayHasKey('var_null', $res, "The result does not contains 'var_null' as key");
        $this->assertArrayHasKey('var_default', $res, "The result does not contains 'var_default' as key");
        $this->assertArrayHasKey('var_accepted_values', $res, "The result does not contains 'var_accepted_values' as key");
        $this->assertArrayHasKey('inp_doc_uid', $res, "The result does not contains 'inp_doc_uid' as key");
    }

    /**
     * Tests the exception
     *
     * @covers \ProcessMaker\BusinessModel\Variable::create()
     * @test
     */
    public function it_return_an_exception_when_var_name_is_empty()
    {
        $process = factory(Process::class)->create();
        factory(ProcessVariables::class)->create([
            'PRJ_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        $properties = [
            'VAR_UID' => G::generateUniqueID(),
            'VAR_NAME' => '',
            'VAR_FIELD_TYPE' => 'string',
            'VAR_FIELD_SIZE' => 10,
            'VAR_LABEL' => 'string',
            'VAR_DBCONNECTION' => '',
            'VAR_SQL' => '',
            'VAR_NULL' => 0,
            'VAR_DEFAULT' => '',
            'VAR_ACCEPTED_VALUES' => '[]',
            'INP_DOC_UID' => ''
        ];
        $variable = new Variable();
        $this->expectExceptionMessage("**ID_CAN_NOT_BE_NULL**");
        $res = $variable->create($process->PRO_UID, $properties);
    }

    /**
     * Tests the exception
     *
     * @covers \ProcessMaker\BusinessModel\Variable::create()
     * @test
     */
    public function it_return_an_exception_when_var_field_type_is_empty()
    {
        $process = factory(Process::class)->create();
        factory(ProcessVariables::class)->create([
            'PRJ_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        $properties = [
            'VAR_UID' => G::generateUniqueID(),
            'VAR_NAME' => 'var_test',
            'VAR_FIELD_TYPE' => '',
            'VAR_FIELD_SIZE' => 10,
            'VAR_LABEL' => 'string',
            'VAR_DBCONNECTION' => '',
            'VAR_SQL' => '',
            'VAR_NULL' => 0,
            'VAR_DEFAULT' => '',
            'VAR_ACCEPTED_VALUES' => '[]',
            'INP_DOC_UID' => ''
        ];
        $variable = new Variable();
        $this->expectExceptionMessage("**ID_CAN_NOT_BE_NULL**");
        $res = $variable->create($process->PRO_UID, $properties);
    }

    /**
     * Tests the exception
     *
     * @covers \ProcessMaker\BusinessModel\Variable::create()
     * @test
     */
    public function it_return_an_exception_when_var_label_is_empty()
    {
        $process = factory(Process::class)->create();
        factory(ProcessVariables::class)->create([
            'PRJ_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        $properties = [
            'VAR_UID' => G::generateUniqueID(),
            'VAR_NAME' => 'var_test',
            'VAR_FIELD_TYPE' => 'string',
            'VAR_FIELD_SIZE' => 10,
            'VAR_LABEL' => '',
            'VAR_DBCONNECTION' => '',
            'VAR_SQL' => '',
            'VAR_NULL' => 0,
            'VAR_DEFAULT' => '',
            'VAR_ACCEPTED_VALUES' => '[]',
            'INP_DOC_UID' => ''
        ];
        $variable = new Variable();
        $this->expectExceptionMessage("**ID_CAN_NOT_BE_NULL**");
        $res = $variable->create($process->PRO_UID, $properties);
    }

    /**
     * Test it return the variables related to the PRO_UID
     *
     * @covers \ProcessMaker\BusinessModel\Variable::getVariables()
     * @test
     */
    public function it_list_variables_by_process()
    {
        $process = factory(Process::class)->create();

        factory(ProcessVariables::class)->create([
            'PRJ_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
        ]);
        $variable = new Variable();
        $res = $variable->getVariables($process->PRO_UID);
        $this->assertNotEmpty($res);
        $res = head($res);
        $this->assertArrayHasKey('var_uid', $res, "The result does not contains 'var_uid' as key");
        $this->assertArrayHasKey('prj_uid', $res, "The result does not contains 'prj_uid' as key");
        $this->assertArrayHasKey('var_name', $res, "The result does not contains 'var_name' as key");
        $this->assertArrayHasKey('var_field_type', $res, "The result does not contains 'var_field_type' as key");
        $this->assertArrayHasKey('var_field_size', $res, "The result does not contains 'var_field_size' as key");
        $this->assertArrayHasKey('var_label', $res, "The result does not contains 'var_label' as key");
        $this->assertArrayHasKey('var_dbconnection', $res, "The result does not contains 'var_dbconnection' as key");
        $this->assertArrayHasKey('var_dbconnection_label', $res, "The result does not contains 'var_dbconnection_label' as key");
        $this->assertArrayHasKey('var_sql', $res, "The result does not contains 'var_sql' as key");
        $this->assertArrayHasKey('var_null', $res, "The result does not contains 'var_null' as key");
        $this->assertArrayHasKey('var_default', $res, "The result does not contains 'var_default' as key");
        $this->assertArrayHasKey('var_accepted_values', $res, "The result does not contains 'var_accepted_values' as key");
        $this->assertArrayHasKey('inp_doc_uid', $res, "The result does not contains 'inp_doc_uid' as key");
    }

    /**
     * Test it return the variables by type related to the PRO_UID
     *
     * @covers \ProcessMaker\BusinessModel\Variable::getVariablesByType()
     * @test
     */
    public function it_list_variables_by_type_related_a_process()
    {
        $process = factory(Process::class)->create();
        $varType = 'integer';
        $varTypeId = 2;
        for ($x = 1; $x <= 5; $x++) {
            $processVar = factory(ProcessVariables::class)->states('foreign_keys')->create([
                'PRO_ID' => $process->PRO_ID,
                'PRJ_UID' => $process->PRO_UID,
                'VAR_FIELD_TYPE' => $varType,
                'VAR_FIELD_TYPE_ID' => $varTypeId,
                'VAR_NAME' => 'varTestName' . $x,
            ]);
        }
        $variable = new Variable();
        // Get all results
        $res = $variable->getVariablesByType($process->PRO_UID, 2);
        $this->assertEquals(5, count($res));
        $res = head($res);
        $this->assertArrayHasKey('value', $res, "The result does not contains 'value' as key");
        // Get a specific start and limit
        $res = $variable->getVariablesByType($process->PRO_UID, 2, 0, 2);
        $this->assertNotEmpty($res);
        $this->assertEquals(2, count($res));
        // Get a specific search
        $res = $variable->getVariablesByType($process->PRO_UID, 2, 0, 4, 'varTest');
        $this->assertNotEmpty($res);
        $this->assertEquals(4, count($res));
        // When the search does not match
        $res = $variable->getVariablesByType($process->PRO_UID, 2, null, null, 'other');
        $this->assertEmpty($res);
    }

    /**
     * This verify method executeSqlControl.
     * @test
     * @covers \ProcessMaker\BusinessModel\Variable::executeSqlControl()
     */
    public function it_should_test_execute_sql_control()
    {
        $pathData = PATH_TRUNK . "/tests/resources/dynaform2.json";
        $data = file_get_contents($pathData);
        $json = json_decode($data);

        $dynaform = factory(Dynaform::class)->create([
            'DYN_CONTENT' => $data
        ]);
        $application = factory(Application::class)->create();

        $proUid = '';
        $params = [
            'app_uid' => $application->APP_UID,
            'countryDropdown1' => 'BO',
            'dyn_uid' => $dynaform->DYN_UID,
            'field_id' => 'stateDropdown',
            'grid_name' => 'gridVar004',
        ];
        $_SERVER["REQUEST_URI"] = '';
        $variable = new Variable();
        $result = $variable->executeSqlControl($proUid, $params);

        $this->assertNotEmpty($result);
    }

    /**
     * This verify method executeSqlControl try exception.
     * @test
     * @covers \ProcessMaker\BusinessModel\Variable::executeSqlControl()
     */
    public function it_should_test_execute_sql_control_with_exception()
    {
        //assert
        $this->expectException(Exception::class);

        $variable = new Variable();
        $result = $variable->executeSqlControl(null, []);
    }

    /**
     * This verify the exception
     * @test
     * @covers \ProcessMaker\BusinessModel\Variable::throwExceptionIfVariableIsAssociatedAditionalTable()
     */
    public function it_should_test_exception_when_a_variable_is_related_table()
    {
        //assert
        $this->expectException(Exception::class);
        // Create process variable
        $variable = factory(ProcessVariables::class)->create();
        $result = ProcessVariables::getVariable($variable->VAR_UID);
        $this->assertNotEmpty($result);
        // Create tables
        $table = factory(AdditionalTables::class)->create([
            'PRO_UID' => $variable->PRO_UID,
        ]);
        // Create fields
        $fields = factory(Fields::class)->create([
            'ADD_TAB_UID' => $table->ADD_TAB_UID,
            'FLD_NAME' => $variable->VAR_NAME,
        ]);
        $variable = new Variable();
        $res = $variable->throwExceptionIfVariableIsAssociatedAditionalTable($variable->VAR_UID);
    }
}
