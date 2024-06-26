<?php

/**
 * cases_ShowDocument.php
 *
 * Download documents related to the input document
 *
 * @link https://wiki.processmaker.com/3.2/Cases/Documents#Downloading_Files
 * @link https://wiki.processmaker.com/3.3/Cases/Information#Uploaded_Documents
 */
if (isset($_REQUEST['actionAjax']) && $_REQUEST['actionAjax'] == "verifySession") {
    if (!isset($_SESSION['USER_LOGGED'])) {
        if ((isset($_POST['request'])) && ($_POST['request'] == true)) {
            $response = new stdclass();
            $response->message = G::LoadTranslation('ID_LOGIN_AGAIN');
            $response->lostSession = true;
            print G::json_encode($response);
            die();
        } else {
            G::SendMessageText(G::LoadTranslation('ID_LOGIN_TO_SEE_OUTPUTDOCS'), "WARNING");
            G::header("location: " . "/");
            die();
        }
    } else {
        $response = new stdclass();
        print G::json_encode($response);
        die();
    }
}
require_once("classes/model/AppDocumentPeer.php");
$oAppDocument = new AppDocument();

if (empty($_GET['a'])) {
    G::header('Location: /errors/error403.php');
    die();
}

if (empty($_GET['v'])) {
    //Load last version of the document
    $docVersion = $oAppDocument->getLastAppDocVersion($_GET['a']);
} else {
    $docVersion = $_GET['v'];
}

//Check if the user can be download the input Document
//Send the parameter v = Version
//Send the parameter a = Case UID
$isGuestUser = false;
if (!empty($_SESSION['GUEST_USER']) && $_SESSION['GUEST_USER'] === RBAC::GUEST_USER_UID) {
    $isGuestUser = true;
}
$access = $RBAC->userCanAccess('PM_FOLDERS_ALL') != 1 && defined('DISABLE_DOWNLOAD_DOCUMENTS_SESSION_VALIDATION') && DISABLE_DOWNLOAD_DOCUMENTS_SESSION_VALIDATION == 0;
if ($access && $isGuestUser === false) {
    if ((isset($_SESSION['USER_LOGGED']) && !$oAppDocument->canDownloadInput($_SESSION['USER_LOGGED'], $_GET['a'], $docVersion)) || !isset($_SESSION['USER_LOGGED'])) {
        G::header('Location: /errors/error403.php?url=' . urlencode($_SERVER['REQUEST_URI']));
        die();
    }
}

$oAppDocument->Fields = $oAppDocument->load($_GET['a'], $docVersion);

$sAppDocUid = $oAppDocument->getAppDocUid();
$iDocVersion = $oAppDocument->getDocVersion();
$info = pathinfo($oAppDocument->getAppDocFilename());
$ext = (isset($info['extension']) ? $info['extension'] : '');//BUG fix: must handle files without any extension

if (isset($_GET['b'])) {
    if ($_GET['b'] == '0') {
        $bDownload = false;
    } else {
        $bDownload = true;
    }
} else {
    $bDownload = true;
}

$app_uid = G::getPathFromUID($oAppDocument->Fields['APP_UID']);
$file = G::getPathFromFileUID($oAppDocument->Fields['APP_UID'], $sAppDocUid);

$realPath = PATH_DOCUMENT . $app_uid . '/' . $file[0] . $file[1] . '_' . $iDocVersion . '.' . $ext;
$realPath1 = PATH_DOCUMENT . $app_uid . '/' . $file[0] . $file[1] . '.' . $ext;
$sw_file_exists = false;
if (file_exists($realPath)) {
    $sw_file_exists = true;
} elseif (file_exists($realPath1)) {
    $sw_file_exists = true;
    $realPath = $realPath1;
}

if (!$sw_file_exists) {
    $error_message = G::LoadTranslation('ID_ERROR_STREAMING_FILE');
    if ((isset($_POST['request'])) && ($_POST['request'] == true)) {
        $res['success'] = 'failure';
        $res['message'] = $error_message;
        print G::json_encode($res);
    } else {
        G::SendMessageText($error_message, "ERROR");
        $backUrlObj = explode("sys" . config("system.workspace"), $_SERVER['HTTP_REFERER']);
        G::header("location: " . "/sys" . config("system.workspace") . $backUrlObj[1]);
        die();
    }
} else {
    if ((isset($_POST['request'])) && ($_POST['request'] == true)) {
        $res['success'] = 'success';
        $res['message'] = $oAppDocument->Fields['APP_DOC_FILENAME'];
        print G::json_encode($res);
    } else {
        $nameFile = $oAppDocument->Fields['APP_DOC_FILENAME'];
        $licensedFeatures = PMLicensedFeatures::getSingleton();
        $downloadStatus = false;
        /*----------------------------------********---------------------------------*/
        if ($licensedFeatures->verifyfeature('AhKNjBEVXZlWUFpWE8wVTREQ0FObmo0aTdhVzhvalFic1M=')) {
            $drive = new AppDocumentDrive();
            if ($drive->getStatusDrive()) {
                $fieldDrive = $oAppDocument->getAppDocDriveDownload();
                $drive->loadUser($_SESSION['USER_LOGGED']);
                $uidDrive = $drive->changeUrlDrive($oAppDocument->Fields, $oAppDocument->getAppDocType());
                $fileContent = $drive->download($uidDrive);
                if ($fileContent !== null) {
                    $downloadStatus = true;
                    header('Content-Description: File Transfer');
                    header('Content-Disposition: attachment; filename=' . $nameFile);
                    header('Content-Transfer-Encoding: binary');
                    header('Set-Cookie: fileLoading=true');
                    echo $fileContent;
                    exit();
                }
            }
        }
        /*----------------------------------********---------------------------------*/
        if (!$downloadStatus) {
            G::streamFile($realPath, $bDownload, $nameFile); //download
        }
    }
}
