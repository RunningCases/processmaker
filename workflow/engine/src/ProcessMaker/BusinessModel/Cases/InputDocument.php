<?php
namespace ProcessMaker\BusinessModel\Cases;

class InputDocument
{
    /**
     * Check if the user has permissions
     *
     * @param string $applicationUid   Unique id of Case
     * @param string $delIndex         Delegataion index
     * @param string $userUid          Unique id of User
     * @param string $inputDocumentUid
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

            //Verify data Supervisor
            $application = \ApplicationPeer::retrieveByPK($applicationUid);

            $flagSupervisor = 0;

            $supervisor = new \ProcessMaker\BusinessModel\ProcessSupervisor();
            $arraySupervisor = $supervisor->getProcessSupervisors($application->getProUid());

            foreach ($arraySupervisor as $value) {
                if($value["usr_uid"] == $userUid) {
                   $flagSupervisor = 1;
                   break;
                }
            }

            if ($flagInbox == 0 && $flagSupervisor == 0) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_INPUT_DOCUMENT", array($userUid)));
            }

            //Verify data permission
            $flagPermission = 0;

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\AppDocumentPeer::DOC_UID);

            $criteria->add(\AppDocumentPeer::APP_DOC_UID, $appDocumentUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_UID, $applicationUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_DOC_TYPE, "INPUT", \Criteria::EQUAL);

            $rsCriteria = \AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $inputDocumentUid = $row["DOC_UID"];

                //Criteria
                $criteria2 = new \Criteria("workflow");

                $criteria2->addSelectColumn(\ObjectPermissionPeer::OP_UID);

                $criteria2->add(\ObjectPermissionPeer::PRO_UID, $application->getProUid(), \Criteria::EQUAL);
                $criteria2->add(\ObjectPermissionPeer::OP_OBJ_TYPE, "INPUT", \Criteria::EQUAL);
                $criteria2->add(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, $inputDocumentUid, \Criteria::EQUAL)->addOr(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, "0", \Criteria::EQUAL))->addOr(
                    $criteria2->getNewCriterion(\ObjectPermissionPeer::OP_OBJ_UID, "", \Criteria::EQUAL))
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

            if ($flagPermission == 0) {
                throw new \Exception(\G::LoadTranslation("ID_USER_NOT_HAVE_PERMISSION_DELETE_INPUT_DOCUMENT", array($userUid)));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if not exists input Document in Steps
     *
     * @param string $applicationUid Unique id of Case
     * @param string $delIndex       Delegataion index
     * @param string $appDocumentUid
     *
     * return void Throw exception if not exists input Document in Steps
     */
    public function throwExceptionIfInputDocumentNotExistsInSteps($applicacionUid, $delIndex, $appDocumentUid)
    {
        try {
            //Verify Case
            $appDelegation = \AppDelegationPeer::retrieveByPK($applicacionUid, $delIndex);

            if (is_null($appDelegation)) {
                throw new \Exception(\G::LoadTranslation("ID_CASE_DEL_INDEX_DOES_NOT_EXIST", array("app_uid", $applicacionUid, "del_index", $delIndex)));
            }

            $taskUid = $appDelegation->getTasUid();

            //Verify Steps
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\AppDocumentPeer::DOC_UID);

            $criteria->add(\AppDocumentPeer::APP_DOC_UID, $appDocumentUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_UID, $applicacionUid, \Criteria::EQUAL);
            $criteria->add(\AppDocumentPeer::APP_DOC_TYPE, "INPUT", \Criteria::EQUAL);

            $rsCriteria = \AppDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $inputDocumentUid = $row["DOC_UID"];

                $criteria = new \Criteria("workflow");

                $criteria->addSelectColumn(\StepPeer::STEP_UID);

                $criteria->add(\StepPeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                $criteria->add(\StepPeer::STEP_TYPE_OBJ, "INPUT_DOCUMENT", \Criteria::EQUAL);
                $criteria->add(\StepPeer::STEP_UID_OBJ, $inputDocumentUid, \Criteria::EQUAL);

                $rsCriteria = \StepPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

                if (!$rsCriteria->next()) {
                    throw new \Exception(\G::LoadTranslation("ID_CASES_INPUT_DOCUMENT_DOES_NOT_EXIST", array($appDocumentUid)));
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     *
     * return array Return an array with data of an InputDocument
     */
    public function getCasesInputDocuments($applicationUid, $userUid)
    {
        try {
            $sApplicationUID = $applicationUid;
            $sUserUID = $userUid;
            \G::LoadClass('case');
            $oCase = new \Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCaseRest = new \ProcessMaker\BusinessModel\Cases();
            $oCaseRest->getAllUploadedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID);
            $result = array ();
            global $_DBArray;
            foreach ($_DBArray['inputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['APP_DOC_FILENAME'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] =     $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     * @param string $inputDocumentUid
     *
     * return array Return an array with data of an InputDocument
     */
    public function getCasesInputDocument($applicationUid, $userUid, $inputDocumentUid)
    {
        try {
            $sApplicationUID = $applicationUid;
            $sUserUID = $userUid;
            \G::LoadClass('case');
            $oCase = new \Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCaseRest = new \ProcessMaker\BusinessModel\Cases();
            $oCaseRest->getAllUploadedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID );
            $result = array ();
            global $_DBArray;
            $flagInputDocument = false;

            foreach ($_DBArray['inputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['APP_DOC_FILENAME'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = 'cases/' . $row['DOWNLOAD_LINK'];

                    if ($docrow["app_doc_uid"] == $inputDocumentUid) {
                        $flagInputDocument = true;

                        $appDocument = \AppDocumentPeer::retrieveByPK($inputDocumentUid, $row["DOC_VERSION"]);

                        if (is_null($appDocument)) {
                            $flagInputDocument = false;
                        }

                        $result = $docrow;
                        break;
                    }
                }
            }

            if (!$flagInputDocument) {
                throw new \Exception(\G::LoadTranslation("ID_CASES_INPUT_DOES_NOT_EXIST", array($inputDocumentUid)));
            }

            $oResponse = json_decode(json_encode($result), false);
            return $oResponse;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete InputDocument
     *
     * @param string $inputDocumentUid
     *
     * return array Return an array with data of an InputDocument
     */
    public function removeInputDocument($inputDocumentUid)
    {
        try {
            $oAppDocument = \AppDocumentPeer::retrieveByPK( $inputDocumentUid, 1 );
            if (is_null( $oAppDocument ) || $oAppDocument->getAppDocStatus() == 'DELETED') {
                throw new \Exception(\G::LoadTranslation("ID_CASES_INPUT_DOES_NOT_EXIST", array($inputDocumentUid)));
            }
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $ws->removeDocument($inputDocumentUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $applicationUid
     * @param string $taskUid
     * @param string $appDocComment
     * @param string $inputDocumentUid
     * @param string $userUid
     *
     * return array Return an array with data of an InputDocument
     */
    public function addCasesInputDocument($applicationUid, $taskUid, $appDocComment, $inputDocumentUid, $userUid)
    {
        try {
            if ((isset( $_FILES['form'] )) && ($_FILES['form']['error'] != 0)) {
                $code = $_FILES['form']['error'];
                switch ($code) {
                    case UPLOAD_ERR_INI_SIZE:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_INI_SIZE' );
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_FORM_SIZE' );
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_PARTIAL' );
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_NO_FILE' );
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_NO_TMP_DIR' );
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_CANT_WRITE' );
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_EXTENSION' );
                        break;
                    default:
                        $message = \G::LoadTranslation( 'ID_UPLOAD_ERR_UNKNOWN' );
                        break;
                }
                \G::SendMessageText( $message, "ERROR" );
                $backUrlObj = explode( "sys" . SYS_SYS, $_SERVER['HTTP_REFERER'] );
                \G::header( "location: " . "/sys" . SYS_SYS . $backUrlObj[1] );
                die();
            }
            \G::LoadClass("case");
            $appDocUid = \G::generateUniqueID();
            $docVersion = '';
            $appDocType = 'INPUT';
            $case = new \Cases();
            $delIndex = \AppDelegation::getCurrentIndex($applicationUid);
            $case->thisIsTheCurrentUser($applicationUid, $delIndex, $userUid, "REDIRECT", "casesListExtJs");
            //Load the fields
            $arrayField = $case->loadCase($applicationUid);
            $arrayField["APP_DATA"] = array_merge($arrayField["APP_DATA"], \G::getSystemConstants());
            //Triggers
            $arrayTrigger = $case->loadTriggers($taskUid, "INPUT_DOCUMENT", $inputDocumentUid, "AFTER");
            //Add Input Document
            if (empty($_FILES)) {
                throw new \Exception(\G::LoadTranslation("ID_CASES_INPUT_FILENAME_DOES_NOT_EXIST"));
            }
            if (!$_FILES["form"]["error"]) {
                $_FILES["form"]["error"] = 0;
            }
            if (isset($_FILES) && isset($_FILES["form"]) && count($_FILES["form"]) > 0) {
                $appDocUid = $case->addInputDocument($inputDocumentUid,
                    $appDocUid,
                    $docVersion,
                    $appDocType,
                    $appDocComment,
                    '',
                    $applicationUid,
                    $delIndex,
                    $taskUid,
                    $userUid,
                    "xmlform",
                    $_FILES["form"]["name"],
                    $_FILES["form"]["error"],
                    $_FILES["form"]["tmp_name"]);
            }
            //Trigger - Execute after - Start
            $arrayField["APP_DATA"] = $case->executeTriggers ($taskUid,
                "INPUT_DOCUMENT",
                $inputDocumentUid,
                "AFTER",
                $arrayField["APP_DATA"]);
            //Trigger - Execute after - End
            //Save data
            $arrayData = array();
            $arrayData["APP_NUMBER"] = $arrayField["APP_NUMBER"];
            //$arrayData["APP_PROC_STATUS"] = $arrayField["APP_PROC_STATUS"];
            $arrayData["APP_DATA"]  = $arrayField["APP_DATA"];
            $arrayData["DEL_INDEX"] = $delIndex;
            $arrayData["TAS_UID"]   = $taskUid;
            $case->updateCase($applicationUid, $arrayData);
            return($this->getCasesInputDocument($applicationUid, $userUid, $appDocUid));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
