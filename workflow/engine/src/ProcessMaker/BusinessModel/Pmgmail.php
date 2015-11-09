<?php
namespace ProcessMaker\BusinessModel;

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
        require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
        $oUsers = new \Users();

        $response = $oUsers->loadByUserEmailInArray($usr_gmail);

        return $response;
    }

    /**
     * Post Token by usrGmail
     *
     * @param string $request_data
     *
     * return token
     *
     */
    public function postTokenbyEmail($request_data)
    {
        //Lets verify the gmail token
        $url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$request_data['token'];

        // init curl object
        $ch = curl_init();
        // define options
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );
        // apply those options
        curl_setopt_array($ch, $optArray);
        // execute request and get response
        $result = curl_exec($ch);
        $response = (json_decode($result));
        // Check if any error occurred
        if(curl_errno($ch))
        {
            throw (new \Exception('The url is not valid.'));
        }
        $info = curl_getinfo($ch);
        curl_close($ch);

        //If there is response
        if($info['http_code'] == 200 && isset($response->email)){
            //If the usermail that was send in the end point es the same of the one in the response
            if($request_data['mail'] == $response->email){
                $oUsers = new \Users();
                $userExist = $oUsers->loadByUserEmailInArray($request_data['mail']);
                if(count($userExist) == 1){
                    if($userExist['0']['USR_STATUS'] == "ACTIVE"){
                        //User Active! lets create the token and register it in the DB for this user
                        $oauthServer = new \ProcessMaker\Services\OAuth2\Server;
                        $server = $oauthServer->getServer();
                        $config = array(
                            'allow_implicit' => $server->getConfig('allow_implicit'),
                            'access_lifetime' => $server->getConfig('access_lifetime')
                        );
                        $storage = $server->getStorages();
                        $accessToken = new \OAuth2\ResponseType\AccessToken($storage['access_token'],$storage['refresh_token'],$config);
                        $token = $accessToken->createAccessToken($request_data['clientid'], $userExist['0']['USR_UID'],$request_data['scope']);
                    }else {
                        throw (new \Exception('The user is not ACTIVE!'));
                    }
                }else{
                    throw (new \Exception('This email is assigned to more than one user. Please contact your administrator.'));
                    die;
                }
            } else {
                throw (new \Exception('The email does not corresponds to the token gmail user.'));
            }
        }else {
            throw (new \Exception('The gmail token is not valid.'));
        }
        return $token;
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
        $response = \AppCacheViewQuery::create()
            ->filterByAppUid($app_uid)
            ->filterByDelIndex($index)
            ->find()
            ->toArray(null, false, \BasePeer::TYPE_FIELDNAME);

        return $response;
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
        $appData = $this->getDraftApp($app_uid, $index);

        foreach ($appData as $application){
            $appNumber = $application['APP_NUMBER'];
            $appStatus = $application['APP_STATUS'];
            $index = $application['DEL_INDEX'];
            $prvUsr = $application['APP_DEL_PREVIOUS_USER'];
            $delegateDate = $application['DEL_DELEGATE_DATE'];
            $nextUsr = $application['USR_UID'];
            $proUid = $application['PRO_UID'];
            $proName = $application['APP_PRO_TITLE'];
            $tasName = $application['APP_TAS_TITLE'];
            $threadStatus = $application['DEL_THREAD_STATUS'];
            $tasUid = $application['TAS_UID'];
            $lastIndex = $application['DEL_LAST_INDEX'];

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
                fwrite($file, '-**- Previous User: @#prevUser<br/>');
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
                'prevUser' => $prvUsr,
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



