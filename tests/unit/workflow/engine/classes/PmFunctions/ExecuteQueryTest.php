<?php

namespace Tests\unit\workflow\engine\classes\PmFunctions;

use Faker\Factory;
use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\DbSource;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\User;
use SQLException;
use Tests\TestCase;

/**
 * Test the executeQuery() function
 *
 * @link https://wiki.processmaker.com/3.1/ProcessMaker_Functions#executeQuery.28.29
 */
class ExecuteQueryTest extends TestCase
{
    protected $nameSystemTables = "system-tables.ini";
    protected $contentSystemTables = "tables =  'APPLICATION|APP_SEQUENCE|APP_DELEGATION|APP_DOCUMENT|APP_MESSAGE|APP_OWNER|CONFIGURATION|CONTENT|DEPARTMENT|DYNAFORM|GROUPWF|GROUP_USER|HOLIDAY|INPUT_DOCUMENT|ISO_COUNTRY|ISO_LOCATION|ISO_SUBDIVISION|LANGUAGE|LEXICO|OUTPUT_DOCUMENT|PROCESS|PROCESS_OWNER|REPORT_TABLE|REPORT_VAR|ROUTE|STEP|STEP_TRIGGER|SWIMLANES_ELEMENTS|TASK|TASK_USER|TRANSLATION|TRIGGERS|USERS|APP_THREAD|APP_DELAY|PROCESS_USER|SESSION|DB_SOURCE|STEP_SUPERVISOR|OBJECT_PERMISSION|CASE_TRACKER|CASE_TRACKER_OBJECT|CASE_CONSOLIDATED|STAGE|SUB_PROCESS|SUB_APPLICATION|LOGIN_LOG|USERS_PROPERTIES|ADDITIONAL_TABLES|FIELDS|SHADOW_TABLE|EVENT|GATEWAY|APP_EVENT|APP_CACHE_VIEW|DIM_TIME_DELEGATE|DIM_TIME_COMPLETE|APP_HISTORY|APP_FOLDER|FIELD_CONDITION|LOG_CASES_SCHEDULER|CASE_SCHEDULER|CALENDAR_DEFINITION|CALENDAR_BUSINESS_HOURS|CALENDAR_HOLIDAYS|CALENDAR_ASSIGNMENTS|PROCESS_CATEGORY|APP_NOTES|DASHLET|DASHLET_INSTANCE|APP_SOLR_QUEUE|SEQUENCES|SESSION_STORAGE|PROCESS_FILES|WEB_ENTRY|OAUTH_ACCESS_TOKENS|OAUTH_AUTHORIZATION_CODES|OAUTH_CLIENTS|OAUTH_REFRESH_TOKENS|OAUTH_SCOPES|PMOAUTH_USER_ACCESS_TOKENS|BPMN_PROJECT|BPMN_PROCESS|BPMN_ACTIVITY|BPMN_ARTIFACT|BPMN_DIAGRAM|BPMN_BOUND|BPMN_DATA|BPMN_EVENT|BPMN_FLOW|BPMN_GATEWAY|BPMN_LANESET|BPMN_LANE|BPMN_PARTICIPANT|BPMN_EXTENSION|BPMN_DOCUMENTATION|PROCESS_VARIABLES|APP_TIMEOUT_ACTION_EXECUTED|ADDONS_STORE|ADDONS_MANAGER|LICENSE_MANAGER|APP_ASSIGN_SELF_SERVICE_VALUE|APP_ASSIGN_SELF_SERVICE_VALUE_GROUP|LIST_INBOX|LIST_PARTICIPATED_HISTORY|LIST_PARTICIPATED_LAST|LIST_COMPLETED|LIST_PAUSED|LIST_CANCELED|LIST_MY_INBOX|LIST_UNASSIGNED|LIST_UNASSIGNED_GROUP|MESSAGE_TYPE|MESSAGE_TYPE_VARIABLE|EMAIL_SERVER|WEB_ENTRY_EVENT|MESSAGE_EVENT_DEFINITION|MESSAGE_EVENT_RELATION|MESSAGE_APPLICATION|ELEMENT_TASK_RELATION|ABE_CONFIGURATION|ABE_REQUESTS|ABE_RESPONSES|USR_REPORTING|PRO_REPORTING|DASHBOARD|DASHBOARD_INDICATOR|DASHBOARD_DAS_IND|CATALOG|SCRIPT_TASK|TIMER_EVENT|EMAIL_EVENT|NOTIFICATION_DEVICE|GMAIL_RELABELING|NOTIFICATION_QUEUE|PLUGINS_REGISTRY|APP_DATA_CHANGE_LOG|JOBS_PENDING|JOBS_FAILED|RBAC_PERMISSIONS|RBAC_ROLES|RBAC_ROLES_PERMISSIONS|RBAC_SYSTEMS|RBAC_USERS|RBAC_USERS_ROLES|RBAC_AUTHENTICATION_SOURCE|'";
    protected $oldContentSystemTables = "";

    public function setUp()
    {
        parent::setUp();
        ProcessCategory::truncate();
        $this->oldContentSystemTables = "";
        $path = PATH_CONFIG . $this->nameSystemTables;
        if (file_exists($path)) {
            $this->oldContentSystemTables = file_get_contents($path);
        }
        file_put_contents($path, $this->contentSystemTables);
    }

    public function tearDown()
    {
        parent::tearDown();
        $path = PATH_CONFIG . $this->nameSystemTables;
        file_put_contents($path, $this->oldContentSystemTables);
    }

    /**
     * This tests if the "executeQuery" method is returning the data of a query.
     * @test
     */
    public function it_must_return_the_result_of_execute_query_method()
    {
        $user = factory(User::class, 5)->create();

        $user = $user->sortByDesc('USR_UID')->values()->map(function($item) {
            $result = [
                'USR_UID' => $item['USR_UID'],
                'USR_USERNAME' => $item['USR_USERNAME'],
                'USR_PASSWORD' => $item['USR_PASSWORD'],
                'USR_FIRSTNAME' => $item['USR_FIRSTNAME'],
                'USR_LASTNAME' => $item['USR_LASTNAME'],
                'USR_EMAIL' => $item['USR_EMAIL'],
            ];
            return $result;
        });
        $expected = $user->toArray();

        foreach ($expected as $value) {
            $sql = "SELECT "
                . "USR_UID ,"
                . "USR_USERNAME ,"
                . "USR_PASSWORD ,"
                . "USR_FIRSTNAME, "
                . "USR_LASTNAME, "
                . "USR_EMAIL "
                . "FROM USERS "
                . "WHERE "
                . "USR_UID = '" . $value['USR_UID'] . "'"
                . "ORDER BY USR_UID DESC";
            $actual = executeQuery($sql);

            $actual = array_values($actual);

            $this->assertEquals($value, head($actual));
        }


    }

    /**
     * Insert a record in the category table using the execute query function.
     * @test
     */
    public function it_should_insert_a_record_in_the_category_table_using_the_execute_query_method()
    {
        $database = env('DB_DATABASE');
        $faker = Factory::create();
        $uid = G::generateUniqueID();
        $id = $faker->unique()->numberBetween(1, 10000000);
        $name = str_replace("'", " ", $faker->name);
        $sql = ""
                . "INSERT INTO {$database}.PROCESS_CATEGORY("
                . "    CATEGORY_UID,"
                . "    CATEGORY_ID,"
                . "    CATEGORY_PARENT,"
                . "    CATEGORY_NAME,"
                . "    CATEGORY_ICON"
                . ") VALUES"
                . "("
                . "	'{$uid}',"
                . "	'{$id}',"
                . "	'0',"
                . "	'{$name}',"
                . "	''"
                . ")";
        executeQuery($sql);
        $expected = [
            [
                'CATEGORY_UID' => $uid,
                'CATEGORY_ID' => $id,
                'CATEGORY_PARENT' => '0',
                'CATEGORY_NAME' => $name,
                'CATEGORY_ICON' => '',
            ]
        ];

        $actual = ProcessCategory::get();

        $this->assertEquals($expected, $actual->toArray());
    }

    /**
     * Replace a record in the category table using the execute query function.
     * @test
     */
    public function it_should_replace_a_record_in_the_category_table_using_the_execute_query_method()
    {
        $database = env('DB_DATABASE');
        $faker = Factory::create();
        $id = $faker->unique()->numberBetween(1, 10000000);
        $newName = str_replace("'", " ", $faker->name);

        $category = factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => $id
        ]);
        $expected = $category->toArray();
        $expected['CATEGORY_NAME'] = $newName;
        unset($expected['id']);

        $sql = "REPLACE INTO {$database}.PROCESS_CATEGORY "
                . "SET "
                . "CATEGORY_UID='{$category->CATEGORY_UID}',"
                . "CATEGORY_PARENT='{$category->CATEGORY_PARENT}', "
                . "CATEGORY_NAME='{$newName}', "
                . "CATEGORY_ICON='{$category->CATEGORY_ICON}', "
                . "CATEGORY_ID='{$category->CATEGORY_ID}'"
                . "";

        executeQuery($sql);

        $actual = ProcessCategory::where('CATEGORY_UID', '=', $category->CATEGORY_UID)
                ->get()
                ->first();

        $this->assertEquals($expected, $actual->toArray());
    }

    /**
     * Update a record in the category table using the execute query function.
     * @test
     */
    public function it_should_update_a_record_in_the_category_table_using_the_execute_query_method()
    {
        $database = env('DB_DATABASE');
        $faker = Factory::create();
        $id = $faker->unique()->numberBetween(1, 10000000);
        $newName = str_replace("'", " ", $faker->name);

        $category = factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => $id
        ]);
        $expected = $category->toArray();
        $expected['CATEGORY_NAME'] = $newName;
        unset($expected['id']);

        $sql = ""
                . "UPDATE {$database}.PROCESS_CATEGORY SET "
                . "CATEGORY_NAME='{$newName}' "
                . "WHERE "
                . "CATEGORY_UID='{$category->CATEGORY_UID}'";
        executeQuery($sql);

        $actual = ProcessCategory::where('CATEGORY_UID', '=', $category->CATEGORY_UID)
                ->get()
                ->first();

        $this->assertEquals($expected, $actual->toArray());
    }

    /**
     * Delete a record in the category table using the execute query function.
     * @test
     */
    public function it_should_delete_a_record_in_the_category_table_using_the_execute_query_method()
    {

        $database = env('DB_DATABASE');
        $category = factory(ProcessCategory::class)->create();

        $sql = ""
                . "DELETE FROM {$database}.PROCESS_CATEGORY "
                . "WHERE "
                . "CATEGORY_UID='{$category->CATEGORY_UID}'";
        executeQuery($sql);

        $actual = ProcessCategory::where('CATEGORY_UID', '=', $category->CATEGORY_UID)
                ->get()
                ->first();

        $this->assertNull($actual);
    }

    /**
     * This performs a test of connectivity to an external database using DBS_UID 
     * in the executeQuery() method.
     * @test
     */
    public function this_connects_to_an_external_database_using_the_execute_query_method()
    {
        $dbName = env('DB_DATABASE');
        $dbSource = factory(DbSource::class)->create([
            'DBS_TYPE' => 'mysql',
            'DBS_SERVER' => env('DB_HOST'),
            'DBS_DATABASE_NAME' => $dbName,
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), $dbName, false, false) . "_2NnV3ujj3w",
            'DBS_PORT' => '3306',
        ]);

        //this is important to get the connection
        $_SESSION['PROCESS'] = $dbSource->PRO_UID;

        $sql = "show tables";
        $result = executeQuery($sql, $dbSource->DBS_UID);

        $this->assertTrue(is_array($result));
    }

    /**
     * This performs a test of connectivity to an external database using DBS_UID 
     * in the executeQuery() method.
     * @test
     */
    public function this_connects_to_an_external_oracle_database_using_the_execute_query_method()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $dbName = "XE";
        $dbSource = factory(DbSource::class)->create([
            'DBS_TYPE' => 'oracle',
            'DBS_CONNECTION_TYPE' => 'NORMAL',
            'DBS_SERVER' => 'localhost',
            'DBS_DATABASE_NAME' => $dbName,
            'DBS_USERNAME' => env('DB_USERNAME'),
            'DBS_PASSWORD' => G::encrypt(env('DB_PASSWORD'), $dbName, false, false) . "_2NnV3ujj3w",
            'DBS_PORT' => '1521',
        ]);

        //this is important to get the connection
        $_SESSION['PROCESS'] = $dbSource->PRO_UID;

        $sql = "select username,account_status from dba_users";
        $result = executeQuery($sql, $dbSource->DBS_UID);

        $this->assertTrue(is_array($result));
    }

    /**
     * This verifies the protection of the system tables.
     * @test     
     */
    public function this_check_the_black_list()
    {
        $faker = Factory::create();
        $uid = G::generateUniqueID();
        $id = $faker->unique()->numberBetween(1, 10000000);
        $name = str_replace("'", " ", $faker->name);
        $sql = ""
                . "INSERT INTO PROCESS_CATEGORY("
                . "    CATEGORY_UID,"
                . "    CATEGORY_ID,"
                . "    CATEGORY_PARENT,"
                . "    CATEGORY_NAME,"
                . "    CATEGORY_ICON"
                . ") VALUES"
                . "("
                . "	'{$uid}',"
                . "	'{$id}',"
                . "	'0',"
                . "	'{$name}',"
                . "	''"
                . ")";

        $this->expectException(SQLException::class);

        /**
         * The executeQuery() function is executing the standard error_log() 
         * output, this test shows error information, but will not stop the 
         * execution of the test.
         * The error_log() method must stop being used.
         */
        executeQuery($sql);
    }

    /**
     * This verifies the protection of the system tables.
     * @test     
     */
    public function this_check_the_black_list_for_multiple_tables()
    {
        $faker = Factory::create();
        $id = $faker->unique()->numberBetween(1, 10000000);
        $newName = str_replace("'", " ", $faker->name);

        $category = factory(ProcessCategory::class)->create([
            'CATEGORY_ID' => $id
        ]);
        $expected = $category->toArray();
        $expected['CATEGORY_NAME'] = $newName;
        unset($expected['id']);

        $sql = ""
                . "UPDATE PROCESS_CATEGORY SET "
                . "CATEGORY_NAME='{$newName}' "
                . "WHERE "
                . "CATEGORY_UID='{$category->CATEGORY_UID}'";

        $this->expectException(SQLException::class);

        /**
         * The executeQuery() function is executing the standard error_log() 
         * output, this test shows error information, but will not stop the 
         * execution of the test.
         * The error_log() method must stop being used.
         */
        executeQuery($sql);
    }
}
