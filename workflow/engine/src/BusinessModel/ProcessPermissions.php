<?php
namespace BusinessModel;

use \G;
use \Cases;
use \Criteria;
use \ObjectPermissionPeer;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class ProcessPermissions
{
    /**
     * Get list for Process Permissions
     *
     * @var string $sProcessUID. Uid for Process
     * @var string $sPermissionUid. Uid for Process Permission
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getProcessPermissions($sProcessUID, $sPermissionUid = '')
    {
        G::LoadClass('case');
        Cases::verifyTable();
        $aObjectsPermissions = array();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->add(ObjectPermissionPeer::PRO_UID, $sProcessUID);
        if ($sPermissionUid != '') {
            $oCriteria->add(ObjectPermissionPeer::OP_UID, $sPermissionUid);
        }
        $oDataset = ObjectPermissionPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            //Obtain task target
            if (($aRow['TAS_UID'] != '') && ($aRow['TAS_UID'] != '0')) {
                try {
                    $oTask = new \Task();
                    $aFields = $oTask->load($aRow['TAS_UID']);
                    $sTaskTarget = $aFields['TAS_TITLE'];
                } catch (\Exception $oError) {
                    $sTaskTarget = 'All Tasks';
                }
            } else {
                $sTaskTarget = G::LoadTranslation('ID_ANY_TASK');
            }
            //Obtain user or group
            if ($aRow['OP_USER_RELATION'] == 1) {
                $oUser = new \Users();
                $aFields = $oUser->load($aRow['USR_UID']);
                $sUserGroup = $aFields['USR_FIRSTNAME'] . ' ' . $aFields['USR_LASTNAME'] . ' (' . $aFields['USR_USERNAME'] . ')';
            } else {
                $oGroup = new \Groupwf();
                if ($aRow['USR_UID'] != '') {
                    try {
                        $aFields = $oGroup->load($aRow['USR_UID']);
                        $sUserGroup = $aFields['GRP_TITLE'];
                    } catch (\Exception $oError) {
                        $sUserGroup = '(GROUP DELETED)';
                    }
                } else {
                    $sUserGroup = G::LoadTranslation('ID_ANY');
                }
            }
            //Obtain task source
            if (($aRow['OP_TASK_SOURCE'] != '') && ($aRow['OP_TASK_SOURCE'] != '0')) {
                try {
                    $oTask = new \Task();
                    $aFields = $oTask->load($aRow['OP_TASK_SOURCE']);
                    $sTaskSource = $aFields['TAS_TITLE'];
                } catch (\Exception $oError) {
                    $sTaskSource = 'All Tasks';
                }
            } else {
                $sTaskSource = G::LoadTranslation('ID_ANY_TASK');
            }
            //Obtain object and type
            switch ($aRow['OP_OBJ_TYPE']) {
                case 'ALL':
                    $sObjectType = G::LoadTranslation('ID_ALL');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
                case 'ANY': //For backward compatibility (some process with ANY instead of ALL
                    $sObjectType = G::LoadTranslation('ID_ALL');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
                /* case 'ANY_DYNAFORM':
                  $sObjectType = G::LoadTranslation('ID_ANY_DYNAFORM');
                  $sObject     = G::LoadTranslation('ID_ALL');
                  break;
                  case 'ANY_INPUT':
                  $sObjectType = G::LoadTranslation('ID_ANY_INPUT');
                  $sObject     = G::LoadTranslation('ID_ALL');
                  break;
                  case 'ANY_OUTPUT':
                  $sObjectType = G::LoadTranslation('ID_ANY_OUTPUT');
                  $sObject     = G::LoadTranslation('ID_ALL');
                  break; */
                case 'DYNAFORM':
                    $sObjectType = G::LoadTranslation('ID_DYNAFORM');
                    if (($aRow['OP_OBJ_UID'] != '') && ($aRow['OP_OBJ_UID'] != '0')) {
                        $oDynaform = new \Dynaform();
                        $aFields = $oDynaform->load($aRow['OP_OBJ_UID']);
                        $sObject = $aFields['DYN_TITLE'];
                    } else {
                        $sObject = G::LoadTranslation('ID_ALL');
                    }
                    break;
                case 'INPUT':
                    $sObjectType = G::LoadTranslation('ID_INPUT_DOCUMENT');
                    if (($aRow['OP_OBJ_UID'] != '') && ($aRow['OP_OBJ_UID'] != '0')) {
                        $oInputDocument = new \InputDocument();
                        $aFields = $oInputDocument->load($aRow['OP_OBJ_UID']);
                        $sObject = $aFields['INP_DOC_TITLE'];
                    } else {
                        $sObject = G::LoadTranslation('ID_ALL');
                    }
                    break;
                case 'OUTPUT':
                    $sObjectType = G::LoadTranslation('ID_OUTPUT_DOCUMENT');
                    if (($aRow['OP_OBJ_UID'] != '') && ($aRow['OP_OBJ_UID'] != '0')) {
                        $oOutputDocument = new \OutputDocument();
                        $aFields = $oOutputDocument->load($aRow['OP_OBJ_UID']);
                        $sObject = $aFields['OUT_DOC_TITLE'];
                    } else {
                        $sObject = G::LoadTranslation('ID_ALL');
                    }
                    break;
                case 'CASES_NOTES':
                    $sObjectType = G::LoadTranslation('ID_CASES_NOTES');
                    $sObject = 'N/A';
                    break;
                case 'MSGS_HISTORY':
                    $sObjectType = G::LoadTranslation('MSGS_HISTORY');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
                default:
                    $sObjectType = G::LoadTranslation('ID_ALL');
                    $sObject = G::LoadTranslation('ID_ALL');
                    break;
            }
            //Participated
            if ($aRow['OP_PARTICIPATE'] == 0) {
                $sParticipated = G::LoadTranslation('ID_NO');
            } else {
                $sParticipated = G::LoadTranslation('ID_YES');
            }
            //Obtain action (permission)
            $sAction = G::LoadTranslation('ID_' . $aRow['OP_ACTION']);
            //Add to array
            $aObjectsPermissions[] = array_merge($aRow, array('OP_UID' => $aRow['OP_UID'], 'TASK_TARGET' => $sTaskTarget, 'GROUP_USER' => $sUserGroup, 'TASK_SOURCE' => $sTaskSource, 'OBJECT_TYPE' => $sObjectType, 'OBJECT' => $sObject, 'PARTICIPATED' => $sParticipated, 'ACTION' => $sAction, 'OP_CASE_STATUS' => $aRow['OP_CASE_STATUS']));
            $oDataset->next();
        }

        if ($sPermissionUid != '' && empty($aObjectsPermissions)) {
            throw (new \Exception( 'This row doesn\'t exist!' ));
        } else if ($sPermissionUid != '' && !empty($aObjectsPermissions)) {
            return current($aObjectsPermissions);
        }
        return $aObjectsPermissions;
    }

    /**
     * Delete Process Permission
     *
     * @var string $sPermissionUid. Uid for Process Permission
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    public function deleteProcessPermission($sPermissionUid)
    {
        try {
            require_once 'classes/model/ObjectPermission.php';
            $oOP = new \ObjectPermission();
            $oOP = ObjectPermissionPeer::retrieveByPK( $sPermissionUid );
            $oOP->delete();
        } catch (Exception $e) {
            throw $e;
        }
    }
}

