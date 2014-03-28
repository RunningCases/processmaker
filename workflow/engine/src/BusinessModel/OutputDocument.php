<?php
namespace BusinessModel;

use \G;

class OutputDocument
{
    /**
     * Return output documents of a project
     * @param string $sProcessUID
     * @return array
     *
     * @access public
     */
    public function getOutputDocuments($sProcessUID = '')
    {
        try {
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_UID);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TYPE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::PRO_UID);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_REPORT_GENERATOR);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_LANDSCAPE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_MEDIA);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_LEFT_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_RIGHT_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TOP_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_BOTTOM_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_GENERATE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TYPE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_CURRENT_REVISION);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_FIELD_MAPPING);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_VERSIONING);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_DESTINATION_PATH);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TAGS);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_ENABLED);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OPEN_PASSWORD);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OWNER_PASSWORD);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_PERMISSIONS);
            $oCriteria->addAsColumn('OUT_DOC_TITLE', 'C1.CON_VALUE');
            $oCriteria->addAsColumn('OUT_DOC_DESCRIPTION', 'C2.CON_VALUE');
            $oCriteria->addAsColumn('OUT_DOC_FILENAME', 'C3.CON_VALUE');
            $oCriteria->addAsColumn('OUT_DOC_TEMPLATE', 'C4.CON_VALUE');
            $oCriteria->addAlias('C1', 'CONTENT');
            $oCriteria->addAlias('C2', 'CONTENT');
            $oCriteria->addAlias('C3', 'CONTENT');
            $oCriteria->addAlias('C4', 'CONTENT');
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C1.CON_ID' );
            $aConditions[] = array('C1.CON_CATEGORY', $sDelimiter . 'OUT_DOC_TITLE' . $sDelimiter );
            $aConditions[] = array('C1.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C2.CON_ID' );
            $aConditions[] = array('C2.CON_CATEGORY', $sDelimiter . 'OUT_DOC_DESCRIPTION' . $sDelimiter );
            $aConditions[] = array('C2.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C3.CON_ID' );
            $aConditions[] = array('C3.CON_CATEGORY', $sDelimiter . 'OUT_DOC_FILENAME' . $sDelimiter );
            $aConditions[] = array('C3.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C4.CON_ID' );
            $aConditions[] = array('C4.CON_CATEGORY', $sDelimiter . 'OUT_DOC_TEMPLATE' . $sDelimiter );
            $aConditions[] = array('C4.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\OutputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = \OutputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $outputDocArray = array();
            while ($aRow = $oDataset->getRow()) {
                if (($aRow['OUT_DOC_TITLE'] == null) || ($aRow['OUT_DOC_TITLE'] == "")) {
                    // There is no transaltion for this Document name, try to get/regenerate the label
                    $outputDocument = new \OutputDocument();
                    $outputDocumentObj = $outputDocument->load($aRow['OUT_DOC_UID']);
                    $aRow['OUT_DOC_TITLE'] = $outputDocumentObj['OUT_DOC_TITLE'];
                    $aRow['OUT_DOC_DESCRIPTION'] = $outputDocumentObj['OUT_DOC_DESCRIPTION'];
                } else {
                    $outputDocArray[] = array('out_doc_uid' => $aRow['OUT_DOC_UID'],
                                              'out_doc_title' => $aRow['OUT_DOC_TITLE'],
                                              'out_doc_description' => $aRow['OUT_DOC_DESCRIPTION'],
                                              'out_doc_filename' => $aRow['OUT_DOC_FILENAME'],
                                              'out_doc_template' => $aRow['OUT_DOC_TEMPLATE'],
                                              'out_doc_report_generator' => $aRow['OUT_DOC_REPORT_GENERATOR'],
                                              'out_doc_landscape' => $aRow['OUT_DOC_LANDSCAPE'],
                                              'out_doc_media' => $aRow['OUT_DOC_MEDIA'],
                                              'out_doc_left_margin' => $aRow['OUT_DOC_LEFT_MARGIN'],
                                              'out_doc_right_margin' => $aRow['OUT_DOC_RIGHT_MARGIN'],
                                              'out_doc_top_margin' => $aRow['OUT_DOC_TOP_MARGIN'],
                                              'out_doc_bottom_margin' => $aRow['OUT_DOC_BOTTOM_MARGIN'],
                                              'out_doc_generate' => $aRow['OUT_DOC_GENERATE'],
                                              'out_doc_type' => $aRow['OUT_DOC_TYPE'],
                                              'out_doc_current_revision' => $aRow['OUT_DOC_CURRENT_REVISION'],
                                              'out_doc_field_mapping' => $aRow['OUT_DOC_FIELD_MAPPING'],
                                              'out_doc_versioning' => $aRow['OUT_DOC_VERSIONING'],
                                              'out_doc_destination_path' => $aRow['OUT_DOC_DESTINATION_PATH'],
                                              'out_doc_tags' => $aRow['OUT_DOC_TAGS'],
                                              'out_doc_pdf_security_enabled' => $aRow['OUT_DOC_PDF_SECURITY_ENABLED'],
                                              'out_doc_pdf_security_open_password' => $aRow['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'],
                                              'out_doc_pdf_security_owner_password' => $aRow['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'],
                                              'out_doc_pdf_security_permissions' => $aRow['OUT_DOC_PDF_SECURITY_PERMISSIONS']);
                }
                $oDataset->next();
            }
            return $outputDocArray;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Return a single output document of a project
     * @param string $sProcessUID
     * @param string $sOutputDocumentUID
     * @return array
     *
     * @access public
     */
    public function getOutputDocument($sProcessUID = '', $sOutputDocumentUID = '')
    {
        try {
            $sDelimiter = \DBAdapter::getStringDelimiter();
            $oCriteria = new \Criteria('workflow');
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_UID);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TYPE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::PRO_UID);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_REPORT_GENERATOR);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_LANDSCAPE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_MEDIA);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_LEFT_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_RIGHT_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TOP_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_BOTTOM_MARGIN);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_GENERATE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TYPE);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_CURRENT_REVISION);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_FIELD_MAPPING);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_VERSIONING);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_DESTINATION_PATH);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_TAGS);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_ENABLED);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OPEN_PASSWORD);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_OWNER_PASSWORD);
            $oCriteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_PDF_SECURITY_PERMISSIONS);
            $oCriteria->add(\OutputDocumentPeer::OUT_DOC_UID, $sOutputDocumentUID);
            $oCriteria->addAsColumn('OUT_DOC_TITLE', 'C1.CON_VALUE');
            $oCriteria->addAsColumn('OUT_DOC_DESCRIPTION', 'C2.CON_VALUE');
            $oCriteria->addAsColumn('OUT_DOC_FILENAME', 'C3.CON_VALUE');
            $oCriteria->addAsColumn('OUT_DOC_TEMPLATE', 'C4.CON_VALUE');
            $oCriteria->addAlias('C1', 'CONTENT');
            $oCriteria->addAlias('C2', 'CONTENT');
            $oCriteria->addAlias('C3', 'CONTENT');
            $oCriteria->addAlias('C4', 'CONTENT');
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C1.CON_ID' );
            $aConditions[] = array('C1.CON_CATEGORY', $sDelimiter . 'OUT_DOC_TITLE' . $sDelimiter );
            $aConditions[] = array('C1.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C2.CON_ID' );
            $aConditions[] = array('C2.CON_CATEGORY', $sDelimiter . 'OUT_DOC_DESCRIPTION' . $sDelimiter );
            $aConditions[] = array('C2.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C3.CON_ID' );
            $aConditions[] = array('C3.CON_CATEGORY', $sDelimiter . 'OUT_DOC_FILENAME' . $sDelimiter );
            $aConditions[] = array('C3.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $aConditions = array();
            $aConditions[] = array(\OutputDocumentPeer::OUT_DOC_UID, 'C4.CON_ID' );
            $aConditions[] = array('C4.CON_CATEGORY', $sDelimiter . 'OUT_DOC_TEMPLATE' . $sDelimiter );
            $aConditions[] = array('C4.CON_LANG', $sDelimiter . SYS_LANG . $sDelimiter );
            $oCriteria->addJoinMC($aConditions, \Criteria::LEFT_JOIN);
            $oCriteria->add(\OutputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = \OutputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $outputDocArray = array();
            while ($aRow = $oDataset->getRow()) {
                if (($aRow['OUT_DOC_TITLE'] == null) || ($aRow['OUT_DOC_TITLE'] == "")) {
                    // There is no transaltion for this Document name, try to get/regenerate the label
                    $outputDocument = new \OutputDocument();
                    $outputDocumentObj = $outputDocument->load($aRow['OUT_DOC_UID']);
                    $aRow['OUT_DOC_TITLE'] = $outputDocumentObj['OUT_DOC_TITLE'];
                    $aRow['OUT_DOC_DESCRIPTION'] = $outputDocumentObj['OUT_DOC_DESCRIPTION'];
                } else {
                    $outputDocArray = array('out_doc_uid' => $aRow['OUT_DOC_UID'],
                                              'out_doc_title' => $aRow['OUT_DOC_TITLE'],
                                              'out_doc_description' => $aRow['OUT_DOC_DESCRIPTION'],
                                              'out_doc_filename' => $aRow['OUT_DOC_FILENAME'],
                                              'out_doc_template' => $aRow['OUT_DOC_TEMPLATE'],
                                              'out_doc_report_generator' => $aRow['OUT_DOC_REPORT_GENERATOR'],
                                              'out_doc_landscape' => $aRow['OUT_DOC_LANDSCAPE'],
                                              'out_doc_media' => $aRow['OUT_DOC_MEDIA'],
                                              'out_doc_left_margin' => $aRow['OUT_DOC_LEFT_MARGIN'],
                                              'out_doc_right_margin' => $aRow['OUT_DOC_RIGHT_MARGIN'],
                                              'out_doc_top_margin' => $aRow['OUT_DOC_TOP_MARGIN'],
                                              'out_doc_bottom_margin' => $aRow['OUT_DOC_BOTTOM_MARGIN'],
                                              'out_doc_generate' => $aRow['OUT_DOC_GENERATE'],
                                              'out_doc_type' => $aRow['OUT_DOC_TYPE'],
                                              'out_doc_current_revision' => $aRow['OUT_DOC_CURRENT_REVISION'],
                                              'out_doc_field_mapping' => $aRow['OUT_DOC_FIELD_MAPPING'],
                                              'out_doc_versioning' => $aRow['OUT_DOC_VERSIONING'],
                                              'out_doc_destination_path' => $aRow['OUT_DOC_DESTINATION_PATH'],
                                              'out_doc_tags' => $aRow['OUT_DOC_TAGS'],
                                              'out_doc_pdf_security_enabled' => $aRow['OUT_DOC_PDF_SECURITY_ENABLED'],
                                              'out_doc_pdf_security_open_password' => $aRow['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'],
                                              'out_doc_pdf_security_owner_password' => $aRow['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'],
                                              'out_doc_pdf_security_permissions' => $aRow['OUT_DOC_PDF_SECURITY_PERMISSIONS']);
                }
                $oDataset->next();
            }
            return $outputDocArray;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a new output document for a project
     * @param string $sProcessUID
     * @param array  $aData
     * @return array
     *
     * @access public
     */
    public function addOutputDocument($sProcessUID, $aData)
    {
        $pemission = $aData['out_doc_pdf_security_permissions'];
        $pemission = explode("|", $pemission);
        foreach ($pemission as $row) {
            if ($row == "print" || $row == "modify" || $row == "copy" || $row == "forms" || $row == "") {
                $aData['out_doc_pdf_security_permissions'] = $aData['out_doc_pdf_security_permissions'];
            } else {
                throw (new \Exception( 'Invalid value specified for `out_doc_pdf_security_permissions`'));
            }
        }
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "OutputDocument.php");
            $aData = array_change_key_case($aData, CASE_UPPER);
            $aData['PRO_UID'] = $sProcessUID;
            //Verify data
            $process = new \Process();
            if (!$process->exists($sProcessUID)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($sProcessUID, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }
            if ($aData["OUT_DOC_TITLE"]=="") {
                throw (new \Exception( 'Invalid value specified for `out_doc_title`, can`t be null'));
            }
            if (isset($aData["OUT_DOC_TITLE"]) && $this->existsTitle($sProcessUID, $aData["OUT_DOC_TITLE"])) {
                throw (new \Exception(\G::LoadTranslation("ID_OUTPUT_NOT_SAVE")));
            }
            $oOutputDocument = new \OutputDocument();
            if (isset( $aData['OUT_DOC_TITLE'] ) && $aData['OUT_DOC_TITLE'] != '') {
                if (isset( $aData['OUT_DOC_PDF_SECURITY_ENABLED'] ) && $aData['OUT_DOC_PDF_SECURITY_ENABLED'] == "0") {
                    $aData['OUT_DOC_PDF_SECURITY_OPEN_PASSWORD'] = "";
                    $aData['OUT_DOC_PDF_SECURITY_OWNER_PASSWORD'] = "";
                    $aData['OUT_DOC_PDF_SECURITY_PERMISSIONS'] = "";
                }
            }
            $outDocUid = $oOutputDocument->create($aData);
            $aData = array_change_key_case($aData, CASE_LOWER);
            if (isset( $aData['out_doc_pdf_security_open_password'] ) && $aData['out_doc_pdf_security_open_password'] != "") {
                $aData['out_doc_pdf_security_open_password'] = \G::encrypt( $aData['out_doc_pdf_security_open_password'], $outDocUid );
                $aData['out_doc_pdf_security_owner_password'] = \G::encrypt( $aData['out_doc_pdf_security_owner_password'], $outDocUid );
            }
            $this->updateOutputDocument($sProcessUID, $aData, $outDocUid, 1);
            //Return
            unset($aData["PRO_UID"]);
            $aData = array_change_key_case($aData, CASE_LOWER);
            $aData["out_doc_uid"] = $outDocUid;
            return $aData;
        } catch (\Exception $e) {
                throw $e;
        }
    }

    /**
     * Update a output document for a project
     * @param string $sProcessUID
     * @param array  $aData
     * @param string $sOutputDocumentUID
     * @param int $sFlag
     *
     * @access public
     */
    public function updateOutputDocument($sProcessUID, $aData, $sOutputDocumentUID = '', $sFlag)
    {
        $oConnection = \Propel::getConnection(\OutputDocumentPeer::DATABASE_NAME);
        $pemission = $aData['out_doc_pdf_security_permissions'];
        $pemission = explode("|", $pemission);
        foreach ($pemission as $row) {
            if ($row == "print" || $row == "modify" || $row == "copy" || $row == "forms" || $row == "") {
                $aData['out_doc_pdf_security_permissions'] = $aData['out_doc_pdf_security_permissions'];
            } else {
                throw (new \Exception( 'Invalid value specified for `out_doc_pdf_security_permissions`'));
            }
        }
        try {
            $aData = array_change_key_case($aData, CASE_UPPER);
            $oOutputDocument = \OutputDocumentPeer::retrieveByPK($sOutputDocumentUID);
            if (!is_null($oOutputDocument)) {
                $oOutputDocument->fromArray($aData, \BasePeer::TYPE_FIELDNAME);
                if ($oOutputDocument->validate()) {
                    $oConnection->begin();
                    if (isset($aData['OUT_DOC_TITLE'])) {
                        if ($this->existsTitle($sProcessUID, $aData["OUT_DOC_TITLE"]) && $sFlag == 0) {
                            throw (new \Exception(\G::LoadTranslation("ID_OUTPUT_NOT_SAVE")));
                        }
                        $oOutputDocument->setOutDocTitle($aData['OUT_DOC_TITLE']);
                    }
                    if (isset($aData['OUT_DOC_DESCRIPTION'])) {
                        $oOutputDocument->setOutDocDescription($aData['OUT_DOC_DESCRIPTION']);
                    }
                    if (isset($aData['OUT_DOC_FILENAME'])) {
                        $oOutputDocument->setOutDocFilename($aData['OUT_DOC_FILENAME']);
                    }
                    if (isset($aData['OUT_DOC_TEMPLATE'])) {
                        $oOutputDocument->setOutDocTemplate($aData['OUT_DOC_TEMPLATE']);
                    }
                    $iResult = $oOutputDocument->save();
                    $oConnection->commit();
                } else {
                    $sMessage = '';
                    $aValidationFailures = $oOutputDocument->getValidationFailures();
                    foreach ($aValidationFailures as $oValidationFailure) {
                        $sMessage .= $oValidationFailure->getMessage();
                    }
                    throw (new \Exception('The registry cannot be updated!' . $sMessage));
                }
            } else {
                throw (new \Exception('This row does not exist!'));
            }
        } catch (\Exception $e) {
                throw $e;
        }
    }

    /**
     * Delete a output document of a project
     *
     * @param string $sProcessUID
     * @param string $sOutputDocumentUID
     *
     * @access public
     */
    public function deleteOutputDocument($sProcessUID, $sOutputDocumentUID)
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "OutputDocument.php");
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "ObjectPermission.php");
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Step.php");
            \G::LoadClass( 'processMap' );
            $oOutputDocument = new \OutputDocument();
            $fields = $oOutputDocument->load( $sOutputDocumentUID );
            $oOutputDocument->remove( $sOutputDocumentUID );
            $oStep = new \Step();
            $oStep->removeStep( 'OUTPUT_DOCUMENT', $sOutputDocumentUID );
            $oOP = new \ObjectPermission();
            $oOP->removeByObject( 'OUTPUT', $sOutputDocumentUID );
            //refresh dbarray with the last change in outputDocument
            $oMap = new \processMap();
            $oCriteria = $oMap->getOutputDocumentsCriteria( $fields['PRO_UID'] );
        } catch (\Exception $e) {
                throw $e;
        }
    }

     /**
     * Checks if the title exists in the OutputDocuments of Process
     *
     * @param string $processUid Unique id of Process
     * @param string $title      Title
     * @param string $outputDocumentUidExclude Unique id of InputDocument to exclude
     *
     * return bool Return true if the title exists in the OutputDocuments of Process, false otherwise
     */
    public function existsTitle($processUid, $title)
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();
            $criteria = new \Criteria("workflow");
            $criteria->addSelectColumn(\OutputDocumentPeer::OUT_DOC_UID);
            $criteria->addAlias("CT", "CONTENT");
            $arrayCondition = array();
            $arrayCondition[] = array(\OutputDocumentPeer::OUT_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "OUT_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);
            $criteria->add(\OutputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->add(\ContentPeer::CON_VALUE, $title, \Criteria::EQUAL);
            $rsCriteria = \OutputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

