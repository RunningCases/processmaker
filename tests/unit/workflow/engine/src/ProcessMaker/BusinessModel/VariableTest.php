<?php
namespace ProcessMaker\BusinessModel;

use G;
use ProcessMaker\BusinessModel\Variable;
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
     * @covers \ProcessMaker\BusinessModel\Variables::create()
     * @test
     */
    public function it_create_variable_by_process()
    {
        $process = factory(Process::class)->create();

        factory(ProcessVariables::class)->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
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
     * @covers \ProcessMaker\BusinessModel\Variables::create()
     * @test
     */
    public function it_return_an_exception_when_var_name_is_empty()
    {
        $process = factory(Process::class)->create();
        factory(ProcessVariables::class)->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
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
     * @covers \ProcessMaker\BusinessModel\Variables::create()
     * @test
     */
    public function it_return_an_exception_when_var_field_type_is_empty()
    {
        $process = factory(Process::class)->create();
        factory(ProcessVariables::class)->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
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
     * @covers \ProcessMaker\BusinessModel\Variables::create()
     * @test
     */
    public function it_return_an_exception_when_var_label_is_empty()
    {
        $process = factory(Process::class)->create();
        factory(ProcessVariables::class)->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
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
     * @covers \ProcessMaker\BusinessModel\Variables::getVariables()
     * @test
     */
    public function it_list_variables_by_process()
    {
        $process = factory(Process::class)->create();

        factory(ProcessVariables::class)->create([
                'PRJ_UID' => $process->PRO_UID,
                'PRO_ID' => $process->PRO_ID,
            ]
        );
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
}
