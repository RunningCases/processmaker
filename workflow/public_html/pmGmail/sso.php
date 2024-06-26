<?php

session_start();
require_once __DIR__ . '/../../../bootstrap/autoload.php';
require_once (dirname(__FILE__) . '/../../../gulliver/system/class.bootstrap.php');
require_once (dirname(__FILE__) . '/../../../gulliver/system/class.g.php');

$gmailToken = $_GET['gmailToken'];
$gmail = $_GET['gmail'];
$pmtoken = $_GET['pmtoken'];
$pmws = $_GET['pmws'];
$appUid = $_GET['appUid'];
$delIndex = $_GET['delIndex'];
$action = $_GET['action'];
$proUid = $_GET['proUid'];
$server = isset($_GET['server']) ? $_GET['server'] : '';

//We do need the server to continue.
if (!isset($_GET['server']) || $server == "") {
    throw new \Exception(Bootstrap::LoadTranslation('ID_GMAIL_NEED_SERVER'));
}

//First check if the feature is enabled in the license.
$gCurl = curl_init('https://' . $server . '/api/1.0/' . $pmws . '/gmailIntegration/verifyGmailfeature/');
curl_setopt($gCurl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $pmtoken));
curl_setopt($gCurl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($gCurl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($gCurl, CURLOPT_CONNECTTIMEOUT, 0);
curl_setopt($gCurl, CURLOPT_SSL_VERIFYHOST, false);

if (curl_exec($gCurl) === false) {
    echo 'Curl error: ' . curl_error($gCurl);
    error_log(Bootstrap::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR'));
    die();
} else {
    $gCurl_response = curl_exec($gCurl);
    curl_close($gCurl);
    $gResp = G::json_decode($gCurl_response);
    if ($gResp === false) {
        echo Bootstrap::LoadTranslation('ID_NO_LICENSE_FEATURE_ENABLED');
        die();
    }
}
set_time_limit(60);

$curl = curl_init('https://' . $server . '/api/1.0/' . $pmws . '/gmailIntegration/userexist/' . $gmail);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $pmtoken));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);

$curl_response = curl_exec($curl);
curl_close($curl);
$decodedResp = G::json_decode($curl_response);

if (!is_object($decodedResp) || property_exists($decodedResp, 'error')) {
    die($decodedResp->error->message);
}

//getting the enviroment
$enviroment = $decodedResp->enviroment;

if (count($decodedResp->user) > 1) {
    echo Bootstrap::LoadTranslation('ID_EMAIL_MORE_THAN_ONE_USER');
    die;
} else if (count($decodedResp->user) < 1) {
    echo Bootstrap::LoadTranslation('ID_USER_NOT_FOUND');
    die;
}

//validationg if there is an actual PM session
if (!isset($_SESSION['USER_LOGGED']) || $_SESSION['USER_LOGGED'] != $decodedResp->user['0']->USR_UID) {
    $url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $gmailToken;

    // init curl object
    $ch = curl_init();
    // define options
    $optArray = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    );
    // apply those options
    curl_setopt_array($ch, $optArray);
    // execute request and get response
    $result = curl_exec($ch);
    $response = (G::json_decode($result));
    curl_close($ch);

    //First validate if this user (mail) corresponds to a PM user
    if (isset($response->email) && ($gmail == $response->email)) {
        //If the email corresponds I get the username and with the gmail user_id the session is created.
        if ($decodedResp->user['0']->USR_STATUS == "ACTIVE") {
            //User Active! lets create the Session
            @session_destroy();
            session_start();
            session_regenerate_id();

            $cookieOptions = Bootstrap::buildCookieOptions(['expires' => time() + (24 * 60 * 60), 'path' => '/sys' . $enviroment, 'httponly' => true]);
            setcookie('workspaceSkin', $enviroment, $cookieOptions);

            $_SESSION = array();
            $_SESSION['__EE_INSTALLATION__'] = 2;
            $_SESSION['__EE_SW_PMLICENSEMANAGER__'] = 1;
            $_SESSION['phpLastFileFound'] = '';
            $_SESSION['USERNAME_PREVIOUS1'] = $decodedResp->user['0']->USR_USERNAME;
            $_SESSION['USERNAME_PREVIOUS2'] = $decodedResp->user['0']->USR_USERNAME;
            $_SESSION['WORKSPACE'] = $pmws;
            $_SESSION['USR_FULLNAME'] = $decodedResp->user['0']->USR_FIRSTNAME . ' ' . $decodedResp->user['0']->USR_LASTNAME;
            $_SESSION['__sw__'] = 1;
            initUserSession(
                    $decodedResp->user['0']->USR_UID, $decodedResp->user['0']->USR_USERNAME
            );
            //session created
        } else {
            echo Bootstrap::LoadTranslation('ID_USER_NOT_ACTIVE');
            die;
        }
    } else {
        echo Bootstrap::LoadTranslation('ID_USER_DOES_NOT_CORRESPOND');
        die;
    }
}

$_SESSION['server'] = 'https://' . $server . '/sys' . $pmws . '/en/' . $enviroment . '/';
$_SESSION['PMCase'] = 'cases/cases_Open?APP_UID=' . $appUid . '&DEL_INDEX=' . $delIndex . '&action=' . $action . '&gmail=1';
$_SESSION['PMProcessmap'] = 'designer?prj_uid=' . $proUid . '&prj_readonly=true&app_uid=' . $appUid;
$_SESSION['PMUploadedDocuments'] = 'cases/ajaxListener?action=uploadedDocuments';
$_SESSION['PMGeneratedDocuments'] = 'cases/casesGenerateDocumentPage_Ajax.php?actionAjax=casesGenerateDocumentPage';

header('location:' . 'templateForm.php');

