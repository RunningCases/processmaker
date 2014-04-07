<?php
namespace ProcessMaker\BusinessModel;
use \G;

class User
{
    /**
     * Create User Uid
     *
     * @param array $arrayData Data
     *
     * return id
     */
    public function createUser($aData)
    {
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");
        $this->userObj = new \RbacUsers();
        if (class_exists('PMPluginRegistry')) {
            $pluginRegistry = & \PMPluginRegistry::getSingleton();
            if ($pluginRegistry->existsTrigger(PM_BEFORE_CREATE_USER)) {
                try {
                    $pluginRegistry->executeTriggers(PM_BEFORE_CREATE_USER, null);
                } catch(Exception $error) {
                    throw new Exception($error->getMessage());
                }
            }
        }
        $oConnection = \Propel::getConnection(\RbacUsersPeer::DATABASE_NAME);
        try {
            $oRBACUsers = new \RbacUsers();
            do {
                $aData['USR_UID'] = \G::generateUniqueID();
            } while ($oRBACUsers->load($aData['USR_UID']));
            $oRBACUsers->fromArray($aData, \BasePeer::TYPE_FIELDNAME);
            $iResult = $oRBACUsers->save();
            return $aData['USR_UID'];
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw($oError);
        }
    }

    /**
     * to put role an user
     *
     * @access public
     * @param string $sUserUID
     * @param string $sRolCode
     * @return void
     */
    public function assignRoleToUser ($sUserUID = '', $sRolCode = '')
    {
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Roles.php");
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersRoles.php");
        $this->usersRolesObj = new \UsersRoles();
        $this->rolesObj = new \Roles();
        $aRol = $this->rolesObj->loadByCode( $sRolCode );
        $this->usersRolesObj->create( $sUserUID, $aRol['ROL_UID'] );
    }

    /**
     * to test Password
     *
     * @access public
     * @param string $sPassword
     * @return array
     */
    public function testPassword ($sPassword = '')
    {
        require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersProperties.php");
        $oUserProperty = new \UsersProperties();
        $aFields = array();
        $dateNow = date('Y-m-d H:i:s');
        $aErrors = $oUserProperty->validatePassword($sPassword, $dateNow, $dateNow);
        if (!empty($aErrors)) {
            if (!defined('NO_DISPLAY_USERNAME')) {
                define('NO_DISPLAY_USERNAME', 1);
            }
            $aFields = array();
            $aFields['DESCRIPTION'] = \G::LoadTranslation('ID_POLICY_ALERT');
            foreach ($aErrors as $sError) {
                switch ($sError) {
                    case 'ID_PPP_MINIMUM_LENGTH':
                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError) . ': ' . PPP_MINIMUM_LENGTH .'. ';
                        $aFields[substr($sError, 3)] = PPP_MINIMUM_LENGTH;
                        break;
                    case 'ID_PPP_MAXIMUM_LENGTH':
                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError) . ': ' . PPP_MAXIMUM_LENGTH .'. ';
                        $aFields[substr($sError, 3)] = PPP_MAXIMUM_LENGTH;
                        break;
                    case 'ID_PPP_EXPIRATION_IN':
                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError) . ' ' . PPP_EXPIRATION_IN . ' ' . \G::LoadTranslation('ID_DAYS') .'. ';
                        $aFields[substr($sError, 3)] = PPP_EXPIRATION_IN;
                        break;
                    default:
                        $aFields['DESCRIPTION'] .= ' - ' . \G::LoadTranslation($sError);
                        $aFields[substr($sError, 3)] = 1;
                        break;
                }
            }
            $aFields['DESCRIPTION'] .= \G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY');
            $aFields['STATUS'] = false;
        } else {
            $aFields['DESCRIPTION'] = \G::LoadTranslation('ID_PASSWORD_COMPLIES_POLICIES');
            $aFields['STATUS'] = true;
        }
        return $aFields;
    }

    /**
     * change status of an user
     *
     * @access public
     * @param array $sUserUID
     * @return void
     */
    public function changeUserStatus ($sUserUID = '', $sStatus = 'ACTIVE')
    {
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");
        $this->userObj = new \RbacUsers();
        if ($sStatus === 'ACTIVE') {
            $sStatus = 1;
        }
        $aFields = $this->userObj->load( $sUserUID );
        $aFields['USR_STATUS'] = $sStatus;
        $this->userObj->update( $aFields );
    }

    /**
     * remove a role from an user
     *
     * @access public
     * @param array $sUserUID
     * @return void
     */
    public function removeRolesFromUser ($sUserUID = '')
    {
        $oCriteria = new \Criteria( 'rbac' );
        $oCriteria->add( \UsersRolesPeer::USR_UID, $sUserUID );
        \UsersRolesPeer::doDelete( $oCriteria );
    }

    /**
     * updated an user
     *
     * @access public
     * @param array $aData
     * @param string $sRolCode
     * @return void
     */
    public function updateUser ($aData = array(), $sRolCode = '')
    {
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");
        $this->userObj = new \RbacUsers();
        if (isset( $aData['USR_STATUS'] )) {
            if ($aData['USR_STATUS'] == 'ACTIVE') {
                $aData['USR_STATUS'] = 1;
            }
        }
        $this->userObj->update( $aData );
        if ($sRolCode != '') {
            $this->removeRolesFromUser( $aData['USR_UID'] );
            $this->assignRoleToUser( $aData['USR_UID'], $sRolCode );
        }
    }

    /**
     * Gets the roles and permission for one RBAC_user
     *
     * gets the Role and their permissions for one User
     *
     * @author Fernando Ontiveros Lira <fernando@colosa.com>
     * @access public
     *
     * @param string $sSystem the system
     * @param string $sUser the user
     * @return $this->aUserInfo[ $sSystem ]
     */
    public function loadUserRolePermission ($sSystem, $sUser)
    {
        //in previous versions  we provided a path data and session we will cache the session Info for this user
        //now this is deprecated, and all the aUserInfo is in the memcache
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersRoles.php");
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Systems.php");
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");
        $this->sSystem = $sSystem;
        $this->usersRolesObj = new \UsersRoles();
        $this->systemObj = new \Systems();
        $fieldsSystem = $this->systemObj->loadByCode( $sSystem );
        $fieldsRoles = $this->usersRolesObj->getRolesBySystem( $fieldsSystem['SYS_UID'], $sUser );
        $fieldsPermissions = $this->usersRolesObj->getAllPermissions( $fieldsRoles['ROL_UID'], $sUser );
        $this->userObj = new \RbacUsers();
        $this->aUserInfo['USER_INFO'] = $this->userObj->load( $sUser );
        $this->aUserInfo[$sSystem]['SYS_UID'] = $fieldsSystem['SYS_UID'];
        $this->aUserInfo[$sSystem]['ROLE'] = $fieldsRoles;
        $this->aUserInfo[$sSystem]['PERMISSIONS'] = $fieldsPermissions;
        return $fieldsPermissions;
    }

    /**
     * Create User
     *
     * @param array $arrayData Data
     *
     * return array Return data of the new User created
     */
    public function create($arrayData)
    {
        try {
            global $RBAC;
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $form = $arrayData;
            if ($form['USR_REPLACED_BY'] != '') {
                $oReplacedBy = \UsersPeer::retrieveByPK($form['USR_REPLACED_BY']);
                if (is_null($oReplacedBy)) {
                    throw new \Exception('`usr_replaced_by`:'.$form['USR_REPLACED_BY'].' '.\G::LoadTranslation('ID_AUTHENTICATION_SOURCE_INVALID'));
                }
            }
            if ($form['USR_COUNTRY'] != '') {
                $oCountry = \IsoCountryPeer::retrieveByPK($form['USR_COUNTRY']);
                if (is_null($oCountry)) {
                    throw new \Exception('Invalid value for `usr_country`: '.$form['USR_COUNTRY']);
                }
            }
            if ($form['USR_CITY'] != '') {
                $oCity = \IsoSubdivisionPeer::retrieveByPK($form['USR_COUNTRY'], $form['USR_CITY']);
                if (is_null($oCity)) {
                    throw new \Exception('Invalid value for `usr_city`: '.$form['USR_CITY']);
                }
            }
            if ($form['USR_LOCATION'] != '') {
                $oLocation = \IsoLocationPeer::retrieveByPK($form['USR_COUNTRY'], $form['USR_LOCATION']);
                if (is_null($oLocation)) {
                    throw new \Exception('Invalid value for `usr_location`: '.$form['USR_LOCATION']);
                }
            }
            if ($form['USR_COUNTRY'] != '') {
                $oReplacedBy = \IsoCountryPeer::retrieveByPK($form['USR_COUNTRY']);
                if (is_null($oReplacedBy)) {
                    throw new \Exception('Invalid value for `usr_country`: '.$form['USR_COUNTRY']);
                }
            }
            if (isset($arrayData['USR_UID'])) {
                $form['USR_UID'] = $arrayData['USR_UID'];
            } else {
                $form['USR_UID'] = '';
            }
            $sConfirm = $this->testPassword($form['USR_NEW_PASS']);
            if ($sConfirm['STATUS'] != 1) {
                throw new \Exception('`usr_new_pass`. '.$sConfirm['DESCRIPTION']);
            }
            if ($form['USR_NEW_PASS'] != $form['USR_CNF_PASS']) {
                throw new \Exception('`usr_new_pass or usr_cnf_pass`. '.\G::LoadTranslation('ID_NEW_PASS_SAME_OLD_PASS'));
            }
            $form['USR_PASSWORD'] = md5($form['USR_NEW_PASS']);
            if (!isset($form['USR_CITY'])) {
                $form['USR_CITY'] = '';
            }
            if (!isset($form['USR_LOCATION'])) {
                $form['USR_LOCATION'] = '';
            }
            if (!isset($form['USR_AUTH_USER_DN'])) {
                $form['USR_AUTH_USER_DN'] = '';
            }
            $criteria = new \Criteria();
            $criteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $criteria->add(\UsersPeer::USR_USERNAME, utf8_encode($arrayData['USR_USERNAME']));
            if (\UsersPeer::doCount($criteria) > 0) {
                throw new \Exception('`usr_username`. '.\G::LoadTranslation('ID_USERNAME_ALREADY_EXISTS', array('USER_ID' => $arrayData['USR_USERNAME'])));
            }
            if ($form['USR_USERNAME'] == '') {
                throw new \Exception('`usr_name`. '.\G::LoadTranslation('ID_MSG_ERROR_USR_USERNAME'));
            } else {
                $aData['USR_USERNAME'] = $form['USR_USERNAME'];
            }
            $aData['USR_PASSWORD'] = $form['USR_PASSWORD'];
            if ($form['USR_FIRSTNAME'] == '') {
                throw new \Exception('`usr_firstname`. '.\G::LoadTranslation('ID_MSG_ERROR_USR_FIRSTNAME'));
            } else {
                $aData['USR_FIRSTNAME'] = $form['USR_FIRSTNAME'];
            }
            if ($form['USR_LASTNAME'] == '') {
                throw new \Exception('`usr_lastname`. '.\G::LoadTranslation('ID_MSG_ERROR_USR_LASTNAME'));
            } else {
                $aData['USR_LASTNAME'] = $form['USR_LASTNAME'];
            }
            if ($form['USR_EMAIL'] == '') {
                throw new \Exception('Invalid value specified for `usr_email`, can`t be null.');
            } else {
                if (!filter_var($form['USR_EMAIL'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('`usr_email`. '.\G::LoadTranslation('ID_INCORRECT_EMAIL'));
                } else {
                    $aData['USR_EMAIL'] = $form['USR_EMAIL'];
                }
            }
            if ($form['USR_DUE_DATE'] == '') {
                throw new \Exception('`usr_due_date`. '.\G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
            } else {
                $dueDate = explode("-", $form['USR_DUE_DATE']);
                if (ctype_digit($dueDate[0])) {
                    if (checkdate($dueDate[1], $dueDate[2], $dueDate[0]) == false) {
                        throw new \Exception('`usr_due_date`. '.\G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
                    } else {
                        $aData['USR_DUE_DATE'] = $form['USR_DUE_DATE'];
                    }
                } else {
                    throw new \Exception('`usr_due_date`. '.\G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
                }
            }
            $aData['USR_CREATE_DATE'] = date('Y-m-d H:i:s');
            $aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
            $aData['USR_BIRTHDAY'] = date('Y-m-d');
            $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
            $statusWF = $form['USR_STATUS'];
            if ($form['USR_STATUS'] == '') {
                throw new \Exception('Invalid value specified for `usr_status`, can`t be null');
            } else {
                if ($form['USR_STATUS'] == 'ACTIVE' || $form['USR_STATUS'] == 'INACTIVE' || $form['USR_STATUS'] == 'VACATION') {
                    $aData['USR_STATUS'] = $form['USR_STATUS'];
                } else {
                    throw new \Exception('`usr_status`. Invalid value for status field.');
                }
            }
            if ($form['USR_ROLE'] == '') {
                throw new \Exception('Invalid value specified for `usr_role`, can`t be null');
            } else {
                $oCriteria = new \Criteria('rbac');
                $oCriteria->add(\RolesPeer::ROL_CODE, $form['USR_ROLE']);
                $oDataset = \RolesPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aRow = $oDataset->getRow();
                if ($oDataset->getRow()) {
                    $aData['USR_ROLE'] = $form['USR_ROLE'];
                } else {
                    throw new \Exception('`usr_role`. Invalid value for role field.');
                }
            }
            try {
                if ($aData['USR_STATUS'] == 'ACTIVE') {
                    $aData['USR_STATUS'] = 1;
                }
                if ($aData['USR_STATUS'] == 'INACTIVE') {
                    $aData['USR_STATUS'] = 0;
                }
                $sUserUID = $this->createUser($aData);
                if ($form['USR_ROLE'] != '') {
                    $this->assignRoleToUser($sUserUID, $form['USR_ROLE']);
                }
            } catch(Exception $oError) {
                throw new \Exception($oError->getMessage());
            }
            $aData['USR_STATUS'] = $statusWF;
            $aData['USR_UID'] = $sUserUID;
            $aData['USR_COUNTRY'] = $form['USR_COUNTRY'];
            $aData['USR_CITY'] = $form['USR_CITY'];
            $aData['USR_LOCATION'] = $form['USR_LOCATION'];
            $aData['USR_ADDRESS'] = $form['USR_ADDRESS'];
            $aData['USR_PHONE'] = $form['USR_PHONE'];
            $aData['USR_ZIP_CODE'] = $form['USR_ZIP_CODE'];
            $aData['USR_POSITION'] = $form['USR_POSITION'];
            $aData['USR_REPLACED_BY'] = $form['USR_REPLACED_BY'];
            $oUser = new \Users();
            $oUser -> create( $aData );
            if ((isset($form['USR_CALENDAR']))) {
                //Save Calendar ID for this user
                \G::LoadClass("calendar");
                $calendarObj = new \Calendar();
                $calendarObj->assignCalendarTo($sUserUID, $form['USR_CALENDAR'], 'USER');
            }
            $oCriteria = $this->getUser($sUserUID);
            return $oCriteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update User
     *
     * @param string $usrUid  Unique id of User
     * @param array  $arrayData Data
     * @param string $usrLoggedUid  Unique id of User logged
     *
     * return array Return data of the User updated
     */
    public function update($usrUid, $arrayData, $usrLoggedUid)
    {
        try {
            global $RBAC;
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $form = $arrayData;
            $countPermission = 0;
            $permission = $this->loadUserRolePermission($RBAC->sSystem, $usrLoggedUid);
            foreach ($permission as $key => $value) {
                if ($value["PER_CODE"] == 'PM_USERS') {
                    $countPermission+=1;
                }
            }
            if ($countPermission != 1) {
                throw new \Exception('This user: '.$usrLoggedUid. ', can`t update the data.');
            }
            $criteria = new \Criteria();
            $criteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $criteria->add(\UsersPeer::USR_USERNAME, utf8_encode($arrayData['USR_USERNAME']));
            if (\UsersPeer::doCount($criteria) > 0) {
                throw new \Exception('`usr_username`. '.\G::LoadTranslation('ID_USERNAME_ALREADY_EXISTS', array('USER_ID' => $arrayData['USR_USERNAME'])));
            }
            if (isset($usrUid)) {
                $form['USR_UID'] = $usrUid;
            } else {
                $form['USR_UID'] = '';
            }
            if (!isset($form['USR_NEW_PASS'])) {
                $form['USR_NEW_PASS'] = '';
            }
            if ($form['USR_NEW_PASS'] != '') {
                $form['USR_PASSWORD'] = md5($form['USR_NEW_PASS']);
            }
            if (!isset($form['USR_AUTH_USER_DN'])) {
                $form['USR_AUTH_USER_DN'] = '';
            }
            $aData['USR_UID'] = $form['USR_UID'];
            if ($form['USR_USERNAME'] != '') {
                $aData['USR_USERNAME'] = $form['USR_USERNAME'];
            }
            if (isset($form['USR_PASSWORD'])) {
                if ($form['USR_PASSWORD'] != '') {
                    if ($form['USR_NEW_PASS'] != $form['USR_CNF_PASS']) {
                        throw new \Exception('`usr_new_pass or usr_cnf_pass`. '.\G::LoadTranslation('ID_NEW_PASS_SAME_OLD_PASS'));
                    }
                    $aData['USR_PASSWORD'] = $form['USR_PASSWORD'];
                    require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "UsersProperties.php");
                    $oUserProperty = new \UsersProperties();
                    $aUserProperty = $oUserProperty->loadOrCreateIfNotExists($form['USR_UID'], array('USR_PASSWORD_HISTORY' => serialize(array(md5($form['USR_PASSWORD'])))));
                    $memKey = 'rbacSession' . session_id();
                    $memcache = & \PMmemcached::getSingleton(defined('SYS_SYS') ? SYS_SYS : '' );
                    if (($RBAC->aUserInfo = $memcache->get($memKey)) === false) {
                        $this->loadUserRolePermission($RBAC->sSystem, $usrLoggedUid);
                        $memcache->set($memKey, $RBAC->aUserInfo, \PMmemcached::EIGHT_HOURS);
                    }
                    if ($RBAC->aUserInfo['PROCESSMAKER']['ROLE']['ROL_CODE'] == 'PROCESSMAKER_ADMIN') {
                        $aUserProperty['USR_LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
                        $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
                        $oUserProperty->update($aUserProperty);
                    }
                    $aErrors = $oUserProperty->validatePassword($form['USR_NEW_PASS'], $aUserProperty['USR_LAST_UPDATE_DATE'], 0);
                    if (count($aErrors) > 0) {
                        $sDescription = \G::LoadTranslation('ID_POLICY_ALERT') . ':,';
                        foreach ($aErrors as $sError) {
                            switch ($sError) {
                                case 'ID_PPP_MINIMUN_LENGTH':
                                    $sDescription .= ' - ' . \G::LoadTranslation($sError) . ': ' . PPP_MINIMUN_LENGTH . '. ';
                                    break;
                                case 'ID_PPP_MAXIMUN_LENGTH':
                                    $sDescription .= ' - ' . \G::LoadTranslation($sError) . ': ' . PPP_MAXIMUN_LENGTH . '. ';
                                    break;
                                case 'ID_PPP_EXPIRATION_IN':
                                    $sDescription .= ' - ' . \G::LoadTranslation($sError) . ' ' . PPP_EXPIRATION_IN . ' ' . G::LoadTranslation('ID_DAYS') . '. ';
                                    break;
                                default:
                                    $sDescription .= ' - ' . \G::LoadTranslation($sError) . ',';
                                    break;
                            }
                        }
                        $sDescription .= '' . \G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY');
                        throw new \Exception('`usr_new_pass or usr_cnf_pass`. '.$sDescription);
                    }
                    $aHistory = unserialize($aUserProperty['USR_PASSWORD_HISTORY']);
                    if (!is_array($aHistory)) {
                        $aHistory = array();
                    }
                    if (!defined('PPP_PASSWORD_HISTORY')) {
                        define('PPP_PASSWORD_HISTORY', 0);
                    }
                    if (PPP_PASSWORD_HISTORY > 0) {
                        //it's looking a password igual into aHistory array that was send for post in md5 way
                        $c = 0;
                        $sw = 1;
                        while (count($aHistory) >= 1 && count($aHistory) > $c && $sw) {
                            if (strcmp(trim($aHistory[$c]), trim($form['USR_PASSWORD'])) == 0) {
                                $sw = 0;
                            }
                            $c++;
                        }
                        if ($sw == 0) {
                            $sDescription = \G::LoadTranslation('ID_POLICY_ALERT') . ':<br /><br />';
                            $sDescription .= ' - ' . \G::LoadTranslation('PASSWORD_HISTORY') . ': ' . PPP_PASSWORD_HISTORY . '<br />';
                            $sDescription .= '<br />' . \G::LoadTranslation('ID_PLEASE_CHANGE_PASSWORD_POLICY') . '';
                            throw new \Exception('`usr_new_pass or usr_cnf_pass`. '.$sDescription);
                        }
                        if (count($aHistory) >= PPP_PASSWORD_HISTORY) {
                            $sLastPassw = array_shift($aHistory);
                        }
                        $aHistory[] = $form['USR_PASSWORD'];
                    }
                    $aUserProperty['USR_LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
                    $aUserProperty['USR_LOGGED_NEXT_TIME'] = 1;
                    $aUserProperty['USR_PASSWORD_HISTORY'] = serialize($aHistory);
                    $oUserProperty->update($aUserProperty);
                }
            }
            if ($form['USR_FIRSTNAME'] != '') {
                $aData['USR_FIRSTNAME'] = $form['USR_FIRSTNAME'];
            }
            if ($form['USR_LASTNAME'] != '') {
                $aData['USR_LASTNAME'] = $form['USR_LASTNAME'];
            }
            if ($form['USR_EMAIL'] != '') {
                if (!filter_var($form['USR_EMAIL'], FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('`usr_email`. '.\G::LoadTranslation('ID_INCORRECT_EMAIL'));
                } else {
                    $aData['USR_EMAIL'] = $form['USR_EMAIL'];
                }
            }
            if ($form['USR_DUE_DATE'] != '') {
                $dueDate = explode("-", $form['USR_DUE_DATE']);
                if (ctype_digit($dueDate[0])) {
                    if (checkdate($dueDate[1], $dueDate[2], $dueDate[0]) == false) {
                        throw new \Exception('`usr_due_date`. '.\G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
                    } else {
                        $aData['USR_DUE_DATE'] = $form['USR_DUE_DATE'];
                    }
                } else {
                    throw new \Exception('`usr_due_date`. '.\G::LoadTranslation('ID_MSG_ERROR_DUE_DATE'));
                }
            }
            $aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
            if ($form['USR_STATUS'] != '') {
                $aData['USR_STATUS'] = $form['USR_STATUS'];
            }
            if ($form['USR_ROLE'] != '') {
                $oCriteria = new \Criteria('rbac');
                $oCriteria->add(\RolesPeer::ROL_CODE, $form['USR_ROLE']);
                $oDataset = \RolesPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                $aRow = $oDataset->getRow();
                if ($oDataset->getRow()) {
                    $aData['USR_ROLE'] = $form['USR_ROLE'];
                } else {
                    throw new \Exception('`usr_role`. Invalid value for field.');
                }
                $this->updateUser($aData, $form['USR_ROLE']);
            } else {
                $this->updateUser($aData);
            }
            if ($form['USR_COUNTRY'] != '') {
                $oReplacedBy = \IsoCountryPeer::retrieveByPK($form['USR_COUNTRY']);
                if (is_null($oReplacedBy)) {
                        throw new \Exception('Invalid value for `usr_country`: '.$form['USR_COUNTRY']);
                } else {
                    $aData['USR_COUNTRY'] = $form['USR_COUNTRY'];
                    $aData['USR_CITY'] = '';
                    $aData['USR_LOCATION'] = '';
                }
            }
            if ($form['USR_CITY'] != '') {
                $oCity = \IsoSubdivisionPeer::retrieveByPK($form['USR_COUNTRY'], $form['USR_CITY']);
                if (is_null($oCity)) {
                    throw new \Exception('Invalid value for `usr_city`: '.$form['USR_CITY']);
                } else {
                    $aData['USR_CITY'] = $form['USR_CITY'];
                }
            }
            if ($form['USR_LOCATION'] != '') {
                $oLocation = \IsoLocationPeer::retrieveByPK($form['USR_COUNTRY'], $form['USR_LOCATION']);
                if (is_null($oLocation)) {
                        throw new \Exception('Invalid value for `usr_location`: '.$form['USR_LOCATION']);
                } else {
                    $aData['USR_LOCATION'] = $form['USR_LOCATION'];
                }
            }
            $aData['USR_ADDRESS'] = $form['USR_ADDRESS'];
            $aData['USR_PHONE'] = $form['USR_PHONE'];
            $aData['USR_ZIP_CODE'] = $form['USR_ZIP_CODE'];
            $aData['USR_POSITION'] = $form['USR_POSITION'];
            if ($form['USR_ROLE'] != '') {
                $aData['USR_ROLE'] = $form['USR_ROLE'];
            }
            if ($form['USR_REPLACED_BY'] != '') {
                $oReplacedBy = \UsersPeer::retrieveByPK($form['USR_REPLACED_BY']);
                if (is_null($oReplacedBy)) {
                    throw new \Exception('`usr_replaced_by`:'.$form['USR_REPLACED_BY'].' '.\G::LoadTranslation('ID_AUTHENTICATION_SOURCE_INVALID'));
                } else {
                    $aData['USR_REPLACED_BY'] = $form['USR_REPLACED_BY'];
                }
            }
            if (isset($form['USR_AUTH_USER_DN'])) {
                $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
            }
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oUser = new \Users();
            $oUser->update($aData);
            $oCriteria = $this->getUser($usrUid);
            return $oCriteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Authenticate User
     *
     * @param array  $arrayData Data
     *
     * return array Return data of the User updated
     */
    public function authenticate($arrayData)
    {
        try {

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete User
     *
     * @param string $usrUid Unique id of User
     *
     * return void
     */
    public function delete($usrUid)
    {
        try {
            \G::LoadClass('case');
            $oProcessMap = new \Cases();
            $USR_UID = $usrUid;
            $total = 0;
            $history = 0;
            $c = $oProcessMap->getCriteriaUsersCases('TO_DO', $USR_UID);
            $total += \ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('DRAFT', $USR_UID);
            $total += \ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('COMPLETED', $USR_UID);
            $history += \ApplicationPeer::doCount($c);
            $c = $oProcessMap->getCriteriaUsersCases('CANCELLED', $USR_UID);
            $history += \ApplicationPeer::doCount($c);
            if ($total > 0) {
                throw (new \Exception( 'The user with usr_uid: '. $USR_UID .', cannot be deleted while it has cases assigned.'));
            } else {
                $UID = $usrUid;
                \G::LoadClass('tasks');
                $oTasks = new \Tasks();
                $oTasks->ofToAssignUserOfAllTasks($UID);
                \G::LoadClass('groups');
                $oGroups = new \Groups();
                $oGroups->removeUserOfAllGroups($UID);
                $this->changeUserStatus($UID, 'CLOSED');
                $_GET['USR_USERNAME'] = '';
                $this->updateUser(array('USR_UID' => $UID, 'USR_USERNAME' => $_GET['USR_USERNAME']), '');
                require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
                $oUser = new \Users();
                $aFields = $oUser->load($UID);
                $aFields['USR_STATUS'] = 'CLOSED';
                $aFields['USR_USERNAME'] = '';
                $oUser->update($aFields);
                //Delete Dashboard
                require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "DashletInstance.php");
                $criteria = new \Criteria( 'workflow' );
                $criteria->add( \DashletInstancePeer::DAS_INS_OWNER_UID, $UID );
                $criteria->add( \DashletInstancePeer::DAS_INS_OWNER_TYPE , 'USER');
                \DashletInstancePeer::doDelete( $criteria );
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Users
     *
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     *
     * return array Return an array with all Users
     */
    public function getUsers($filter, $start, $limit)
    {
        try {
            $aUserInfo = array();
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oCriteria = new \Criteria();
            if ($filter != '') {
                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
            }
            if ($start) {
                if ($start < 0) {
                    throw (new \Exception( 'Invalid value specified for `start`.'));
                } else {
                    $oCriteria->setOffset($start);
                }
            }
            if ($limit != '') {
                if ($limit < 0) {
                    throw (new \Exception( 'Invalid value specified for `limit`.'));
                } else {
                    if ($limit == 0) {
                        return $aUserInfo;
                    } else {
                        $oCriteria->setLimit($limit);
                    }
                }
            }
            $oCriteria->add(\UsersPeer::USR_STATUS, 'CLOSED', \Criteria::ALT_NOT_EQUAL);
            $oDataset = \UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $aRow1 = $oDataset->getRow();
                $aRow1 = array_change_key_case($aRow1, CASE_LOWER);
                $aUserInfo[] = $aRow1;
            }
            //Return
            return $aUserInfo;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a User
     *
     * @param string $userUid Unique id of User
     *
     * return array Return an array with data of a User
     */
    public function getUser($userUid)
    {
        try {
            $filter = '';
            $aUserInfo = array();
            $oUser = \UsersPeer::retrieveByPK($userUid);
            if (is_null($oUser)) {
                throw (new \Exception( 'This id for `usr_uid`: '. $userUid .' does not correspond to a registered user'));
            }
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oCriteria = new \Criteria();
            if ($filter != '') {
                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
            }
            $oCriteria->add(\UsersPeer::USR_UID, $userUid);
            $oCriteria->add(\UsersPeer::USR_STATUS, 'CLOSED', \Criteria::ALT_NOT_EQUAL);
            $oDataset = \UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $aRow1 = $oDataset->getRow();
                $aRow1 = array_change_key_case($aRow1, CASE_LOWER);
                $aUserInfo = $aRow1;
            }
            //Return
            return $aUserInfo;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Upload image User
     *
     * @param string $userUid Unique id of User
     *
     */
    public function uploadImage($userUid)
    {
        try {
            if ($_FILES['USR_PHOTO']['error'] != 1) {
                if ($_FILES['USR_PHOTO']['tmp_name'] != '') {
                    $aAux = explode('.', $_FILES['USR_PHOTO']['name']);
                    \G::uploadFile($_FILES['USR_PHOTO']['tmp_name'], PATH_IMAGES_ENVIRONMENT_USERS, $userUid . '.' . $aAux[1]);
                    \G::resizeImage(PATH_IMAGES_ENVIRONMENT_USERS . $userUid . '.' . $aAux[1], 96, 96, PATH_IMAGES_ENVIRONMENT_USERS . $userUid . '.gif');
                }
            } else {
                $result->success = false;
                $result->fileError = true;
                throw (new \Exception($result));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

