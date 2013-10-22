<?php

G::pr($_GET);

if (! empty($_GET['error'])) {
    G::pr($_GET);
    die();
}

$http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
$host = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');
$endpoint = sprintf('%s://%s/api/1.0/%s/token', $http, $host, SYS_SYS);
$code = empty($_GET['code']) ? 'NN' : $_GET['code'];

$clientId = 'x-pm-local-client';
$secret = '179ad45c6ce2cb97cf1029e212046e81';

$data = array(
    'grant_type' => 'authorization_code',
    'code' => $code
);

$ch = curl_init($endpoint);

curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$data = @json_decode(curl_exec($ch));
curl_close($ch);

G::pr((array) $data);