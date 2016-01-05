<?php

/**
 * pmGmail controller
 * @inherits Controller
 *
 * @access public
 */

class pmGmail extends Controller
{
    public function saveConfigPmGmail($httpData)
    {
        G::LoadClass( "pmGoogleApi" );
        $pmGoogle = new PMGoogleApi();
        $result = new StdClass();
        $result->success = true;

        if (!(empty($httpData->serviceGmailStatus) || empty($httpData->serviceGmailStatus))) {
            $httpData->serviceGmailStatus = !empty($httpData->serviceGmailStatus) ? $httpData->serviceGmailStatus == 1 ? true : false : false;
            $httpData->serviceDriveStatus = !empty($httpData->serviceDriveStatus) ? $httpData->serviceDriveStatus == 1 ? true : false : false;

            $pmGoogle->setServiceGmailStatus($httpData->serviceGmailStatus);
            $pmGoogle->setServiceDriveStatus($httpData->serviceDriveStatus);

            $message = G::LoadTranslation('ID_ENABLE_PMGMAIL') . ': ' . ($httpData->serviceGmailStatus ? G::LoadTranslation('ID_ENABLE') : G::LoadTranslation('ID_DISABLE'));
            $message .= G::LoadTranslation('ID_ENABLE_PMDRIVE') . ': ' . ($httpData->serviceDriveStatus ? G::LoadTranslation('ID_ENABLE') : G::LoadTranslation('ID_DISABLE'));

            if (!empty($httpData->emailServiceAccount)) {
                $pmGoogle->setServiceAccountEmail($httpData->emailServiceAccount);
                $message .= ', ' . G::LoadTranslation('ID_PMG_EMAIL') . ': ' . $httpData->emailServiceAccount;
            }
            if (!empty($_FILES)) {
                if (!empty($_FILES['googleCertificate']) && $_FILES['googleCertificate']['error'] != 1) {
                    if ($_FILES['googleCertificate']['tmp_name'] != '') {
                        G::uploadFile($_FILES['googleCertificate']['tmp_name'], PATH_DATA_SITE, $_FILES['googleCertificate']['name']);
                        $pmGoogle->setServiceAccountCertificate($_FILES['googleCertificate']['name']);
                        $message .= ', ' . G::LoadTranslation('ID_PMG_FILE') . ': ' . $_FILES['googleCertificate']['name'];
                    }
                } else {
                    $result->success = false;
                    $result->fileError = true;
                    print(G::json_encode($result));
                    die();
                }
            }
        } else {
            $pmGoogle->setStatusService(false);
            $message = G::LoadTranslation('ID_ENABLE_PMGMAIL') . ': ' . G::LoadTranslation('ID_DISABLE');
        }
        G::auditLog("Update Settings Gmail", $message);

        print(G::json_encode($result));
    }

    public function formPMGmail()
    {
        try {
            $this->includeExtJS('admin/pmGmail');
            if (!empty ($_SESSION['__PMGMAIL_ERROR__'])) {
                $this->setJSVar('__PMGMAIL_ERROR__', $_SESSION['__PMGMAIL_ERROR__']);
                unset($_SESSION['__PMGMAIL_ERROR__']);
            }
            G::LoadClass("pmGoogleApi");
            $pmGoogle = new PMGoogleApi();
            $accountEmail = $pmGoogle->getServiceAccountEmail();
            $googleCertificate = $pmGoogle->getServiceAccountCertificate();
            $statusGmail = $pmGoogle->getServiceGmailStatus();
            $statusDrive = $pmGoogle->getServiceDriveStatus();

            $this->setJSVar('accountEmail', $accountEmail);
            $this->setJSVar('googleCertificate', $googleCertificate);
            $this->setJSVar('statusGmail', $statusGmail);
            $this->setJSVar('statusDrive', $statusDrive);


            G::RenderPage('publish', 'extJs');
        } catch (Exception $error) {
            $_SESSION['__PMGMAIL_ERROR__'] = $error->getMessage();
            die();
        }
    }

    /**
     * @param $httpData
     */
    public function testConfigPmGmail($httpData)
    {
        G::LoadClass( "pmGoogleApi" );
        $pmGoogle = new PMGoogleApi();

        $result = new stdClass();

        $result->emailServiceAccount = empty($httpData->emailServiceAccount) ? $pmGoogle->getServiceAccountEmail() : $httpData->emailServiceAccount;
        $result->pathServiceAccountCertificate = empty($_FILES['googleCertificate']['tmp_name']) ? PATH_DATA_SITE . $pmGoogle->getServiceAccountCertificate() : $_FILES['googleCertificate']['tmp_name'];

        print(G::json_encode($pmGoogle->testService($result)));
    }

    /**
     *
     */
    public function testUserGmail()
    {
        $criteria = new Criteria();
        $criteria->clearSelectColumns();
        $criteria->addSelectColumn('COUNT(*) AS NUM_EMAIL');
        $criteria->addSelectColumn(UsersPeer::USR_UID);
        $criteria->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_LASTNAME);
        $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
        $criteria->addGroupByColumn(UsersPeer::USR_EMAIL);

        $rs = UsersPeer::doSelectRS($criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $userRepeat = array();
        while ($rs->next()) {
            $row = $rs->getRow();
            if ($row['NUM_EMAIL'] > 1) {
                $userRepeat[] = array(
                    'USR_UID' => $row['USR_UID'],
                    'FULL_NAME' => $row['USR_FIRSTNAME'] . ' ' . $row['USR_LASTNAME'],
                    'EMAIL' => $row['USR_EMAIL']
                );
            }
        }

        print(G::json_encode($userRepeat));
    }
}
