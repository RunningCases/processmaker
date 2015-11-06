<?php

/**
 * class.pmGoogleApi.php
 *
 */

require_once PATH_TRUNK . 'vendor' . PATH_SEP . 'google' . PATH_SEP . 'apiclient' . PATH_SEP . 'src' . PATH_SEP . 'Google' . PATH_SEP . 'autoload.php';

class PMGoogleApi
{
    private $scope = array();
    private $serviceAccountEmail;
    private $serviceAccountP12;
    private $statusService;
    private $domain;
    private $user;

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
        $conf = $this->getConfigGmail();

        $conf->aConfig['statusService'] = $status;
        $conf->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->statusService = $status;
    }

    public function getStatusService()
    {
        return $this->statusService;
    }

    public function getConfigGmail()
    {
        $conf = new Configurations();
        $conf->loadConfig($gmail, 'GOOGLE_API_SETTINGS', '');
        return $conf;
    }

    public function setServiceAccountEmail($serviceAccountEmail)
    {
        $conf = $this->getConfigGmail();

        $conf->aConfig['serviceAccountEmail'] = $serviceAccountEmail;
        $conf->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->serviceAccountEmail = $serviceAccountEmail;
    }

    public function getServiceAccountEmail()
    {
        return $this->serviceAccountEmail;
    }

    public function setServiceAccountP12($serviceAccountP12)
    {
        $conf = $this->getConfigGmail();

        $conf->aConfig['serviceAccountP12'] = $serviceAccountP12;
        $conf->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->serviceAccountP12 = $serviceAccountP12;
    }

    public function getserviceAccountP12()
    {
        return $this->serviceAccountP12;
    }

    public function setDomain($domain)
    {
        $conf = $this->getConfigGmail();

        $conf->aConfig['domain'] = $domain;
        $conf->saveConfig('GOOGLE_API_SETTINGS', '', '', '');

        $this->domain = $domain;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * load configuration gmail service account
     *
     */
    public function loadSettings()
    {
        $conf = $this->getConfigGmail();

        $serviceAccountP12 = empty($conf->aConfig['serviceAccountP12']) ? '' : $conf->aConfig['serviceAccountP12'];
        $serviceAccountEmail = empty($conf->aConfig['serviceAccountEmail']) ? '' : $conf->aConfig['serviceAccountEmail'];
        $statusService = empty($conf->aConfig['statusService']) ? '' : $conf->aConfig['statusService'];

        $this->scope = array();
        $this->setServiceAccountEmail($serviceAccountEmail);
        $this->setServiceAccountP12($serviceAccountP12);
        $this->setStatusService($statusService);
    }

    /**
     * New service client - Authentication google Api
     *
     * @return Google_Service_Client $service API service instance.
     */
    public function serviceClient()
    {
        $key = file_get_contents(PATH_DATA_SITE . $this->serviceAccountP12);

        $assertionCredentials = new Google_Auth_AssertionCredentials(
            $this->serviceAccountEmail,
            $this->scope,
            $key
        );
        $assertionCredentials->sub = $this->user;

        $client = new Google_Client();
        $client->setApplicationName("PMDrive");
        $client->setAssertionCredentials($assertionCredentials);

        return $client;
    }

    /**
     * New service client - Authentication google Api
     *
     * @return Google_Service_Client $service API service instance.
     */
    public function testService($serviceAccountEmail, $pathServiceAccountP12)
    {
        $key = file_get_contents($pathServiceAccountP12);

        $assertionCredentials = new Google_Auth_AssertionCredentials(
            $serviceAccountEmail,
            array(
                'https://www.googleapis.com/auth/drive',
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive.readonly',
                'https://www.googleapis.com/auth/drive.metadata.readonly',
                'https://www.googleapis.com/auth/drive.appdata',
                'https://www.googleapis.com/auth/drive.metadata',
                'https://www.googleapis.com/auth/drive.photos.readonly'
            ),
            $key
        );
        $assertionCredentials->sub = $this->user;

        $client = new Google_Client();
        $client->setApplicationName("PMDrive");
        $client->setAssertionCredentials($assertionCredentials);

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
