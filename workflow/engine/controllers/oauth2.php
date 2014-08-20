<?php
class Oauth2 extends Controller
{
    public function index()
    {
        $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');

        //$applicationsLink = sprintf('%s://%s/sys%s/%s/%s/oauth2/applications', $http, $host, SYS_SYS, SYS_LANG, SYS_SKIN);
        $applicationsLink = sprintf('%s://%s/%s/oauth2/apps', $http, $host, SYS_SYS);
        $authorizationLink = sprintf('%s://%s/%s/oauth2/authorize?response_type=code&client_id=[the-client-id]&scope=*', $http, $host, SYS_SYS);
        //http://<your-pm-server>/sys<your-workspace>/en/neoclassic/oauth2/authorize?response_type=code&client_id={your-client-d}&scope=view_processes%20edit_processes
        $this->setVar('applications_link', $applicationsLink);
        $this->setVar('authorization_link', $authorizationLink);

        $this->setView('oauth2/index');
        $this->render();
    }

    public function apps()
    {
        $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');

        $applicationsLink = sprintf('%s://%s/sys%s/%s/%s/oauth2/applications', $http, $host, SYS_SYS, SYS_LANG, SYS_SKIN);

        header('location: ' . $applicationsLink);

    }

    public function authorize()
    {
        session_start();
        if (! isset($_SESSION['USER_LOGGED'])) {
            $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
            $host = $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != '80' ? ':' . $_SERVER['SERVER_PORT'] : '');

            $loginLink = sprintf('%s://%s/sys%s/%s/%s/login/login?u=/%s/oauth2/authorize', $http, $host, SYS_SYS, SYS_LANG, SYS_SKIN, SYS_SYS);
            header('location: ' . $loginLink);
            die;
        }

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                require_once PATH_CORE . 'src/ProcessMaker/Services/OAuth2/PmPdo.php';


                list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
                $port = empty($port) ? '' : ";port=$port";

                $dsn      = DB_ADAPTER.':host='.$host.';dbname='.DB_NAME.$port;
                $username = DB_USER;
                $password = DB_PASS;

                $this->scope = array(
                    'view_processes' => 'View Processes',
                    'edit_processes' => 'Edit Processes'
                );

                // $dsn is the Data Source Name for your database, for example "mysql:dbname=my_oauth2_db;host=localhost"
                $storage = new ProcessMaker\Services\OAuth2\PmPdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

                $clientId = $_GET['client_id'];
                $requestedScope = isset($_GET['scope']) ? $_GET['scope'] : '';
                $requestedScope = empty($requestedScope) ? array() : explode(' ', $requestedScope);

                if (! empty($clientId)) {
                    $client = $storage->getClientDetails($clientId);
                    // throw error, client does not exist.
                }

                //echo '<pre>';print_r($client); echo '</pre>';

                $client = array('name' => $client['client_name'], 'desc' => $client['client_description']);

                //echo '<pre>';print_r($_SESSION); echo '</pre>'; die;
                $user = array('name' => $_SESSION['USR_FULLNAME']);

                $this->setVar('user', $user);
                $this->setVar('client', $client);
                $this->setVar('postUri', '/' . SYS_SYS . '/oauth2/authorize?' . $_SERVER['QUERY_STRING']);
                //$this->setVar('postUri', '/' . SYS_SYS . '/oauth2/authorize');
                //$this->setVar('query_string', $_SERVER['QUERY_STRING']);
                $this->setView('oauth2/authorize');
                $this->render();
                break;

            case 'POST':
                require_once PATH_CORE . 'src/ProcessMaker/Services/OAuth2/Server.php';

                list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
                $port = empty($port) ? '' : ";port=$port";

                \ProcessMaker\Services\OAuth2\Server::setDatabaseSource(DB_USER, DB_PASS, DB_ADAPTER.":host=$host;dbname=".DB_NAME.$port);
                \ProcessMaker\Services\OAuth2\Server::setPmClientId('x-pm-local-client');

                $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
                $userid = $_SESSION['USER_LOGGED'];
                $authorize = array_key_exists('cancel', $_POST)? false: true;


                $response = $oauthServer->postAuthorize($authorize, $userid, true);

                //$code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

                //echo 'session_id ' . session_id() . '<br>';
                //exit("SUCCESS! ==>  Authorization Code: $code");

                die($response->send());
                break;
        }
    }

    public function token()
    {

    }
}