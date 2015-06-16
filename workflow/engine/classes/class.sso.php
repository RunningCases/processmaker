<?php
/**
 * class.sso.php
 *  
 */
G::LoadClass ('pmFunctions');
 
  class ssoClass extends PMPlugin {
    function __construct() {
    }

    function setup()
    {
    }

    function getFieldsForPageSetup()
    {
    }

    function updateFieldsForPageSetup()
    {
    }

    function ssocVerifyUser(){
        $res = false;
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();		
        $server = $_SERVER['SERVER_SOFTWARE'];		
        $webserver = explode("/", $server);
        if(isset($_SERVER['REMOTE_USER']) && $_SERVER['REMOTE_USER'] !=''){
            // IIS Verification
            if (!is_array($webserver) || (is_array($webserver) && ($webserver[0] == 'Microsoft-IIS'))){
                $userFull = $_SERVER['REMOTE_USER'];
                $userPN = explode("\\", $userFull);
                if (is_array($userPN)){
                    $user = $userPN[1];
                } else {
                    $user = $userFull;
                }
            } else {
                $userFull = $_SERVER['REMOTE_USER'];
                $user = $_SERVER['REMOTE_USER'];
            }
            // End IIS Verification

            $resVerifyUser = $RBAC->verifyUser($user);
            if ($resVerifyUser == 0) {
                // Here we are checking if the automatic user Register is enabled, ioc return -1
                $fakepswd = G::generate_password();
                $res = $RBAC->checkAutomaticRegister($user, $fakepswd);
                if ($res === -1) {
                	return false; // No successful auto register, skipping the auto register and back to normal login form
                }
                $RBAC->verifyUser($user);
            }
            if (!isset($RBAC->userObj->fields['USR_STATUS']) || $RBAC->userObj->fields['USR_STATUS'] == 0) {
                $errLabel = 'ID_USER_INACTIVE';
                G::SendTemporalMessage($errLabel, "warning");
                return false;
            }
            $sSQL = "SELECT USR_UID FROM USERS WHERE USR_USERNAME = '$user' ";
            $aResSQL = executeQuery($sSQL);			
            if(sizeof($aResSQL)){
                $nUserId = $aResSQL[1]['USR_UID'];		
                $RBAC->singleSignOn = true;
                $RBAC->userObj->fields['USR_UID'] = $nUserId;
                $RBAC->userObj->fields['USR_USERNAME'] = $user;
                $res = true;
            }
        }
        return $res;
    }
 }
?>