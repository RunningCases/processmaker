<?php
use Gulliver\core\ServiceContainer;

$sc = ServiceContainer::getInstance();
$session = $sc->make('session.store');

$licensedFeatures = & PMLicensedFeatures::getSingleton();
if (!$licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
    G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
    G::header( 'location: ../login/login' );
    die;
}
$caseId = $session->get('APPLICATION');
$usrUid = $session->get('USER_LOGGED');
$usrName = $session->get('USR_FULLNAME');
$actualIndex = $session->get('INDEX');
$cont = 0;

use \ProcessMaker\Services\Api;
$appDel = new AppDelegation();

$actualThread = $appDel->Load($caseId, $actualIndex);
$actualLastIndex = $actualThread['DEL_PREVIOUS'];

$appDelPrev = $appDel->LoadParallel($caseId);
if($appDelPrev == array()){
    $appDelPrev['0'] = $actualThread;
}

$Pmgmail = new \ProcessMaker\BusinessModel\Pmgmail();
foreach ($appDelPrev as $app){
    if( ($app['DEL_INDEX'] != $actualIndex) && ($app['DEL_PREVIOUS'] != $actualLastIndex) ){ //Sending the email to all threads of the case except the actual thread
        $response = $Pmgmail->sendEmail($caseId, "", $app['DEL_INDEX']);
    }
}

$oLabels = new labelsGmail();
$oResponse = $oLabels->setLabels($caseId, $actualIndex, $actualLastIndex, false);
if( $session->get('gmail') === 1 ){
	//$session->set('gmail', 0);
	$mUrl = '/sys'. $session->get('WORKSPACE') .'/en/neoclassic/cases/cases_Open?APP_UID='.$caseId.'&DEL_INDEX='.$actualIndex.'&action=sent';
} else{
	$mUrl = 'casesListExtJs';
}

header( 'location:' . $mUrl );

