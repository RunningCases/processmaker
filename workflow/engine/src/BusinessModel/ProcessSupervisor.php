<?php
namespace BusinessModel;

use \G;

class ProcessSupervisor
{
    /**
     * Return output documents of a project
     * @param string $sProcessUID
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     *
     * @return array
     *
     * @access public
     */
    public function getSupervisors($sProcessUID = '', $filter, $start, $limit)
    {
        try {
        // Groups
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
        $oCriteria->addAsColumn('GRP_TITLE', \ContentPeer::CON_VALUE);
        $aConditions [] = array(\ProcessUserPeer::USR_UID, \ContentPeer::CON_ID);
        $aConditions [] = array(\ContentPeer::CON_CATEGORY, \DBAdapter::getStringDelimiter().'GRP_TITLE'.\DBAdapter::getStringDelimiter());
        $aConditions [] = array(\ContentPeer::CON_LANG, \DBAdapter::getStringDelimiter().SYS_LANG.\DBAdapter::getStringDelimiter());
        $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
        $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'GROUP_SUPERVISOR');
        $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
        $oCriteria->addAscendingOrderByColumn(\ContentPeer::CON_VALUE);
        $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');
        $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);
        $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);
        $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset2->next();
        $aRow2 = $oDataset2->getRow();
        while ($aRow = $oDataset->getRow()) {
            $aResp[] = array('sup_uid' => $aRow['USR_UID'],
                             'sup_name' => (!isset($aRow2['GROUP_INACTIVE']) ? $aRow['GRP_TITLE'] .
                             ' (' . $aRow2['MEMBERS_NUMBER'] . ' ' .
                             ((int) $aRow2['MEMBERS_NUMBER'] == 1 ? \G::LoadTranslation('ID_USER') : \G::LoadTranslation('ID_USERS')).
                             ')' . '' : $aRow['GRP_TITLE'] . ' ' . $aRow2['GROUP_INACTIVE']),
                             'sup_lastname' => "",
                             'sup_username' => "",
                             'sup_type' => "group" );
            $oDataset->next();
        }
        // Users
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
        $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
        $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
        $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
        $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);
        if ($filter) {
            $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
        }
        $oCriteria->addJoin(\ProcessUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
        $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
        $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
        if ($start) {
            $oCriteria->setOffset( $start );
        }
        if ($limit) {
            $oCriteria->setLimit( $limit );
        }
        $oCriteria->addAscendingOrderByColumn(\UsersPeer::USR_FIRSTNAME);
        $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $aResp[] = array('sup_uid' => $aRow['USR_UID'],
                              'sup_name' => $aRow['USR_FIRSTNAME'],
                              'sup_lastname' => $aRow['USR_LASTNAME'],
                              'sup_username' => $aRow['USR_USERNAME'],
                              'sup_type' => "user" );
            $oDataset->next();
        }
        return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return output documents of a project
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getDynaformSupervisor($sProcessUID = '')
    {
        try {
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::PRO_UID);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_TYPE_OBJ);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID_OBJ);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_POSITION);
            $oCriteria->addAsColumn('DYN_TITLE', 'C.CON_VALUE');
            $oCriteria->addAlias('C', 'CONTENT');
            $aConditions = array();
            $aConditions[] = array(\StepSupervisorPeer::STEP_UID_OBJ, \DynaformPeer::DYN_UID );
            $aConditions[] = array(\StepSupervisorPeer::STEP_TYPE_OBJ, $sDelimiter . 'DYNAFORM' . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\DynaformPeer::DYN_UID, 'C.CON_ID' );
            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'DYN_TITLE' . $sDelimiter );
            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
            $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp[] = array('step_uid' => $aRow['STEP_UID'],
                              'pro_uid' => $aRow['PRO_UID'],
                              'step_type_obj' => $aRow['STEP_TYPE_OBJ'],
                              'step_uid_obj' => $aRow['STEP_UID_OBJ'],
                              'step_position' => $aRow['STEP_POSITION'],
                              'title' => $aRow['DYN_TITLE']);
                $oDataset->next();
        }
        return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return output documents of a project
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getInputDocumentSupervisor($sProcessUID = '')
    {
        try {
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::PRO_UID);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_TYPE_OBJ);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID_OBJ);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_POSITION);
            $oCriteria->addAsColumn('INP_DOC_TITLE', 'C.CON_VALUE');
            $oCriteria->addAlias('C', 'CONTENT');
            $aConditions = array();
            $aConditions[] = array(\StepSupervisorPeer::STEP_UID_OBJ, \InputDocumentPeer::INP_DOC_UID);
            $aConditions[] = array(\StepSupervisorPeer::STEP_TYPE_OBJ, $sDelimiter . 'INPUT_DOCUMENT' . $sDelimiter);
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\InputDocumentPeer::INP_DOC_UID, 'C.CON_ID');
            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'INP_DOC_TITLE' . $sDelimiter);
            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'INPUT_DOCUMENT');
            $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp[] = array('step_uid' => $aRow['STEP_UID'],
                              'pro_uid' => $aRow['PRO_UID'],
                              'step_type_obj' => $aRow['STEP_TYPE_OBJ'],
                              'step_uid_obj' => $aRow['STEP_UID_OBJ'],
                              'step_position' => $aRow['STEP_POSITION'],
                              'title' => $aRow['INP_DOC_TITLE']);
                $oDataset->next();
        }
        return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove a supervisor of an activity
     *
     * @param string $sProcessUID
     * @param string $sUserUID
     * @access public
     */
    public function removeProcessSupervisor($sProcessUID, $sUserUID)
    {
        $oConnection = \Propel::getConnection(\ProcessUserPeer::DATABASE_NAME);
        try {
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn( \ProcessUserPeer::PU_UID );
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\ProcessUserPeer::USR_UID, $sUserUID);
            $oPuUid = \ProcessUserPeer::doSelectRS($oCriteria);
            $oPuUid->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oPuUid->next()) {
                $aRow = $oPuUid->getRow();
                $iPuUid = $aRow['PU_UID'];
            }
            $oProcessUser = \ProcessUserPeer::retrieveByPK($iPuUid);
            if (!is_null($oProcessUser)) {
                $oConnection->begin();
                $iResult = $oProcessUser->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new \Exception('This row doesn\'t exist!'));
            }
        } catch (\Exception $e) {
            $oConnection->rollback();
            throw $e;
        }
    }

    /**
     * Remove a dynaform supervisor of an activity
     *
     * @param string $sProcessUID
     * @param string $sDynaformUID
     * @access public
     */
    public function removeDynaformSupervisor($sProcessUID, $sDynaform)
    {
        $oConnection = \Propel::getConnection(\StepSupervisorPeer::DATABASE_NAME);
        try {
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn( \StepSupervisorPeer::STEP_UID );
            $oCriteria->add(\StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $sDynaform);
            $oPuUid = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oPuUid->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oPuUid->next()) {
                $aRow = $oPuUid->getRow();
                $iStepUid = $aRow['STEP_UID'];
            }
            $oDynaformSupervidor = \StepSupervisorPeer::retrieveByPK($iStepUid);
            if (!is_null($oDynaformSupervidor)) {
                $oConnection->begin();
                $iResult = $oDynaformSupervidor->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new \Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

    /**
     * Remove a dynaform supervisor of an activity
     *
     * @param string $sProcessUID
     * @param string $sInputDocumentUID
     * @access public
     */
    public function removeInputDocumentSupervisor($sProcessUID, $sInputDocumentUID)
    {
        $oConnection = \Propel::getConnection(\StepSupervisorPeer::DATABASE_NAME);
        try {
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn( \StepSupervisorPeer::STEP_UID );
            $oCriteria->add(\StepSupervisorPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $sInputDocumentUID);
            $oPuUid = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oPuUid->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oPuUid->next()) {
                $aRow = $oPuUid->getRow();
                $iStepUid = $aRow['STEP_UID'];
            }
            $oInputDocumentSupervidor = \StepSupervisorPeer::retrieveByPK($iStepUid);
            if (!is_null($oInputDocumentSupervidor)) {
                $oConnection->begin();
                $iResult = $oInputDocumentSupervidor->delete();
                $oConnection->commit();
                return $iResult;
            } else {
                throw (new \Exception('This row doesn\'t exist!'));
            }
        } catch (Exception $oError) {
            $oConnection->rollback();
            throw ($oError);
        }
    }

}

