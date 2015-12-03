<?php
namespace ProcessMaker\BusinessModel;

use \G;
use Symfony\Component\Config\Definition\Exception\Exception;

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
     * Get Application data by appUid. Searches just in list_inbox, list_paused, list_unassigned
     * because those are the only places from which an email to update user gmail trays will
     * be sent.
     * the reason to seach in thos 3 tables is because We prefer to make 3 queries in
     * 3 tables with few rows than using app_cache_view that has
     * a lot more.
     *
     * @param string $appUid Unique id of the app
     * @param string $index
     *
     * return row in list_inbox, list_paused or list_unassigned, if nothing is
     * found a null value is returned
     *
     */
    public function getAppData($appUid, $index=1)
    {
        if (empty($appUid)) {
           throw new Exception('Null or undefined appUids are not accepted.');
        }

        //the current version of Propel does not support union  queries, so
        //3 queries are sent. The first time that a row that conforms to the
        //seach criteria is found, the function terminates and returns this row.

        $rowInbox = $this->getAppDataFromListInbox($appUid, $index);
        if (count($rowInbox) > 0) {
            return $rowInbox;
        }

        $rowUnassigned = $this->getAppDataFromListUnassigned($appUid, $index);
        if (count($rowUnassigned) > 0) {
            return $rowUnassigned;
        }

        $rowPaused = $this->getAppDataFromListPaused($appUid, $index);
        if (count($rowPaused) > 0) {
            return $rowPaused;
        }

        return null;
    }

    /**
     * Get Application data in a del index searchin in the list_inbox table
     *
     * @param string $appUid Unique id of the app
     * @param string $index
     *
     * return row
     *
     */
    private function getAppDataFromListInbox($appUid, $index) {
        $c = new \Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( \ListInboxPeer::APP_NUMBER );
        $c->addSelectColumn( \ListInboxPeer::APP_STATUS );
        $c->addSelectColumn( \ListInboxPeer::DEL_INDEX );
        $c->addSelectColumn( \ListInboxPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn( \ListInboxPeer::USR_UID );
        $c->addSelectColumn( \ListInboxPeer::PRO_UID );
        $c->addSelectColumn( \ListInboxPeer::APP_PRO_TITLE );
        $c->addSelectColumn( \ListInboxPeer::APP_TAS_TITLE );
        $c->addSelectColumn( \ListInboxPeer::TAS_UID );
        $c->add( \ListInboxPeer::APP_UID, $appUid );
        $c->add( \ListInboxPeer::DEL_INDEX, $index );

        $rs = \ListInboxPeer::doSelectRS( $c );
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        return $this->resultSetToArray($rs);
    }


    /**
     * Get Application data in a del index searchin in the list_inbox table
     *
     * @param string $appUid Unique id of the app
     * @param string $index
     *
     * return row
     *
     */
    private function getAppDataFromListPaused($appUid, $index) {
        $c = new \Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( \ListPausedPeer::APP_NUMBER );
        $c->addSelectColumn("'PAUSED' APP_STATUS");
        $c->addSelectColumn( \ListPausedPeer::DEL_INDEX );
        $c->addSelectColumn( \ListPausedPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn( \ListPausedPeer::USR_UID );
        $c->addSelectColumn( \ListPausedPeer::PRO_UID );
        $c->addSelectColumn( \ListPausedPeer::APP_PRO_TITLE );
        $c->addSelectColumn( \ListPausedPeer::APP_TAS_TITLE );
        $c->addSelectColumn( \ListPausedPeer::TAS_UID );
        $c->add( \ListPausedPeer::APP_UID, $appUid );
        $c->add( \ListPausedPeer::DEL_INDEX, $index );

        $rs = \ListPausedPeer::doSelectRS( $c );
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        return $this->resultSetToArray($rs);
    }
    /**
     * Get Application data in a del index searchin in the list_inbox table
     *
     * @param string $appUid Unique id of the app
     * @param string $index
     *
     * return row
     *
     */
    private function getAppDataFromListUnassigned($appUid, $index) {
        $c = new \Criteria( 'workflow' );
        $c->clearSelectColumns();
        $c->addSelectColumn( \ListUnassignedPeer::APP_NUMBER );
        $c->addSelectColumn("'UNASSIGNED' APP_STATUS");
        $c->addSelectColumn( \ListUnassignedPeer::DEL_INDEX );
        $c->addSelectColumn( \ListUnassignedPeer::DEL_DELEGATE_DATE );
        $c->addSelectColumn("'' USR_UID");
        $c->addSelectColumn( \ListUnassignedPeer::PRO_UID );
        $c->addSelectColumn( \ListUnassignedPeer::APP_PRO_TITLE );
        $c->addSelectColumn( \ListUnassignedPeer::APP_TAS_TITLE );
        $c->addSelectColumn( \ListUnassignedPeer::TAS_UID );
        $c->add( \ListUnassignedPeer::APP_UID, $appUid );
        $c->add( \ListUnassignedPeer::DEL_INDEX, $index );

        $rs = \ListUnassignedPeer::doSelectRS( $c );
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        return $this->resultSetToArray($rs);
    }

    /**
     * Returns an array create from a resultset
     *
     * @param string $rs result set from which the array will be created
     *
     * return row array
     *
     */
    private function resultSetToArray($rs) {
        $rows = Array ();
        while ($rs->next()) {
            $rows[] = $rs->getRow();
        }
        return $rows;
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
    public function sendEmail($app_uid, $mail, $index)
    {
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Application.php");
        $oApplication = new \Application();
        $formData = $oApplication->Load($app_uid);

        $frmData = unserialize($formData['APP_DATA']);
        $dataFormToShowString = "";
        foreach ($frmData as $field=>$value){
            if( ($field != 'SYS_LANG') &&
                ($field != 'SYS_SKIN') &&
                ($field != 'SYS_SYS') &&
                ($field != 'APPLICATION') &&
                ($field != 'PROCESS') &&
                ($field != 'TASK') &&
                ($field != 'INDEX') &&
                ($field != 'USER_LOGGED') &&
                ($field != 'USR_USERNAME') &&
                ($field != 'DYN_CONTENT_HISTORY') &&
                ($field != 'PIN') ){
                $dataFormToShowString .= " " . $field . " " . $value;
            }
        }
        $appData = $this->getAppData($app_uid, $index);

        foreach ($appData as $application){
            $appNumber = $application['APP_NUMBER'];
            $appStatus = $application['APP_STATUS'];
            $index = $application['DEL_INDEX'];
            $delegateDate = $application['DEL_DELEGATE_DATE'];
            $nextUsr = $application['USR_UID'];
            $proUid = $application['PRO_UID'];
            $proName = $application['APP_PRO_TITLE'];
            $tasName = $application['APP_TAS_TITLE'];
            $tasUid = $application['TAS_UID'];

            if($appStatus == "DRAFT"){
                $labelID = "PMDRFT";
            } else {
                $labelID = "PMIBX";
            }

            if($mail == ""){
                require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
                $oUsers = new \Users();

                if($nextUsr == ""){
                    //Unassigned:
                    $mail = "";

                    require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "TaskUser.php");
                    $oTaskUsers = new \TaskUser();

                    $taskUsers = $oTaskUsers->getAllUsersTask($tasUid);
                    foreach ($taskUsers as $user){
                        $usrData = $oUsers->loadDetails($user['USR_UID']);
                        $nextMail = $usrData['USR_EMAIL'];
                        $mail .= ($mail == '') ? $nextMail : ','. $nextMail;
                    }
                    $labelID = "PMUASS";
                }else {
                    $usrData = $oUsers->loadDetails($nextUsr);
                    $mail = $usrData['USR_EMAIL'];
                }
            }

            //first template
            $pathTemplate = PATH_DATA_SITE . "mailTemplates" . PATH_SEP . "pmGmail.html";
            if (!file_exists($pathTemplate)){
                $file = @fopen($pathTemplate, "w");
                fwrite($file, '<div>');
                fwrite($file, '<span style="display: none !important;">');
                fwrite($file, '-**- Process Name: @#proName<br/>');
                fwrite($file, '-**- Case Number: @#appNumber<br/>');
                fwrite($file, '-**- Case UID: @#caseUid<br/>');
                fwrite($file, '-**- Task Name: @#taskName<br/>');
                fwrite($file, '-**- Index: @#index<br/>');
                fwrite($file, '-**- Action: @#action<br/>');
                fwrite($file, '-**- Delegate Date: @#delDate<br/>');
                fwrite($file, '-**- Process Id: @#proUid<br/>');
                fwrite($file, '-**- Type: @#type<br/>');
                fwrite($file, '-**- FormFields: @@oform<br/>');
                fwrite($file, '</span>');
                fwrite($file, '</div>');
                fclose($file);
            }

            $change = array('[', ']', '"');
            $fdata = str_replace($change, ' ', $dataFormToShowString);
            $aFields = array('proName' => $proName,
                'appNumber' => $appNumber,
                'caseUid' => $app_uid,
                'taskName' => $tasName,
                'index' => $index,
                'action' => $appStatus,
                'delDate' => $delegateDate,
                'proUid' => $proUid,
                'type' => $labelID,
                'oform' => $fdata
            );

            $subject = "[PM] " .$proName. " (" . $index . ") Case: ". $appNumber;

            require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.wsBase.php");
            $ws = new \wsBase();
            $resultMail = $ws->sendMessage(
                $app_uid,
                'inbox.pm@processmaker.com', //From,
                $mail,//To,
                '',
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
            return $resultMail;
        }
        return 'The appUid cant be founded';
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

}



