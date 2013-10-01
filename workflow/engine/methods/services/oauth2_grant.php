<?php

//$host = 'http://pmos/sysworkflow/en/neoclassic/services/oauth2_grant';
$host = 'http://pmos/api/1.0/workflow/token';

$clientId = 'testclient';
$secret = 'testpass';

$data = array(
    'grant_type' => 'authorization_code',
    'code' => $_GET['code']
);

$ch = curl_init($host);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$return = @json_decode(curl_exec($ch));

echo '<pre>';
print_r($return);

curl_close($ch);