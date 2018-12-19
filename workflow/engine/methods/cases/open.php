<?php

$tBarGmail = false;
if (isset($_GET['gmail']) && $_GET['gmail'] == 1) {
    $_SESSION['gmail'] = 1;
    $tBarGmail = true;
}

//Check if we have the information for open the case
if (!isset($_GET['APP_UID']) && !isset($_GET['APP_NUMBER']) && !isset($_GET['DEL_INDEX'])) {
    throw new Exception(G::LoadTranslation('ID_APPLICATION_OR_INDEX_MISSING'));
}
//Get the APP_UID related to APP_NUMBER
if (!isset($_GET['APP_UID']) && isset($_GET['APP_NUMBER'])) {
    $oCase = new Cases();
    $appUid = $oCase->getApplicationUIDByNumber(htmlspecialchars($_GET['APP_NUMBER']));
    if (is_null($appUid)) {
        throw new Exception(G::LoadTranslation('ID_CASE_DOES_NOT_EXISTS'));
    }
} else {
    $appUid = htmlspecialchars($_GET['APP_UID']);
}
//If we don't have the DEL_INDEX we get the current delIndex. Data reporting tool does not have this information
if (!isset($_GET['DEL_INDEX'])) {
    $oCase = new Cases();
    $delIndex = $oCase->getCurrentDelegation($appUid, $_SESSION['USER_LOGGED']);
    if (is_null($delIndex)) {
        throw new Exception(G::LoadTranslation('ID_CASE_IS_CURRENTLY_WITH_ANOTHER_USER'));
    }
    $_GET['DEL_INDEX'] = $delIndex;
} else {
    $delIndex = htmlspecialchars($_GET['DEL_INDEX']);
}

$tasUid = (isset($_GET['TAS_UID'])) ? $tasUid = htmlspecialchars($_GET['TAS_UID']) : '';

$oCase = new Cases();
$conf = new Configurations();

$oHeadPublisher = headPublisher::getSingleton();

$urlToRedirectAfterPause = 'casesListExtJs';

/*----------------------------------********---------------------------------*/
$licensedFeatures = PMLicensedFeatures::getSingleton();
if ($licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
    $pmGoogle = new PmGoogleApi();
    if (array_key_exists('gmail', $_SESSION) && $_SESSION['gmail'] == 1 && $pmGoogle->getServiceGmailStatus()) {
        $_SESSION['gmail'] = 0;
        $urlToRedirectAfterPause = '/sys' . $_SESSION['WORKSPACE'] . '/en/neoclassic/cases/cases_Open?APP_UID=' . $_SESSION['APPLICATION'] . '&DEL_INDEX=' . $_SESSION['INDEX'] . '&action=sent';
    }
}
/*----------------------------------********---------------------------------*/


$oHeadPublisher->assign('urlToRedirectAfterPause', $urlToRedirectAfterPause);


$oHeadPublisher->addExtJsScript('app/main', true);
$oHeadPublisher->addExtJsScript('cases/open', true);
$oHeadPublisher->assign('FORMATS', $conf->getFormats());
$uri = '';
foreach ($_GET as $k => $v) {
    $uri .= ($uri == '') ? "$k=$v" : "&$k=$v";
}

if (isset($_GET['action']) && ($_GET['action'] == 'jump')) {
    $oNewCase = new \ProcessMaker\BusinessModel\Cases();
    //We need to get the last index OPEN or CLOSED (by Paused cases)
    //Set true because we need to check if the case is paused
    $delIndex = $oNewCase->getOneLastThread($appUid, true);
    $case = $oCase->loadCase($appUid, $delIndex, $_GET['action']);
} else {
    $case = $oCase->loadCase($appUid, $delIndex);
}

if (isset($_GET['actionFromList']) && ($_GET['actionFromList'] === 'to_revise')) {
    $oSupervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
    $caseCanBeReview = $oSupervisor->reviewCaseStatusForSupervisor($appUid, $delIndex);
    //Check if the case has the correct status for update the information from supervisor/review
    if (!$caseCanBeReview) {
        //The supervisor can not edit the information
        $script = 'cases_Open?';
    } else {
        //The supervisor can edit the information, the case are in TO_DO
        $script = 'cases_OpenToRevise?APP_UID=' . $appUid . '&DEL_INDEX=' . $delIndex . '&TAS_UID=' . $tasUid;
        $oHeadPublisher->assign('treeToReviseTitle', G::loadtranslation('ID_STEP_LIST'));
        $casesPanelUrl = 'casesToReviseTreeContent?APP_UID=' . $appUid . '&DEL_INDEX=' . $delIndex;
        $oHeadPublisher->assign('casesPanelUrl', $casesPanelUrl); //translations
        echo "<div id='toReviseTree'></div>";
    }
} else {
    $script = 'cases_Open?';
}

$process = new Process();
$fields = $process->load($case['PRO_UID']);
$isBpmn = $fields['PRO_BPMN'] === 1 ? true : false;

$showCustomForm = false;

/*----------------------------------********---------------------------------*/

$respView = $oCase->getAllObjectsFrom($case['PRO_UID'], $appUid, $case['TAS_UID'], $_SESSION['USER_LOGGED'], 'VIEW');
$viewSummaryForm = isset($respView['SUMMARY_FORM']) && $respView['SUMMARY_FORM'] === 1 ? true : false;
$isNoEmpty = isset($fields['PRO_DYNAFORMS']['PROCESS']) && !empty($fields['PRO_DYNAFORMS']['PROCESS']);

if ($isBpmn && $viewSummaryForm && $isNoEmpty) {
    $showCustomForm = true;
}

/*----------------------------------********---------------------------------*/

$oStep = new Step();
$oStep = $oStep->loadByProcessTaskPosition($case['PRO_UID'], $case['TAS_UID'], 1);
$oHeadPublisher->assign('uri', $script . $uri);
$oHeadPublisher->assign('_APP_NUM', '#: ' . $case['APP_NUMBER']);
$oHeadPublisher->assign('_PROJECT_TYPE', $isBpmn ? 'bpmn' : 'classic');
$oHeadPublisher->assign('_PRO_UID', $case['PRO_UID']);
$oHeadPublisher->assign('_APP_UID', $appUid);
$oHeadPublisher->assign('_ENV_CURRENT_DATE', $conf->getSystemDate(date('Y-m-d')));
$oHeadPublisher->assign('_ENV_CURRENT_DATE_NO_FORMAT', date('Y-m-d-h-i-A'));
$oHeadPublisher->assign('idfirstform', is_null($oStep) ? '-1' : $oStep->getStepUidObj());
$oHeadPublisher->assign('appStatus', $case['APP_STATUS']);
$oHeadPublisher->assign('tbarGmail', $tBarGmail);
$oHeadPublisher->assign('showCustomForm', $showCustomForm);

if (!isset($_SESSION['APPLICATION']) || !isset($_SESSION['TASK']) || !isset($_SESSION['INDEX'])) {
    $_SESSION['PROCESS'] = $case['PRO_UID'];
    $_SESSION['APPLICATION'] = $case['APP_UID'];
    $_SESSION['TASK'] = $case['TAS_UID'];
    $_SESSION['INDEX'] = $case['DEL_INDEX'];
}
$_SESSION['actionCaseOptions'] = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';
G::RenderPage('publish', 'extJs');
