<?php
//
// pm-bootstrap.php
//

/*
 * PmBootstrap for Test Unit Suite
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
$config = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . "config.ini");

$workspace = $config['workspace'];
$lang = $config['lang'];
$processMakerHome = $config['pm_home_dir'];
$rootDir = realpath($processMakerHome) . DIRECTORY_SEPARATOR;

define('SYS_LANG', $lang);
define('PATH_SEP', DIRECTORY_SEPARATOR);

define('PATH_TRUNK',    $rootDir . PATH_SEP);
define('PATH_OUTTRUNK', realpath($rootDir . '/../') . PATH_SEP);
define('PATH_HOME',     $rootDir . PATH_SEP . 'workflow' . PATH_SEP);

define('PATH_HTML', PATH_HOME . 'public_html' . PATH_SEP);
define('PATH_RBAC_HOME', PATH_TRUNK . 'rbac' . PATH_SEP);
define('PATH_GULLIVER_HOME', PATH_TRUNK . 'gulliver' . PATH_SEP);
define('PATH_GULLIVER', PATH_GULLIVER_HOME . 'system' . PATH_SEP); //gulliver system classes
define('PATH_GULLIVER_BIN', PATH_GULLIVER_HOME . 'bin' . PATH_SEP); //gulliver bin classes
define('PATH_TEMPLATE', PATH_GULLIVER_HOME . 'templates' . PATH_SEP);
define('PATH_THIRDPARTY', PATH_GULLIVER_HOME . 'thirdparty' . PATH_SEP);
define('PATH_RBAC', PATH_RBAC_HOME . 'engine' . PATH_SEP . 'classes' . PATH_SEP); //to enable rbac version 2
define('PATH_RBAC_CORE', PATH_RBAC_HOME . 'engine' . PATH_SEP);
define('PATH_CORE', PATH_HOME . 'engine' . PATH_SEP);
define('PATH_SKINS', PATH_CORE . 'skins' . PATH_SEP);
define('PATH_SKIN_ENGINE', PATH_CORE . 'skinEngine' . PATH_SEP);
define('PATH_METHODS', PATH_CORE . 'methods' . PATH_SEP);
define('PATH_XMLFORM', PATH_CORE . 'xmlform' . PATH_SEP);
define('PATH_CONFIG', PATH_CORE . 'config' . PATH_SEP);
define('PATH_PLUGINS', PATH_CORE . 'plugins' . PATH_SEP);
define('PATH_HTMLMAIL', PATH_CORE . 'html_templates' . PATH_SEP);
define('PATH_TPL', PATH_CORE . 'templates' . PATH_SEP);
define('PATH_TEST', PATH_CORE . 'test' . PATH_SEP);
define('PATH_FIXTURES', PATH_TEST . 'fixtures' . PATH_SEP);
define('PATH_RTFDOCS', PATH_CORE . 'rtf_templates' . PATH_SEP);
define('PATH_DYNACONT', PATH_CORE . 'content' . PATH_SEP . 'dynaform' . PATH_SEP);
define('SYS_UPLOAD_PATH', PATH_HOME . "public_html/files/" );
define('PATH_UPLOAD', PATH_HTML . 'files' . PATH_SEP);
define('PATH_WORKFLOW_MYSQL_DATA', PATH_CORE . 'data' . PATH_SEP . 'mysql' . PATH_SEP);
define('PATH_RBAC_MYSQL_DATA', PATH_RBAC_CORE . 'data' . PATH_SEP . 'mysql' . PATH_SEP);
define('FILE_PATHS_INSTALLED', PATH_CORE . 'config' . PATH_SEP . 'paths_installed.php' );
define('PATH_WORKFLOW_MSSQL_DATA', PATH_CORE . 'data' . PATH_SEP . 'mssql' . PATH_SEP);
define('PATH_RBAC_MSSQL_DATA', PATH_RBAC_CORE . 'data' . PATH_SEP . 'mssql' . PATH_SEP);
define('PATH_CONTROLLERS', PATH_CORE . 'controllers' . PATH_SEP);
define('PATH_SERVICES_REST', PATH_CORE . 'services' . PATH_SEP . 'rest' . PATH_SEP);

require_once PATH_GULLIVER . PATH_SEP . 'class.bootstrap.php';

spl_autoload_register(array("Bootstrap", "autoloadClass"));

Bootstrap::registerClass("G", PATH_GULLIVER . "class.g.php");
Bootstrap::registerClass("System", PATH_HOME . "engine/classes/class.system.php");

// define autoloading for others
Bootstrap::registerClass("wsBase", PATH_HOME . "engine/classes/class.wsBase.php");
Bootstrap::registerClass('Xml_Node', PATH_GULLIVER . "class.xmlDocument.php");
Bootstrap::registerClass('XmlForm_Field_TextPM', PATH_HOME . "engine/classes/class.XmlForm_Field_TextPM.php");
Bootstrap::registerClass('XmlForm_Field_SimpleText', PATH_GULLIVER . "class.xmlformExtension.php");
Bootstrap::registerClass('XmlForm_Field', PATH_GULLIVER . "class.xmlform.php");

Bootstrap::LoadThirdParty("smarty/libs", "Smarty.class");

Bootstrap::registerSystemClasses();

Bootstrap::registerDir('src', PATH_HOME . 'engine/src/');
Bootstrap::registerDir('model', PATH_CORE . 'classes' . PATH_SEP . 'model');

$config = System::getSystemConfiguration();

define('DEBUG_SQL_LOG', $config['debug_sql']);
define('DEBUG_TIME_LOG', $config['debug_time']);
define('DEBUG_CALENDAR_LOG', $config['debug_calendar']);
define('MEMCACHED_ENABLED',  $config['memcached']);
define('MEMCACHED_SERVER',   $config['memcached_server']);
define('TIME_ZONE', $config['time_zone']);


// set include path
set_include_path(
    PATH_CORE . PATH_SEPARATOR .
    PATH_THIRDPARTY . PATH_SEPARATOR .
    PATH_THIRDPARTY . 'pear' . PATH_SEPARATOR .
    PATH_RBAC_CORE . PATH_SEPARATOR .
    get_include_path()
);


/*
 * Setting Up Workspace
 */

// include the server installed configuration
require_once FILE_PATHS_INSTALLED;

define('SYS_SYS', $workspace);

// defining system constant when a valid server environment exists
define( 'PATH_LANGUAGECONT', PATH_DATA . "META-INF" . PATH_SEP );
define( 'PATH_CUSTOM_SKINS', PATH_DATA . 'skins' . PATH_SEP );
define( 'PATH_TEMPORAL', PATH_C . 'dynEditor/' );
define( 'PATH_DB', PATH_DATA . 'sites' . PATH_SEP );

$workspaceDir = PATH_DB . $workspace;

// smarty constants
define( 'PATH_SMARTY_C', PATH_C . 'smarty' . PATH_SEP . 'c' );
define( 'PATH_SMARTY_CACHE', PATH_C . 'smarty' . PATH_SEP . 'cache' );


//***************** PM Paths DATA **************************
define('PATH_DATA_SITE',                PATH_DATA      . 'sites/' . SYS_SYS . '/');
define('PATH_DOCUMENT',                 PATH_DATA_SITE . 'files/');
define('PATH_DATA_MAILTEMPLATES',       PATH_DATA_SITE . 'mailTemplates/');
define('PATH_DATA_PUBLIC',              PATH_DATA_SITE . 'public/');
define('PATH_DATA_REPORTS',             PATH_DATA_SITE . 'reports/');
define('PATH_DYNAFORM',                 PATH_DATA_SITE . 'xmlForms/');
define('PATH_IMAGES_ENVIRONMENT_FILES', PATH_DATA_SITE . 'usersFiles' . PATH_SEP);
define('PATH_IMAGES_ENVIRONMENT_USERS', PATH_DATA_SITE . 'usersPhotographies' . PATH_SEP);

if (is_file(PATH_DATA_SITE.PATH_SEP . '.server_info')) {
    $SERVER_INFO = file_get_contents(PATH_DATA_SITE.PATH_SEP.'.server_info');
    $SERVER_INFO = unserialize($SERVER_INFO);

    define('SERVER_NAME', $SERVER_INFO ['SERVER_NAME']);
    define('SERVER_PORT', $SERVER_INFO ['SERVER_PORT']);
} else {
    echo "WARNING! No server info found!";
}

// create memcached singleton
Bootstrap::LoadClass( 'memcached' );
//$memcache = PMmemcached::getSingleton( SYS_SYS );

Propel::init(PATH_CONFIG . "databases.php");

