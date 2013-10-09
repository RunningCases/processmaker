<?php

if (! empty($_GET['error'])) {
    echo '<h1>'.$_GET['error'] . '</h1><br/>';
    die($_GET['error_description']);
}


$host = 'http://pmos/api/1.0/workflow/token';
$code = empty($_GET['code']) ? 'NN' : $_GET['code'];

$clientId = 'x-pm-local-client';
$secret = '179ad45c6ce2cb97cf1029e212046e81';

$data = array(
    'grant_type' => 'authorization_code',
    'code' => $code
);

$ch = curl_init($host);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = @json_decode(curl_exec($ch));

if (is_object($data)) {
    /*$data = (array) $data;
    require_once PATH_CORE . 'classes/model/DesignerOauthAccessTokens.php';

    $model = new DesignerOauthAccessTokens();
    $model->setAccessToken($data['access_token']);
    $model->setExpires($data['expires_in']);
    $model->setTokenType($data['token_type']);
    $model->setScope($data['scope']);
    $model->setRefreshToken($data['refresh_token']);
    $model->setClientId($clientId);
    $model->setUserId($_SESSION['USER_LOGGED']);

    $model->save();*/
}

echo '<pre>';
//print_r($_SESSION);
print_r($data);

curl_close($ch);