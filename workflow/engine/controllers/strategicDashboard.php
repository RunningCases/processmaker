<?php

/**
 * StrategicDashboard controller
 * @inherits Controller
 *
 * @access public
 */

class StrategicDashboard extends Controller
{

    // Class properties
    private $urlProxy;
    private $clientToken;
    private $usrId;

    // Class constructor
    public function __construct ()
    {
        global $RBAC;

        if ($RBAC->userCanAccess('PM_SETUP') != 1) {
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header( 'location: login/login' );
            exit(0);
        }
        $licensedFeatures = & PMLicensedFeatures::getSingleton();
        if (!$licensedFeatures->verifyfeature('r19Vm5DK1UrT09MenlLYjZxejlhNUZ1b1NhV0JHWjBsZEJ6dnpJa3dTeWVLVT0=')) {
            G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
            G::header( 'location: ../login/login' );
            die;
        }
        $this->usrId = $RBAC->aUserInfo['USER_INFO']['USR_UID'];
        $this->urlProxy = '/api/1.0/' . SYS_SYS . '/';
        //change
        $clientId = 'x-pm-local-client';
        $client = $this->getClientCredentials($clientId);
        $authCode = $this->getAuthorizationCode($client);
        $debug = false; //System::isDebugMode();

        $loader = Maveriks\Util\ClassLoader::getInstance();
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

        $request = new \OAuth2\Request(array(), $request, array(), array(), array(), $server, null, $headers);
        $oauthServer = new \ProcessMaker\Services\OAuth2\Server();
        $response = $oauthServer->postToken($request, true);
        $this->clientToken = $response->getParameters();
        $this->clientToken["client_id"] = $client['CLIENT_ID'];
        $this->clientToken["client_secret"] = $client['CLIENT_SECRET'];
    }

    private function getClientCredentials($clientId)
    {
        $oauthQuery = new ProcessMaker\Services\OAuth2\PmPdo($this->getDsn());
        return $oauthQuery->getClientDetails($clientId);
    }

    private function getAuthorizationCode($client)
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

    // Functions for the StrategicDashboards

    public function dashboardList ()
    {
        try {
            $this->includeExtJS( 'strategicDashboard/dashboardList' );
            if (isset( $_SESSION['__StrategicDashboard_ERROR__'] )) {
                $this->setJSVar( '__StrategicDashboard_ERROR__', $_SESSION['__StrategicDashboard_ERROR__'] );
                unset( $_SESSION['__StrategicDashboard_ERROR__'] );
            }
            $this->setView( 'strategicDashboard/dashboardList' );

            $this->setJSVar('urlProxy',$this->urlProxy);
            $this->setJSVar('credentials',$this->clientToken);
            G::RenderPage( 'publish', 'extJs' );
        } catch (Exception $error) {
            $_SESSION['__DASHBOARD_ERROR__'] = $error->getMessage();
            die();
        }
    }

    public function formDashboard ($data)
    {
        try {
            
            $this->includeExtJS( 'strategicDashboard/formDashboard', true, true );
            $this->setView( 'strategicDashboard/formDashboard' );

            $this->setJSVar('DAS_UID', '');
            $this->setJSVar('urlProxy',$this->urlProxy);
            $this->setJSVar('credentials',$this->clientToken);

            G::RenderPage( 'publish', 'extJs' );
            return null;
        } catch (Exception $error) {
            $_SESSION['__DASHBOARD_ERROR__'] = $error->getMessage();
            G::header( 'Location: dashboardList' );
            die();
        }
    }

    public function formEditDashboard ($data)
    {
        try {
            
            $this->includeExtJS( 'strategicDashboard/formDashboard', true, true );
            $this->setView( 'strategicDashboard/formDashboard' );

            $dasUid = isset($_REQUEST['DAS_UID']) ? $_REQUEST['DAS_UID'] : '';
            $this->setJSVar('DAS_UID', $dasUid);
            $this->setJSVar('urlProxy',$this->urlProxy);
            $this->setJSVar('credentials',$this->clientToken);

            G::RenderPage( 'publish', 'extJs' );
            return null;
        } catch (Exception $error) {
            $_SESSION['__DASHBOARD_ERROR__'] = $error->getMessage();
            G::header( 'Location: dashboardList' );
            die();
        }
    }

    public function viewDashboard ()
    {
        try {
            if (isset( $_SESSION['__StrategicDashboard_ERROR__'] )) {
                $this->setJSVar( '__StrategicDashboard_ERROR__', $_SESSION['__StrategicDashboard_ERROR__'] );
                unset( $_SESSION['__StrategicDashboard_ERROR__'] );
            }
            $this->setView( 'strategicDashboard/viewDashboard' );

            $this->setVar('urlProxy',$this->urlProxy);
            $this->setVar('usrId',$this->usrId);
            $this->setVar('credentials',$this->clientToken);

            $translation = array();

            $translation['ID_MANAGERS_DASHBOARDS'] = G::LoadTranslation( 'ID_MANAGERS_DASHBOARDS');
            $translation['ID_PRO_EFFICIENCY_INDEX'] = G::LoadTranslation( 'ID_PRO_EFFICIENCY_INDEX');
            $translation['ID_EFFICIENCY_USER'] = G::LoadTranslation( 'ID_EFFICIENCY_USER');
            $translation['ID_COMPLETED_CASES'] = G::LoadTranslation( 'ID_COMPLETED_CASES');
            $translation['ID_WELL_DONE'] = G::LoadTranslation( 'ID_WELL_DONE');
            $translation['ID_NUMBER_CASES'] = G::LoadTranslation( 'ID_NUMBER_CASES');
            $translation['ID_EFFICIENCY_INDEX'] = G::LoadTranslation( 'ID_EFFICIENCY_INDEX');
            $translation['ID_INEFFICIENCY_COST'] = G::LoadTranslation( 'ID_INEFFICIENCY_COST');
            $translation['ID_EFFICIENCY_COST'] = G::LoadTranslation( 'ID_EFFICIENCY_COST');
            $translation['ID_RELATED_PROCESS'] = G::LoadTranslation( 'ID_RELATED_PROCESS');
            $translation['ID_RELATED_GROUPS'] = G::LoadTranslation( 'ID_RELATED_GROUPS');
            $translation['ID_RELATED_TASKS'] = G::LoadTranslation( 'ID_RELATED_TASKS');
            $translation['ID_RELATED_USERS'] = G::LoadTranslation( 'ID_RELATED_USERS');
            $translation['ID_GRID_PAGE_NO_DASHBOARD_MESSAGE'] = G::LoadTranslation( 'ID_GRID_PAGE_NO_DASHBOARD_MESSAGE');
            $translation['ID_PROCESS_TASKS'] = G::LoadTranslation( 'ID_PROCESS_TASKS');
            $translation['ID_TIME_HOURS'] = G::LoadTranslation( 'ID_TIME_HOURS');
            $translation['ID_GROUPS'] = G::LoadTranslation( 'ID_GROUPS');
            $translation['ID_YEAR'] = G::LoadTranslation( 'ID_YEAR');
            $translation['ID_USERS'] = G::LoadTranslation( 'ID_USERS');

            $this->setVar('translation', $translation);
            $this->render();
        } catch (Exception $error) {
            $_SESSION['__DASHBOARD_ERROR__'] = $error->getMessage();
            die();
        }
    }

    public function viewDashboardIE ()
    {
        try {
            $this->setView( 'strategicDashboard/viewDashboardIE' );
            $this->render();
        } catch (Exception $error) {
            $_SESSION['__DASHBOARD_ERROR__'] = $error->getMessage();
            die();
        }
    }
}
