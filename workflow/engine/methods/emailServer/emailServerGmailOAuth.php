<?php

use ProcessMaker\Core\System;
use ProcessMaker\GmailOAuth\GmailOAuth;

$header = "location:" . System::getServerMainPath() . "/setup/main?s=EMAIL_SERVER";

$validInput = empty($_GET['code']) || empty($_SESSION['gmailOAuth']) || !is_object($_SESSION['gmailOAuth']);
if ($validInput) {
    G::header($header);
}

$RBAC->allows(basename(__FILE__), "code");
$gmailOAuth = $_SESSION['gmailOAuth'];

$googleClient = $gmailOAuth->getGoogleClient();
$result = $googleClient->authenticate($_GET['code']);
if (isset($result["error"])) {
    G::header($header);
}

$gmailOAuth->setRefreshToken($googleClient->getRefreshToken());
$gmailOAuth->saveEmailServer();
$gmailOAuth->sendTestMailWithPHPMailerOAuth();

G::header($header);
