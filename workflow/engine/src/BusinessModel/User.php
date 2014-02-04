<?php
namespace BusinessModel;
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
     * change status of an user
     *
     * @access public
     * @param array $sUserUID
     * @return void
     */
    public function changeUserStatus ($sUserUID = '', $sStatus = 'ACTIVE')
    {
        require_once (PATH_RBAC_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "RbacUsers.php");
        //require_once ("classes/model/RbacUsers.php");
        $this->userObj = new \RbacUsers();
        if ($sStatus === 'ACTIVE') {
            $sStatus = 1;
        }

        $aFields = $this->userObj->load( $sUserUID );
        $aFields['USR_STATUS'] = $sStatus;
        $this->userObj->update( $aFields );
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
        //require_once ("classes/model/RbacUsers.php");
        $this->userObj = new \RbacUsers();

        if (isset( $aData['USR_STATUS'] )) {
            if ($aData['USR_STATUS'] == 'ACTIVE') {
                $aData['USR_STATUS'] = 1;
            }
        }
        $this->userObj->update( $aData );
        if ($sRolCode != '') {
            echo 'entra rol';
            $this->removeRolesFromUser( $aData['USR_UID'] );
            $this->assignRoleToUser( $aData['USR_UID'], $sRolCode );
        }
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
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);
            $form = $arrayData;
            if (isset($arrayData['USR_UID'])) {
                $form['USR_UID'] = $arrayData['USR_UID'];
            } else {
                $form['USR_UID'] = '';
            }
            if (!isset($form['USR_NEW_PASS'])) {
                $form['USR_NEW_PASS'] = '';
            }
            if ($form['USR_NEW_PASS'] != '') {
                $form['USR_PASSWORD'] = md5($form['USR_NEW_PASS']);
            }
            if (!isset($form['USR_CITY'])) {
                $form['USR_CITY'] = '';
            }
            if (!isset($form['USR_LOCATION'])) {
                $form['USR_LOCATION'] = '';
            }
            if (!isset($form['USR_AUTH_USER_DN'])) {
                $form['USR_AUTH_USER_DN'] = '';
            }
            if ($form['USR_UID'] == '') {
                $criteria = new \Criteria();
                $criteria->addSelectColumn(\UsersPeer::USR_USERNAME);
                $criteria->add(\UsersPeer::USR_USERNAME, utf8_encode($arrayData['USR_USERNAME']));
                if (\UsersPeer::doCount($criteria) > 0) {
                    throw new \Exception(\G::LoadTranslation('ID_USERNAME_ALREADY_EXISTS', array('USER_ID' => $arrayData['USR_USERNAME'])));
                }
                $aData['USR_USERNAME'] = $form['USR_USERNAME'];
                $aData['USR_PASSWORD'] = md5($form['USR_PASSWORD']);
                $aData['USR_FIRSTNAME'] = $form['USR_FIRSTNAME'];
                $aData['USR_LASTNAME'] = $form['USR_LASTNAME'];
                $aData['USR_EMAIL'] = $form['USR_EMAIL'];
                $aData['USR_DUE_DATE'] = $form['USR_DUE_DATE'];
                $aData['USR_CREATE_DATE'] = date('Y-m-d H:i:s');
                $aData['USR_UPDATE_DATE'] = date('Y-m-d H:i:s');
                $aData['USR_BIRTHDAY'] = date('Y-m-d');
                $aData['USR_AUTH_USER_DN'] = $form['USR_AUTH_USER_DN'];
                $statusWF = $form['USR_STATUS'];
                $aData['USR_STATUS'] = $form['USR_STATUS'] ;
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
                $aData['USR_PASSWORD'] = md5($sUserUID);
                $aData['USR_PASSWORD'] = $sUserUID;
                $aData['USR_COUNTRY'] = $form['USR_COUNTRY'];
                $aData['USR_CITY'] = $form['USR_CITY'];
                $aData['USR_LOCATION'] = $form['USR_LOCATION'];
                $aData['USR_ADDRESS'] = $form['USR_ADDRESS'];
                $aData['USR_PHONE'] = $form['USR_PHONE'];
                $aData['USR_ZIP_CODE'] = $form['USR_ZIP_CODE'];
                $aData['USR_POSITION'] = $form['USR_POSITION'];
                $aData['USR_ROLE'] = $form['USR_ROLE'];
                $aData['USR_REPLACED_BY'] = $form['USR_REPLACED_BY'];
                require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
                $oUser = new \Users();
                $oUser -> create( $aData );
            }
            $oCriteria = $this->getUser($sUserUID);
            return $oCriteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Group
     *
     * @param string $groupUid  Unique id of Group
     * @param array  $arrayData Data
     *
     * return array Return data of the Group updated
     */
    public function update($groupUid, $arrayData)
    {
        try {
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Group
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
                throw (new \Exception( 'The user with usr_uid: '. $USR_UID .', cannot be deleted while has assigned cases.'));
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
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oCriteria = new \Criteria();
            if ($filter != '') {
                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
            }
            if ($start) {
                if ($start < 0) {
                    throw (new \Exception( 'invalid value specified for `start`.'));
                } else {
                    $oCriteria->setOffset( $start );
                }
            }
            if (isset($limit)) {
                if ($limit < 0) {
                    throw (new \Exception( 'invalid value specified for `limit`.'));
                } else {
                    if ($limit == 0) {
                        return $aUsers;
                    } else {
                        $oCriteria->setLimit( $limit );
                    }
                }
            }
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
     * @param string $userUid Unique id of Group
     *
     * return array Return an array with data of a User
     */
    public function getUser($userUid)
    {
        try {
            $oUser = \UsersPeer::retrieveByPK($userUid);
            if (is_null($oUser)) {
                throw (new \Exception( 'This id for `usr_uid`: '. $userUid .' do not correspond to a registered user'));
            }
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oCriteria = new \Criteria();
            if ($filter != '') {
                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
            }
            $oCriteria->add(\UsersPeer::USR_UID, $userUid);
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
}

