<?php

namespace Tests\unit\workflow\engine\bin\tasks;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessVariables;
use ProcessMaker\Model\Triggers;
use Tests\TestCase;

class CliWorkspacesTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp()
    {
        $this->markTestIncomplete();//@todo: Please correct this unit test
    }

    /**
     * Test that the deprecated files are removed successfully
     *
     * @covers WorkspaceTools::removeDeprecatedFiles
     * @test
     */
    public function it_should_delete_the_deprecated_files()
    {
        include(PATH_TRUNK . 'workflow/engine/bin/tasks/cliWorkspaces.php');
        if (!file_exists(PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php')) {
            $filename = PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php';
            $handle = fopen($filename, 'w');
            fclose($handle);
        }

        // This assert the data_usersList.php file do exists before being deleted
        $this->assertTrue(file_exists(PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php'));

        $path = PATH_TRUNK . 'workflow/engine/methods/users/';

        if (getmyuid() == fileowner($path)) {
            if (substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php'),
                    1, 2) == 'rw' &&
                substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/users/'), 2, 1) == 'w' &&
                substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/'), 3, 1) == 'x' &&
                substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/'), 3, 1) == 'x' &&
                substr($this->getPermissions(PATH_TRUNK . 'workflow/'), 3, 1) == 'x'
            ) {
                remove_deprecated_files();
            } else {
                dd("Could not delete the file. Please, make sure the file have write permission for the direct parent directory and 
                execute permission for all parent directories.");
            }
        } else {
            if (getmygid() == filegroup($path)) {
                if (substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php'),
                        4, 2) == 'rw' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/users/'), 5,
                        1) == 'w' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/'), 6, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/'), 6, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/'), 6, 1) == 'x'
                ) {
                    remove_deprecated_files();
                } else {
                    dd("Could not delete the file. Please, make sure the file have write permission for the direct parent directory and 
                execute permission for all parent directories.");
                }

            } else {
                if (substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php'),
                        7, 2) == 'rw' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/users/'), 8,
                        1) == 'w' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/methods/'), 9, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/engine/'), 9, 1) == 'x' &&
                    substr($this->getPermissions(PATH_TRUNK . 'workflow/'), 9, 1) == 'x'
                ) {
                    remove_deprecated_files();
                } else {
                    dd("Could not delete the file. Please, make sure the file have write permission for the direct parent directory and 
                execute permission for all parent directories.");
                }
            }
        }

        // This assert the data_usersList.php does not exist anymore
        $this->assertFalse(file_exists(PATH_TRUNK . 'workflow/engine/methods/users/data_usersList.php'));
    }

    /**
     * Get the permissions of a file or directory
     *
     * @param string $path
     * @return string
     */
    public function getPermissions($path)
    {
        $per = fileperms($path);
        switch ($per & 0xF000) {
            case 0xC000: // socket
                $permissions = 's';
                break;
            case 0xA000: // symbolic link
                $permissions = 'l';
                break;
            case 0x8000: // regular
                $permissions = '-';
                break;
            case 0x6000: // block special
                $permissions = 'b';
                break;
            case 0x4000: // directory
                $permissions = 'd';
                break;
            case 0x2000: // character special
                $permissions = 'c';
                break;
            case 0x1000: // FIFO pipe
                $permissions = 'p';
                break;
            default: // unknown
                $permissions = 'u';
        }

        // Owner
        $permissions .= (($per & 0x0100) ? 'r' : '-');
        $permissions .= (($per & 0x0080) ? 'w' : '-');
        $permissions .= (($per & 0x0040) ?
            (($per & 0x0800) ? 's' : 'x') :
            (($per & 0x0800) ? 'S' : '-'));

        // Group
        $permissions .= (($per & 0x0020) ? 'r' : '-');
        $permissions .= (($per & 0x0010) ? 'w' : '-');
        $permissions .= (($per & 0x0008) ?
            (($per & 0x0400) ? 's' : 'x') :
            (($per & 0x0400) ? 'S' : '-'));

        // Others
        $permissions .= (($per & 0x0004) ? 'r' : '-');
        $permissions .= (($per & 0x0002) ? 'w' : '-');
        $permissions .= (($per & 0x0001) ?
            (($per & 0x0200) ? 't' : 'x') :
            (($per & 0x0200) ? 'T' : '-'));

        return $permissions;
    }

    /**
     * Test the queries incompatibilities in dynaforms
     * @test
     */
    public function it_should_test_the_incompatibilities_in_the_dynaforms_queries()
    {
        config(["system.workspace" => 'workflow']);

        $process = factory(Process::class, 2)->create();

        factory(Dynaform::class)->create(
            [
                'PRO_UID' => $process[0]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"2","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6170264265d1b544bebdbd5098250194","name":"2","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"title","id":"title0000000001","label":"title_1","colSpan":12}],[{"type":"text","variable":"textVar002","var_uid":"9778460595d1b545088dd69091601043","dataType":"string","protectedValue":false,"id":"textVar002","name":"textVar002","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"","var_name":"textVar002","colSpan":12}],[{"type":"textarea","variable":"textareaVar001","var_uid":"2934510045d1b5453f21373072798412","dataType":"string","protectedValue":false,"id":"textareaVar001","name":"textareaVar001","label":"textarea_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","validate":"","validateMessage":"","mode":"parent","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"","rows":"5","var_name":"textareaVar001","colSpan":12}],[{"type":"datetime","variable":"datetimeVar001","var_uid":"9780823375d1b5455e9c3a2064729484","dataType":"datetime","protectedValue":false,"id":"datetimeVar001","name":"datetimeVar001","label":"datetime_1","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","mode":"parent","format":"YYYY-MM-DD","dayViewHeaderFormat":"MMMM YYYY","extraFormats":false,"stepping":1,"minDate":"","maxDate":"","useCurrent":"false","collapse":true,"locale":"","defaultDate":"","disabledDates":false,"enabledDates":false,"icons":{"time":"glyphicon glyphicon-time","date":"glyphicon glyphicon-calendar","up":"glyphicon glyphicon-chevron-up","down":"glyphicon glyphicon-chevron-down","previous":"glyphicon glyphicon-chevron-left","next":"glyphicon glyphicon-chevron-right","today":"glyphicon glyphicon-screenshot","clear":"glyphicon glyphicon-trash"},"useStrict":false,"sideBySide":false,"daysOfWeekDisabled":false,"calendarWeeks":false,"viewMode":"days","toolbarPlacement":"default","showTodayButton":false,"showClear":"false","widgetPositioning":{"horizontal":"auto","vertical":"auto"},"widgetParent":null,"keepOpen":false,"var_name":"datetimeVar001","colSpan":12}],[{"type":"submit","id":"submit0000000001","name":"submit0000000001","label":"submit_1","colSpan":12}]],"variables":[{"var_uid":"9778460595d1b545088dd69091601043","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar002","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""},{"var_uid":"2934510045d1b5453f21373072798412","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textareaVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""},{"var_uid":"9780823375d1b5455e9c3a2064729484","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"datetimeVar001","var_field_type":"datetime","var_field_size":10,"var_label":"datetime","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        $dynaform = factory(Dynaform::class)->create(
            [
                'PRO_UID' => $process[1]['PRO_UID'],
                'DYN_CONTENT' => '{"name":"1","description":"","items":[{"type":"form","variable":"","var_uid":"","dataType":"","id":"6817532755d16225629cb05061521548","name":"1","description":"","mode":"edit","script":"","language":"en","externalLibs":"","printable":false,"items":[[{"type":"text","variable":"textVar001","var_uid":"4746221155d1622658943d1014840579","dataType":"string","protectedValue":false,"id":"textVar001","name":"textVar001","label":"text_1","defaultValue":"","placeholder":"","hint":"","required":false,"requiredFieldErrorMessage":"","textTransform":"none","validate":"","validateMessage":"","maxLength":1000,"formula":"","mode":"parent","operation":"","dbConnection":"workflow","dbConnectionLabel":"PM Database","sql":"SELECT * FROM USERS WHERE \nUSR_UID=\'$UID\' UNION SELECT * from PROCESS","var_name":"textVar001","colSpan":12}]],"variables":[{"var_uid":"4746221155d1622658943d1014840579","prj_uid":"5139642915ccb3fca429a36061714972","var_name":"textVar001","var_field_type":"string","var_field_size":10,"var_label":"string","var_dbconnection":"workflow","var_dbconnection_label":"PM Database","var_sql":"","var_null":0,"var_default":"","var_accepted_values":"[]","inp_doc_uid":""}]}]}'
            ]
        );

        check_queries_incompatibilities('workflow');

        $result = ob_get_contents();

        // This assert that the message contains the second process name
        $this->assertRegExp('/'.$process[1]['PRO_TITLE'].'/',$result);

        // This assert that the message contains the second dynaform with the UNION query
        $this->assertRegExp('/'.$dynaform['DYN_TITLE'].'/',$result);
    }

    /**
     * Test the queries incompatibilities in variables
     * @test
     */
    public function it_should_test_the_incompatibilities_in_the_variables_queries()
    {
        config(["system.workspace" => 'workflow']);

        $process = factory(Process::class, 2)->create();

        $variables = factory(ProcessVariables::class)->create(
            [
                'PRJ_UID' => $process[0]['PRO_UID'],
                'VAR_SQL' => 'SELECT * FROM USERS WHERE USR_UID="213" UNION SELECT * from PROCESS'
            ]
        );

        factory(ProcessVariables::class)->create(
            [
                'PRJ_UID' => $process[1]['PRO_UID'],
                'VAR_SQL' => ''
            ]
        );

        check_queries_incompatibilities('workflow');

        $result = ob_get_contents();

        // This assert that the message contains the first process name
        $this->assertRegExp('/'.$process[0]['PRO_TITLE'].'/',$result);

        // This assert that the message contains the first dynaform with the UNION query
        $this->assertRegExp('/'.$variables['VAR_TITLE'].'/',$result);
    }

    /**
     * Test the queries incompatibilities in triggers
     * @test
     */
    public function it_should_test_the_incompatibilities_in_the_triggers_queries()
    {
        config(["system.workspace" => 'workflow']);

        $process = factory(Process::class, 3)->create();
        $trigger = factory(Triggers::class)->create(
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

        factory(Triggers::class)->create(
            [
                'PRO_UID' => $process[1]['PRO_UID'],
                'TRI_WEBBOT' => 'die();'
            ]
        );

        factory(Triggers::class)->create(
            [
                'PRO_UID' => $process[2]['PRO_UID'],
                'TRI_WEBBOT' => 'executeQuery("select * from USERS");'
            ]
        );

        check_queries_incompatibilities('workflow');
        $result = ob_get_contents();

        // This assert that the message contains the first process name
        $this->assertRegExp('/'.$process[0]['PRO_TITLE'].'/',$result);

        // This assert that the message contains the first trigger with the UNION query
        $this->assertRegExp('/'.$trigger['TRI_TITLE'].'/',$result);
    }
}