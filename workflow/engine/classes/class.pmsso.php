<?php
/**
 * class.sso.php
 *  
 */
G::LoadClass ('pmFunctions');
 
  class pmssoClass extends PMPlugin {
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
        $RBAC = RBAC::getSingleton();
        $RBAC->initRBAC();
        $res = false;
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
            $users = new Users();
            $criteria = $users->loadByUsername($user);
            $dataset = SubApplicationPeer::doSelectRS($criteria);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            if ($dataset->next()) {
                $dataUser = $dataset->getRow();
                $RBAC->singleSignOn = true;
                $RBAC->userObj->fields['USR_UID'] = $dataUser['USR_UID'];
                $RBAC->userObj->fields['USR_USERNAME'] = $user;
                $res = true;
            }
        }
        return $res;
    }
 }
?>