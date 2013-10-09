<?php
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $G_PUBLISH = new Publisher();

        $G_PUBLISH->AddContent( 'view', 'oauth2/authorize' );

        G::RenderPage('publish', 'minimal');
    break;

    case 'POST':
        require_once PATH_CORE . 'services/oauth2/Server.php';

        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";

        \Api\OAuth2\Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER.":host=$host;dbname=".DB_NAME.$port);
        \Api\OAuth2\Server::setPmClientId('x-pm-local-client');

        $oauthServer = new \Api\OAuth2\Server();
        $userid = $_SESSION['USER_LOGGED'];
        $authorize = isset($_POST['authorize']) ? (bool) $_POST['authorize'] : false;


        $response = $oauthServer->postAuthorize($authorize, $userid, true);

        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

        echo 'session_id ' . session_id() . '<br>';
        exit("SUCCESS! ==>  Authorization Code: $code");

        //die($response->send());
    break;
}