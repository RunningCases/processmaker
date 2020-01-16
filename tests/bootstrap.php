<?php

/**
 * Test harness bootstrap that sets up initial defines and builds up the initial database schema
 */
include_once(__DIR__ . '/../bootstrap/autoload.php');

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Setup basic app services
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

/**
 * @todo Migrate to configuration parameters
 */
define('PATH_TRUNK', dirname(__DIR__));
define('PATH_CORE', PATH_TRUNK . '/workflow/engine/');
define('PATH_CONFIG', PATH_CORE . 'config/');
if (!defined("PATH_DATA")) {
    define('PATH_DATA', dirname(__DIR__) . '/shared/');
}
define('PATH_RBAC_CORE', dirname(__DIR__) . '/rbac/engine/');
define('PATH_DB', PATH_DATA . 'sites/');
// Define some values related to the workspace
define('SYS_LANG', 'en');
define('SYS_SKIN', 'neoclassic');
define('SYS_SYS', env('MAIN_SYS_SYS', 'workflow'));
define('PMTABLE_KEY', 'pmtable');
define('DB_ADAPTER', 'mysql');
// Path related some specific directories
define('PATH_SEP', '/');
define('PATH_WORKSPACE', PATH_TRUNK . '/shared/sites/' . SYS_SYS . '/');
define('PATH_METHODS', dirname(__DIR__) . '/workflow/engine/methods/');
define('PATH_WORKFLOW_MYSQL_DATA', PATH_TRUNK . '/workflow/engine/data/mysql/');
define('PATH_RBAC_MYSQL_DATA', PATH_TRUNK . '/rbac/engine/data/mysql/');
define('PATH_LANGUAGECONT', PATH_DATA . '/META-INF/');
define('PATH_RBAC_HOME', PATH_TRUNK . '/rbac/');
define('PATH_RBAC', PATH_RBAC_HOME . 'engine/classes/');
define("PATH_CUSTOM_SKINS", PATH_DATA . "skins/");
define("PATH_TPL", PATH_CORE . "templates/");
if (!defined("PATH_C")) {
    define('PATH_C', PATH_DATA . 'compiled/');
}
define('DB_HOST', env('DB_HOST'));
define('DB_NAME', env('DB_DATABASE'));
define('DB_USER', env('DB_USERNAME'));
define('DB_PASS', env('DB_PASSWORD'));
define('PATH_HOME', PATH_TRUNK . '/workflow/');
define('PATH_HTML', PATH_HOME . 'public_html/');
define('PATH_SMARTY_C', PATH_TRUNK . '/shared/compiled/smarty/c');
define('PATH_SMARTY_CACHE', PATH_TRUNK . '/shared/compiled/smarty/cache');
define('PATH_THIRDPARTY', PATH_TRUNK . '/thirdparty/');
define("URL_KEY", 'c0l0s40pt1mu59r1m3');
define("PATH_XMLFORM", PATH_CORE . "xmlform" . PATH_SEP);

// Set Time Zone
$_SESSION['__SYSTEM_UTC_TIME_ZONE__'] = (int) (env('MAIN_SYSTEM_UTC_TIME_ZONE', 'workflow')) == 1;
ini_set('date.timezone', $_SESSION['__SYSTEM_UTC_TIME_ZONE__'] ? 'UTC' : env('MAIN_TIME_ZONE', 'America/New_York'));
define('TIME_ZONE', ini_get('date.timezone'));

// Overwrite with the ProcessMaker env.ini configuration used in production environments
//@todo: move env.ini configuration to .env
ini_set('date.timezone', TIME_ZONE); //Set Time Zone
date_default_timezone_set(TIME_ZONE);
// Configuration values
config(['app.timezone' => TIME_ZONE]);
config([
    "system.workspace" => SYS_SYS
]);
// Defining constants related to the workspace
define("PATH_DATA_SITE", PATH_DATA . "sites/" . config("system.workspace") . "/");
define("PATH_DYNAFORM", PATH_DATA_SITE . "xmlForms/");
define("PATH_DATA_MAILTEMPLATES", PATH_DATA_SITE . "mailTemplates/");
define("PATH_DATA_PUBLIC", PATH_DATA_SITE . "public/");
define("PATH_CONTROLLERS", PATH_CORE . "controllers" . PATH_SEP);
G::defineConstants();

/**
 * Database configurations
 */
// Setup connection to database SQLServer
config([
    'database.connections.testexternal' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'database' => 'testexternal',
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', 'password'),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null
    ]
]);

/**
 * Configuration for MSSQL
 */
if (env('RUN_MSSQL_TESTS')) {
    config([
        'database.connections.mssql' => [
            'driver' => 'sqlsrv',
            'host' => env('MSSQL_HOST', '127.0.0.1'),
            'database' => env('MSSQL_DATABASE', 'testexternal'),
            'username' => env('MSSQL_USERNAME', 'root'),
            'password' => env('MSSQL_PASSWORD', 'password'),
        ]
    ]);

    Schema::connection('mssql')->dropIfExists('test');
    Schema::connection('mssql')->create('test', function ($table) {
        $table->increments('id');
        $table->string('value');
    });
    DB::connection('mssql')->table('test')->insert([
        'value' => 'testvalue'
    ]);
}

/**
 * This is for standard ProcessMaker tables
 */
if (!env('POPULATE_DATABASE')) {
    // Create a table for define the connection
    Schema::connection('testexternal')->dropIfExists('test');
    Schema::connection('testexternal')->create('test', function ($table) {
        $table->increments('id');
        $table->string('value');
    });
    DB::connection('testexternal')->table('test')->insert([
        'value' => 'testvalue'
    ]);

    // Now, drop all test tables and repopulate with schema
    DB::unprepared('SET FOREIGN_KEY_CHECKS = 0');
    $colname = 'Tables_in_' . env('DB_DATABASE', 'test');
    $tables = DB::select('SHOW TABLES');
    $drop = [];
    foreach ($tables as $table) {
        $drop[] = $table->$colname;
    }
    if (count($drop)) {
        $drop = implode(',', $drop);
        DB::statement("DROP TABLE $drop");
        DB::unprepared('SET FOREIGN_KEY_CHECKS = 1');
    }

    // Repopulate with schema and standard inserts
    DB::unprepared(file_get_contents(PATH_CORE . 'data/mysql/schema.sql'));
    DB::unprepared(file_get_contents(PATH_RBAC_CORE . 'data/mysql/schema.sql'));
    DB::unprepared(file_get_contents(PATH_CORE . 'data/mysql/insert.sql'));
    DB::unprepared(file_get_contents(PATH_RBAC_CORE . 'data/mysql/insert.sql'));

    // Set our APP_SEQUENCE val
    DB::table('APP_SEQUENCE')->insert([
        'ID' => 1
    ]);

    // Setup our initial oauth client for our web designer
    DB::table('OAUTH_CLIENTS')->insert([
        'CLIENT_ID' => 'x-pm-local-client',
        'CLIENT_SECRET' => '179ad45c6ce2cb97cf1029e212046e81',
        'CLIENT_NAME' => 'PM Web Designer',
        'CLIENT_DESCRIPTION' => 'ProcessMaker Web Designer App',
        'CLIENT_WEBSITE' => 'www.processmaker.com',
        'REDIRECT_URI' => config('app.url') . '/sys' . config('system.workspace') . '/en/neoclassic/oauth2/grant',
        'USR_UID' => '00000000000000000000000000000001'
    ]);
    DB::table('OAUTH_ACCESS_TOKENS')->insert([
        'ACCESS_TOKEN' => '39704d17049f5aef45e884e7b769989269502f83',
        'CLIENT_ID' => 'x-pm-local-client',
        'USER_ID' => '00000000000000000000000000000001',
        'EXPIRES' => '2017-06-15 17:55:19',
        'SCOPE' => 'view_processes edit_processes *'
    ]);
}

// We need to manually initialize Propel with our test database
Propel::initConfiguration([
    'datasources' => [
        'workflow' => [
            'connection' => 'mysql://' . config('database.connections.workflow.username') . ':' . config('database.connections.workflow.password') . '@' . config('database.connections.workflow.host') . '/test?encoding=utf8',
            'adapter' => 'mysql'
        ],
        'rbac' => [
            'connection' => 'mysql://' . config('database.connections.workflow.username') . ':' . config('database.connections.workflow.password') . '@' . config('database.connections.workflow.host') . '/test?encoding=utf8',
            'adapter' => 'mysql'
        ],
        'rp' => [
            'connection' => 'mysql://' . config('database.connections.workflow.username') . ':' . config('database.connections.workflow.password') . '@' . config('database.connections.workflow.host') . '/test?encoding=utf8',
            'adapter' => 'mysql'
        ]
    ]
]);
