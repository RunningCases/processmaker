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
        $pmGoogle = new PmGoogleApi();
        $result = new StdClass();
        $result->success = true;

        $httpData->serviceGmailStatus = !empty($httpData->serviceGmailStatus) ? $httpData->serviceGmailStatus == 1 ? true : false : false;
        $httpData->serviceDriveStatus = !empty($httpData->serviceDriveStatus) ? $httpData->serviceDriveStatus == 1 ? true : false : false;

        if ($httpData->serviceGmailStatus || $httpData->serviceDriveStatus) {
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
            $pmGoogle->setServiceGmailStatus(false);
            $pmGoogle->setServiceDriveStatus(false);
            $message = G::LoadTranslation('ID_ENABLE_PMGMAIL') . ': ' . G::LoadTranslation('ID_DISABLE');
        }
        G::auditLog("Update Settings Gmail", $message);

        print(G::json_encode($result));
    }

    public function formPMGmail()
    {
        try {
            $this->includeExtJS('admin/pmGmail');
            if (!empty($_SESSION['__PMGMAIL_ERROR__'])) {
                $this->setJSVar('__PMGMAIL_ERROR__', $_SESSION['__PMGMAIL_ERROR__']);
                unset($_SESSION['__PMGMAIL_ERROR__']);
            }
            $pmGoogle = new PmGoogleApi();
            $accountEmail = $pmGoogle->getServiceAccountEmail();
            $googleCertificate = $pmGoogle->getServiceAccountCertificate();
            $statusGmail = $pmGoogle->getServiceGmailStatus();
            $statusDrive = $pmGoogle->getServiceDriveStatus();
            $disableGmail = true;
            $disableDrive = true;

            $licensedFeatures = PMLicensedFeatures::getSingleton();
            if ($licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
                $disableGmail = false;
            }
            if ($licensedFeatures->verifyfeature('AhKNjBEVXZlWUFpWE8wVTREQ0FObmo0aTdhVzhvalFic1M=')) {
                $disableDrive = false;
            }

            $this->setJSVar('accountEmail', $accountEmail);
            $this->setJSVar('googleCertificate', $googleCertificate);
            $this->setJSVar('statusGmail', $statusGmail);
            $this->setJSVar('statusDrive', $statusDrive);
            $this->setJSVar('disableGmail', $disableGmail);
            $this->setJSVar('disableDrive', $disableDrive);


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
        $pmGoogle = new PmGoogleApi();

        $result = new stdClass();

        $result->emailServiceAccount = empty($httpData->emailServiceAccount) ? $pmGoogle->getServiceAccountEmail() : $httpData->emailServiceAccount;
        $result->pathServiceAccountCertificate = empty($_FILES['googleCertificate']['tmp_name']) ? PATH_DATA_SITE . $pmGoogle->getServiceAccountCertificate() : $_FILES['googleCertificate']['tmp_name'];

        print(G::json_encode($pmGoogle->testService($result)));
    }

    /**
     * Search users with same email
     */
    public function testUserGmail()
    {
        $criteria = new Criteria();
        $criteria->clearSelectColumns();
        $criteria->addSelectColumn('COUNT(*) AS NUM_EMAIL');

        $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
        $criteria->addGroupByColumn(UsersPeer::USR_EMAIL);

        $criteria->add(UsersPeer::USR_STATUS, 'ACTIVE');

        $rs = UsersPeer::doSelectRS($criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $userRepeat = [];
        while ($rs->next()) {
            $row = $rs->getRow();
            if ($row['NUM_EMAIL'] > 1) {
                $criteriaUsers = new Criteria();
                $criteriaUsers->clearSelectColumns();
                $criteriaUsers->addSelectColumn(UsersPeer::USR_UID);
                $criteriaUsers->addSelectColumn(UsersPeer::USR_FIRSTNAME);
                $criteriaUsers->addSelectColumn(UsersPeer::USR_LASTNAME);
                $criteriaUsers->addSelectColumn(UsersPeer::USR_EMAIL);

                $criteriaUsers->add(UsersPeer::USR_EMAIL, $row['USR_EMAIL']);
                $criteriaUsers->add(UsersPeer::USR_STATUS, 'ACTIVE');

                $rsUsers = UsersPeer::doSelectRS($criteriaUsers);
                $rsUsers->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($rsUsers->next()) {
                    $rowUser = $rsUsers->getRow();

                    array_push(
                        $userRepeat,
                        [
                            'USR_UID' => $rowUser['USR_UID'],
                            'FULL_NAME' => $rowUser['USR_FIRSTNAME'] . ' ' . $rowUser['USR_LASTNAME'],
                            'EMAIL' => $rowUser['USR_EMAIL']
                        ]
                    );
                }
            }
        }

        print(G::json_encode($userRepeat));
    }
}
