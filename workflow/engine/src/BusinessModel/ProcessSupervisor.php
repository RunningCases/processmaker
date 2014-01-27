<?php
namespace BusinessModel;

use \G;

class ProcessSupervisor
{
    /**
     * Return supervisors
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getProcessSupervisors($sProcessUID = '')
    {
        try {
            // Groups
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\ProcessUserPeer::PU_UID);
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
            while ($aRow = $oDataset->getRow()) {
                $aResp[] = array('pu_uid' => $aRow['PU_UID'],
                                 'pu_type' => "GROUP_SUPERVISOR",
                                 'grp_uid' => $aRow['USR_UID'],
                                 'grp_name' => $aRow['GRP_TITLE']);
                $oDataset->next();
            }
            // Users
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
            $oCriteria->addSelectColumn(\ProcessUserPeer::PU_UID);
            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $oCriteria->addJoin(\ProcessUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
            $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            $oCriteria->addAscendingOrderByColumn(\UsersPeer::USR_FIRSTNAME);
            $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp[] = array('pu_uid' => $aRow['PU_UID'],
                                 'pu_type' => "SUPERVISOR",
                                 'usr_uid' => $aRow['USR_UID'],
                                 'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                 'usr_lastname' => $aRow['USR_LASTNAME'],
                                 'usr_username' => $aRow['USR_USERNAME'],
                                 'usr_email' => $aRow['USR_EMAIL'] );
                $oDataset->next();
            }
            if ($aResp == null) {
                $aResp = array();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return a spefic supervisor
     * @param string $sProcessUID
     * @param string $sPuUID
     *
     * @return object
     *
     * @access public
     */
    public function getProcessSupervisor($sProcessUID = '', $sPuUID = '')
    {
        try {
            // Groups
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\ProcessUserPeer::PU_UID);
            $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
            $oCriteria->addAsColumn('GRP_TITLE', \ContentPeer::CON_VALUE);
            $aConditions [] = array(\ProcessUserPeer::USR_UID, \ContentPeer::CON_ID);
            $aConditions [] = array(\ContentPeer::CON_CATEGORY, \DBAdapter::getStringDelimiter().'GRP_TITLE'.\DBAdapter::getStringDelimiter());
            $aConditions [] = array(\ContentPeer::CON_LANG, \DBAdapter::getStringDelimiter().SYS_LANG.\DBAdapter::getStringDelimiter());
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'GROUP_SUPERVISOR');
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\ProcessUserPeer::PU_UID, $sPuUID);
            $oCriteria->addAscendingOrderByColumn(\ContentPeer::CON_VALUE);
            $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp = array('pu_uid' => $aRow['PU_UID'],
                               'pu_type' => "GROUP_SUPERVISOR",
                               'grp_uid' => $aRow['USR_UID'],
                               'grp_name' => $aRow['GRP_TITLE']);
                $oDataset->next();
            }
            // Users
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
            $oCriteria->addSelectColumn(\ProcessUserPeer::PU_UID);
            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $oCriteria->addJoin(\ProcessUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
            $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\ProcessUserPeer::PU_UID, $sPuUID);
            $oCriteria->addAscendingOrderByColumn(\UsersPeer::USR_FIRSTNAME);
            $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp = array('pu_uid' => $aRow['PU_UID'],
                               'pu_type' => "SUPERVISOR",
                               'usr_uid' => $aRow['USR_UID'],
                               'usr_firstname' => $aRow['USR_FIRSTNAME'],
                               'usr_lastname' => $aRow['USR_LASTNAME'],
                               'usr_username' => $aRow['USR_USERNAME'],
                               'usr_email' => $aRow['USR_EMAIL'] );
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return available supervisors
     * @param string $sProcessUID
     * @param string $obj_type
     *
     * @return array
     *
     * @access public
     */
    public function getAvailableProcessSupervisors($sProcessUID = '', $obj_type)
    {
        try {
            // Groups
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
            $oCriteria->addSelectColumn(\ProcessUserPeer::PU_TYPE);
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\ProcessUserPeer::PU_TYPE, '%SUPERVISOR%', \Criteria::LIKE);
            $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aUIDS = array();
            $aGRUS = array();
            while ($aRow = $oDataset->getRow()) {
                if ($aRow['PU_TYPE'] == 'SUPERVISOR') {
                    $aUIDS [] = $aRow ['USR_UID'];
                } else {
                    $aGRUS [] = $aRow ['USR_UID'];
                }
                $oDataset->next();
            }
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\GroupwfPeer::GRP_UID);
            $oCriteria->addAsColumn('GRP_TITLE', \ContentPeer::CON_VALUE);
            $aConditions [] = array(\GroupwfPeer::GRP_UID, \ContentPeer::CON_ID);
            $aConditions [] = array(\ContentPeer::CON_CATEGORY, \DBAdapter::getStringDelimiter() . 'GRP_TITLE' . \DBAdapter::getStringDelimiter());
            $aConditions [] = array(\ContentPeer::CON_LANG, \DBAdapter::getStringDelimiter() . SYS_LANG . \DBAdapter::getStringDelimiter());
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\GroupwfPeer::GRP_UID, $aGRUS, \Criteria::NOT_IN);
            $oCriteria->addAscendingOrderByColumn(\ContentPeer::CON_VALUE);
            $oDataset = \GroupwfPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn('COUNT(*) AS MEMBERS_NUMBER');
            $oCriteria->add(\GroupUserPeer::GRP_UID, $results['GRP_UID']);
            $oDataset2 = \GroupUserPeer::doSelectRS($oCriteria);
            $oDataset2->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset2->next();
            $aRow2 = $oDataset2->getRow();
            if ($obj_type == 'group' || $obj_type == '') {
                while ($aRow = $oDataset->getRow()) {
                    $aRespLi[] = array('grp_uid' => $aRow['GRP_UID'],
                                         'grp_name' => $aRow['GRP_TITLE'],
                                         'obj_type' => "group");
                    $oDataset->next();
                }
            }

            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);
            $oCriteria->add(\UsersPeer::USR_UID, $aUIDS, \Criteria::NOT_IN);
            $oDataset = \UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aUIDS = array();
            while ($aRow = $oDataset->getRow()) {
                $aUIDS [] = $aRow ['USR_UID'];
                $oDataset->next();
            }
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\UsersPeer::USR_UID);
            $oCriteria->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_LASTNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_USERNAME);
            $oCriteria->addSelectColumn(\UsersPeer::USR_EMAIL);
            $oCriteria->add(\UsersPeer::USR_UID, $aUIDS, \Criteria::IN);
            $oCriteria->addAscendingOrderByColumn(\UsersPeer::USR_FIRSTNAME);
            $oCriteria->add(\UsersPeer::USR_ROLE, 'PROCESSMAKER_ADMIN', \Criteria::EQUAL);
            $oDataset = \UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            if ($obj_type == 'user' || $obj_type == '') {
                while ($aRow = $oDataset->getRow()) {
                    $aRespLi[] = array('usr_uid' => $aRow['USR_UID'],
                                       'usr_firstname' => $aRow['USR_FIRSTNAME'],
                                       'usr_lastname' => $aRow['USR_LASTNAME'],
                                       'usr_username' => $aRow['USR_USERNAME'],
                                       'usr_email' => $aRow['USR_EMAIL'],
                                       "obj_type" => "user" );
                    $oDataset->next();
                }
            }
            return $aRespLi;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return dynaforms supervisor
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getProcessSupervisorDynaforms($sProcessUID = '')
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
                $aResp[] = array('pud_uid' => $aRow['STEP_UID'],
                                 'pud_position' => $aRow['STEP_POSITION'],
                                 'dyn_uid' => $aRow['STEP_UID_OBJ'],
                                 'dyn_title' => $aRow['DYN_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return a specific dynaform supervisor
     * @param string $sProcessUID
     * @param string $sPudUID
     *
     * @return array
     *
     * @access public
     */
    public function getProcessSupervisorDynaform($sProcessUID = '', $sPudUID = '')
    {
        try {
            $oDynaformSupervisor = \StepSupervisorPeer::retrieveByPK( $sPudUID );
            if (is_null( $oDynaformSupervisor ) ) {
                throw (new \Exception( 'This id: '. $sPudUID .' do not correspond to a registered process supervisor '));
            }
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
            $oCriteria->add(\StepSupervisorPeer::STEP_UID, $sPudUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
            $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp = array('pud_uid' => $aRow['STEP_UID'],
                                 'pud_position' => $aRow['STEP_POSITION'],
                                 'dyn_uid' => $aRow['STEP_UID_OBJ'],
                                 'dyn_title' => $aRow['DYN_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return available dynaform supervisor
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getAvailableProcessSupervisorDynaform($sProcessUID = '')
    {
        try {
            $oCriteria = $this->getProcessSupervisorDynaforms($sProcessUID);
            $aUIDS = array();
            foreach ($oCriteria as $oCriteria => $value) {
                $aUIDS[] = $value["dyn_uid"];
            }
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\DynaformPeer::DYN_UID);
            $oCriteria->addSelectColumn(\DynaformPeer::PRO_UID);
            $oCriteria->addAsColumn('DYN_TITLE', 'C.CON_VALUE');
            $oCriteria->addAlias('C', 'CONTENT');
            $aConditions = array();
            $aConditions[] = array(\DynaformPeer::DYN_UID, 'C.CON_ID');
            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'DYN_TITLE' . $sDelimiter);
            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\DynaformPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\DynaformPeer::DYN_TYPE, 'xmlform');
            $oCriteria->add(\DynaformPeer::DYN_UID, $aUIDS, \Criteria::NOT_IN);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp[] = array('dyn_uid' => $aRow['DYN_UID'],
                                 'dyn_title' => $aRow['DYN_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return input documents supervisor
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getProcessSupervisorInputDocuments($sProcessUID = '')
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
                $aResp[] = array('pui_uid' => $aRow['STEP_UID'],
                                 'pui_position' => $aRow['STEP_POSITION'],
                                 'input_doc_uid' => $aRow['STEP_UID_OBJ'],
                                 'input_doc_title' => $aRow['INP_DOC_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return a specific input document supervisor
     * @param string $sProcessUID
     * @param string $sPuiUID
     *
     * @return array
     *
     * @access public
     */
    public function getProcessSupervisorInputDocument($sProcessUID = '', $sPuiUID = '')
    {
        try {
            $oInputDocumentSupervisor = \StepSupervisorPeer::retrieveByPK( $sPuiUID );
            if (is_null( $oInputDocumentSupervisor ) ) {
                throw (new \Exception( 'This id: '. $sPuiUID .' do not correspond to a registered process supervisor '));
            }
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
            $oCriteria->add(\StepSupervisorPeer::STEP_UID, $sPuiUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'INPUT_DOCUMENT');
            $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp = array('pui_uid' => $aRow['STEP_UID'],
                                 'pui_position' => $aRow['STEP_POSITION'],
                                 'input_doc_uid' => $aRow['STEP_UID_OBJ'],
                                 'input_doc_title' => $aRow['INP_DOC_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return available inputdocuments supervisor
     * @param string $sProcessUID
     *
     * @return array
     *
     * @access public
     */
    public function getAvailableProcessSupervisorInputDocument($sProcessUID = '')
    {
        try {
            $oCriteria = $this->getProcessSupervisorInputDocuments($sProcessUID);
            $aUIDS = array();
            foreach ($oCriteria as $oCriteria => $value) {
                $aUIDS[] = $value["input_doc_uid"];
            }
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);
            $oCriteria->addSelectColumn(\InputDocumentPeer::PRO_UID);
            $oCriteria->addAsColumn('INP_DOC_TITLE', 'C.CON_VALUE');
            $oCriteria->addAlias('C', 'CONTENT');
            $aConditions = array();
            $aConditions[] = array(\InputDocumentPeer::INP_DOC_UID, 'C.CON_ID');
            $aConditions[] = array('C.CON_CATEGORY', $sDelimiter . 'INP_DOC_TITLE' . $sDelimiter);
            $aConditions[] = array('C.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter);
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\InputDocumentPeer::PRO_UID, $sProcessUID);
            $oCriteria->add(\InputDocumentPeer::INP_DOC_UID, $aUIDS, \Criteria::NOT_IN);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp[] = array('inp_doc_uid' => $aRow['INP_DOC_UID'],
                                 'inp_doc_title' => $aRow['INP_DOC_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Assign a supervisor of a process
     *
     * @param string $sProcessUID
     * @param string $sUsrUID
     * @param string $sTypeUID
     * @access public
     */
    public function addProcessSupervisor($sProcessUID, $sUsrUID, $sTypeUID)
    {
        $oProcessUser = new \ProcessUser ( );
        $oTypeAssigneeG = \GroupwfPeer::retrieveByPK( $sUsrUID );
        $oTypeAssigneeU = \UsersPeer::retrieveByPK( $sUsrUID );
        if (is_null( $oTypeAssigneeG ) && is_null( $oTypeAssigneeU ) ) {
            throw (new \Exception( 'This id: '. $sUsrUID .' do not correspond to a registered ' .$sTypeUID ));
        }
        if (is_null( $oTypeAssigneeG ) && ! is_null( $oTypeAssigneeU) ) {
            if ( "SUPERVISOR"!= $sTypeUID ) {
                throw (new \Exception( 'This id: '. $sUsrUID .' do not correspond to a registered ' .$sTypeUID ));
            }
        }
        if (! is_null( $oTypeAssigneeG ) && is_null( $oTypeAssigneeU ) ) {
            if ( "GROUP_SUPERVISOR" != $sTypeUID ) {
                throw (new \Exception( 'This id: '. $sUsrUID .' do not correspond to a registered ' .$sTypeUID ));
            }
        }
        // validate Groups
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\ProcessUserPeer::PU_UID);
        $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
        $oCriteria->addAsColumn('GRP_TITLE', \ContentPeer::CON_VALUE);
        $aConditions [] = array(\ProcessUserPeer::USR_UID, \ContentPeer::CON_ID);
        $aConditions [] = array(\ContentPeer::CON_CATEGORY, \DBAdapter::getStringDelimiter().'GRP_TITLE'.\DBAdapter::getStringDelimiter());
        $aConditions [] = array(\ContentPeer::CON_LANG, \DBAdapter::getStringDelimiter().SYS_LANG.\DBAdapter::getStringDelimiter());
        $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
        $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'GROUP_SUPERVISOR');
        $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
        $oCriteria->add(\ProcessUserPeer::USR_UID, $sUsrUID);
        $oCriteria->addAscendingOrderByColumn(\ContentPeer::CON_VALUE);
        $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $sPuUIDT = $aRow['PU_UID'];
            $oDataset->next();
        }
        // validate Users
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\ProcessUserPeer::USR_UID);
        $oCriteria->addSelectColumn(\ProcessUserPeer::PU_UID);
        $oCriteria->addJoin(\ProcessUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::LEFT_JOIN);
        $oCriteria->add(\ProcessUserPeer::PU_TYPE, 'SUPERVISOR');
        $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
        $oCriteria->add(\ProcessUserPeer::USR_UID, $sUsrUID);
        $oCriteria->addAscendingOrderByColumn(\UsersPeer::USR_FIRSTNAME);
        $oDataset = \ProcessUserPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $sPuUIDT = $aRow['PU_UID'];
            $oDataset->next();
        }
        if (is_null($sPuUIDT)) {
            $sPuUID = \G::generateUniqueID();
            $oProcessUser->create(array('PU_UID' => $sPuUID,
                                        'PRO_UID' => $sProcessUID,
                                        'USR_UID' => $sUsrUID,
                                        'PU_TYPE' => $sTypeUID));
            $oCriteria = $this->getProcessSupervisor($sProcessUID, $sPuUID);
            return $oCriteria;
        } else {
            throw (new \Exception('This relation already exist!'));
        }
    }

    /**
     * Assign a dynaform supervisor of a process
     *
     * @param string $sProcessUID
     * @param string $sDynUID
     * @access public
     */
    public function addProcessSupervisorDynaform($sProcessUID, $sDynUID)
    {
        $oTypeDynaform = \DynaformPeer::retrieveByPK($sDynUID);
        if (is_null( $oTypeDynaform )) {
            throw (new \Exception( 'This id for `dyn_uid`: '. $sDynUID .' do not correspond to a registered Dynaform'));
        }
        $sDelimiter = \DBAdapter::getStringDelimiter();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID);
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
        $oCriteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $sDynUID);
        $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
        $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
        $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $sPuUIDT = $aRow['STEP_UID'];
            $oDataset->next();
        }
        if (is_null($sPuUIDT)) {
            $oStepSupervisor = new \StepSupervisor();
            $oStepSupervisor->create(array('PRO_UID' => $sProcessUID,
                                           'STEP_TYPE_OBJ' => "DYNAFORM",
                                           'STEP_UID_OBJ' => $sDynUID,
                                           'STEP_POSITION' => $oStepSupervisor->getNextPosition($sProcessUID, "DYNAFORM")));
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID);
            $oCriteria->addSelectColumn(\StepSupervisorPeer::PRO_UID);
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
            $oCriteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $sDynUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'DYNAFORM');
            $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp = array('pud_uid' => $aRow['STEP_UID'],
                               'pud_position' => $aRow['STEP_POSITION'],
                               'dyn_uid' => $aRow['STEP_UID_OBJ'],
                               'dyn_title' => $aRow['DYN_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } else {
            throw (new \Exception('This relation already exist!'));
        }
    }

    /**
     * Assign a inputdocument supervisor of a process
     *
     * @param string $sProcessUID
     * @param string $sInputDocumentUID
     * @access public
     */
    public function addProcessSupervisorInputDocument($sProcessUID, $sInputDocumentUID)
    {
        $oTypeInputDocument= \InputDocumentPeer::retrieveByPK($sInputDocumentUID);
        if (is_null( $oTypeInputDocument )) {
            throw (new \Exception( 'This id for `inp_doc_uid`: '. $sInputDocumentUID .' do not correspond to a registered InputDocument'));
        }
        $sDelimiter = \DBAdapter::getStringDelimiter();
        $oCriteria = new \Criteria('workflow');
        $oCriteria->addSelectColumn(\StepSupervisorPeer::STEP_UID);
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
        $oCriteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $sInputDocumentUID);
        $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'INPUT_DOCUMENT');
        $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
        $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            $sPuUIDT = $aRow['STEP_UID'];
            $oDataset->next();
        }
        if (is_null($sPuUIDT)) {
            $oStepSupervisor = new \StepSupervisor();
            $oStepSupervisor->create(array('PRO_UID' => $sProcessUID,
                                           'STEP_TYPE_OBJ' => "INPUT_DOCUMENT",
                                           'STEP_UID_OBJ' => $sInputDocumentUID,
                                           'STEP_POSITION' => $oStepSupervisor->getNextPosition($sProcessUID, "DYNAFORM")));
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
            $oCriteria->add(\StepSupervisorPeer::STEP_UID_OBJ, $sInputDocumentUID);
            $oCriteria->add(\StepSupervisorPeer::STEP_TYPE_OBJ, 'INPUT_DOCUMENT');
            $oCriteria->addAscendingOrderByColumn(\StepSupervisorPeer::STEP_POSITION);
            $oDataset = \StepSupervisorPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $aResp = array('pui_uid' => $aRow['STEP_UID'],
                               'pui_position' => $aRow['STEP_POSITION'],
                               'input_doc_uid' => $aRow['STEP_UID_OBJ'],
                               'input_doc_title' => $aRow['INP_DOC_TITLE']);
                $oDataset->next();
            }
            return $aResp;
        } else {
            throw (new \Exception('This relation already exist!'));
        }
    }

    /**
     * Remove a supervisor
     *
     * @param string $sProcessUID
     * @param string $sPuUID
     * @access public
     */
    public function removeProcessSupervisor($sProcessUID, $sPuUID)
    {
        $oConnection = \Propel::getConnection(\ProcessUserPeer::DATABASE_NAME);
        try {
            $oProcessUser = \ProcessUserPeer::retrieveByPK($sPuUID);
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
     * Remove a dynaform supervisor
     *
     * @param string $sProcessUID
     * @param string $sPudUID
     * @access public
     */
    public function removeDynaformSupervisor($sProcessUID, $sPudUID)
    {
        $oConnection = \Propel::getConnection(\StepSupervisorPeer::DATABASE_NAME);
        try {
            $oDynaformSupervidor = \StepSupervisorPeer::retrieveByPK($sPudUID);
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
     * Remove a input document supervisor
     *
     * @param string $sProcessUID
     * @param string $sPuiUID
     * @access public
     */
    public function removeInputDocumentSupervisor($sProcessUID, $sPuiUID)
    {
        $oConnection = \Propel::getConnection(\StepSupervisorPeer::DATABASE_NAME);
        try {
            $oInputDocumentSupervidor = \StepSupervisorPeer::retrieveByPK($sPuiUID);
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

