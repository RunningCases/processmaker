<?php 
$licensedFeatures = PMLicensedFeatures::getSingleton();
if (!$licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
    G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
    G::header('location: ../login/login');
    die;
}
$caseId = $_SESSION['APPLICATION'];
$usrUid = $_SESSION['USER_LOGGED'];
$usrName = $_SESSION['USR_FULLNAME'];
$actualIndex = $_SESSION['INDEX'];
$cont = 0;

use \ProcessMaker\Services\Api;

$appDel = new AppDelegation();

$actualThread = $appDel->Load($caseId, $actualIndex);
$actualLastIndex = $actualThread['DEL_PREVIOUS'];

$oLabels = new labelsGmail();
$oLabels->addRelabelingToQueue($caseId, $actualIndex, $actualLastIndex, false);

$pmGoogle = new PmGoogleApi();
if (array_key_exists('gmail', $_SESSION) && $_SESSION['gmail'] == 1 && $pmGoogle->getServiceGmailStatus()) {
    $_SESSION['gmail'] = 0;
    unset($_SESSION['gmail']); //cleaning session
    $mUrl = '/sys'. $_SESSION['WORKSPACE'] .'/en/'.$_SESSION['currentSkin'].'/cases/cases_Open?APP_UID='.$caseId.'&DEL_INDEX='.$actualIndex.'&action=sent';
} else {
    $mUrl = 'casesListExtJs';
    if (isset($_SESSION["currentSkin"]) && $_SESSION["currentSkin"] === 'uxs') {
        $mUrl = '../home';
    }
}

header('location:' . $mUrl);
