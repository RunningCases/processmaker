<?php
require_once(dirname(__FILE__) . '/../../../gulliver/init.php');
use Gulliver\core\ServiceContainer;
$sc = ServiceContainer::getInstance();
$session = $sc->make('session.store');
$request = $sc->make('request');

$gmailToken = $request->query->get('gmailToken');
$gmail = $request->query->get('gmail');
$pmtoken = $request->query->get('pmtoken');
$pmws = $request->query->get('pmws');
$appUid = $request->query->get('appUid');
$delIndex = $request->query->get('delIndex');
$action = $request->query->get('action');
$proUid = $request->query->has('proUid') ? $request->query->get('proUid') : '';
$server = $request->query->get('server');

//First check if the feature is enabled in the license.
$gCurl = curl_init( 'https://' . $server . '/api/1.0/' . $pmws . '/gmailIntegration/verifyGmailfeature/' );
curl_setopt( $gCurl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $pmtoken ) );
curl_setopt( $gCurl, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $gCurl, CURLOPT_SSL_VERIFYPEER,false);
curl_setopt( $gCurl, CURLOPT_CONNECTTIMEOUT ,0);

$gCurl_response = curl_exec( $gCurl );
curl_close($gCurl);
$gResp = json_decode($gCurl_response);

if($gResp == false){
	echo Bootstrap::LoadTranslation( 'ID_NO_LICENSE_FEATURE_ENABLED' );
	die;
}

set_time_limit(60);

$curl = curl_init( 'https://' . $server . '/api/1.0/' . $pmws . '/gmailIntegration/userexist/' . $gmail );
curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $pmtoken ) );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER,false);
curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT ,0);

$curl_response = curl_exec( $curl );
curl_close($curl);
$decodedResp = json_decode($curl_response);

if(count($decodedResp) > 1){
	echo Bootstrap::LoadTranslation( 'ID_EMAIL_MORE_THAN_ONE_USER' );
	die;
}

//validationg if there is an actual PM session
if( !$session->has('USER_LOGGED') || $session->get('USER_LOGGED') != $decodedResp['0']->USR_UID){
	$url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$gmailToken;

	// init curl object
	$ch = curl_init();
	// define options
	$optArray = array(
	    CURLOPT_URL => $url,
	    CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false
	);
	// apply those options
	curl_setopt_array($ch, $optArray);
	// execute request and get response
	$result = curl_exec($ch);
	$response = (json_decode($result));
	curl_close($ch);

	//First validate if this user (mail) corresponds to a PM user
	if(isset($response->email) && ($gmail == $response->email)){
	    //If the email corresponds I get the username and with the gmail user_id the session is created.
	    if($decodedResp['0']->USR_STATUS == "ACTIVE"){
		    //User Active! lets create the Session
	    	$request = $sc->make('request');
	    	$session = $sc->make('session.store');

	    	$session->setId($request->cookies->get($session->getName()));
	    	$session->start();
	    	setcookie($session->getName(), $session->getId(), 0, '/');
	    	$request->setSession($session);

			if (PHP_VERSION < 5.2) {
		        setcookie("workspaceSkin", "neoclasic", time() + (24 * 60 * 60), "/sys" . "neoclasic", "; HttpOnly");
		    } else {
		        setcookie("workspaceSkin", "neoclasic", time() + (24 * 60 * 60), "/sys" . "neoclasic", null, false, true);
		    }

			$session->set('__EE_INSTALLATION__', 2);
			$session->set('__EE_SW_PMLICENSEMANAGER__', 1);
			$session->set('phpLastFileFound', '');
		    $session->set('USERNAME_PREVIOUS1', 'admin');
			$session->set('USERNAME_PREVIOUS2', 'admin');
			$session->set('WORKSPACE', $pmws);
			$session->set('USER_LOGGED', $decodedResp['0']->USR_UID);
			$session->set('USR_USERNAME', $decodedResp['0']->USR_USERNAME);
			$session->set('USR_FULLNAME', $decodedResp['0']->USR_FIRSTNAME. ' ' .$decodedResp['0']->USR_LASTNAME);
			$session->set('__sw__', 1);
			$session->save();
		    //session created
		} else {
			echo Bootstrap::LoadTranslation( 'ID_USER_NOT_ACTIVE' );
		    die;
		}
	} else {
		echo Bootstrap::LoadTranslation( 'ID_USER_DOES_NOT_CORRESPOND' );
	    die;
	}
}

if ($action == "draft"){
	//sending the email
	$curlApp = curl_init( 'https://' . $server . '/api/1.0/' . $pmws . '/gmailIntegration/sendEmail/' . $appUid . '/to/' . $gmail . '/index/' . $delIndex );
	curl_setopt( $curlApp, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $pmtoken ) );
	curl_setopt( $curlApp, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt( $curlApp, CURLOPT_RETURNTRANSFER, true);
	curl_setopt( $curlApp, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt( $curlApp, CURLOPT_CONNECTTIMEOUT ,0);

	$curl_response_app = curl_exec( $curlApp );
	curl_close( $curlApp );

	$mainUrl = '/sys'. $pmws .'/en/neoclassic/cases/open?APP_UID='.$appUid.'&DEL_INDEX='.$delIndex.'&action='.$action.'&gmail=1';
	header( 'location:' . $mainUrl );
	die;
}
$session->set('server', 'https://' . $server . '/sys'. $pmws .'/en/neoclassic/');

$session->set('PMCase', 'cases/cases_Open?APP_UID='.$appUid.'&DEL_INDEX='.$delIndex.'&action='.$action.'&gmail=1');

$session->set('PMProcessmap', 'designer?prj_uid=' . $proUid . '&prj_readonly=true&app_uid=' . $appUid);

$session->set('PMCasesHistory', 'cases/ajaxListener?action=caseHistory');

$session->set('PMHistoryDynaform', 'cases/casesHistoryDynaformPage_Ajax?actionAjax=historyDynaformPage');

$session->set('PMUploadedDocuments', 'cases/ajaxListener?action=uploadedDocuments');

$session->set('PMGeneratedDocuments', 'cases/casesGenerateDocumentPage_Ajax.php?actionAjax=casesGenerateDocumentPage');
ob_end_flush();
$session->save();
header( 'location:' . 'templateForm.php' );

