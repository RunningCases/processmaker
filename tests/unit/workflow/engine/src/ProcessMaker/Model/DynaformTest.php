<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use Tests\TestCase;

/**
 * Class DynaformTest
 *
 * @coversDefaultClass \ProcessMaker\Model\Dynaform
 */
class DynaformTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Call the setUp parent method
     */
    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * Test belongs to PRO_UID
     *
     * @covers \ProcessMaker\Model\Dynaform::process()
     * @test
     */
    public function it_has_a_process()
    {
        $dynaForm = factory(Dynaform::class)->create([
            'PRO_UID' => function () {
                return factory(Process::class)->create()->PRO_UID;
            }
        ]);
        $this->assertInstanceOf(Process::class, $dynaForm->process);
    }

    /**
     * Tests get form by process
     * 
     * @covers \ProcessMaker\Model\Dynaform::getByProUid()
     * @test
     */
    public function it_tests_get_by_pro_uid()
    {
        $dynaForm = factory(Dynaform::class)->states('foreign_keys')->create();
        $result = Dynaform::getByProUid($dynaForm->PRO_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests get form by uid
     * 
     * @covers \ProcessMaker\Model\Dynaform::getByDynUid()
     * @test
     */
    public function it_tests_get_by_dyn_uid()
    {
        $dynaForm = factory(Dynaform::class)->states('foreign_keys')->create();
        $result = Dynaform::getByDynUid($dynaForm->DYN_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Tests get form by process excluding a uid
     * 
     * @covers \ProcessMaker\Model\Dynaform::getByProUidExceptDynUid()
     * @test
     */
    public function it_tests_get_by_process_exclude_dyn_uid()
    {
        $dynaForm = factory(Dynaform::class)->states('foreign_keys')->create();
        $result = Dynaform::getByProUidExceptDynUid($dynaForm->PRO_UID, $dynaForm->DYN_UID);
        $this->assertEmpty($result);
    }

    /**
     * It tests the process scope in the dynaform model
     * 
     * @covers \ProcessMaker\Model\Dynaform::scopeProcess()
     * @test
     */
    public function it_should_test_process_scope_in_dynaform_model()
    {
        $process = factory(Process::class, 3)->create();

        factory(Dynaform::class)->create(
            [
                'PRO_UID' => $process[0]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"2","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6170264265d1b544bebdbd5098250194","name":"2","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"title","id":"title0000000001","label":"title_1","colSpan":12}],[{"type":"text","variable":"textVar002","var_uid":"9778460595d1b545088dd69091601043","dataType":"string","protectedValue":false,"id":"textVar002","name":"textVar002","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"","var_name":"textVar002","colSpan":12}],[{"type":"textarea","variable":"textareaVar001","var_uid":"2934510045d1b5453f21373072798412","dataType":"string","protectedValue":false,"id":"textareaVar001","name":"textareaVar001","label":"textarea_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","validate":"","validateMessage":"","mode":"parent","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"","rows":"5","var_name":"textareaVar001","colSpan":12}],[{"type":"datetime","variable":"datetimeVar001","var_uid":"9780823375d1b5455e9c3a2064729484","dataType":"datetime","protectedValue":false,"id":"datetimeVar001","name":"datetimeVar001","label":"datetime_1","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","mode":"parent","format":"YYYY-MM-DD","dayViewHeaderFormat":"MMMM YYYY","extraFormats":false,"stepping":1,"minDate":"","maxDate":"","useCurrent":"false","collapse":true,"locale":"","defaultDate":"","disabledDates":false,"enabledDates":false,"icons":{"time":"glyphicon glyphicon-time","date":"glyphicon glyphicon-calendar","up":"glyphicon glyphicon-chevron-up","down":"glyphicon glyphicon-chevron-down","previous":"glyphicon glyphicon-chevron-left","next":"glyphicon glyphicon-chevron-right","today":"glyphicon glyphicon-screenshot","clear":"glyphicon glyphicon-trash"},"useStrict":false,"sideBySide":false,"daysOfWeekDisabled":false,"calendarWeeks":false,"viewMode":"days","toolbarPlacement":"default","showTodayButton":false,"showClear":"false","widgetPositioning":{"horizontal":"auto","vertical":"auto"},"widgetParent":null,"keepOpen":false,"var_name":"datetimeVar001","colSpan":12}],[{"type":"submit","id":"submit0000000001","name":"submit0000000001","label":"submit_1","colSpan":12}]],"variables":[{"var_uid":"9778460595d1b545088dd69091601043","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar002","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""},{"var_uid":"2934510045d1b5453f21373072798412","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textareaVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""},{"var_uid":"9780823375d1b5455e9c3a2064729484","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"datetimeVar001","var_field_type":"datetime","var_field_size":10,"var_label":"datetime","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        factory(Dynaform::class)->create(
            [
                'PRO_UID' => $process[1]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"1","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6817532755d16225629cb05061521548","name":"1","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"text","variable":"textVar001","var_uid":"4746221155d1622658943d1014840579","dataType":"string","protectedValue":false,"id":"textVar001","name":"textVar001","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"SELECT * FROM USERS WHERE \nUSR_UID=\'$UID\' UNION SELECT * from PROCESS","var_name":"textVar001","colSpan":12}]],"variables":[{"var_uid":"4746221155d1622658943d1014840579","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        factory(Dynaform::class)->create(
            [
                'PRO_UID' => $process[2]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"1","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6817532755d16225629cb05061521548","name":"1","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"text","variable":"textVar001","var_uid":"4746221155d1622658943d1014840579","dataType":"string","protectedValue":false,"id":"textVar001","name":"textVar001","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"SELECT * FROM USERS WHERE \nUSR_UID=\'$UID\' UNION SELECT * from PROCESS","var_name":"textVar001","colSpan":12}]],"variables":[{"var_uid":"4746221155d1622658943d1014840579","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        $dynaformQuery = Dynaform::query()->select();
        $dynaformQuery->process($process[0]['PRO_UID']);
        $result = $dynaformQuery->get()->values()->toArray();

        // Assert there is a dynaform for the specific process
        $this->assertCount(1, $result);

        // Assert that the result has the correct filtered process
        $this->assertEquals($process[0]['PRO_UID'], $result[0]['PRO_UID']);
    }
}