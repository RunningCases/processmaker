<?php
namespace Api\OAuth2;

use Luracast\Restler\iAuthenticate;


/**
 * Class Server
 *
 * @package OAuth2
 * @author Erik Amaru Ortiz <aortiz.erik at gmail dot com>
 *
 */
class Server implements iAuthenticate
{
    /**
     * @var OAuth2_Server
     */
    protected $server;
    /**
     * @var OAuth2_Storage_Pdo
     */
    protected $storage;
    protected $scope = array();

    protected static $pmClientId;
    protected static $userId;
    protected static $dbUser;
    protected static $dbPassword;
    protected static $dsn;

    public function __construct()
    {
        require_once 'PmPdo.php';

        $this->scope = array(
            'view_processes' => 'View Processes',
            'edit_processes' => 'Edit Processes'
        );

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $config = array('dsn' => self::$dsn, 'username' => self::$dbUser, 'password' => self::$dbPassword);
        //var_dump($config); die;
        $this->storage = new PmPdo($config);

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new \OAuth2\Server($this->storage);

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($this->storage));

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($this->storage));

        // Add the "Refresh token" grant type
        $this->server->addGrantType(new \OAuth2\GrantType\RefreshToken($this->storage));

        $scope = new \OAuth2\Scope(array(
            'supported_scopes' => array_keys($this->scope)
        ));
        $this->server->setScopeUtil($scope);
    }

    public static function setDatabaseSource($user, $password, $dsn)
    {
        self::$dbUser = $user;
        self::$dbPassword = $password;
        self::$dsn = $dsn;
    }

    /**
     * @view oauth2/server/register.php
     * @format HtmlFormat
     */
    public function register()
    {
        static::$server->getResponse(\OAuth2\Request::createFromGlobals());
        return array('queryString' => $_SERVER['QUERY_STRING']);
    }

    /**
     * Stage 1: Client sends the user to this page
     *
     * User responds by accepting or denying
     *
     * @view oauth2/server/authorize.php
     * @format HtmlFormat
     */
    public function authorize()
    {
        $clientId = \OAuth2\Request::createFromGlobals()->query('client_id', '');
        $requestedScope = \OAuth2\Request::createFromGlobals()->query('scope', '');
        $requestedScope = empty($requestedScope) ? array() : explode(' ', $requestedScope);

        if (! empty($clientId)) {
            $clientDetails = $this->storage->getClientDetails(\OAuth2\Request::createFromGlobals()->query('client_id'));
        }

        return array(
            'client_details' => $clientDetails,
            'query_string'   => $_SERVER['QUERY_STRING'],
            'supportedScope' => $this->scope,
            'requestedScope' => $requestedScope
        );
    }

    /**
     * Stage 2: User response is captured here
     *
     * Success or failure is communicated back to the Client using the redirect
     * url provided by the client
     *
     * On success authorization code is sent along
     *
     *
     * @param bool $authorize
     * @param string $userId optional user id
     * @param bool $returnResponse optional flag to specify if the function should return the Response object
     * @return \OAuth2\ResponseInterface
     * @format JsonFormat,UploadFormat
     */
    public function postAuthorize($authorize = false, $userId = null, $returnResponse = false)
    {
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();

        $response = $this->server->handleAuthorizeRequest(
            $request,
            $response,
            (bool)$authorize,
            $userId
        );

        if ($returnResponse) {
            return $response;
        }

        die($response->send());
    }


    /**
     * Stage 3: Client directly calls this api to exchange access token
     *
     * It can then use this access token to make calls to protected api
     *
     * @format JsonFormat,UploadFormat
     */
    public function postToken()
    {
        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        $request = \OAuth2\Request::createFromGlobals();
        $response = $this->server->handleTokenRequest($request);

        /* DEPREACATED
        $token = $response->getParameters();
        if (array_key_exists('access_token', $token)) {
            $data = $this->storage->getAccessToken($token['access_token']);

            // verify if the client is our local PM Designer client
            if ($data['client_id'] == self::getPmClientId()) {
                error_log('do stuff - is a request from local pm client');
                require_once "classes/model/PmoauthUserAccessTokens.php";

                $userToken = new \PmoauthUserAccessTokens();
                $userToken->setAccessToken($token['access_token']);
                $userToken->setRefreshToken($token['refresh_token']);
                $userToken->setUserId($data['user_id']);
                $userToken->setSessionId(session_id());

                $userToken->save();
            }
        }*/

        $response->send();
    }

    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @return boolean true when api access is allowed; false otherwise
     */
    public function __isAllowed()
    {
        $request = \OAuth2\Request::createFromGlobals();
        $allowed = $this->server->verifyResourceRequest($request);
        $token = $this->server->getAccessTokenData($request);

        self::$userId = $token['user_id'];

        // verify if the client is not our local PM Designer client
        if ($token['client_id'] != self::getPmClientId()) {
            return $allowed;
        }

        // making a partcular session verification for PM Web Designer Client
        if (! isset($_SESSION) || ! array_key_exists('USER_LOGGED', $_SESSION)) {
            return false;
        }

        return true;
    }

    public static function setPmClientId($clientId)
    {
        self::$pmClientId = $clientId;
    }

    public static function getPmClientId()
    {
        return self::$pmClientId;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getUserId()
    {
        return self::$userId;
    }
}