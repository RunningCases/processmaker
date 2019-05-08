<?php

/**
 * @see workflow/engine/methods/services/ActionsByEmailDataForm.php
 * @link https://wiki.processmaker.com/3.3/Actions_by_Email#Link_to_Fill_a_Form
 */

use ProcessMaker\BusinessModel\Cases\InputDocument;
use ProcessMaker\ChangeLog\ChangeLog;
use ProcessMaker\Validation\ValidationUploadedFiles;

if (PMLicensedFeatures::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
    
    /**
     * To do: The following evaluation must be moved after saving the data (so as not to lose the data entered in the form).
     * It only remains because it is an old behavior, which must be defined by "Product Owner".
     * @see workflow/engine/methods/cases/cases_SaveData.php
     */
    $validator = ValidationUploadedFiles::getValidationUploadedFiles()->runRulesForFileEmpty();
    if ($validator->fails()) {
        G::SendMessageText($validator->getMessage(), "ERROR");
        $url = explode("sys" . config("system.workspace"), $_SERVER['HTTP_REFERER']);
        G::header("location: " . "/sys" . config("system.workspace") . $url[1]);
        die();
    }

    $G_PUBLISH = new Publisher();
    try {

        $backupSession = serialize($_SESSION);

        if (empty($_GET['APP_UID'])) {
            $sw = empty($_REQUEST['APP_UID']);
            if (!$sw && !G::verifyUniqueID32($_REQUEST['APP_UID'])) {
                $_GET['APP_UID'] = $_REQUEST['APP_UID'];
            }
            if ($sw) {
                throw new Exception('The parameter APP_UID is empty.');
            }
        }

        if (empty($_REQUEST['DEL_INDEX'])) {
            throw new Exception('The parameter DEL_INDEX is empty.');
        }

        if (empty($_REQUEST['ABER'])) {
            throw new Exception('The parameter ABER is empty.');
        }

        $appUid = G::decrypt($_GET['APP_UID'], URL_KEY);
        $delIndex = G::decrypt($_REQUEST['DEL_INDEX'], URL_KEY);
        $aber = G::decrypt($_REQUEST['ABER'], URL_KEY);
        $dynUid = G::decrypt($_REQUEST['DYN_UID'], URL_KEY);
        $forms = isset($_REQUEST['form']) ? $_REQUEST['form'] : [];

        //Load data related to the case
        $case = new Cases();
        $casesFields = $case->loadCase($appUid, $delIndex);
        $casesFields['APP_DATA'] = array_merge($casesFields['APP_DATA'], $forms);

        //Get current user info
        $delegation = new AppDelegation();
        $currentUsrUid = $delegation->getUserAssignedInThread($appUid, $delIndex);
        if (!is_null($currentUsrUid)) {
            $users = new Users();
            $userInfo = $users->loadDetails($currentUsrUid);
            $casesFields["APP_DATA"]["USER_LOGGED"] = $currentUsrUid;
            $casesFields["APP_DATA"]["USR_USERNAME"] = $userInfo['USR_USERNAME'];
        }

        foreach ($casesFields["APP_DATA"] as $index => $value) {
            $_SESSION[$index] = $value;
        }

        $casesFields['CURRENT_DYNAFORM'] = $dynUid;
        $casesFields['USER_UID'] = $casesFields['CURRENT_USER_UID'];

        ChangeLog::getChangeLog()
                ->getUsrIdByUsrUid($casesFields['USER_UID'], true)
                ->setSourceId(ChangeLog::FromABE);

        //Update case info
        $case->updateCase($appUid, $casesFields);
        if (isset($_FILES ['form'])) {
            if (isset($_FILES["form"]["name"]) && count($_FILES["form"]["name"]) > 0) {
                $oInputDocument = new InputDocument();
                $oInputDocument->uploadFileCase($_FILES, $case, $casesFields, $currentUsrUid, $appUid, $delIndex);
            }
        }
        $wsBaseInstance = new WsBase();
        $result = $wsBaseInstance->derivateCase(
                $casesFields['CURRENT_USER_UID'], $appUid, $delIndex, true
        );
        $code = (is_array($result) ? $result['status_code'] : $result->status_code);

        $dataResponses = array();
        $dataResponses['ABE_REQ_UID'] = $aber;
        $dataResponses['ABE_RES_CLIENT_IP'] = $_SERVER['REMOTE_ADDR'];
        $dataResponses['ABE_RES_DATA'] = serialize($forms);
        $dataResponses['ABE_RES_STATUS'] = 'PENDING';
        $dataResponses['ABE_RES_MESSAGE'] = '';

        try {
            require_once 'classes/model/AbeResponses.php';

            $abeAbeResponsesInstance = new AbeResponses();
            $dataResponses['ABE_RES_UID'] = $abeAbeResponsesInstance->createOrUpdate($dataResponses);
        } catch (Exception $error) {
            throw $error;
        }

        if ($code == 0) {
            //Save Cases Notes
            $dataAbeRequests = loadAbeRequest($aber);
            $dataAbeConfiguration = loadAbeConfiguration($dataAbeRequests['ABE_UID']);

            if ($dataAbeConfiguration['ABE_CASE_NOTE_IN_RESPONSE'] == 1) {
                $response = new stdclass();
                $response->usrUid = $casesFields['APP_DATA']['USER_LOGGED'];
                $response->appUid = $appUid;
                $response->delIndex = $delIndex;
                $response->noteText = "Check the information that was sent for the receiver: " . $dataAbeRequests['ABE_REQ_SENT_TO'];
                postNote($response);
            }

            $dataAbeRequests['ABE_REQ_ANSWERED'] = 1;
            $code == 0 ? uploadAbeRequest($dataAbeRequests) : '';

            $assign = $result['message'];
            $aMessage['MESSAGE'] = '<strong>' . G::loadTranslation('ID_ABE_INFORMATION_SUBMITTED') . '</strong>';
        } else {
            throw new Exception('An error occurred while the application was being processed.<br /><br />
                                 Error code: ' . $result->status_code . '<br />
                                 Error message: ' . $result->message . '<br /><br />');
        }

        // Update
        $dataResponses['ABE_RES_STATUS'] = ($code == 0 ? 'SENT' : 'ERROR');
        $dataResponses['ABE_RES_MESSAGE'] = ($code == 0 ? '-' : $result->message);

        try {
            $abeAbeResponsesInstance = new AbeResponses();
            $abeAbeResponsesInstance->createOrUpdate($dataResponses);
        } catch (Exception $error) {
            throw $error;
        }

        $_SESSION = unserialize($backupSession);
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showInfo', '', $aMessage);
    } catch (Exception $error) {
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', array('MESSAGE' => $error->getMessage() . ' Please contact to your system administrator.'));
    }
    $_SESSION = unserialize($backupSession);
    G::RenderPage('publish', 'blank');
}

