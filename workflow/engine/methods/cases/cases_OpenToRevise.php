<?php

use ProcessMaker\BusinessModel\Cases as BusinessModelCases;

if ($RBAC->userCanAccess( 'PM_SUPERVISOR' ) != 1) {
    switch ($RBAC->userCanAccess( 'PM_SUPERVISOR' )) {
        case - 2:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_SYSTEM', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
        default:
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die();
            break;
    }
}

/* GET , POST & $_SESSION Vars */
if (isset( $_SESSION['APPLICATION'] )) {
    unset( $_SESSION['APPLICATION'] );
}
if (isset( $_SESSION['PROCESS'] )) {
    unset( $_SESSION['PROCESS'] );
}
if (isset( $_SESSION['INDEX'] )) {
    unset( $_SESSION['INDEX'] );
}
if (isset( $_SESSION['STEP_POSITION'] )) {
    unset( $_SESSION['STEP_POSITION'] );
}

/* Process the info */
$oCase = new Cases();
$sAppUid = $_GET['APP_UID'];
$iDelIndex = $_GET['DEL_INDEX'];
$tasUid = (isset($_GET['TAS_UID'])) ? $_GET['TAS_UID'] : '';

$_SESSION['APPLICATION'] = $_GET['APP_UID'];
$_SESSION['INDEX'] = $_GET['DEL_INDEX'];

$aFields = $oCase->loadCase( $sAppUid, $iDelIndex );

$_SESSION['PROCESS'] = $aFields['PRO_UID'];

$_SESSION['TASK'] = $aFields['TAS_UID'];
$_SESSION['STEP_POSITION'] = 0;
$_SESSION['CURRENT_TASK'] = $aFields['TAS_UID'];

$flag = true;

$cases = new BusinessModelCases();
$urls = $cases->getAllUrlStepsToRevise($_SESSION['APPLICATION'] , $_SESSION['INDEX']);

if (!empty($url)) {
    $url = $urls[0]['url'];
} else {
    $aMessage = array ();
    $aMessage["MESSAGE"] = G::LoadTranslation("ID_NO_ASSOCIATED_INPUT_DOCUMENT_DYN");
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent("xmlform", "xmlform", "login/showMessage", "", $aMessage);
    G::RenderPage("publishBlank", "blank"); 
}

$processUser = new ProcessUser();
$userAccess = $processUser->validateUserAccess($_SESSION['PROCESS'], $_SESSION['USER_LOGGED']);
if (!$userAccess) {
    $flag = false;
}

if ($flag) {
    G::header("Location: " . $url);
} else {
    $aMessage = array ();
    $aMessage["MESSAGE"] = G::LoadTranslation("ID_SUPERVISOR_DOES_NOT_HAVE_DYNAFORMS");
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent("xmlform", "xmlform", "login/showMessage", "", $aMessage);
    G::RenderPage("publishBlank", "blank");
}
