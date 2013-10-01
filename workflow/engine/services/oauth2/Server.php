<?php
namespace Api\OAuth2;

use Luracast\Restler\iAuthenticate;

use OAuth2_Request;
use OAuth2_Response;
use OAuth2_GrantType_AuthorizationCode;
use OAuth2_Storage_Pdo;
use OAuth2_Server;

/**
 * Class Server
 *
 * @package OAuth2
 *
 */
class Server implements iAuthenticate
{
    /**
     * @var OAuth2_Server
     */
    //protected static $server;
    protected $server;
    protected $storage;

    /**
     * @var OAuth2_Storage_Pdo
     */
    //protected static $storage;
    /**
     * @var OAuth2_Request
     */
    protected static $request;
    public function __construct()
    {
        /*$dir = __DIR__ . '/db/';
        $file = 'oauth.sqlite';
        if (!file_exists($dir . $file)) {
            include_once $dir . 'rebuild_db.php';
        }
        static::$storage = new \OAuth2\Storage\Pdo(
            array('dsn' => 'sqlite:' . $dir . $file)
        );
        static::$request = \OAuth2\Request::createFromGlobals();
        static::$server = new \OAuth2\Server(static::$storage);
        static::$server->addGrantType(
            new \OAuth2\GrantType\AuthorizationCode(static::$storage)
        );*/

        static::$request = \OAuth2\Request::createFromGlobals();

        require_once 'PmPdo.php';

        $dsn      = 'mysql:dbname=wf_workflow;host=localhost';
        $username = 'root';
        $password = 'sample';

        // error reporting (this is a demo, after all!)
        //ini_set('display_errors',1);error_reporting(E_ALL);

        // Autoloading (composer is preferred, but for this example let's just do this)
        //require_once('oauth2-server-php/src/OAuth2/Autoloader.php');
        //\OAuth2\Autoloader::register();

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage = new PmPdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new \OAuth2\Server($storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));

    }

    /**
     * @view oauth2/server/register.php
     * @format HtmlFormat
     */
    public function register()
    {
        static::$server->getResponse(static::$request);
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
        $this->server->getResponse(static::$request);

        return array('queryString' => $_SERVER['QUERY_STRING']);
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
     *
     * @format JsonFormat,UploadFormat
     */
    public function postAuthorize($authorize = false)
    {
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();

        $response = $this->server->handleAuthorizeRequest(
            $request,
            $response,
            (bool)$authorize
        );

        if ($authorize) {
            // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
            $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
            //exit("SUCCESS! Authorization Code: $code");
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
    public function postGrant()
    {
        $response = static::$server->handleGrantRequest(
            static::$request
        );
        die($response->send());
    }
    /**
     * Sample api protected with OAuth2
     *
     * For testing the oAuth token
     *
     * @access protected
     */
    public function postAccess()
    {
        return array(
            'friends' => array('john', 'matt', 'jane')
        );
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
        return $this->server->verifyResourceRequest(\OAuth2\Request::createFromGlobals());
    }



    /****************************************/

    /**
     * Stage 3: Client directly calls this api to exchange access token
     *
     * It can then use this access token to make calls to protected api
     */
    public function postToken()
    {
        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        return $this->server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
    }
}