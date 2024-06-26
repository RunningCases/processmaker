<?php
namespace ProcessMaker\BusinessModel\Cases;

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;
use stdClass;

class OutputDocument
{
    /**
     * Verify exists app_doc_uid in table APP_DOCUMENT when is output
     *
     * @param string $applicationUid
     *
     * return void Throw exception output not exists
     */
    private function throwExceptionIfNotExistsAppDocument($appDocumentUid)
    {
        try {
            $appDocument = \AppDocumentPeer::retrieveByPK($appDocumentUid, 1);

            if (is_null($appDocument)) {
                throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($appDocumentUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if the user has permissions
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $userUid        Unique id of User
     * @param string $appDocumentUid
     *
     * return void Throw exception the user does not have permission to delete
     */
    public function throwExceptionIfHaventPermissionToDelete($applicationUid, $delIndex, $userUid, $appDocumentUid)
    {
        try {
            //Verify data inbox
            $case = new \ProcessMaker\BusinessModel\Cases();
            $arrayResult = $case->getStatusInfo($applicationUid, $delIndex, $userUid);

            $flagInbox = 1;

            if (empty($arrayResult) || !preg_match("/^(?:TO_DO|DRAFT)$/", $arrayResult["APP_STATUS"])) {
                $flagInbox = 0;
            }

            //Verfiry exists $appDocumentUid
            $this->throwExceptionIfNotExistsAppDocument($appDocumentUid);

            //Verify data permission
            $flagPermission = 0;

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\AppDocumentPeer::DOC_UID);

            $criteria->add(\AppDocumentPeer::APP_DOC_UID, $appDocumentUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_DOC_TYPE, "OUTPUT", \Criteria::EQUAL);

            $rsCriteria = \AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $outputDocumentUid = $row["DOC_UID"];

                $application = \ApplicationPeer::retrieveByPK($applicationUid);

                //Criteria
                $criteria2 = new \Criteria("workflow");

                $criteria2->addSelectColumn(\ObjectPermissionPeer::OP_UID);

                $criteria2->add(\ObjectPermissionPeer::PRO_UID, $application->getProUid(), \Criteria::EQUAL);
                $criteria2->add(\ObjectPermissionPeer::OP_OBJ_TYPE, "OUTPUT", \Criteria::EQUAL);
                $criteria2->add(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, $outputDocumentUid, \Criteria::EQUAL)->addOr(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, "0", \Criteria::EQUAL)
                    )->addOr(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, "", \Criteria::EQUAL)
                    )
                );
                $criteria2->add(\ObjectPermissionPeer::OP_ACTION, "DELETE", \Criteria::EQUAL);

                //User
                $criteriaU = clone $criteria2;

                $criteriaU->add(\ObjectPermissionPeer::OP_USER_RELATION, 1, \Criteria::EQUAL);
                $criteriaU->add(\ObjectPermissionPeer::USR_UID, $userUid, \Criteria::EQUAL);

                $rsCriteriaU = \ObjectPermissionPeer::doSelectRS($criteriaU);
                $rsCriteriaU->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                if ($rsCriteriaU->next()) {
                    $flagPermission = 1;
                }

                //Group
                if ($flagPermission == 0) {
                    $criteriaG = clone $criteria2;

                    $criteriaG->add(\ObjectPermissionPeer::OP_USER_RELATION, 2, \Criteria::EQUAL);

                    $criteriaG->addJoin(\ObjectPermissionPeer::USR_UID, \GroupUserPeer::GRP_UID, \Criteria::LEFT_JOIN);
                    $criteriaG->add(\GroupUserPeer::USR_UID, $userUid, \Criteria::EQUAL);

                    $rsCriteriaG = \ObjectPermissionPeer::doSelectRS($criteriaG);
                    $rsCriteriaG->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                    if ($rsCriteriaG->next()) {
                        $flagPermission = 1;
                    }
                }
            }

            if ($flagInbox == 1) {
                if ($flagPermission == 0) {
                    throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_OUTPUT_DOCUMENT", array($userUid)));
                }
            } else {
                if ($flagPermission == 0) {
                    throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_OUTPUT_DOCUMENT", array($userUid)));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if does not exists the inbox
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $userUid        Unique id of User
     *
     * return void Throw exception if not exists in inbox
     */
    public function throwExceptionIfCaseNotIsInInbox($applicationUid, $delIndex, $userUid)
    {
        try {
            //Verify data
            $case = new \ProcessMaker\BusinessModel\Cases();
            $arrayResult = $case->getStatusInfo($applicationUid, $delIndex, $userUid);

            if (empty($arrayResult) || !preg_match("/^(?:TO_DO|DRAFT)$/", $arrayResult["APP_STATUS"])) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION", array($userUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if not exists OuputDocument in Steps
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $outDocUuid
     *
     * return void Throw exception if not exists OuputDocument in Steps
     */
    public function throwExceptionIfOuputDocumentNotExistsInSteps($applicacionUid, $delIndex, $outputDocumentUid)
    {
        try {
            //Verify data
            $appDelegation = \AppDelegationPeer::retrieveByPK($applicacionUid, $delIndex);

            $taskUid = $appDelegation->getTasUid();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\StepPeer::STEP_UID);

            $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
            $criteria->add(\StepPeer::STEP_TYPE_OBJ, "OUTPUT_DOCUMENT", \Criteria::EQUAL);
            $criteria->add(\StepPeer::STEP_UID_OBJ, $outputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \StepPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if (!$rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($outputDocumentUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     *
     * return array Return an array with data of an OutputDocument
     */
    public function getCasesOutputDocuments($applicationUid, $userUid)
    {
        try {
            $oCase = new \Cases();
            $fields = $oCase->loadCase($applicationUid);
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = new \ProcessMaker\BusinessModel\Cases();
            $oCriteria->getAllGeneratedDocumentsCriteria($sProcessUID, $applicationUid, $sTaskUID, $userUid);
            $result = array();
            global $_DBArray;
            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset($row['DOC_VERSION'])) {
                    $docrow = array();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['DOWNLOAD_FILE'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     * @param string $applicationDocumentUid
     *
     * return object Return an object with data of an OutputDocument
     */
    public function getCasesOutputDocument($applicationUid, $userUid, $applicationDocumentUid)
    {
        try {
            $sApplicationUID = $applicationUid;
            $sUserUID = $userUid;
            $oCase = new \Cases();
            $fields = $oCase->loadCase($sApplicationUID);
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCaseRest = new \ProcessMaker\BusinessModel\Cases();
            $oCaseRest->getAllGeneratedDocumentsCriteria($sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID);
            $result = array();
            global $_DBArray;
            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset($row['DOC_VERSION'])) {
                    $docrow = array();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['DOWNLOAD_FILE'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = $row['DOWNLOAD_LINK'];
                    if ($docrow['app_doc_uid'] == $applicationDocumentUid) {
                        $oAppDocument = \AppDocumentPeer::retrieveByPK($applicationDocumentUid, $row['DOC_VERSION']);
                        if (is_null($oAppDocument)) {
                            throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($applicationDocumentUid)));
                        }
                        $result = $docrow;
                    }
                }
            }
            $oResponse = json_decode(json_encode($result), false);
            return $oResponse;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Ouput Document generated
     *
     * @param string $appDocumentUid Unique id of Documents in an Application
     * @param string $applicationUid Unique id of Case
     * @param array  $arrayVariableNameForException Variable name for exception
     * @param bool   $throwException Flag to throw the exception if the main parameters are invalid or do not exist
     *                               (TRUE: throw the exception; FALSE: returns FALSE)
     *
     * @return array Returns an array with Ouput Document generated to record, ThrowTheException/FALSE otherwise
     */
    public function getOuputDocumentRecordByPk($appDocumentUid, $applicationUid, array $arrayVariableNameForException, $throwException = true)
    {
        $criteria = new \Criteria('workflow');

        $criteria->add(\AppDocumentPeer::APP_DOC_UID, $appDocumentUid, \Criteria::EQUAL);
        $criteria->add(\AppDocumentPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
        $criteria->add(\AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT', \Criteria::EQUAL);

        $rsCriteria = \AppDocumentPeer::doSelectRS($criteria);
        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        if (!$rsCriteria->next()) {
            if ($throwException) {
                throw new \Exception(\G::LoadTranslation('ID_CASE_OUTPUT_DOCUMENT_DOES_NOT_EXIST', [$arrayVariableNameForException['$appDocumentUid'], $appDocumentUid]));
            } else {
                return false;
            }
        }

        $record = $rsCriteria->getRow();

        //Return
        return $record;
    }

    /**
     * Stream file (OutputDocument)
     *
     * @param string $appDocumentUid Unique id of Documents in an Application
     * @param string $applicationUid Unique id of Case
     * @param string $extension      Extension (pdf, doc)
     *
     * @return string Returns a string (string of bytes)
     */
    public function streamFile($appDocumentUid, $applicationUid, $extension)
    {
        //Verify data and Set variables
        $arrayAppDocumentData = $this->getOuputDocumentRecordByPk($appDocumentUid, $applicationUid, ['$appDocumentUid' => '$appDocumentUid']);

        $appDocument = new \AppDocument();

        $appDocument->Fields = $appDocument->load(
            $appDocumentUid,
            $appDocument->getLastDocVersion($appDocumentUid, $applicationUid)
        );

        $outputDocument = \OutputDocumentPeer::retrieveByPK($arrayAppDocumentData['DOC_UID']);
        $outdocDocGenerate = $outputDocument->getOutDocGenerate();

        $ext = '';

        if (!is_null($extension)) {
            if (!preg_match('/^(?:PDF|DOC)$/', strtoupper($extension))) {
                throw new \Exception(\G::LoadTranslation('ID_OUTPUT_DOCUMENT_INVALID_EXTENSION'));
            }

            if ($outdocDocGenerate != 'BOTH' && strtoupper($extension) != $outdocDocGenerate) {
                throw new \Exception(\G::LoadTranslation('ID_OUTPUT_DOCUMENT_CONFIG_NOT_SUPPORT_EXTENSION'));
            }

            $ext = $extension;
        } else {
            $ext = ($outdocDocGenerate != 'BOTH')? strtolower($outdocDocGenerate) : 'pdf';
        }

        //Stream file (OutputDocument)
        $appDocUid = $appDocument->getAppDocUid();
        $docUid = $appDocument->Fields['DOC_UID'];

        $outputDocument = new \OutputDocument();

        $outputDocument->Fields = $outputDocument->getByUid($docUid);

        $download = $outputDocument->Fields['OUT_DOC_OPEN_TYPE'];

        $pathInfo = pathinfo($appDocument->getAppDocFilename());

        // Plugin Hook PM_REDIRECT to redirect to a custom page
        $pluginRegistry = PluginRegistry::loadSingleton();

        // If the hook exists try to execute
        if ($pluginRegistry->existsTrigger(PM_REDIRECT)) {
            // Build the object to send
            $data = new stdClass();
            $data->sApplicationUid = $appDocument->getAppUid();
            $data->appDocUid = $appDocument->getAppDocUid();
            $data->docType = $ext;

            // Execute hook
            $pluginRegistry->executeTriggers(PM_REDIRECT, $data);
        }

        $extensionDoc = $ext;

        $versionDoc =  '_' . $arrayAppDocumentData['DOC_VERSION'];

        $realPath = PATH_DOCUMENT . \G::getPathFromUID($appDocument->Fields['APP_UID']) . '/outdocs/' . $appDocUid . $versionDoc . '.' . $extensionDoc;
        $realPath1 = PATH_DOCUMENT . \G::getPathFromUID($appDocument->Fields['APP_UID']) . '/outdocs/' . $pathInfo['basename'] . $versionDoc . '.' . $extensionDoc;
        $realPath2 = PATH_DOCUMENT . \G::getPathFromUID($appDocument->Fields['APP_UID']) . '/outdocs/' . $pathInfo['basename'] . '.' . $extensionDoc;

        $flagFileExists = false;

        if (file_exists($realPath)) {
            $flagFileExists = true;
        } else {
            if (file_exists($realPath1)) {
                $flagFileExists = true;
                $realPath = $realPath1;
            } else {
                if (file_exists($realPath2)) {
                    $flagFileExists = true;
                    $realPath = $realPath2;
                }
            }
        }

        if ($flagFileExists) {
            $nameFile = $pathInfo['basename'] . $versionDoc . '.' . $extensionDoc;

            \G::streamFile($realPath, true, $nameFile); //download
        } else {
            throw new \Exception(\G::LoadTranslation('ID_NOT_EXISTS_FILE'));
        }
    }

    /**
     * Delete OutputDocument
     *
     * @param string $applicationDocumentUid
     *
     */
    public function removeOutputDocument($applicationDocumentUid)
    {
        try {
            $oAppDocumentVersion = new \AppDocument();
            $lastDocVersion = $oAppDocumentVersion->getLastAppDocVersion($applicationDocumentUid);
            $oAppDocument = \AppDocumentPeer::retrieveByPK($applicationDocumentUid, $lastDocVersion);
            if (is_null($oAppDocument) || $oAppDocument->getAppDocStatus() == 'DELETED') {
                throw new \Exception(\G::LoadTranslation("ID_CASES_OUTPUT_DOES_NOT_EXIST", array($applicationDocumentUid)));
            }
            $aFields = array('APP_DOC_UID' => $applicationDocumentUid,'DOC_VERSION' => $lastDocVersion,'APP_DOC_STATUS' => 'DELETED');
            $oAppDocument->update($aFields);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $outputDocumentUid
     * @param string $userUid
     *
     * return object Return an object with data of an OutputDocument
     */
    public function addCasesOutputDocument($applicationUid, $outputDocumentUid, $userUid)
    {
        try {
            $sApplication = $applicationUid;
            $index = \AppDelegation::getCurrentIndex($applicationUid);
            $sUserLogged = $userUid;
            $outputID = $outputDocumentUid;
            $g = new \G();
            $g->sessionVarSave();
            $oCase = new \Cases();
            $oCase->thisIsTheCurrentUser($sApplication, $index, $sUserLogged, '', 'casesListExtJs');
            //require_once 'classes/model/OutputDocument.php';
            $oOutputDocument = new \OutputDocument();
            $aOD = $oOutputDocument->load($outputID);
            $Fields = $oCase->loadCase($sApplication);
            $outDocFile = replacePrefixes($aOD['OUT_DOC_FILENAME']);
            $sFilename = preg_replace('[^A-Za-z0-9_]', '_', \G::replaceDataField($outDocFile, $Fields['APP_DATA']));
            require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppFolder.php");
            require_once(PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "AppDocument.php");
            //Get the Custom Folder ID (create if necessary)
            $oFolder = new \AppFolder();
            $folderId = $oFolder->createFromPath($aOD['OUT_DOC_DESTINATION_PATH'], $sApplication);
            //Tags
            $fileTags = $oFolder->parseTags($aOD['OUT_DOC_TAGS'], $sApplication);
            //Get last Document Version and apply versioning if is enabled
            $oAppDocument = new \AppDocument();
            $lastDocVersion = $oAppDocument->getLastDocVersion($outputID, $sApplication);
            $oCriteria = new \Criteria('workflow');
            $oCriteria->add(\AppDocumentPeer::APP_UID, $sApplication);
            $oCriteria->add(\AppDocumentPeer::DOC_UID, $outputID);
            $oCriteria->add(\AppDocumentPeer::DOC_VERSION, $lastDocVersion);
            $oCriteria->add(\AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT');
            $oDataset = \AppDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            if (($aOD['OUT_DOC_VERSIONING']) && ($lastDocVersion != 0)) {
                //Create new Version of current output
                $lastDocVersion ++;
                if ($aRow = $oDataset->getRow()) {
                    $aFields = array('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion + 1,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags);
                    $oAppDocument = new \AppDocument();
                    $oAppDocument->create($aFields);
                    $sDocUID = $aRow['APP_DOC_UID'];
                }
            } else {
                ////No versioning so Update a current Output or Create new if no exist
                if ($aRow = $oDataset->getRow()) {
                    //Update
                    $aFields = array('APP_DOC_UID' => $aRow['APP_DOC_UID'],'APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
                    );
                    $oAppDocument = new \AppDocument();
                    $oAppDocument->update($aFields);
                    $sDocUID = $aRow['APP_DOC_UID'];
                } else {
                    //we are creating the appdocument row
                    //create
                    if ($lastDocVersion == 0) {
                        $lastDocVersion ++;
                    }
                    $aFields = array('APP_UID' => $sApplication,'DEL_INDEX' => $index,'DOC_UID' => $outputID,'DOC_VERSION' => $lastDocVersion,'USR_UID' => $sUserLogged,'APP_DOC_TYPE' => 'OUTPUT','APP_DOC_CREATE_DATE' => date('Y-m-d H:i:s'),'APP_DOC_FILENAME' => $sFilename,'FOLDER_UID' => $folderId,'APP_DOC_TAGS' => $fileTags
                    );
                    $oAppDocument = new \AppDocument();
                    $aFields['APP_DOC_UID'] = $sDocUID = $oAppDocument->create($aFields);
                }
            }
            $sFilename = $aFields['APP_DOC_UID'] . "_" . $lastDocVersion;
            $pathOutput = PATH_DOCUMENT . \G::getPathFromUID($sApplication) . PATH_SEP . 'outdocs' . PATH_SEP; //G::pr($sFilename);die;
            \G::mk_dir($pathOutput);
            $aProperties = array();
            if (! isset($aOD['OUT_DOC_MEDIA'])) {
                $aOD['OUT_DOC_MEDIA'] = 'Letter';
            }
            if (! isset($aOD['OUT_DOC_LEFT_MARGIN'])) {
                $aOD['OUT_DOC_LEFT_MARGIN'] = '15';
            }
            if (! isset($aOD['OUT_DOC_RIGHT_MARGIN'])) {
                $aOD['OUT_DOC_RIGHT_MARGIN'] = '15';
            }
            if (! isset($aOD['OUT_DOC_TOP_MARGIN'])) {
                $aOD['OUT_DOC_TOP_MARGIN'] = '15';
            }
            if (! isset($aOD['OUT_DOC_BOTTOM_MARGIN'])) {
                $aOD['OUT_DOC_BOTTOM_MARGIN'] = '15';
            }
            $aProperties['media'] = $aOD['OUT_DOC_MEDIA'];
            $aProperties['margins'] = array('left' => $aOD['OUT_DOC_LEFT_MARGIN'],'right' => $aOD['OUT_DOC_RIGHT_MARGIN'],'top' => $aOD['OUT_DOC_TOP_MARGIN'],'bottom' => $aOD['OUT_DOC_BOTTOM_MARGIN']
            );
            if (isset($aOD['OUT_DOC_REPORT_GENERATOR'])) {
                $aProperties['report_generator'] = $aOD['OUT_DOC_REPORT_GENERATOR'];
            }
            $this->generate($outputID, $Fields['APP_DATA'], $pathOutput, $sFilename, $aOD['OUT_DOC_TEMPLATE'], (boolean) $aOD['OUT_DOC_LANDSCAPE'], $aOD['OUT_DOC_GENERATE'], $aProperties, $applicationUid);

            //Plugin Hook PM_UPLOAD_DOCUMENT for upload document
            $oPluginRegistry = PluginRegistry::loadSingleton();
            if ($oPluginRegistry->existsTrigger(PM_UPLOAD_DOCUMENT) && class_exists('uploadDocumentData')) {
                $triggerDetail = $oPluginRegistry->getTriggerInfo(PM_UPLOAD_DOCUMENT);
                $aFields['APP_DOC_PLUGIN'] = $triggerDetail->getNamespace();
                $oAppDocument1 = new \AppDocument();
                $oAppDocument1->update($aFields);
                $sPathName = PATH_DOCUMENT . \G::getPathFromUID($sApplication) . PATH_SEP;
                $oData['APP_UID'] = $sApplication;
                $oData['ATTACHMENT_FOLDER'] = true;
                switch ($aOD['OUT_DOC_GENERATE']) {
                    case "BOTH":
                        $documentData = new \uploadDocumentData($sApplication, $sUserLogged, $pathOutput . $sFilename . '.pdf', $oAppDocument->getAppDocFilename() . '.pdf', $sDocUID, $oAppDocument->getDocVersion());
                        $documentData->sFileType = "PDF";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink($pathOutput . $sFilename . '.pdf');
                        }
                        $documentData = new \uploadDocumentData($sApplication, $sUserLogged, $pathOutput . $sFilename . '.doc', $oAppDocument->getAppDocFilename() . '.doc', $sDocUID, $oAppDocument->getDocVersion());
                        $documentData->sFileType = "DOC";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink($pathOutput . $sFilename . '.doc');
                        }
                        break;
                    case "PDF":
                        $documentData = new \uploadDocumentData($sApplication, $sUserLogged, $pathOutput . $sFilename . '.pdf', $oAppDocument->getAppDocFilename() . '.pdf', $sDocUID, $oAppDocument->getDocVersion());
                        $documentData->sFileType = "PDF";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink($pathOutput . $sFilename . '.pdf');
                        }
                        break;
                    case "DOC":
                        $documentData = new \uploadDocumentData($sApplication, $sUserLogged, $pathOutput . $sFilename . '.doc', $oAppDocument->getAppDocFilename() . '.doc', $sDocUID, $oAppDocument->getDocVersion());
                        $documentData->sFileType = "DOC";
                        $documentData->bUseOutputFolder = true;
                        $uploadReturn = $oPluginRegistry->executeTriggers(PM_UPLOAD_DOCUMENT, $documentData);
                        if ($uploadReturn) {
                            //Only delete if the file was saved correctly
                            unlink($pathOutput . $sFilename . '.doc');
                        }
                        break;
                }
            }
            $g->sessionVarRestore();
            $oAppDocument = \AppDocumentPeer::retrieveByPK($aFields['APP_DOC_UID'], $lastDocVersion);
            if ($oAppDocument->getAppDocStatus() == 'DELETED') {
                $oAppDocument->setAppDocStatus('ACTIVE');
                $oAppDocument->save();
            }
            $response = $this->getCasesOutputDocument($applicationUid, $userUid, $aFields['APP_DOC_UID']);
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /*
     * Generate the output document
     * @param string $sUID
     * @param array $aFields
     * @param string $sPath
     * @return variant
     */
    public function generate($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape = false, $sTypeDocToGener = 'BOTH', $aProperties = array(), $sApplication)
    {
        if (($sUID != '') && is_array($aFields) && ($sPath != '')) {
            $sContent = \G::replaceDataGridField($sContent, $aFields);
            \G::verifyPath($sPath, true);
            //Start - Create .doc
            $oFile = fopen($sPath . $sFilename . '.doc', 'wb');
            $size = array();
            $size["Letter"] = "216mm  279mm";
            $size["Legal"] = "216mm  357mm";
            $size["Executive"] = "184mm  267mm";
            $size["B5"] = "182mm  257mm";
            $size["Folio"] = "216mm  330mm";
            $size["A0Oversize"] = "882mm  1247mm";
            $size["A0"] = "841mm  1189mm";
            $size["A1"] = "594mm  841mm";
            $size["A2"] = "420mm  594mm";
            $size["A3"] = "297mm  420mm";
            $size["A4"] = "210mm  297mm";
            $size["A5"] = "148mm  210mm";
            $size["A6"] = "105mm  148mm";
            $size["A7"] = "74mm   105mm";
            $size["A8"] = "52mm   74mm";
            $size["A9"] = "37mm   52mm";
            $size["A10"] = "26mm   37mm";
            $size["Screenshot640"] = "640mm  480mm";
            $size["Screenshot800"] = "800mm  600mm";
            $size["Screenshot1024"] = "1024mm 768mm";
            $sizeLandscape["Letter"] = "279mm  216mm";
            $sizeLandscape["Legal"] = "357mm  216mm";
            $sizeLandscape["Executive"] = "267mm  184mm";
            $sizeLandscape["B5"] = "257mm  182mm";
            $sizeLandscape["Folio"] = "330mm  216mm";
            $sizeLandscape["A0Oversize"] = "1247mm 882mm";
            $sizeLandscape["A0"] = "1189mm 841mm";
            $sizeLandscape["A1"] = "841mm  594mm";
            $sizeLandscape["A2"] = "594mm  420mm";
            $sizeLandscape["A3"] = "420mm  297mm";
            $sizeLandscape["A4"] = "297mm  210mm";
            $sizeLandscape["A5"] = "210mm  148mm";
            $sizeLandscape["A6"] = "148mm  105mm";
            $sizeLandscape["A7"] = "105mm  74mm";
            $sizeLandscape["A8"] = "74mm   52mm";
            $sizeLandscape["A9"] = "52mm   37mm";
            $sizeLandscape["A10"] = "37mm   26mm";
            $sizeLandscape["Screenshot640"] = "480mm  640mm";
            $sizeLandscape["Screenshot800"] = "600mm  800mm";
            $sizeLandscape["Screenshot1024"] = "768mm  1024mm";
            if (!isset($aProperties['media'])) {
                $aProperties['media'] = 'Letter';
            }
            if ($sLandscape) {
                $media = $sizeLandscape[$aProperties['media']];
            } else {
                $media = $size[$aProperties['media']];
            }
            $marginLeft = '15';
            if (isset($aProperties['margins']['left'])) {
                $marginLeft = $aProperties['margins']['left'];
            }
            $marginRight = '15';
            if (isset($aProperties['margins']['right'])) {
                $marginRight = $aProperties['margins']['right'];
            }
            $marginTop = '15';
            if (isset($aProperties['margins']['top'])) {
                $marginTop = $aProperties['margins']['top'];
            }
            $marginBottom = '15';
            if (isset($aProperties['margins']['bottom'])) {
                $marginBottom = $aProperties['margins']['bottom'];
            }
            fwrite($oFile, '<html xmlns:v="urn:schemas-microsoft-com:vml"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:w="urn:schemas-microsoft-com:office:word"
            xmlns="http://www.w3.org/TR/REC-html40">
            <head>
            <meta http-equiv=Content-Type content="text/html; charset=utf-8">
            <meta name=ProgId content=Word.Document>
            <meta name=Generator content="Microsoft Word 9">
            <meta name=Originator content="Microsoft Word 9">
            <!--[if !mso]>
            <style>
            v\:* {behavior:url(#default#VML);}
            o\:* {behavior:url(#default#VML);}
            w\:* {behavior:url(#default#VML);}
            .shape {behavior:url(#default#VML);}
            </style>
            <![endif]-->
            <!--[if gte mso 9]><xml>
             <w:WordDocument>
              <w:View>Print</w:View>
              <w:DoNotHyphenateCaps/>
              <w:PunctuationKerning/>
              <w:DrawingGridHorizontalSpacing>9.35 pt</w:DrawingGridHorizontalSpacing>
              <w:DrawingGridVerticalSpacing>9.35 pt</w:DrawingGridVerticalSpacing>
             </w:WordDocument>
            </xml><![endif]-->

            <style>
            <!--
            @page WordSection1
             {size:' . $media . ';
             margin-left:' . $marginLeft . 'mm;
             margin-right:' . $marginRight . 'mm;
             margin-bottom:' . $marginBottom . 'mm;
             margin-top:' . $marginTop . 'mm;
             mso-header-margin:35.4pt;
             mso-footer-margin:35.4pt;
             mso-paper-source:0;}
            div.WordSection1
             {page:WordSection1;}
            -->
            </style>
            </head>
            <body>
            <div class=WordSection1>');
            fwrite($oFile, $sContent);
            fwrite($oFile, "\n</div></body></html>\n\n");
            fclose($oFile);
            /* End - Create .doc */
            if ($sTypeDocToGener == 'BOTH' || $sTypeDocToGener == 'PDF') {
                $oFile = fopen($sPath . $sFilename . '.html', 'wb');
                fwrite($oFile, $sContent);
                fclose($oFile);
                /* Start - Create .pdf */
                if (isset($aProperties['report_generator'])) {
                    switch ($aProperties['report_generator']) {
                        case 'TCPDF':
                        default:
                            $o = new \OutputDocument();
                            if (strlen($sContent) == 0) {
                                libxml_use_internal_errors(true);
                                $o->generateTcpdf($sUID, $aFields, $sPath, $sFilename, ' ', $sLandscape, $aProperties);
                                libxml_use_internal_errors(false);
                            } else {
                                $o->generateTcpdf($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape, $aProperties);
                            }
                            break;
                    }
                } else {
                    $o = new \OutputDocument();
                    $o->generateTcpdf($sUID, $aFields, $sPath, $sFilename, $sContent, $sLandscape, $aProperties);
                }
            }
            //end if $sTypeDocToGener
            /* End - Create .pdf */
        } else {
            return \PEAR::raiseError(
                null,
                G_ERROR_USER_UID,
                null,
                null,
                'You tried to call to a generate method without send the Output Document UID, fields to use and the file path!',
                'G_Error',
                true
            );
        }
    }

    /**
     * Check of user has permission of process
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegation index
     * @param string $userUid        Unique id of User
     * @param string $appDocumentUid Unique id of Documents in an Application
     * @param string $action
     *
     * @return bool Return
     */
    public function checkProcessPermission($applicationUid, $delIndex, $userUid, $appDocumentUid, $action = 'VIEW')
    {
        $delIndex = (!is_null($delIndex))? $delIndex : 0;

        $case = new \ProcessMaker\BusinessModel\Cases();
        $arrayApplicationData = $case->getApplicationRecordByPk($applicationUid, [], false);

        $processUid = $arrayApplicationData['PRO_UID'];

        $case = new \Cases();
        $arrayAllObjectsFrom = $case->getAllObjectsFrom($processUid, $applicationUid, null, $userUid, $action, $delIndex);

        if (array_key_exists('OUTPUT_DOCUMENTS', $arrayAllObjectsFrom) && !empty($arrayAllObjectsFrom['OUTPUT_DOCUMENTS'])) {
            foreach ($arrayAllObjectsFrom['OUTPUT_DOCUMENTS'] as $value) {
                if ($value == $appDocumentUid) {
                    return true;
                }
            }
        }

        //Return
        return false;
    }

    /**
     * Check of user
     *
     * @param string $applicationUid Unique id of Case
     * @param string $appDocumentUid Unique id of Documents in an Application
     * @param string $userUid        Unique id of User
     *
     * @return bool Return
     */
    public function checkUser($applicationUid, $appDocumentUid, $userUid)
    {
        $criteria = new \Criteria('workflow');

        $criteria->addSelectColumn(\AppDocumentPeer::DOC_UID);

        $criteria->add(\AppDocumentPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
        $criteria->add(\AppDocumentPeer::APP_DOC_UID, $appDocumentUid, \Criteria::EQUAL);
        $criteria->add(\AppDocumentPeer::APP_DOC_TYPE, 'OUTPUT', \Criteria::EQUAL);
        $criteria->add(\AppDocumentPeer::USR_UID, $userUid, \Criteria::EQUAL);

        $rsCriteria = \AppDocumentPeer::doSelectRS($criteria);

        if ($rsCriteria->next()) {
            return true;
        }

        //Return
        return false;
    }
}
