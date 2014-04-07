<?php

/**
 * Designer Controller
 *
 * @inherits Controller
 * @access public
 */

class Designer extends Controller
{
    protected $clientId = 'x-pm-local-client';

    public function __construct ()
    {

    }

    /**
     * Index Action
     *
     * @param string $httpData (opional)
     */
    public function index($httpData)
    {
        $proUid = isset($httpData->prj_uid) ? $httpData->prj_uid : '';
        $client = $this->getClientCredentials();
        $authCode = $this->getAuthorizationCode($client);

        $this->setVar('prj_uid', $proUid);

        $credentials = array();
        $credentials['client_id'] = $client['CLIENT_ID'];
        $credentials['secret'] = $client['CLIENT_SECRET'];
        $credentials['authorization_code'] = $authCode;

        $this->setVar('credentials', base64_encode(json_encode($credentials)));
        $this->setVar('isDebugMode', System::isDebugMode());

        if (System::isDebugMode()) {
            if (! file_exists(PATH_HTML . "lib-dev/pmUI/build.cache")) {
                throw new RuntimeException("Development JS Files were are not generated!.\nPlease execute: \$>rake pmBuildDebug in pmUI project");
            }
            if (! file_exists(PATH_HTML . "lib-dev/mafe/build.cache")) {
                throw new RuntimeException("Development JS Files were are not generated!.\nPlease execute: \$>rake pmBuildDebug in MichelangeloFE project");
            }

            $mafeFiles = file(PATH_HTML . "lib-dev/mafe/build.cache", FILE_IGNORE_NEW_LINES);
            $mafeCssFiles = array();
            $mafeJsFiles = array();

            foreach ($mafeFiles as $file) {
                if (substr($file, -3) == ".js") {
                    $mafeJsFiles[] = $file;
                } else {
                    $mafeCssFiles[] = $file;
                }
            }

            $this->setVar('pmuiJsCacheFile', file(PATH_HTML . "lib-dev/pmUI/build.cache", FILE_IGNORE_NEW_LINES));
            $this->setVar('pmuiCssCacheFile', file(PATH_HTML . "lib-dev/pmUI/css.cache", FILE_IGNORE_NEW_LINES));

            $this->setVar('designerCacheFile', file(PATH_HTML . "lib-dev/mafe/applications.cache", FILE_IGNORE_NEW_LINES));
            $this->setVar('mafeJsFiles', $mafeJsFiles);
            $this->setVar('mafeCssFiles', $mafeCssFiles);
        } else {
            $buildhashFile = PATH_HTML . "lib/buildhash";
            if (! file_exists($buildhashFile)) {
                throw new RuntimeException("CSS and JS Files were are not generated!.\nPlease review install process");
            }
            $buildhash = file_get_contents($buildhashFile);
            $this->setVar('buildhash', $buildhash);
        }

        $this->setView('designer/index');
        $this->render();
    }

    protected function getClientCredentials()
    {
        $oauthQuery = new ProcessMaker\Services\OAuth2\PmPdo($this->getDsn());
        return $oauthQuery->getClientDetails($this->clientId);
    }

    protected function getAuthorizationCode($client)
    {
        \ProcessMaker\Services\OAuth2\Server::setDatabaseSource($this->getDsn());
        \ProcessMaker\Services\OAuth2\Server::setPmClientId($client['CLIENT_ID']);

        $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
        $userId = $_SESSION['USER_LOGGED'];
        $authorize = true;
        $_GET = array_merge($_GET, array(
            'response_type' => 'code',
            'client_id' => $client['CLIENT_ID'],
            'scope' => implode(' ', $oauthServer->getScope())
        ));

        $response = $oauthServer->postAuthorize($authorize, $userId, true);
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);

        return $code;
    }

    private function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER.':host='.$host.';dbname='.DB_NAME.$port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }
}

