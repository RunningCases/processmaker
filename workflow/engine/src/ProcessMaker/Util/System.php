<?php
namespace ProcessMaker\Util;

use \ProcessMaker\Services\OAuth2\PmPdo;
use \ProcessMaker\Services\OAuth2\Server;
use \OAuth2\Request;

class System
{
    const CLIENT_ID = 'x-pm-local-client';

    /**
     * Get Time Zone
     *
     * @return string Return Time Zone
     * @throws \Exception
     */
    public static function getTimeZone()
    {
        try {
            $arraySystemConfiguration = \System::getSystemConfiguration('', '', SYS_SYS);

            //Return
            return $arraySystemConfiguration['time_zone'];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Token with USER_LOGGED saved in $_SESSION
     *
     * @return array
     */
    public static function token()
    {
        $client = self::getClientCredentials();

        $authCode = self::getAuthorizationCode($client);

        $loader = \Maveriks\Util\ClassLoader::getInstance();
        $loader->add(PATH_TRUNK . 'vendor/bshaffer/oauth2-server-php/src/', "OAuth2");

        $request = array(
            'grant_type' => 'authorization_code',
            'code' => $authCode
        );
        $server = array(
            'REQUEST_METHOD' => 'POST'
        );
        $headers = array(
            "PHP_AUTH_USER" => $client['CLIENT_ID'],
            "PHP_AUTH_PW" => $client['CLIENT_SECRET'],
            "Content-Type" => "multipart/form-data;",
            "Authorization" => "Basic " . base64_encode($client['CLIENT_ID'] . ":" . $client['CLIENT_SECRET'])
        );

        $request = new Request(array(), $request, array(), array(), array(), $server, null, $headers);
        $oauthServer = new Server();
        $response = $oauthServer->postToken($request, true);
        $clientToken = $response->getParameters();
        $clientToken["client_id"] = $client['CLIENT_ID'];
        $clientToken["client_secret"] = $client['CLIENT_SECRET'];
        return $clientToken;
    }

    protected function getClientCredentials()
    {
        $oauthQuery = new PmPdo(self::getDsn());
        return $oauthQuery->getClientDetails(self::CLIENT_ID);
    }

    protected function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER . ':host=' . $host . ';dbname=' . DB_NAME . $port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }

    protected function getAuthorizationCode($client)
    {
        Server::setDatabaseSource(self::getDsn());
        Server::setPmClientId($client['CLIENT_ID']);

        $oauthServer = new Server();

        $userId = $_SESSION['USER_LOGGED'];
        $authorize = true;
        $_GET = array_merge($_GET, array(
            'response_type' => 'code',
            'client_id' => $client['CLIENT_ID'],
            'scope' => implode(' ', $oauthServer->getScope())
        ));

        $response = $oauthServer->postAuthorize($authorize, $userId, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
        return $code;
    }
}
