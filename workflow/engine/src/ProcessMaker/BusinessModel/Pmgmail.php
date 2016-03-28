<?php
namespace ProcessMaker\BusinessModel;
require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Task.php");

use \G;

/**
 * @copyright Colosa - Bolivia
 */
class Pmgmail {

    /**
     * Get User by usrGmail
     *
     * @param string $usr_gmail Unique id of User
     *
     * return uid
     *
     */
    public function getUserByEmail($usr_gmail)
    {
        //getting the user data
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
        $oUsers = new \Users();
        $response['user'] = $oUsers->loadByUserEmailInArray($usr_gmail);

        //getting the skin
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.system.php");
        $sysConf = new \System();
        $responseSysConfig = $sysConf->getSystemConfiguration( PATH_CONFIG . 'env.ini' );
        $response['enviroment'] = $responseSysConfig['default_skin'];

        return $response;
    }

    /**
     * Get Application data by appUid
     *
     * @param string $app_uid Unique id of the app
     * @param string $index
     *
     * return row app_cache_view
     *
     */
    public function getDraftApp($app_uid, $index=1)
    {
        $c = new \Criteria( 'workflow' );

        $c->clearSelectColumns();
        $c->addSelectColumn( \AppCacheViewPeer::APP_NUMBER );
        $c->addSelectColumn( \AppCacheViewPeer::APP_STATUS );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_INDEX );
        $c->addSelectColumn( \AppCacheViewPeer::APP_DEL_PREVIOUS_USER );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn( \AppCacheViewPeer::USR_UID );
        $c->addSelectColumn( \AppCacheViewPeer::PRO_UID );
        $c->addSelectColumn( \AppCacheViewPeer::APP_PRO_TITLE );
        $c->addSelectColumn( \AppCacheViewPeer::APP_TAS_TITLE );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_THREAD_STATUS );
        $c->addSelectColumn( \AppCacheViewPeer::TAS_UID );
        $c->addSelectColumn( \AppCacheViewPeer::DEL_LAST_INDEX );
        $c->addSelectColumn( \AppCacheViewPeer::APP_UID );

        $c->add( \AppCacheViewPeer::APP_UID, $app_uid );
        $c->add( \AppCacheViewPeer::DEL_INDEX, $index );

        $rs = \AppCacheViewPeer::doSelectRS( $c );
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

        $rows = Array ();
        while ($rs->next()) {
            $rows[] = $rs->getRow();
        }
        return $rows;
    }

    public function gmailsForRouting($sUsrUid, $sTasUid, $sAppUid, $delIndex, $isSubprocess) {

        $taskProxy =  new \Task();
        $taskData = $taskProxy-> load($sTasUid);

        //guard condition, message events do not need to send emails
        if ($taskData['TAS_TYPE'] === 'START-MESSAGE-EVENT') {
            return;
        }

        if($sUsrUid === "")  {
            $targetEmails = $this->targetEmailsForUnassigned($sTasUid, $sAppUid);
            if ($targetEmails['to'] !== "" && $targetEmails['to'] !== null ) {
                $this->sendGmail($sAppUid, $targetEmails['to'].','.$targetEmails['cc'], $delIndex, $isSubprocess, true, null, null);
            }
        }
        else {
            $userObject = new \Users();
            $userData = $userObject->loadDetails($sUsrUid);
            if ($userData !== null) {
                $this->sendGmail($sAppUid, $userData['USR_EMAIL'], $delIndex, $isSubprocess, false, null, null);
            }
        }
    }

    public function gmailsIfSelfServiceValueBased($app_uid, $index, $arrayTask, $arrayData)
    {
        require_once(PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Application.php");
        $resultMail = "";
        foreach ($arrayTask as $aTask) {
            //just self service tasks are processed in this function
            if ($aTask ["TAS_ASSIGN_TYPE"] !== "SELF_SERVICE") {
                continue;
            }
            $appData = $this->getDraftApp($app_uid, $index);
            if (count($appData) === 0) {
                return "appUid not found";
            }
            if (!isset ($aTask ["USR_UID"])) {
                $aTask ["USR_UID"] = "";
            }
            $oCases = new \Cases ();
            $application = $appData[0];

            $respTo = $oCases->getTo($aTask ["TAS_ASSIGN_TYPE"], $aTask ["TAS_UID"], $aTask ["USR_UID"], $arrayData);
            $mailToAddresses = $respTo ['to'];
            $mailCcAddresses = $respTo ['cc'];
            $labelID = "PMUASS";

            if (( string )$mailToAddresses === "") { // Self Service Value Based
                $isSelfServiceValueBased = true;
                $criteria = new \Criteria ("workflow");
                $criteria->addSelectColumn(\AppAssignSelfServiceValuePeer::GRP_UID);
                $criteria->add(\AppAssignSelfServiceValuePeer::APP_UID, $app_uid);

                $rsCriteria = \AppAssignSelfServiceValuePeer::doSelectRs($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();
                }
                $targetIds = unserialize($row ['GRP_UID']);
                $oUsers = new \Users ();

                if (is_array($targetIds)) {
                    foreach ($targetIds as $user) {
                        $usrData = $oUsers->loadDetails($user);
                        $nextMail = $usrData ['USR_EMAIL'];
                        $mailToAddresses .= ($mailToAddresses == '') ? $nextMail : ',' . $nextMail;
                    }
                } else {
                    $group = new \Groups();
                    $users = $group->getUsersOfGroup($targetIds);
                    foreach ($users as $user) {
                        $nextMail = $user['USR_EMAIL'];
                        $mailToAddresses .= ($mailToAddresses == '') ? $nextMail : ',' . $nextMail;
                    }
                }
                $resultMail = $this->sendEmailWithApplicationData($application,  $labelID, $mailToAddresses, $mailCcAddresses);
            }
        }
        return $resultMail;
    }

    /**
     * Send email using appUid and mail
     *
     * @param string $app_uid Unique id of the app
     * @param string $mail
     *
     * return uid
     *
     */
    public function sendGmail($app_uid, $mailToAddresses, $index, $isSubprocess = false, $isSelfService = false, $arrayTask = null, $arrayData = null)
    {
        //getting the default email server
        $defaultEmail = $this->emailAccount();
        if ($defaultEmail === null) {
            error_log(G::LoadTranslation('ID_EMAIL_ENGINE_IS_NOT_ENABLED'));
            return false;
        }

        $mailCcAddresses = "";

        $appData =  $this->getDraftApp($app_uid, $index);
        if (count($appData) === 0) {
            return;
        }
        $application = $appData[0];
        $this->sendEmailWithApplicationData($application,
                $this->getEmailType($index, $isSubprocess, $isSelfService),
                $mailToAddresses,
                $mailCcAddresses);
    }

    /**
     * Get if the license has the feature
     *
     * return uid
     *
     */
    public function hasGmailFeature()
    {
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.licensedFeatures.php");

        $licensedFeatures = new \PMLicensedFeatures();
        if (!$licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
            return false;
        }else {
            return true;
        }
    }

    /**
     * Get the default 'email from account' that is used to send emails in the server email in PM
     *
     * return uid
     *
     */
    public function emailAccount()
    {
        $emailServer = new \EmailServer();
        $response = $emailServer->loadDefaultAccount();

        return $response['MESS_ACCOUNT'];
    }

    /**
     * Business Model to delete all the labels of an acount
     *
     * @param string $mail
     *
     * return uid
     *
     */
    public function deleteLabels($mail)
    {
        require_once(PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.labelsGmail.php");
        $oLabels = new \labelsGmail();

        $response = $oLabels->deletePMGmailLabels($mail);

        return $response;
    }

    public function modifyMailToPauseCase($appUid, $appDelIndex)
    {
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.labelsGmail.php");
        $oLabels = new \labelsGmail();
        $oResponse = $oLabels->setLabelsToPauseCase($appUid, $appDelIndex);
    }

    public function modifyMailToUnpauseCase($appUid, $appDelIndex)
    {
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.labelsGmail.php");
        $oLabels = new \labelsGmail();
        $oResponse = $oLabels->setLabelsToUnpauseCase($appUid, $appDelIndex);
    }

    private function getEmailType($index, $isSubprocess, $isSelfService) {
        $retval = "";
        if ($isSelfService) {
            $retval = "PMUASS";
        }
        else {
            $retval = ($index === 1 && !$isSubprocess) ? "PMDRFT" : "PMIBX";
        }
        if ($retval === "") {
            throw new Exception('Is not possible to determine the email type');
        }
        return $retval;
    }

    private function createMailTemplateFile()
    {
        $pathTemplate = PATH_DATA_SITE . "mailTemplates" . PATH_SEP . "pmGmail.html";
        if (!file_exists($pathTemplate)) {
            $file = @fopen($pathTemplate, "w");
            fwrite($file, '<div>');
            fwrite($file, '<span style="display: none !important;">');
            fwrite($file, '-**- Process Name: @#proName<br/>');
            fwrite($file, '-**- Case Number: @#appNumber<br/>');
            fwrite($file, '-**- Case UID: @#caseUid<br/>');
            fwrite($file, '-**- Task Name: @#taskName<br/>');
            fwrite($file, '-**- Index: @#index<br/>');
            fwrite($file, '-**- Action: @#action<br/>');
            fwrite($file, '-**- Previous User: @#prevUser<br/>');
            fwrite($file, '-**- Delegate Date: @#delDate<br/>');
            fwrite($file, '-**- Process Id: @#proUid<br/>');
            fwrite($file, '-**- Type: @#type<br/>');
            fwrite($file, '-**- FormFields: @@oform<br/>');
            fwrite($file, '</span>');
            fwrite($file, '</div>');
            fclose($file);
        }
    }

    private function getFormData($appUid, $index) {
        $oApplication = new \Application();
        $formData = $oApplication->Load($appUid);

        $frmData = unserialize($formData['APP_DATA']);
        $dataFormToShowString = "";
        foreach ($frmData as $field => $value) {
            if (($field != 'SYS_LANG') &&
                ($field != 'SYS_SKIN') &&
                ($field != 'SYS_SYS') &&
                ($field != 'APPLICATION') &&
                ($field != 'PROCESS') &&
                ($field != 'TASK') &&
                ($field != 'INDEX') &&
                ($field != 'USER_LOGGED') &&
                ($field != 'USR_USERNAME') &&
                ($field != 'DYN_CONTENT_HISTORY') &&
                ($field != 'PIN') &&
                (!is_array($value))
            ) {
                $dataFormToShowString .= " " . $field . " " . $value;
            }
        }
        $change = array('[', ']', '"');
        $fdata = str_replace($change, ' ', $dataFormToShowString);
        return $fdata;
    }

    private function sendEmailWithApplicationData ($application,  $emailTypeLabel, $mailToAddresses, $mailCcAddresses) {
        $dataFormToShowString = $this->getFormData($application['APP_UID'],$application['DEL_INDEX']);
        $this->createMailTemplateFile();
        $change = array('[', ']', '"');
        $fdata = str_replace($change, ' ', $dataFormToShowString);
        $aFields = array(
            'proName' => $application['APP_PRO_TITLE'],
            'appNumber' => $application['APP_NUMBER'],
            'caseUid' => $application['APP_UID'],
            'taskName' => $application['APP_TAS_TITLE'],
            'index' =>  $application['DEL_INDEX'],
            'action' => $application['APP_STATUS'],
            'prevUser' => $application['APP_DEL_PREVIOUS_USER'],
            'delDate' => $application['DEL_DELEGATE_DATE'],
            'proUid' => $application['PRO_UID'],
            'type' => $emailTypeLabel,
            'oform' => $fdata
        );

        $subject = "[PM] " . $application['APP_PRO_TITLE'] . " (" . $application['DEL_INDEX'] . ") Case: " . $application['APP_NUMBER'];

        //getting the default email server
        $defaultEmail = $this->emailAccount();

        if ($defaultEmail === null) {
            error_log(G::LoadTranslation('ID_EMAIL_ENGINE_IS_NOT_ENABLED'));
            return false;
        }

        $ws = new \wsBase();
        $resultMail = $ws->sendMessage(
            $application['APP_UID'],
            $defaultEmail, //From,
            $mailToAddresses,//$To,
            $mailCcAddresses,//$Cc
            '',
            $subject,
            'pmGmail.html',//template
            $aFields, //fields
            array(),
            true,
            0,
            array(),
            1
        );
    }

    private function targetEmailsForUnassigned($taskUid, $appUid)
    {
        $sTo = null;
        $sCc = null;
        $arrayResp = array ();
        $task = new \Tasks ();
        $group = new \Groups ();
        $oUser = new \Users ();

        $to = null;
        $cc = null;

        if (isset ( $taskUid ) && ! empty ( $taskUid )) {
            $arrayTaskUser = array ();

            $arrayAux1 = $task->getGroupsOfTask ( $taskUid, 1 );

            foreach ( $arrayAux1 as $arrayGroup ) {
                $arrayAux2 = $group->getUsersOfGroup ( $arrayGroup ["GRP_UID"] );

                foreach ( $arrayAux2 as $arrayUser ) {
                    $arrayTaskUser [] = $arrayUser ["USR_UID"];
                }
            }

            $arrayAux1 = $task->getUsersOfTask ( $taskUid, 1 );

            foreach ( $arrayAux1 as $arrayUser ) {
                $arrayTaskUser [] = $arrayUser ["USR_UID"];
            }

            $arrayTaskUser = array_unique($arrayTaskUser);

            $criteria = new \Criteria ( "workflow" );

            $criteria->addSelectColumn ( \UsersPeer::USR_UID );
            $criteria->addSelectColumn ( \UsersPeer::USR_USERNAME );
            $criteria->addSelectColumn ( \UsersPeer::USR_FIRSTNAME );
            $criteria->addSelectColumn ( \UsersPeer::USR_LASTNAME );
            $criteria->addSelectColumn ( \UsersPeer::USR_EMAIL );
            $criteria->add (\UsersPeer::USR_UID, $arrayTaskUser, \Criteria::IN);
            $rsCriteria = \UsersPeer::doSelectRs ( $criteria );
            $rsCriteria->setFetchmode (\ResultSet::FETCHMODE_ASSOC);

            $sw = 1;

            while ( $rsCriteria->next () ) {
                $row = $rsCriteria->getRow ();

                $toAux = ((($row ["USR_FIRSTNAME"] != "") || ($row ["USR_LASTNAME"] != "")) ? $row ["USR_FIRSTNAME"] . " " . $row ["USR_LASTNAME"] . " " : "") . "<" . $row ["USR_EMAIL"] . ">";

                if ($sw == 1) {
                    $to = $toAux;
                    $sw = 0;
                } else {
                    $cc = $cc . (($cc != null) ? "," : null) . $toAux;
                }
            }


            $arrayResp ['to'] = $to;
            $arrayResp ['cc'] = $cc;
        }
        return $arrayResp;
    }
}

