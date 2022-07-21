<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Validation;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessVariables;
use ProcessMaker\Model\Triggers;
use ProcessMaker\Validation\MySQL57;
use Tests\TestCase;

class MySQL57Test extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test the MySQL 5.7 incompatibilities in dynaforms
     *
     * @test
     */
    public function it_should_test_incompatibilities_with_dynaforms()
    {
        $process = Process::factory(2)->create();

        Dynaform::factory()->create(
            [
                'PRO_UID' => $process[0]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"2","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6170264265d1b544bebdbd5098250194","name":"2","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"title","id":"title0000000001","label":"title_1","colSpan":12}],[{"type":"text","variable":"textVar002","var_uid":"9778460595d1b545088dd69091601043","dataType":"string","protectedValue":false,"id":"textVar002","name":"textVar002","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"","var_name":"textVar002","colSpan":12}],[{"type":"textarea","variable":"textareaVar001","var_uid":"2934510045d1b5453f21373072798412","dataType":"string","protectedValue":false,"id":"textareaVar001","name":"textareaVar001","label":"textarea_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","validate":"","validateMessage":"","mode":"parent","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"","rows":"5","var_name":"textareaVar001","colSpan":12}],[{"type":"datetime","variable":"datetimeVar001","var_uid":"9780823375d1b5455e9c3a2064729484","dataType":"datetime","protectedValue":false,"id":"datetimeVar001","name":"datetimeVar001","label":"datetime_1","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","mode":"parent","format":"YYYY-MM-DD","dayViewHeaderFormat":"MMMM YYYY","extraFormats":false,"stepping":1,"minDate":"","maxDate":"","useCurrent":"false","collapse":true,"locale":"","defaultDate":"","disabledDates":false,"enabledDates":false,"icons":{"time":"glyphicon glyphicon-time","date":"glyphicon glyphicon-calendar","up":"glyphicon glyphicon-chevron-up","down":"glyphicon glyphicon-chevron-down","previous":"glyphicon glyphicon-chevron-left","next":"glyphicon glyphicon-chevron-right","today":"glyphicon glyphicon-screenshot","clear":"glyphicon glyphicon-trash"},"useStrict":false,"sideBySide":false,"daysOfWeekDisabled":false,"calendarWeeks":false,"viewMode":"days","toolbarPlacement":"default","showTodayButton":false,"showClear":"false","widgetPositioning":{"horizontal":"auto","vertical":"auto"},"widgetParent":null,"keepOpen":false,"var_name":"datetimeVar001","colSpan":12}],[{"type":"submit","id":"submit0000000001","name":"submit0000000001","label":"submit_1","colSpan":12}]],"variables":[{"var_uid":"9778460595d1b545088dd69091601043","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar002","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""},{"var_uid":"2934510045d1b5453f21373072798412","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textareaVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""},{"var_uid":"9780823375d1b5455e9c3a2064729484","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"datetimeVar001","var_field_type":"datetime","var_field_size":10,"var_label":"datetime","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        Dynaform::factory()->create(
            [
                'PRO_UID' => $process[1]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"1","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6817532755d16225629cb05061521548","name":"1","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"text","variable":"textVar001","var_uid":"4746221155d1622658943d1014840579","dataType":"string","protectedValue":false,"id":"textVar001","name":"textVar001","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"SELECT * FROM USERS WHERE \nUSR_UID=\'$UID\' UNION SELECT * from PROCESS","var_name":"textVar001","colSpan":12}]],"variables":[{"var_uid":"4746221155d1622658943d1014840579","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        $processes = [
            [
                "PRO_UID" => $process[0]['PRO_UID'],
                "PRO_TITLE" => $process[0]['PRO_TITLE']
            ],
            [
                "PRO_UID" => $process[1]['PRO_UID'],
                "PRO_TITLE" => $process[1]['PRO_TITLE']
            ]
        ];

        $object = new MySQL57();
        $result = $object->checkIncompatibilityDynaforms($processes);

        // This asserts that there is a result
        $this->assertNotEmpty($result);

        // This asserts that there is a process that contains an UNION query inside a dynaform
        $this->assertCount(1, $result);

        // This asserts that the process containing the UNION queries inside a dynaform, is the first one
        $this->assertEquals($result[0]['PRO_UID'], $process[1]['PRO_UID']);
    }

    /**
     * Test the MySQL 5.7 incompatibilities in variables
     *
     * @test
     */
    public function it_should_test_incompatibilities_with_variables()
    {
        $process = Process::factory(2)->create();

        ProcessVariables::factory()->create(
            [
                'PRJ_UID' => $process[0]['PRO_UID'],
                'VAR_SQL' => 'SELECT * FROM USERS WHERE USR_UID="213" UNION SELECT * from PROCESS',
            ]
        );

        $variables = ProcessVariables::factory()->create(
            [
                'PRJ_UID' => $process[1]['PRO_UID'],
                'VAR_SQL' => '',
            ]
        );


        $processes = [
            [
                "PRO_UID" => $process[0]['PRO_UID'],
                "PRO_TITLE" => $process[0]['PRO_TITLE']
            ],
            [
                "PRO_UID" => $process[1]['PRO_UID'],
                "PRO_TITLE" => $process[1]['PRO_TITLE']
            ]
        ];

        $object = new MySQL57();
        $result = $object->checkIncompatibilityVariables($processes);

        // This asserts that there is a result
        $this->assertNotEmpty($result);

        // This asserts that there is a process that contains an UNION query in a variable
        $this->assertCount(1, $result);

        // This asserts that the process containing the UNION query in a variable, is the first one
        $this->assertEquals($result[0]['PRO_UID'], $process[0]['PRO_UID']);

        // This asserts that the result does not contain a variable that does not have a UNION query
        $this->assertNotEquals($result[0]['VAR_UID'], $variables['VAR_UID']);
    }

    /**
     * Test the MySQL 5.7 incompatibilities in triggers
     *
     * @test
     */
    public function it_should_test_incompatibilities_with_triggers()
    {
        $process = Process::factory(3)->create();
        Triggers::factory()->create(
            [
                'PRO_UID' => $process[0]['PRO_UID'],
                'TRI_WEBBOT' => '$text=222;
                                $var1= executeQuery("SELECT * 
                                FROM USERS WHERE 
                                USR_UID=\'$UID\' UNION SELECT * from PROCESS");
                                
                                $var1= executeQuery("SELECT * 
                                FROM USERS WHERE 
                                USR_UID=\'$UID\' UNION SELECT * from PROCESS");
                                
                                $query = "SELECT * FROM USERS UNION 
                                
                                SELECT * FROM TASKS";
                                
                                $QUERY2 = "select * from USERS union SELECT * from GROUPS";
                                
                                $s1 = "select * from USER";
                                $s2 = "select * from TASK";
                                
                                $query3 = $s1. " UNION " . $s2;
                                
                                executeQuery($query3);'
            ]
        );

        Triggers::factory()->create(
            [
                'PRO_UID' => $process[1]['PRO_UID'],
                'TRI_WEBBOT' => 'die();'
            ]
        );

        Triggers::factory()->create(
            [
                'PRO_UID' => $process[2]['PRO_UID'],
                'TRI_WEBBOT' => 'executeQuery("select * from USERS");'
            ]
        );

        $processes = [
            [
                "PRO_UID" => $process[0]['PRO_UID'],
                "PRO_TITLE" => $process[0]['PRO_TITLE']
            ],
            [
                "PRO_UID" => $process[1]['PRO_UID'],
                "PRO_TITLE" => $process[1]['PRO_TITLE']
            ],
            [
                "PRO_UID" => $process[2]['PRO_UID'],
                "PRO_TITLE" => $process[2]['PRO_TITLE']
            ]
        ];

        $object = new MySQL57();
        $result = $object->checkIncompatibilityTriggers($processes);

        // This asserts that there is a result
        $this->assertNotEmpty($result);

        // This asserts that there is a process that contains an UNION query
        $this->assertCount(1, $result);

        // This asserts that the process containing the UNION queries is the first one
        $this->assertEquals($result[0]['PRO_UID'], $process[0]['PRO_UID']);
    }

    /**
     * Test the query analyzer method
     *
     * @test
     */
    public function it_should_test_the_query_analyzer()
    {
        $query = "";

        $object = new MySQL57();
        $result = $object->analyzeQuery($query);

        // This asserts that there is not a UNION query
        $this->assertFalse($result);

        $query = "select * from USERS UNION select '1241412515'";
        $result = $object->analyzeQuery($query);

        // This asserts that there is a UNION query
        $this->assertTrue($result);

        $query = "select * from USERS LEFT JOIN TASKS ON 'USERS.USR_UID = TASKS.USR_UID '";
        $result = $object->analyzeQuery($query);

        // This asserts that there is not a UNION query
        $this->assertFalse($result);
    }
}