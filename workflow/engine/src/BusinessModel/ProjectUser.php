<?php
namespace BusinessModel;

use \G;

class ProjectUser
{
    /**
     * Return the users to assigned to a process
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProjectUsers($sProcessUID)
    {
        try {
            $aUsers = array();
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->setDistinct();
            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $oCriteria->addSelectColumn(\TaskUserPeer::TAS_UID);
            $oCriteria->addSelectColumn(\TaskUserPeer::USR_UID);
            $oCriteria->addSelectColumn(\TaskUserPeer::TU_TYPE);
            $oCriteria->addSelectColumn(\TaskUserPeer::TU_RELATION);
            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
            $oCriteria->addJoin(\TaskUserPeer::TAS_UID, \TaskPeer::TAS_UID,  \Criteria::LEFT_JOIN);
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);
            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);
            $oCriteria->addGroupByColumn(USR_UID);
            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aUsers[] = array('usr_uid' => $aRow['USR_UID'],
                                  'usr_username' => $aRow['USR_USERNAME'],
                                  'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                  'usr_lastname' => $aRow['USR_LASTNAME']);
                $oDataset->next();
            }
            return $aUsers;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the users and users groups to assigned to a process
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProjectStartingTasks($sProcessUID)
    {
        try {
            $aUsers = array();
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->setDistinct();
            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);
            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $oCriteria->addJoin(\TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
            $oCriteria->addJoin(\TaskUserPeer::TAS_UID, \TaskPeer::TAS_UID,  \Criteria::LEFT_JOIN);
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\TaskUserPeer::TU_TYPE, 1);
            $oCriteria->add(\TaskUserPeer::TU_RELATION, 1);
            $oCriteria->addGroupByColumn(USR_UID);
            $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                \G::LoadClass( 'case' );
                $oCase = new \Cases();
                $startTasks = $oCase->getStartCases( $aRow['USR_UID'] );
                foreach ($startTasks as $task) {
                    if ((isset( $task['pro_uid'] )) && ($task['pro_uid'] == $sProcessUID) ) {
                        $taskValue = explode( '(', $task['value'] );
                        $tasksLastIndex = count( $taskValue ) - 1;
                        $taskValue = explode( ')', $taskValue[$tasksLastIndex] );
                        $aUsers[] = array('act_name' => $taskValue[0],
                                          'act_uid' => $task['uid']);
                    }
                }
                $oDataset->next();
            }
            $new = array();
            $exclude = array("");
            for ($i = 0; $i<=count($aUsers)-1; $i++) {
                if (!in_array(trim($aUsers[$i]["act_uid"]) ,$exclude)) {
                     $new[] = $aUsers[$i];
                     $exclude[] = trim($aUsers[$i]["act_uid"]);
                }
            }
            return $new;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the users and users groups to assigned to a process
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param string $sUserUID {@min 32} {@max 32}
     *
     * return array
     *
     * @access public
     */
    public function getProjectStartingTaskUsers($sProcessUID, $sUserUID)
    {
        try {
            $aUsers = array();
            \G::LoadClass( 'case' );
            $oCase = new \Cases();
            $startTasks = $oCase->getStartCases($sUserUID);
            foreach ($startTasks as $task) {
                if ((isset( $task['pro_uid'] )) && ($task['pro_uid'] == $sProcessUID)) {
                    $taskValue = explode( '(', $task['value'] );
                    $tasksLastIndex = count( $taskValue ) - 1;
                    $taskValue = explode( ')', $taskValue[$tasksLastIndex] );
                    $aUsers[] = array('act_uid' => $task['uid'],
                                      'act_name' => $taskValue[0]);
                }
            }
            return $aUsers;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the users and users groups to assigned to a process
     *
     * @param string $sProcessUID {@min 32} {@max 32}
     * @param array  $oData
     *
     * return array
     *
     * @access public
     */
    public function postProjectWsUserCanStartTask($sProcessUID, $oData)
    {
        try {
            /**
             * process_webEntryValidate
             * validates if the username and password are valid data and if the user assigned
             * to the webentry has the rights and persmissions required
             */
            $sPRO_UID = $sProcessUID;
            $sTASKS = $oData['act_uid'];
            $sTASKS_SEL = $oData->TASKS_NAME;
            $sDYNAFORM = $oData->DYNAFORM;
            $sWE_TYPE = $oData->WE_TYPE;
            $sWS_USER = trim( $oData['username'] );
            $sWS_PASS = trim( $oData['password'] );
            $sWS_ROUNDROBIN = $oData->WS_ROUNDROBIN;
            $sWE_USR = $oData->WE_USR;
            if (\G::is_https())
                $http = 'https://';
            else
                $http = 'http://';
            $endpoint = $http . $_SERVER['HTTP_HOST'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/' . SYS_SKIN . '/services/wsdl2';
            @$client = new \SoapClient( $endpoint );
            $user = $sWS_USER;
            $pass = $sWS_PASS;
            $params = array ('userid' => $user,'password' => $pass);
            $result = $client->__SoapCall('login', array ($params));
            $fields['status_code'] = $result->status_code;
            $fields['message'] = 'ProcessMaker WebService version: ' . $result->version . "\n" . $result->message;
            $fields['version'] = $result->version;
            $fields['time_stamp'] = $result->timestamp;
            $messageCode = 1;
            \G::LoadClass( 'Task' );
            \G::LoadClass( 'User' );
            \G::LoadClass( 'TaskUser' );
            \G::LoadClass( 'Groupwf' );
            /**
             * note added by gustavo cruz gustavo-at-colosa-dot-com
             * This is a little check to see if the GroupUser class has been declared or not.
             * Seems that the problem its present in a windows installation of PM however.
             * It's seems that could be replicated in a Linux server easily.
             * I recomend that in some way check already if a imported class is declared
             * somewhere else or maybe delegate the task to the G Class LoadClass method.
             */
            if (! class_exists( 'GroupUser' )) {
                \G::LoadClass( 'GroupUser' );
            }
            // if the user has been authenticated, then check if has the rights or
            // permissions to create the webentry
            if ($result->status_code == 0) {
                $oCriteria = new \Criteria( 'workflow' );
                $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                $oCriteria->addSelectColumn( \TaskUserPeer::USR_UID );
                $oCriteria->addSelectColumn( \TaskUserPeer::TAS_UID );
                $oCriteria->addSelectColumn( \UsersPeer::USR_USERNAME );
                $oCriteria->addSelectColumn( \UsersPeer::USR_FIRSTNAME );
                $oCriteria->addSelectColumn( \UsersPeer::USR_LASTNAME );
                $oCriteria->addJoin( \TaskUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN );
                $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                //$oCriteria->add(TaskUserPeer::TU_RELATION,1);
                $userIsAssigned = \TaskUserPeer::doCount( $oCriteria );
                // if the user is not assigned directly, maybe a have the task a group with the user
                if ($userIsAssigned < 1) {
                    $oCriteria = new \Criteria( 'workflow' );
                    $oCriteria->addSelectColumn( \UsersPeer::USR_UID );
                    $oCriteria->addJoin( \UsersPeer::USR_UID, \GroupUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                    $oCriteria->addJoin( \GroupUserPeer::GRP_UID, \TaskUserPeer::USR_UID, \Criteria::LEFT_JOIN );
                    $oCriteria->add( \TaskUserPeer::TAS_UID, $sTASKS );
                    $oCriteria->add( \UsersPeer::USR_USERNAME, $sWS_USER );
                    $userIsAssigned = \GroupUserPeer::doCount( $oCriteria );
                    if (! ($userIsAssigned >= 1)) {
                        $messageCode = "The User `" . $sWS_USER . "` doesn't have the activity `" . $sTASKS . "` assigned";
                    }
                }
                $oDataset = \TaskUserPeer::doSelectRS($oCriteria);
                $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                $oDataset->next();
                while ($aRow = $oDataset->getRow()) {
                    $messageCode = array('usr_uid' => $aRow['USR_UID'],
                                         'usr_username' => $aRow['USR_USERNAME'],
                                         'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                         'usr_lastname' => $aRow['USR_LASTNAME']);
                    $oDataset->next();
                }
            } else {
                $messageCode = $result->message;
            }
            return $messageCode;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * User Login
     *
     * @param string $username Username
     * @param string $password Password
     *
     * return object Return object $response
     *               $response->status_code, 0 when User has been authenticated, any number otherwise
     *               $response->message, message
     */
    public function userLogin($username, $password)
    {
        try {
            $http = (\G::is_https())? "https://" : "http://";

            $client = new \SoapClient($http . $_SERVER["HTTP_HOST"] . "/sys" . SYS_SYS . "/" . SYS_LANG . "/" . SYS_SKIN . "/services/wsdl2");

            $params = array(
                "userid"   => $username,
                "password" => "md5:" . md5($password)
            );

            $response = $client->login($params);

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if the User is assigned to Task
     *
     * @param string $userUid Unique id of User
     * @param string $taskUid Unique id of Task
     *
     * return bool Return true if the User is assigned to Task, false otherwise
     */
    public function userIsAssignedToTask($userUid, $taskUid)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TaskUserPeer::TAS_UID);
            $criteria->add(\TaskUserPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->add(\TaskUserPeer::USR_UID, $userUid, \Criteria::EQUAL);

            $rsCriteria = \TaskUserPeer::doSelectRS($criteria);

            //If the User is not assigned directly, maybe a have the Task a Group with the User
            if (!$rsCriteria->next()) {
                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\UsersPeer::USR_UID);
                $criteria->addJoin(\UsersPeer::USR_UID, \GroupUserPeer::USR_UID, \Criteria::LEFT_JOIN);
                $criteria->addJoin(\GroupUserPeer::GRP_UID, \TaskUserPeer::USR_UID, \Criteria::LEFT_JOIN);
                $criteria->add(\TaskUserPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                $criteria->add(\UsersPeer::USR_UID, $userUid, \Criteria::EQUAL);

                $rsCriteria = \UsersPeer::doSelectRS($criteria);

                if (!$rsCriteria->next()) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

