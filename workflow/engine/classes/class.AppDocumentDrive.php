<?php

G::LoadClass( "pmDrive" );

/**
 * Class InputDocumentDrive
 */
class AppDocumentDrive
{
    /**
     * @var PMDrive $drive
     */
    private $drive;
    /**
     * @var Application $app
     */
    private $app;

    /**
     * @var Users $user
     */
    private $user;

    private $statusDrive;
    private $usersDrive = '';

    /**
     * InputDocumentDrive constructor.
     */
    public function __construct()
    {
        $this->drive = new PMDrive();
        $status = $this->drive->getStatusService();
        $status = !empty($status) ? ($status == 1 ? true : false): false;
        $this->usersDrive = '';
        $this->setStatusDrive($status);
    }

    /**
     * @return boolean
     */
    public function getStatusDrive()
    {
        return $this->statusDrive;
    }

    /**
     * @param boolen $statusDrive
     */
    public function setStatusDrive($statusDrive)
    {
        $this->statusDrive = $statusDrive;
    }

    public function loadUser ($usrUid)
    {
        $this->user = new Users();
        $this->user->load($usrUid);
        $this->drive->setDriveUser($this->user->getUsrEmail());
    }

    public  function loadApplication ($appUid)
    {
        $this->app = new Application();
        $this->app->Load($appUid);
    }

    public function existAppFolderDrive ()
    {
        try {
            if ($this->app->getAppDriveFolderUid() == null) {
                $process = new Process();
                $process->setProUid($this->app->getProUid());

                $result = $this->drive->createFolder(
                    $process->getProTitle() . ' - ' . G::LoadTranslation("ID_CASE") . ' #' . $this->app->getAppNumber(),
                    $this->drive->getFolderIdPMDrive($this->user->getUsrUid())
                );
                $this->app->setAppDriveFolderUid($result->id);
                $this->app->update($this->app->toArray(BasePeer::TYPE_FIELDNAME));
            }
        } catch (Exception $e) {
            error_log('Error create folder Drive: ' . $e->getMessage());
        }
    }

    public function permission ($appUid, $folderUid, $fileIdDrive)
    {
        $criteria = new Criteria('workflow');
        $criteria->addSelectColumn(ApplicationPeer::PRO_UID);
        $criteria->addSelectColumn(TaskUserPeer::TAS_UID);
        $criteria->addSelectColumn(TaskUserPeer::USR_UID);
        $criteria->addSelectColumn(TaskUserPeer::TU_RELATION);

        $criteria->add(ApplicationPeer::APP_UID, $appUid);
        $criteria->addJoin(ApplicationPeer::PRO_UID, TaskPeer::PRO_UID, Criteria::LEFT_JOIN);
        $criteria->addJoin(TaskPeer::TAS_UID, TaskUserPeer::TAS_UID, Criteria::LEFT_JOIN);

        $rs = ApplicationPeer::doSelectRS($criteria);
        $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

        $userPermission = array();
        $user = new Users();

        while ($rs->next()) {
            $row = $rs->getRow();
            if ($row['TU_RELATION'] == 1) {
                //users
                $dataUser = $user->load($row['USR_UID']);
                if (array_search($dataUser['USR_EMAIL'], $userPermission) === false) {
                    $objectPermissions = $this->getAllObjects($row['PRO_UID'], $appUid, $row['TAS_UID'],
                        $row['USR_UID']);
                    $userPermission[] = $dataUser['USR_EMAIL'];
                }
            } else {
                //Groups
                $criteria = new Criteria('workflow');
                $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
                $criteria->addSelectColumn(UsersPeer::USR_UID);
                $criteria->add(GroupUserPeer::GRP_UID, $row['USR_UID']);
                $criteria->addJoin(GroupUserPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

                $rsGroup = AppDelegationPeer::doSelectRS($criteria);
                $rsGroup->setFetchmode(ResultSet::FETCHMODE_ASSOC);
                while ($rsGroup->next()) {
                    $aRow = $rsGroup->getRow();
                    if (array_search($aRow['USR_EMAIL'], $userPermission) === false) {
                        $objectPermissions = $this->getAllObjects($row['PRO_UID'], $appUid,
                            $row['TAS_UID'], $aRow['USR_UID']);
                        $userPermission[] = $aRow['USR_EMAIL'];
                    }
                }
            }
        }
        $userPermission = array_unique($userPermission);

        foreach ($userPermission as $key => $val) {
            $this->drive->setPermission($folderUid, $val, 'user', 'writer');
            $this->drive->setPermission($fileIdDrive, $val);
        }
    }

    public function addUserDrive ($email)
    {
        if (empty($email)) {
            return;
        }
        if ($this->usersDrive == '') {
            $this->usersDrive = $email;
        } else {
            $emails = explode('|', $this->usersDrive);
            if (array_search($email, $emails) === false) {
                $this->usersDrive .= '|' . $email;
            }
        }
    }
    /**
     * @param AppDocument $appDoc
     * @param array $arrayTask
     * @param $arrayData
     *
     * @throws \Exception
     */
    public function getEmailUsersTask($arrayTask, $arrayData)
    {
        try {
            G::LoadClass("tasks");
            G::LoadClass("groups");
            G::LoadClass("spool");

            $task = new Tasks();
            $group = new Groups();
            $oUser = new Users();

            foreach ($arrayTask as $aTask) {
                switch ($aTask["TAS_ASSIGN_TYPE"]) {
                    case "SELF_SERVICE":
                        if (isset($aTask["TAS_UID"]) && !empty($aTask["TAS_UID"])) {
                            $usersTask = array();

                            $groupsTask = $task->getGroupsOfTask($aTask["TAS_UID"], 1);

                            foreach ($groupsTask as $arrayGroup) {
                                $usersGroup = $group->getUsersOfGroup($arrayGroup["GRP_UID"]);

                                foreach ($usersGroup as $userGroup) {
                                    $usersTask[] = $userGroup["USR_UID"];
                                }
                            }

                            $groupsTask = $task->getUsersOfTask($aTask["TAS_UID"], 1);

                            foreach ($groupsTask as $userGroup) {
                                $usersTask[] = $userGroup["USR_UID"];
                            }

                            $criteria = new Criteria("workflow");

                            $criteria->addSelectColumn(UsersPeer::USR_UID);
                            $criteria->addSelectColumn(UsersPeer::USR_EMAIL);
                            $criteria->add(UsersPeer::USR_UID, $usersTask, Criteria::IN);
                            $rsCriteria = UsersPeer::doSelectRs($criteria);
                            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                            while ($rsCriteria->next()) {
                                $row = $rsCriteria->getRow();
                                $this->addUserDrive($row['USR_EMAIL']);
                            }
                        }
                        break;
                    case "MULTIPLE_INSTANCE":
                        $oDerivation = new Derivation();
                        $userFields = $oDerivation->getUsersFullNameFromArray($oDerivation->getAllUsersFromAnyTask($aTask["TAS_UID"]));
                        if(isset($userFields)){
                            foreach($userFields as $row){
                                $this->addUserDrive($row['USR_EMAIL']);
                            }
                        }
                        break;
                    case "MULTIPLE_INSTANCE_VALUE_BASED":
                        $taskNext = $task->load($aTask["TAS_UID"]);
                        if(isset($taskNext["TAS_ASSIGN_VARIABLE"]) && !empty($taskNext["TAS_ASSIGN_VARIABLE"])){
                            $nextTaskAssignVariable = trim($taskNext["TAS_ASSIGN_VARIABLE"], " @#");
                            $arrayUsers = $arrayData[$nextTaskAssignVariable];
                            $oDerivation = new Derivation();
                            $userFields = $oDerivation->getUsersFullNameFromArray($arrayUsers);
                            foreach ($userFields as $row) {
                                $this->addUserDrive($row['USR_EMAIL']);
                            }
                        }
                        break;
                    default:
                        if (isset($aTask["USR_UID"]) && !empty($aTask["USR_UID"])) {
                            $aUser = $oUser->load($aTask["USR_UID"]);
                            $this->addUserDrive($aUser["USR_EMAIL"]);
                        }
                        break;
                }
            }
        } catch (Exception $exception) {
            error_log('Error: ' . $exception);
        }
    }

    /**
     * @param array $appDocument
     * @param string $typeDocument type document INPUT, OUTPUT_DOC, OUTPUT_PDF, ATTACHED
     * @param string $mime MIME type of the file to insert.
     * @param string $src location of the file to insert.
     * @param string $name Title of the file to insert, including the extension.
     * return string uid
     */
    public function upload ($appDocument, $typeDocument, $mime, $src, $name)
    {
        try
        {
            $idFileDrive = null;
            $this->existAppFolderDrive();
            $appDoc = new AppDocument();
            $result = $this->drive->uploadFile(
                $mime,
                $src,
                $name,
                $this->app->getAppDriveFolderUid()
            );
            if ($result->id !== null) {
                $idFileDrive = $result->id;
                $appDoc->setDriveDownload($typeDocument, $result->id);
                $appDoc->update($appDocument);
            }
            return $idFileDrive;
        } catch (Exception $e) {
            error_log('Error upload file drive: ' . $e->getMessage());
        }
    }

    /**
     * Download file drive
     * @param $uidFileDrive
     */
    public function download ($uidFileDrive)
    {
        try
        {
            $result = $this->drive->downloadFile($uidFileDrive);

        } catch (Exception $e) {
            error_log('Error Download file drive: ' . $e->getMessage());
        }
        return $result;
    }


    /**
     * @param array $data
     * @param string $typeDoc value INPUT, OUTPUT_DOC, OUTPUT_PDF, ATTACHED
     *
     * @return string url drive
     */
    public function changeUrlDrive ($data, $typeDoc)
    {
        try
        {

            $urlDrive = $data['APP_DOC_DRIVE_DOWNLOAD'];
            if ($this->getStatusDrive()) {
                $driveDownload = @unserialize($data['APP_DOC_DRIVE_DOWNLOAD']);
                $urlDrive = $driveDownload !== false
                    && is_array($driveDownload)
                    && array_key_exists($typeDoc, $driveDownload) ?
                    $driveDownload[$typeDoc] : $urlDrive;
            }

        } catch (Exception $e) {
            error_log('Error change url drive: ' . $e->getMessage());
        }

        return $urlDrive;
    }

    /**
     * Synchronize documents drive
     *
     * @param boolean $log enable print cron
     */
    public function synchronizeDrive ($log)
    {
        if (!$this->statusDrive) {
            error_log("It has not enabled Feature Gmail");
            return;
        }
        $criteria = new Criteria( 'workflow' );
        $criteria->addSelectColumn(AppDocumentPeer::APP_DOC_UID);
        $criteria->addSelectColumn(AppDocumentPeer::DOC_VERSION);
        $criteria->add( AppDocumentPeer::SYNC_WITH_DRIVE, 'UNSYNCHRONIZED' );
        //Verify other permissions
        /*$criteria->add(
            $criteria->getNewCriterion( AppDocumentPeer::SYNC_WITH_DRIVE, 'UNSYNCHRONIZED', Criteria::EQUAL )->
            addOr($criteria->getNewCriterion( AppDocumentPeer::SYNC_WITH_DRIVE, 'NO_EXIST_FILE_PM', Criteria::NOT_EQUAL )->
            addAnd($criteria->getNewCriterion( AppDocumentPeer::SYNC_PERMISSIONS, null, Criteria::NOT_EQUAL )))
        );*/
        $criteria->addAscendingOrderByColumn( 'APP_DOC_CREATE_DATE' );
        $criteria->addAscendingOrderByColumn( 'APP_UID' );
        $rs = AppDocumentPeer::doSelectRS( $criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        while ($rs->next()) {
            $row = $rs->getRow();
            $appDoc = new AppDocument();
            $fields = $appDoc->load($row['APP_DOC_UID'], $row['DOC_VERSION']);

            $appDocUid = $appDoc->getAppDocUid();
            $docVersion = $appDoc->getDocVersion();
            $filename = pathinfo( $appDoc->getAppDocFilename() );
            $name = !empty($filename['basename'])? $filename['basename'] : '';
            $ext = !empty($filename['extension'])? $filename['extension'] : '';
            $appUid = G::getPathFromUID($appDoc->getAppUid());
            $file = G::getPathFromFileUID($appDoc->getAppUid(), $appDocUid );


            $sw_file_exists_doc = false;
            $sw_file_exists_pdf = false;
            if ($appDoc->getAppDocType() == 'OUTPUT') {
                //$name = substr($name, 1, -1);
                $realPathDoc = PATH_DOCUMENT . $appUid . '/outdocs/' . $appDocUid . '_' . $docVersion . '.' . 'doc';
                $realPathDoc1 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '_' . $docVersion . '.' . 'doc';
                $realPathDoc2 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '.' . 'doc';

                $sw_file_exists = false;
                if (file_exists( $realPathDoc )) {
                    $sw_file_exists = true;
                    $sw_file_exists_doc = true;
                } elseif (file_exists( $realPathDoc1 )) {
                    $sw_file_exists = true;
                    $sw_file_exists_doc = true;
                    $realPathDoc = $realPathDoc1;
                } elseif (file_exists( $realPathDoc2 )) {
                    $sw_file_exists = true;
                    $sw_file_exists_doc = true;
                    $realPathDoc = $realPathDoc2;
                }

                $realPathPdf = PATH_DOCUMENT . $appUid . '/outdocs/' . $appDocUid . '_' . $docVersion . '.' . 'pdf';
                $realPathPdf1 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '_' .$docVersion . '.' . 'pdf';
                $realPathPdf2 = PATH_DOCUMENT . $appUid . '/outdocs/' . $name . '.' . 'pdf';

                if (file_exists( $realPathPdf )) {
                    $sw_file_exists = true;
                    $sw_file_exists_pdf = true;
                } elseif (file_exists( $realPathPdf1 )) {
                    $sw_file_exists = true;
                    $sw_file_exists_pdf = true;
                    $realPathPdf = $realPathPdf1;
                } elseif (file_exists( $realPathPdf2 )) {
                    $sw_file_exists = true;
                    $sw_file_exists_pdf = true;
                    $realPathPdf = $realPathPdf2;
                }
            } else {
                $realPath = PATH_DOCUMENT .  $appUid . '/' . $file[0] . $file[1] . '_' . $docVersion . '.' . $ext;
                $realPath1 = PATH_DOCUMENT . $appUid . '/' . $file[0] . $file[1] . '.' . $ext;
                $sw_file_exists = false;
                if (file_exists( $realPath )) {
                    $sw_file_exists = true;
                } elseif (file_exists( $realPath1 )) {
                    $sw_file_exists = true;
                    $realPath = $realPath1;
                }
            }
            if ($sw_file_exists) {

                $this->loadApplication($appDoc->getAppUid());

                $emails = $appDoc->getSyncPermissions();
                $emails = !empty($emails) ? explode('|', $emails) : array();
                $result = null;
                foreach ($emails as $index => $email) {
                    if (!empty($email)) {
                        if ($index == 0 && $fields['SYNC_WITH_DRIVE'] == 'UNSYNCHRONIZED') {
                            if ($log) {
                                eprintln('upload file:' .  $name , 'green');
                            }
                            $this->drive->setDriveUser($email);
                            $this->loadUser($fields['USR_UID']);
                            $info = finfo_open(FILEINFO_MIME_TYPE);


                            if ($appDoc->getAppDocType() == 'OUTPUT') {

                                if ($sw_file_exists_doc) {
                                    $nameDoc = explode('/', $realPathDoc);
                                    $result = $this->upload($fields, 'OUTPUT_DOC', 'application/msword', $realPathDoc, array_pop($nameDoc));
                                }
                                if ($sw_file_exists_pdf) {
                                    $namePdf = explode('/', $realPathPdf);
                                    $mime = finfo_file($info, $realPathPdf);
                                    $result = $this->upload($fields, 'OUTPUT_PDF', $mime, $realPathPdf, array_pop($namePdf));
                                }
                            } else {
                                $mime = finfo_file($info, $realPath);
                                $result = $this->upload($fields, $appDoc->getAppDocType(), $mime, $realPath, $name);
                            }

                            if ($log) {
                                eprintln('Set Permission:' .  $email , 'green');
                            }

                            $this->drive->setPermission($this->app->getAppDriveFolderUid(), $email, 'user', 'writer');
                        } else {
                            if ($log) {
                                eprintln('Set Permission:' .  $email , 'green');
                            }
                            $this->drive->setPermission($this->app->getAppDriveFolderUid(), $email, 'user', 'writer');
                        }

                    }
                }
                if ($result != null) {
                    $fields['SYNC_WITH_DRIVE'] = 'SYNCHRONIZED';
                    $fields['SYNC_PERMISSIONS'] = null;
                }
            } else {
                $fields['SYNC_WITH_DRIVE'] = 'NO_EXIST_FILE_PM';
                if ($log) {
                    eprintln('File no exists:' . $name , 'red');
                }
            }
            $appDoc->update($fields);
        }
    }

    public function addUsersDocumentDrive ($appUid, $arrayTask, $arrayData )
    {
        $this->getEmailUsersTask($arrayTask, $arrayData);

        $criteria = new Criteria( 'workflow' );
        $criteria->add( AppDocumentPeer::APP_UID, $appUid );
        $criteria->addAscendingOrderByColumn( 'DOC_VERSION' );
        $rs = AppDocumentPeer::doSelectRS( $criteria );
        $rs->setFetchmode( ResultSet::FETCHMODE_ASSOC );

        $appDoc = new AppDocument();
        while ($rs->next()) {
            $row = $rs->getRow();
            $row['SYNC_PERMISSIONS'] = $this->usersDrive;
            $appDoc->update($row);
        }
    }
}