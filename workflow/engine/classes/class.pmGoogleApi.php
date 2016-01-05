<?php

/**
 * class.pmGoogleApi.php
 *
 */

require_once PATH_TRUNK . 'vendor' . PATH_SEP . 'google' . PATH_SEP . 'apiclient' . PATH_SEP . 'src' . PATH_SEP . 'Google' . PATH_SEP . 'autoload.php';

class PMGoogleApi
{
    const DRIVE = 'https://www.googleapis.com/auth/drive';
    const DRIVE_FILE = 'https://www.googleapis.com/auth/drive.file';
    const DRIVE_APPS_READONLY = 'https://www.googleapis.com/auth/drive.apps.readonly';
    const DRIVE_READONLY = 'https://www.googleapis.com/auth/drive.readonly';
    const DRIVE_METADATA = 'https://www.googleapis.com/auth/drive.metadata';
    const DRIVE_METADATA_READONLY = 'https://www.googleapis.com/auth/drive.metadata.readonly';
    const DRIVE_APPDATA = 'https://www.googleapis.com/auth/drive.appdata';
    const DRIVE_PHOTOS_READONLY = 'https://www.googleapis.com/auth/drive.photos.readonly';

    private $scope = array();
    private $serviceAccountEmail;
    private $serviceAccountP12;
    private $statusService;
    private $domain;
    private $user;

    private $typeAuthentication;
    private $accountJson;

    private $serviceGmailStatus = false;
    private $serviceDriveStatus = false;
    private $configuration;

    public function __construct()
    {
        $licensedFeatures = &PMLicensedFeatures::getSingleton();
        if (!$licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
            G::SendTemporalMessage('ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels');
            G::header('location: ../login/login');
            die;
        }
        $this->loadSettings();
    }

    public function setScope($scope)
    {
        $this->scope[] = $scope;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setStatusService($status)
    {
        $this->configuration->aConfig['statusService'] = $status;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->statusService = $status;
    }

    public function getStatusService()
    {
        return $this->statusService;
    }

    public function getConfigGmail()
    {
        $this->configuration = new Configurations();
        $this->configuration->loadConfig($gmail, 'GOOGLE_API_SETTINGS', '');
    }

    public function setServiceAccountEmail($serviceAccountEmail)
    {
        $this->configuration->aConfig['serviceAccountEmail'] = $serviceAccountEmail;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->serviceAccountEmail = $serviceAccountEmail;
    }

    public function getServiceAccountEmail()
    {
        return $this->serviceAccountEmail;
    }

    public function setServiceAccountP12($serviceAccountP12)
    {
        $this->configuration->aConfig['serviceAccountP12'] = $serviceAccountP12;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->serviceAccountP12 = $serviceAccountP12;
    }

    public function getServiceAccountP12()
    {
        return $this->serviceAccountP12;
    }

    public function setDomain($domain)
    {
        $this->configuration->aConfig['domain'] = $domain;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setTypeAuthentication($type)
    {
        $this->configuration->aConfig['typeAuthentication'] = $type;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->typeAuthentication = $type;
    }

    public function getTypeAuthentication()
    {
        return $this->typeAuthentication;
    }

    public function setAccountJson($accountJson)
    {
        $this->configuration->aConfig['accountJson'] = $accountJson;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->accountJson = $accountJson;
    }

    public function getAccountJson()
    {
        return $this->accountJson;
    }

    public function setServiceGmailStatus($status)
    {
        $this->configuration->aConfig['serviceGmailStatus'] = $status;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->serviceGmailStatus = $status;
    }

    public function getServiceGmailStatus()
    {
        return $this->serviceGmailStatus;
    }

    public function setServiceDriveStatus($status)
    {
        $this->configuration->aConfig['serviceDriveStatus'] = $status;
        $this->configuration->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->serviceDriveStatus = $status;
    }

    public function getServiceDriveStatus()
    {
        return $this->serviceDriveStatus;
    }

    /**
     * load configuration gmail service account
     *
     */
    public function loadSettings()
    {
        $this->getConfigGmail();

        $typeAuthentication = empty($this->configuration->aConfig['typeAuthentication']) ? '' : $this->configuration->aConfig['typeAuthentication'];
        $accountJson = empty($this->configuration->aConfig['accountJson']) ? '' : $this->configuration->aConfig['accountJson'];

        $serviceAccountP12 = empty($this->configuration->aConfig['serviceAccountP12']) ? '' : $this->configuration->aConfig['serviceAccountP12'];
        $serviceAccountEmail = empty($this->configuration->aConfig['serviceAccountEmail']) ? '' : $this->configuration->aConfig['serviceAccountEmail'];
        $statusService = empty($this->configuration->aConfig['statusService']) ? '' : $this->configuration->aConfig['statusService'];

        $serviceGmailStatus = empty($this->configuration->aConfig['serviceGmailStatus']) ? false : $this->configuration->aConfig['serviceGmailStatus'];
        $serviceDriveStatus = empty($this->configuration->aConfig['serviceDriveStatus']) ? false : $this->configuration->aConfig['serviceDriveStatus'];

        $this->scope = array();

        $this->typeAuthentication = $typeAuthentication;
        $this->accountJson = $accountJson;
        $this->serviceAccountEmail = $serviceAccountEmail;
        $this->serviceAccountP12 = $serviceAccountP12;
        $this->statusService = $statusService;
        $this->serviceGmailStatus = $serviceGmailStatus;
        $this->serviceDriveStatus = $serviceDriveStatus;
    }

    /**
     * New service client - Authentication google Api
     *
     * @return Google_Service_Client $service API service instance.
     */
    public function serviceClient()
    {
        $client = null;
        if ($this->typeAuthentication == 'webApplication') {
            if (file_exists(PATH_DATA_SITE . $this->accountJson)) {
                $credential = file_get_contents(PATH_DATA_SITE . $this->accountJson);
            } else {
                throw new Exception(G::LoadTranslation('ID_GOOGLE_FILE_JSON_ERROR'));
            }


            $client = new Google_Client();
            $client->setAuthConfig($credential);
            $client->addScope($this->scope);

            if (!empty($_SESSION['google_token'])) {
                $client->setAccessToken($_SESSION['google_token']);
                if ($client->isAccessTokenExpired()) {
                    $client->getRefreshToken();
                    unset($_SESSION['google_token']);
                    $_SESSION['google_token'] = $client->getAccessToken();
                }
            } else if (!empty($_SESSION['CODE_GMAIL'])) {
                $token = $client->authenticate($_SESSION['CODE_GMAIL']);
                $_SESSION['google_token'] = $client->getAccessToken();
            } else {
                $authUrl = $client->createAuthUrl();
                echo '<script type="text/javascript">
                    var opciones = "width=450,height=480,scrollbars=NO, locatin=NO,toolbar=NO, status=NO, menumbar=NO, top=10%, left=25%";
                    window.open("' . $authUrl . '","Gmail", opciones);
                    </script>';
                die;
            }
        } else if ($this->typeAuthentication == 'serviceAccount') {

            if (file_exists(PATH_DATA_SITE . $this->serviceAccountP12)) {
                $key = file_get_contents(PATH_DATA_SITE . $this->serviceAccountP12);
            } else {
                throw new Exception(G::LoadTranslation('ID_GOOGLE_FILE_P12_ERROR'));
            }

            $data = json_decode($key);
            $assertionCredentials = new Google_Auth_AssertionCredentials(
                $this->serviceAccountEmail,
                $this->scope,
                $data->private_key
            );

            $assertionCredentials->sub = $this->user;

            $client = new Google_Client();
            $client->setApplicationName("PMDrive");
            $client->setAssertionCredentials($assertionCredentials);
        } else {
            throw new Exception(G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR'));
        }

        return $client;
    }

    /**
     * New service client - Authentication google Api
     *
     * @return Google_Service_Client $service API service instance.
     */
    public function testService($credentials)
    {

        $scope = array(
            static::DRIVE,
            static::DRIVE_FILE,
            static::DRIVE_READONLY,
            static::DRIVE_METADATA,
            static::DRIVE_METADATA_READONLY,
            static::DRIVE_APPDATA,
            static::DRIVE_PHOTOS_READONLY
        );

        if ($credentials->typeAuth == 'webApplication') {

            if (file_exists($credentials->pathFileJson)) {
                $credential = file_get_contents($credentials->pathFileJson);
            } else {
                throw new Exception(G::LoadTranslation('ID_GOOGLE_FILE_JSON_ERROR'));
            }

            $client = new Google_Client();
            $client->setAuthConfig($credential);
            $client->addScope($scope);

            if (!empty($_SESSION['google_token'])) {
                $client->setAccessToken($_SESSION['google_token']);
                if ($client->isAccessTokenExpired()) {
                    unset($_SESSION['google_token']);
                }
            } else if (!empty($_SESSION['CODE_GMAIL'])) {
                $token = $client->authenticate($_SESSION['CODE_GMAIL']);
                $_SESSION['google_token'] = $client->getAccessToken();
            } else {
                $authUrl = $client->createAuthUrl();
                echo '<script type="text/javascript">
                    var opciones = "width=450,height=480,scrollbars=NO, locatin=NO,toolbar=NO, status=NO, menumbar=NO, top=10%, left=25%";
                    window.open("' . $authUrl . '","Gmail", opciones);
                    </script>';
                die;
            }
        } else {

            if (file_exists($credentials->pathServiceAccountP12)) {
                $key = file_get_contents($credentials->pathServiceAccountP12);
            } else {
                throw new Exception(G::LoadTranslation('ID_GOOGLE_FILE_P12_ERROR'));
            }
            $data = json_decode($key);
            $assertionCredentials = new Google_Auth_AssertionCredentials(
                $credentials->emailServiceAccount,
                $scope,
                $data->private_key
            );
            $assertionCredentials->sub = $this->user;

            $client = new Google_Client();
            $client->setApplicationName("PMDrive");
            $client->setAssertionCredentials($assertionCredentials);
        }



        $service = new Google_Service_Drive($client);

        $result = new StdClass();
        $result->success = true;

        $result->currentUserName = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->rootFolderId = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->quotaType = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->quotaBytesTotal = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        $result->quotaBytesUsed = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');

        try {
            $about = $service->about->get();

            $result->currentUserName = $about->getName();
            $result->rootFolderId = $about->getRootFolderId();
            $result->quotaType = $about->getQuotaType();
            $result->quotaBytesTotal = $about->getQuotaBytesTotal();
            $result->quotaBytesUsed = $about->getQuotaBytesUsed();
            $result->responseGmailTest = G::LoadTranslation('ID_SUCCESSFUL_CONNECTION');
        } catch (Exception $e) {
            $result->success = false;
            $result->responseGmailTest = G::LoadTranslation('ID_SERVER_COMMUNICATION_ERROR');
        }

        return $result;
    }
}
