<?php
$accesstoken = "e79057f4276661bedb6154eed3834f6cbd738853";
$headr = array();
$inp_doc_uid = '68671480353319e5e1dee74089764900';
$tas_uid = '19582733053319e304cfa76025663570';
$app_doc_comment = 'desde script php';
$headr[] = 'Authorization: Bearer '.$accesstoken;
$file = "/home/wendy/uploadfiles/test1.html";
$url = "http://wendy.pmos.colosa.net/api/1.0/wendy/cases/64654381053382b8bb4c415067063003/input-document";
$ch = curl_init();
$a = array('form'=>'@'.$file, 'inp_doc_uid'=>$inp_doc_uid, 'tas_uid' =>$tas_uid, 'app_doc_comment' =>$app_doc_comment);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
curl_setopt($ch, CURLOPT_POSTFIELDS, $a);
curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$postResult = curl_exec($ch);
curl_close($ch);
print_r($postResult);