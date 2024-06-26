<?php

use Illuminate\Support\Facades\Crypt;

/*----------------------------------********---------------------------------*/
//Browser Compatibility
$browserSupported = G::checkBrowserCompatibility();
if ($browserSupported==false) {
    if (!isset($_SESSION['G_MESSAGE']) || $_SESSION['G_MESSAGE'] == "") {
        G::SendTemporalMessage('ID_CURRENT_BROWSER_NOT_SUPPORTED', 'warning');
    }
}
/*----------------------------------********---------------------------------*/
if (isset ($_POST['form']['USER_ENV'])) {
    $data = Crypt::encryptString(serialize($_POST));
    $url = sprintf('/sys%s/%s/%s/login/sysLoginVerify?d=%s', $_POST['form']['USER_ENV'], SYS_LANG, SYS_SKIN, $data);
    G::header("location: $url");
    die();
}

//Save session variables
$arraySession = array();

if (!empty($_SESSION['G_MESSAGE'])) {
    $arraySession["G_MESSAGE"] = $_SESSION["G_MESSAGE"];
}

if (!empty($_SESSION['G_MESSAGE_TYPE'])) {
    $arraySession["G_MESSAGE_TYPE"] = $_SESSION["G_MESSAGE_TYPE"];
}

//Initialize session
@session_destroy();
session_start();
session_regenerate_id();

//Set options for PHP session cookie
$cookieOptions = Bootstrap::buildCookieOptions(['expires' => time() + (24 * 60 * 60), 'httponly' => true]);
setcookie(session_name(), session_id(), $cookieOptions);

//Restore session variables
$_SESSION = array_merge($_SESSION, $arraySession);

//Required classes for dbArray work
Propel::init(PATH_CORE . "config/databases.php");
Creole::registerDriver('dbarray', 'creole.contrib.DBArrayConnection');

function getLangFiles()
{
    $dir = PATH_LANGUAGECONT;
    $filesArray = array();
    if (file_exists($dir)) {
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                $fileParts = explode(".", $file);
                if ($fileParts [0] == "translation") {
                    $filesArray [$fileParts [1]] = $file;
                }
            }
            closedir($handle);
        }
    }
    return $filesArray;
}

function getWorkspacesAvailable()
{
    $oServerConf = ServerConf::getSingleton();
    $dir = PATH_DB;
    $filesArray = array();
    if (file_exists($dir)) {
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (($file != ".") && ($file != "..")) {
                    if (file_exists(PATH_DB . $file . '/db.php')) {
                        if (! $oServerConf->isWSDisabled($file)) {
                            $filesArray [] = $file;
                        }
                    }
                }
            }
            closedir($handle);
        }
    }
    sort($filesArray, SORT_STRING);
    return $filesArray;
}

$availableWorkspace = getWorkspacesAvailable();

//Translations
//$Translations = G::getModel("Translation");  <-- ugly way to get a class
require_once "classes/model/Translation.php";
$Translations = new Translation();
$translationsTable = $Translations->getTranslationEnvironments();

$availableLangArray = array();
$availableLangArray [] = array('LANG_ID' => 'char', 'LANG_NAME' => 'char');

foreach ($translationsTable as $locale) {
    $aFields['LANG_ID'] = $locale['LOCALE'];
    if ($locale['COUNTRY'] != '.') {
        $aFields['LANG_NAME'] = $locale['LANGUAGE'] . ' (' . (ucwords(strtolower($locale['COUNTRY']))) . ')';
    } else {
        $aFields['LANG_NAME'] = $locale['LANGUAGE'];
    }

    $availableLangArray [] = $aFields;
}

$availableWorkspaceArray = array();
$availableWorkspaceArray [] = array('ENV_ID' => 'char', 'ENV_NAME' => 'char');
foreach ($availableWorkspace as $envKey => $envName) {
    $aFields = array('ENV_ID' => $envName, 'ENV_NAME' => $envName);
    $availableWorkspaceArray [] = $aFields;
}

global $_DBArray;

$_DBArray ['langOptions'] = $availableLangArray;
$_DBArray ['availableWorkspace'] = $availableWorkspaceArray;

$_SESSION ['_DBArray'] = $_DBArray;

$aField ['LOGIN_VERIFY_MSG'] = G::loadTranslation('LOGIN_VERIFY_MSG');
$aField['USER_LANG'] = SYS_LANG;

$G_PUBLISH = new Publisher();
if (!defined('WS_IN_LOGIN')) {
    define('WS_IN_LOGIN', 'serverconf');
}
$fileLogin = 'login/sysLogin';
$version = explode('.', trim(file_get_contents(PATH_GULLIVER . 'VERSION')));
$version = isset($version[0]) ? intval($version[0]) : 0;
switch (WS_IN_LOGIN) {
    case 'serverconf':
        //Get Server Configuration
        $oServerConf = ServerConf::getSingleton();
        if ($oServerConf->getProperty('LOGIN_NO_WS')) {
            $fileLogin = $version >= 3 ? 'login/sysLoginNoWSpm3' : 'login/sysLoginNoWS';
        } else {
            $fileLogin = 'login/sysLogin';
        }
        break;
    case 'no':
        $fileLogin = $version >= 3 ? 'login/sysLoginNoWSpm3' : 'login/sysLoginNoWS';
        break;
    case 'yes':
        $fileLogin = 'login/sysLogin';
        break;
    default:
        $fileLogin = 'login/sysLogin';
        break;
}
$cookieOptions = Bootstrap::buildCookieOptions(['expires' => time() + (24 * 60 * 60), 'path' => SYS_CURRENT_URI]);
setcookie('PM-Warning', trim(G::LoadTranslation('ID_BLOCKER_MSG'), '*'), $cookieOptions);

$cookieOptions = Bootstrap::buildCookieOptions(['expires' => time() + (24 * 60 * 60)]);
setcookie('PM-TabPrimary', uniqid(), $cookieOptions);

$oHeadPublisher = headPublisher::getSingleton();
$oHeadPublisher->addScriptFile('/jscore/src/PM.js');
$oHeadPublisher->addScriptFile('/jscore/src/Sessions.js');
$oHeadPublisher->addScriptFile('/jscore/src/Register.js');

$G_PUBLISH->AddContent('xmlform', 'xmlform', $fileLogin, '', $aField, 'sysLogin');
G::RenderPage("publish");
