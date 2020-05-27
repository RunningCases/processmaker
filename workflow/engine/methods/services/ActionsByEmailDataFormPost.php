<?php

/**
 * @see workflow/engine/methods/services/ActionsByEmailDataForm.php
 * @link https://wiki.processmaker.com/3.3/Actions_by_Email#Link_to_Fill_a_Form
 */
use App\Jobs\ActionByEmail;
use ProcessMaker\Core\JobsManager;
use ProcessMaker\Validation\ValidationUploadedFiles;

$featureEnable = PMLicensedFeatures::getSingleton()
        ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=');
if ($featureEnable) {

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
        return;
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
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        $files = $_FILES;

        //Now we dispatch the derivation of the case through Jobs Laravel.
        $closure = function() use ($appUid, $delIndex, $aber, $dynUid, $forms, $remoteAddr, $files) {
            $cases = new Cases();
            $cases->routeCaseActionByEmail($appUid, $delIndex, $aber, $dynUid, $forms, $remoteAddr, $files);
        };
        JobsManager::getSingleton()->dispatch(ActionByEmail::class, $closure);

        $message = [];
        $message['MESSAGE'] = '<strong>' . G::loadTranslation('ID_ABE_INFORMATION_SUBMITTED') . '</strong>';
        $_SESSION = unserialize($backupSession);
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showInfo', '', $message);
    } catch (Exception $error) {
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', ['MESSAGE' => $error->getMessage() . ' Please contact to your system administrator.']);
    }
    $_SESSION = unserialize($backupSession);
    G::RenderPage('publish', 'blank');
}