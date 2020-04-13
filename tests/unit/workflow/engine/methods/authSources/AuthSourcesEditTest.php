<?php

namespace Tests\unit\workflow\engine\methods\authSources;

use Faker\Factory;
use ProcessMaker\Model\RbacAuthenticationSource;
use ProcessMaker\Model\User;
use RBAC;
use Tests\TestCase;

class AuthSourcesEditTest extends TestCase
{

    /**
     * This set initial parameters for each test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->settingUserLogged();
    }

    /**
     * This starts a valid user in session with the appropriate permissions.
     * @global object $RBAC
     */
    private function settingUserLogged()
    {
        global $RBAC;

        $user = User::where('USR_ID', '=', 1)
                ->get()
                ->first();

        $_SESSION['USER_LOGGED'] = $user['USR_UID'];

        $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
        $RBAC->initRBAC();
        $RBAC->loadUserRolePermission('PROCESSMAKER', $_SESSION['USER_LOGGED']);
    }

    /**
     * This test the configuration page of a existent authentication source.
     * @test
     */
    public function it_should_test_edit_configuration_page()
    {
        $string = "tables = 'APPLICATION|APP_SEQUENCE|APP_DELEGATION|APP_DOCUMENT|APP_MESSAGE|APP_OWNER|CONFIGURATION|CONTENT|DEPARTMENT|DYNAFORM|GROUPWF|GROUP_USER|HOLIDAY|INPUT_DOCUMENT|ISO_COUNTRY|ISO_LOCATION|ISO_SUBDIVISION|LANGUAGE|LEXICO|OUTPUT_DOCUMENT|PROCESS|PROCESS_OWNER|REPORT_TABLE|REPORT_VAR|ROUTE|STEP|STEP_TRIGGER|SWIMLANES_ELEMENTS|TASK|TASK_USER|TRANSLATION|TRIGGERS|USERS|APP_THREAD|APP_DELAY|PROCESS_USER|SESSION|DB_SOURCE|STEP_SUPERVISOR|OBJECT_PERMISSION|CASE_TRACKER|CASE_TRACKER_OBJECT|CASE_CONSOLIDATED|STAGE|SUB_PROCESS|SUB_APPLICATION|LOGIN_LOG|USERS_PROPERTIES|ADDITIONAL_TABLES|FIELDS|SHADOW_TABLE|EVENT|GATEWAY|APP_EVENT|APP_CACHE_VIEW|DIM_TIME_DELEGATE|DIM_TIME_COMPLETE|APP_HISTORY|APP_FOLDER|FIELD_CONDITION|LOG_CASES_SCHEDULER|CASE_SCHEDULER|CALENDAR_DEFINITION|CALENDAR_BUSINESS_HOURS|CALENDAR_HOLIDAYS|CALENDAR_ASSIGNMENTS|PROCESS_CATEGORY|APP_NOTES|DASHLET|DASHLET_INSTANCE|APP_SOLR_QUEUE|SEQUENCES|SESSION_STORAGE|PROCESS_FILES|WEB_ENTRY|OAUTH_ACCESS_TOKENS|OAUTH_AUTHORIZATION_CODES|OAUTH_CLIENTS|OAUTH_REFRESH_TOKENS|OAUTH_SCOPES|PMOAUTH_USER_ACCESS_TOKENS|BPMN_PROJECT|BPMN_PROCESS|BPMN_ACTIVITY|BPMN_ARTIFACT|BPMN_DIAGRAM|BPMN_BOUND|BPMN_DATA|BPMN_EVENT|BPMN_FLOW|BPMN_GATEWAY|BPMN_LANESET|BPMN_LANE|BPMN_PARTICIPANT|BPMN_EXTENSION|BPMN_DOCUMENTATION|PROCESS_VARIABLES|APP_TIMEOUT_ACTION_EXECUTED|ADDONS_STORE|ADDONS_MANAGER|LICENSE_MANAGER|APP_ASSIGN_SELF_SERVICE_VALUE|APP_ASSIGN_SELF_SERVICE_VALUE_GROUP|LIST_INBOX|LIST_PARTICIPATED_HISTORY|LIST_PARTICIPATED_LAST|LIST_COMPLETED|LIST_PAUSED|LIST_CANCELED|LIST_MY_INBOX|LIST_UNASSIGNED|LIST_UNASSIGNED_GROUP|MESSAGE_TYPE|MESSAGE_TYPE_VARIABLE|EMAIL_SERVER|WEB_ENTRY_EVENT|MESSAGE_EVENT_DEFINITION|MESSAGE_EVENT_RELATION|MESSAGE_APPLICATION|ELEMENT_TASK_RELATION|ABE_CONFIGURATION|ABE_REQUESTS|ABE_RESPONSES|USR_REPORTING|PRO_REPORTING|DASHBOARD|DASHBOARD_INDICATOR|DASHBOARD_DAS_IND|CATALOG|SCRIPT_TASK|TIMER_EVENT|EMAIL_EVENT|NOTIFICATION_DEVICE|GMAIL_RELABELING|NOTIFICATION_QUEUE|PLUGINS_REGISTRY|APP_DATA_CHANGE_LOG|JOBS_PENDING|JOBS_FAILED|RBAC_PERMISSIONS|RBAC_ROLES|RBAC_ROLES_PERMISSIONS|RBAC_SYSTEMS|RBAC_USERS|RBAC_USERS_ROLES|RBAC_AUTHENTICATION_SOURCE|' ";
        $systemTablesPath = PATH_CONFIG . 'system-tables.ini';
        if (!file_exists($systemTablesPath)) {
            file_put_contents($systemTablesPath, $string);
        } else {
            $systemTables = @parse_ini_file($systemTablesPath);
            if (!isset($systemTables['tables'])) {
                file_put_contents($systemTablesPath, $string);
            }
        }

        $fileName = PATH_METHODS . 'authSources/authSources_Edit.php';
        $_REQUEST['AUTH_SOURCE_PROVIDER'] = 'ldapAdvanced';

        $authenticationSource = factory(RbacAuthenticationSource::class)
                ->create();

        $_GET['sUID'] = $authenticationSource->AUTH_SOURCE_UID;

        ob_start();
        require_once $fileName;
        $content = ob_get_clean();

        //check if the variable is in the html content
        $this->assertTrue(strpos($content, 'var Fields = ') !== false);

        //check one of the required fields in the html content
        $this->assertTrue(strpos($content, 'AUTH_SOURCE_PROVIDER') !== false);

        //verify that this field is no longer present in the html content
        $this->assertTrue(!(strpos($content, 'USR_ID') !== false));
    }
}
