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
                \G::LoadClass( 'case' );
                $oCase = new \Cases();
                $startTasks = $oCase->getStartCases( $aRow['USR_UID'] );
                foreach ($startTasks as $task) {
                    if ((isset( $task['pro_uid'] )) && ($task['pro_uid'] == $sProcessUID)) {
                        $taskValue = explode( '(', $task['value'] );
                        $tasksLastIndex = count( $taskValue ) - 1;
                        $taskValue = explode( ')', $taskValue[$tasksLastIndex] );
                        //echo "<option value=\"" . $task['uid'] . "\">" . $taskValue[0] . "</option>";
                        echo  $task['uid'] ."     ------    ".$aUsers." fin ";
                        //var_dump($aUsers);
                        if (in_array($task['uid'], $aUsers)) {
                             echo "Es mac";
                        }

                        $aUsers[] = array(/*'usr_uid' => $aRow['USR_UID'],
                                  'usr_username' => $aRow['USR_USERNAME'],
                                  'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                  'usr_lastname' => $aRow['USR_LASTNAME'],*/
                                  'tas_name' => $taskValue[0],
                                  'tas_uid' => $task['uid']);
                        if (in_array($task['uid'], $aUsers['tas_uid'] )) {
                             echo "Es mac";
                        }
                       echo $task['uid'] . " ";
                       $oDataset->next(); 
                    }
                //$oDataset->next();
                }
                //die();
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
                    $aUsers[] = array('tas_uid' => $task['uid'],
                                      'tas_name' => $taskValue[0]);
                }
            }
            return $aUsers;
        } catch (Exception $e) {
            throw $e;
        }
    }

}

