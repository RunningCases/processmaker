<?php

//Check guest user
if (isset($_GET['USR_UID']) && RBAC::isGuestUserUid($_GET['USR_UID'])) {
    throw new Exception(G::LoadTranslation("ID_USER_CAN_NOT_UPDATE", array($_GET['USR_UID'])));
    return;
}

//calculating the max upload file size;
use ProcessMaker\Core\System;

if (isset($_REQUEST['userInterface']) && $_REQUEST['userInterface'] === "v2") {
    $lang = defined("SYS_LANG") ? SYS_LANG : "en";

    $html = file_get_contents(PATH_HTML . "lib/userPersonalInformation/index.html");
    $html = str_replace("var USR_UID='';", "var USR_UID='{$_GET['USR_UID']}';", $html);
    $html = str_replace("translation.en.js", "translation.{$lang}.js", $html);
    $html = str_replace("csrfToken", csrfToken(), $html);
    echo $html;
    exit();
}

$POST_MAX_SIZE = ini_get('post_max_size');
$mul = substr($POST_MAX_SIZE, - 1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$postMaxSize = (int) $POST_MAX_SIZE * $mul;

$UPLOAD_MAX_SIZE = ini_get('upload_max_filesize');
$mul = substr($UPLOAD_MAX_SIZE, - 1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$uploadMaxSize = (int) $UPLOAD_MAX_SIZE * $mul;

//We need to use the following abbreviations: Bytes, KB, MB, GB
$maxFileSize = changeAbbreviationOfDirectives(ini_get('upload_max_filesize'));

if ($postMaxSize < $uploadMaxSize) {
    $uploadMaxSize = $postMaxSize;
}
//Add year by expiration date
$expirationDate = 1;
$envFile = PATH_CONFIG . 'env.ini';
if (file_exists($envFile)) {
    $sysConf = System::getSystemConfiguration($envFile);
    if (isset($sysConf['expiration_year']) && $sysConf['expiration_year'] > 0) {
        $expirationDate = abs($sysConf['expiration_year']);
    }
}

$languageManagement = 0;
/*----------------------------------********---------------------------------*/
$licensedFeatures = PMLicensedFeatures::getSingleton();
if ($licensedFeatures->verifyfeature('w2LL3o4NFNiaDRXcFFCYVpJS3Jsall5dmh0ZWtBTkdKR3ROS0VzWGdoLzNQYz0=')) {
    $languageManagement = 1;
}
/*----------------------------------********---------------------------------*/

$arraySystemConfiguration = System::getSystemConfiguration('', '', config("system.workspace"));

$oHeadPublisher = headPublisher::getSingleton();
$oHeadPublisher->addExtJsScript('users/users', true); //adding a javascript file .js
$oHeadPublisher->assign('USR_UID', $_GET['USR_UID']);
$oHeadPublisher->assign('MODE', $_GET['MODE']);
$oHeadPublisher->assign('MAX_FILES_SIZE', ' (' . $maxFileSize . ') ');
$oHeadPublisher->assign('SYSTEM_TIME_ZONE', $arraySystemConfiguration['time_zone']);
$oHeadPublisher->assign('TIME_ZONE_DATA', array_map(function ($value) {
        return [$value, $value];
    }, DateTimeZone::listIdentifiers()));
$oHeadPublisher->assign('__SYSTEM_UTC_TIME_ZONE__', (isset($_SESSION['__SYSTEM_UTC_TIME_ZONE__']) && $_SESSION['__SYSTEM_UTC_TIME_ZONE__']) ? 1 : 0);
$oHeadPublisher->assign('EXPIRATION_DATE', $expirationDate);
$oHeadPublisher->assign('LANGUAGE_MANAGEMENT', $languageManagement);
$oHeadPublisher->assign('__ACTION__', 'saveUser');

G::RenderPage('publish', 'extJs');
