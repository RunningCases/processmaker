<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Importer;

use Carbon\Carbon;
use ProcessMaker\Importer\Importer;
use ProcessMaker\Model\BpmnProject;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use ReflectionClass;
use Tests\TestCase;

class ImporterTest extends TestCase
{
    /**
     * Declared to avoid the incompatibility exception
     */
    public function setUp()
    {
        parent::setUp();
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        config(["system.workspace" => "test"]);
        $workspace = config("system.workspace");

        if (!file_exists(PATH_DB . $workspace)) {
            mkdir(PATH_DB . $workspace);
        }

        if (!file_exists(PATH_DB . $workspace . PATH_SEP . "db.php")) {
            $myfile = fopen(PATH_DB . $workspace . PATH_SEP . "db.php", "w");
            fwrite($myfile, "<?php
define ('DB_ADAPTER',     'mysql' );
define ('DB_HOST',        '" . env('DB_HOST') . "' );
define ('DB_NAME',        '" . env('DB_DATABASE') . "' );
define ('DB_USER',        '" . env('DB_USERNAME') . "' );
define ('DB_PASS',        '" . env('DB_PASSWORD') . "' );
define ('DB_RBAC_HOST',   '" . env('DB_HOST') . "' );
define ('DB_RBAC_NAME',   '" . env('DB_DATABASE') . "' );
define ('DB_RBAC_USER',   '" . env('DB_USERNAME') . "' );
define ('DB_RBAC_PASS',   '" . env('DB_PASSWORD') . "' );
define ('DB_REPORT_HOST', '" . env('DB_HOST') . "' );
define ('DB_REPORT_NAME', '" . env('DB_DATABASE') . "' );
define ('DB_REPORT_USER', '" . env('DB_USERNAME') . "' );
define ('DB_REPORT_PASS', '" . env('DB_PASSWORD') . "' );");
        }
    }

    /**
     * Sets protected property on an object using reflection
     *
     * @param object $object
     * @param string $property
     * @param array $value
     *
     * @return void
     */
    private function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }

    /**
     * Tests the import method when importing a process with a new uid
     *
     * @test
     */
    public function it_should_test_the_import_method_when_importing_a_process_with_a_new_uid()
    {
        // Create the existing process
        $process = factory(Process::class)->create(
            ['PRO_CREATE_DATE' => '2019-07-10 10:00:00']
        );

        // Mock the abstract class
        $importer = $this
            ->getMockBuilder('ProcessMaker\Importer\Importer')
            ->getMockForAbstractClass();

        // create the array that will be passed to the load method
        $array = [];

        $array["files"]["workflow"] = [];

        $array['tables'] = [
            'bpmn' =>
                [
                    'activity' =>
                        [],
                    'artifact' =>
                        [],
                    'bound' =>
                        [],
                    'data' =>
                        [],
                    'diagram' =>
                        [
                            0 =>
                                [
                                    'dia_uid' => '9367735405d247f616ed440021337333',
                                    'prj_uid' => $process['PRO_UID'],
                                    'dia_name' => 'Custom',
                                    'dia_is_closable' => '0',

                                ],
                        ],
                    'documentation' =>
                        [],
                    'event' =>
                        [],
                    'extension' =>
                        [],
                    'flow' =>
                        [],
                    'gateway' =>
                        [],
                    'lane' =>
                        [],
                    'laneset' =>
                        [],
                    'participant' =>
                        [],
                    'process' =>
                        [
                            0 =>
                                [
                                    'pro_uid' => '3209565785d247f618b6235074913840',
                                    'prj_uid' => $process['PRO_UID'],
                                    'dia_uid' => '9367735405d247f616ed440021337333',
                                    'pro_name' => 'Custom',
                                    'pro_type' => 'NONE',
                                    'pro_is_executable' => '0',
                                    'pro_is_closed' => '0',
                                    'pro_is_subprocess' => '0',
                                ],
                        ],
                    'project' =>
                        [
                            0 =>
                                [
                                    'prj_uid' => $process['PRO_UID'],
                                    'prj_name' => 'Custom',
                                    'prj_description' => '',
                                    'prj_target_namespace' => '',
                                    'prj_expresion_language' => '',
                                    'prj_type_language' => '',
                                    'prj_exporter' => '',
                                    'prj_exporter_version' => '',
                                    'prj_create_date' => '2019-07-09 11:49:53',
                                    'prj_update_date' => '',
                                    'prj_author' => '00000000000000000000000000000001',
                                    'prj_author_version' => '',
                                    'prj_original_source' => '',
                                ],
                        ],
                ],
            'workflow' =>
                [
                    'stepSupervisor' =>
                        [],
                    'processUser' =>
                        [],
                    'groupwfs' =>
                        [],
                    'objectPermissions' =>
                        [],
                    'dbconnections' =>
                        [],
                    'filesManager' =>
                        [],
                    'reportTablesDefinition' =>
                        [],
                    'reportTablesFields' =>
                        [],
                    'steptriggers' =>
                        [],
                    'triggers' =>
                        [],
                    'steps' =>
                        [],
                    'outputs' =>
                        [],
                    'inputs' =>
                        [],
                    'dynaforms' =>
                        [],
                    'processVariables' =>
                        [],
                    'tasks' =>
                        [],
                    'taskusers' =>
                        [],
                    'routes' =>
                        [],
                    'lanes' =>
                        [],
                    'gateways' =>
                        [],
                    'subProcess' =>
                        [],
                    'caseTracker' =>
                        [],
                    'caseTrackerObject' =>
                        [],
                    'stage' =>
                        [],
                    'fieldCondition' =>
                        [],
                    'event' =>
                        [],
                    'caseScheduler' =>
                        [],
                    'processCategory' =>
                        [],
                    'taskExtraProperties' =>
                        [],
                    'webEntry' =>
                        [],
                    'webEntryEvent' =>
                        [],
                    'messageType' =>
                        [],
                    'messageTypeVariable' =>
                        [],
                    'messageEventDefinition' =>
                        [],
                    'scriptTask' =>
                        [],
                    'timerEvent' =>
                        [],
                    'emailEvent' =>
                        [],
                    'abeConfiguration' =>
                        [],
                    'process' =>
                        [
                            0 =>
                                [
                                    'PRO_UID' => $process['PRO_UID'],
                                    'PRO_TITLE' => 'Custom',
                                    'PRO_DESCRIPTION' => '',
                                    'PRO_PARENT' => $process['PRO_UID'],
                                    'PRO_TIME' => '1',
                                    'PRO_TIMEUNIT' => 'DAYS',
                                    'PRO_STATUS' => 'ACTIVE',
                                    'PRO_TYPE_DAY' => '',
                                    'PRO_TYPE' => 'NORMAL',
                                    'PRO_ASSIGNMENT' => 'FALSE',
                                    'PRO_SHOW_MAP' => '0',
                                    'PRO_SHOW_MESSAGE' => '0',
                                    'PRO_SUBPROCESS' => '0',
                                    'PRO_TRI_CREATE' => '',
                                    'PRO_TRI_OPEN' => '',
                                    'PRO_TRI_DELETED' => '',
                                    'PRO_TRI_CANCELED' => '',
                                    'PRO_TRI_PAUSED' => '',
                                    'PRO_TRI_REASSIGNED' => '',
                                    'PRO_TRI_UNPAUSED' => '',
                                    'PRO_TYPE_PROCESS' => 'PUBLIC',
                                    'PRO_SHOW_DELEGATE' => '0',
                                    'PRO_SHOW_DYNAFORM' => '0',
                                    'PRO_CATEGORY' => '',
                                    'PRO_SUB_CATEGORY' => '',
                                    'PRO_INDUSTRY' => '0',
                                    'PRO_UPDATE_DATE' => '',
                                    'PRO_CREATE_DATE' => '2019-07-09 11:49:53',
                                    'PRO_CREATE_USER' => '00000000000000000000000000000001',
                                    'PRO_HEIGHT' => '5000',
                                    'PRO_WIDTH' => '10000',
                                    'PRO_TITLE_X' => '0',
                                    'PRO_TITLE_Y' => '0',
                                    'PRO_DEBUG' => '0',
                                    'PRO_DYNAFORMS' => '',
                                    'PRO_DERIVATION_SCREEN_TPL' => '',
                                    'PRO_COST' => '0',
                                    'PRO_UNIT_COST' => '',
                                    'PRO_ITEE' => '1',
                                    'PRO_ACTION_DONE' => '',
                                    'PRO_CATEGORY_LABEL' => 'No Category',
                                    'PRO_BPMN' => '1',
                                ],
                        ],
                    'reportTables' =>
                        [],
                    'reportTablesVars' =>
                        [],
                ],
            'plugins' =>
                [],
        ];

        // Mock the load method
        $importer->method("load")
            ->willReturn($array);
        $importer->setData("usr_uid", factory(User::class)->create()->USR_UID);

        // Call the import method
        $res = $importer->import(Importer::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW,
            Importer::GROUP_IMPORT_OPTION_CREATE_NEW, true);

        // Query the new process created
        $query = Process::query();
        $query->select()->where('PRO_UID', $res);
        $result = $query->get()->values()->toArray();

        // Assert the created date is the same as the updated date
        $createDate = Carbon::createFromTimeString($result[0]['PRO_CREATE_DATE'])->format("Y-m-d");
        $updateDate = Carbon::createFromTimeString($result[0]['PRO_UPDATE_DATE'])->format("Y-m-d");
        $this->assertEquals($createDate, $updateDate);
    }

    /**
     * Tests the import method when importing a process with a new uid
     *
     * @test
     */
    public function it_should_test_the_import_method_when_importing_a_process_without_change_the_uid()
    {
        // Create the existing process
        $process = factory(Process::class)->create(
            ['PRO_CREATE_DATE' => '2019-07-10 10:00:00']
        );
        factory(BpmnProject::class)->create(
            ['PRJ_UID' => $process['PRO_UID']]
        );

        // Mock the abstract class
        $importer = $this
            ->getMockBuilder('ProcessMaker\Importer\Importer')
            ->getMockForAbstractClass();

        // create the array that will be passed to the load method
        $array = [];

        $array["files"]["workflow"] = [];

        $array['tables'] = [
            'bpmn' =>
                [
                    'activity' =>
                        [],
                    'artifact' =>
                        [],
                    'bound' =>
                        [],
                    'data' =>
                        [],
                    'diagram' =>
                        [
                            0 =>
                                [
                                    'dia_uid' => '9367735405d247f616ed440021337333',
                                    'prj_uid' => $process['PRO_UID'],
                                    'dia_name' => 'Custom',
                                    'dia_is_closable' => '0',

                                ],
                        ],
                    'documentation' =>
                        [],
                    'event' =>
                        [],
                    'extension' =>
                        [],
                    'flow' =>
                        [],
                    'gateway' =>
                        [],
                    'lane' =>
                        [],
                    'laneset' =>
                        [],
                    'participant' =>
                        [],
                    'process' =>
                        [
                            0 =>
                                [
                                    'pro_uid' => '3209565785d247f618b6235074913840',
                                    'prj_uid' => $process['PRO_UID'],
                                    'dia_uid' => '9367735405d247f616ed440021337333',
                                    'pro_name' => 'Custom',
                                    'pro_type' => 'NONE',
                                    'pro_is_executable' => '0',
                                    'pro_is_closed' => '0',
                                    'pro_is_subprocess' => '0',
                                ],
                        ],
                    'project' =>
                        [
                            0 =>
                                [
                                    'prj_uid' => $process['PRO_UID'],
                                    'prj_name' => 'Custom',
                                    'prj_description' => '',
                                    'prj_target_namespace' => '',
                                    'prj_expresion_language' => '',
                                    'prj_type_language' => '',
                                    'prj_exporter' => '',
                                    'prj_exporter_version' => '',
                                    'prj_create_date' => '2019-07-09 11:49:53',
                                    'prj_update_date' => '',
                                    'prj_author' => '00000000000000000000000000000001',
                                    'prj_author_version' => '',
                                    'prj_original_source' => '',
                                ],
                        ],
                ],
            'workflow' =>
                [
                    'stepSupervisor' =>
                        [],
                    'processUser' =>
                        [],
                    'groupwfs' =>
                        [],
                    'objectPermissions' =>
                        [],
                    'dbconnections' =>
                        [],
                    'filesManager' =>
                        [],
                    'reportTablesDefinition' =>
                        [],
                    'reportTablesFields' =>
                        [],
                    'steptriggers' =>
                        [],
                    'triggers' =>
                        [],
                    'steps' =>
                        [],
                    'outputs' =>
                        [],
                    'inputs' =>
                        [],
                    'dynaforms' =>
                        [],
                    'processVariables' =>
                        [],
                    'tasks' =>
                        [],
                    'taskusers' =>
                        [],
                    'routes' =>
                        [],
                    'lanes' =>
                        [],
                    'gateways' =>
                        [],
                    'subProcess' =>
                        [],
                    'caseTracker' =>
                        [],
                    'caseTrackerObject' =>
                        [],
                    'stage' =>
                        [],
                    'fieldCondition' =>
                        [],
                    'event' =>
                        [],
                    'caseScheduler' =>
                        [],
                    'processCategory' =>
                        [],
                    'taskExtraProperties' =>
                        [],
                    'webEntry' =>
                        [],
                    'webEntryEvent' =>
                        [],
                    'messageType' =>
                        [],
                    'messageTypeVariable' =>
                        [],
                    'messageEventDefinition' =>
                        [],
                    'scriptTask' =>
                        [],
                    'timerEvent' =>
                        [],
                    'emailEvent' =>
                        [],
                    'abeConfiguration' =>
                        [],
                    'process' =>
                        [
                            0 =>
                                [
                                    'PRO_UID' => $process['PRO_UID'],
                                    'PRO_TITLE' => 'Custom',
                                    'PRO_DESCRIPTION' => '',
                                    'PRO_PARENT' => $process['PRO_UID'],
                                    'PRO_TIME' => '1',
                                    'PRO_TIMEUNIT' => 'DAYS',
                                    'PRO_STATUS' => 'ACTIVE',
                                    'PRO_TYPE_DAY' => '',
                                    'PRO_TYPE' => 'NORMAL',
                                    'PRO_ASSIGNMENT' => 'FALSE',
                                    'PRO_SHOW_MAP' => '0',
                                    'PRO_SHOW_MESSAGE' => '0',
                                    'PRO_SUBPROCESS' => '0',
                                    'PRO_TRI_CREATE' => '',
                                    'PRO_TRI_OPEN' => '',
                                    'PRO_TRI_DELETED' => '',
                                    'PRO_TRI_CANCELED' => '',
                                    'PRO_TRI_PAUSED' => '',
                                    'PRO_TRI_REASSIGNED' => '',
                                    'PRO_TRI_UNPAUSED' => '',
                                    'PRO_TYPE_PROCESS' => 'PUBLIC',
                                    'PRO_SHOW_DELEGATE' => '0',
                                    'PRO_SHOW_DYNAFORM' => '0',
                                    'PRO_CATEGORY' => '',
                                    'PRO_SUB_CATEGORY' => '',
                                    'PRO_INDUSTRY' => '0',
                                    'PRO_UPDATE_DATE' => '',
                                    'PRO_CREATE_DATE' => '2019-07-09 11:49:53',
                                    'PRO_CREATE_USER' => '00000000000000000000000000000001',
                                    'PRO_HEIGHT' => '5000',
                                    'PRO_WIDTH' => '10000',
                                    'PRO_TITLE_X' => '0',
                                    'PRO_TITLE_Y' => '0',
                                    'PRO_DEBUG' => '0',
                                    'PRO_DYNAFORMS' => '',
                                    'PRO_DERIVATION_SCREEN_TPL' => '',
                                    'PRO_COST' => '0',
                                    'PRO_UNIT_COST' => '',
                                    'PRO_ITEE' => '1',
                                    'PRO_ACTION_DONE' => '',
                                    'PRO_CATEGORY_LABEL' => 'No Category',
                                    'PRO_BPMN' => '1',
                                ],
                        ],
                    'reportTables' =>
                        [],
                    'reportTablesVars' =>
                        [],
                ],
            'plugins' =>
                [],
        ];

        // Mock the load method
        $importer->method("load")
            ->willReturn($array);
        $importer->setData("usr_uid", factory(User::class)->create()->USR_UID);

        // Call the setProtectedProperty method
        $this->setProtectedProperty($importer, 'metadata', ['uid' => $process['PRO_UID']]);

        // Call the import method
        $res = $importer->import(Importer::IMPORT_OPTION_OVERWRITE,
            Importer::GROUP_IMPORT_OPTION_CREATE_NEW, false);

        // Query the new process created
        $query = Process::query();
        $query->select()->where('PRO_UID', $res);
        $result = $query->get()->values()->toArray();

        // Assert the created date is the same as the updated date
        $this->assertNotEquals($result[0]['PRO_CREATE_DATE'], $result[0]['PRO_UPDATE_DATE']);
    }
}