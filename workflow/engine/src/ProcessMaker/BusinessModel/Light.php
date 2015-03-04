<?php

namespace ProcessMaker\BusinessModel;

use G;
use Criteria;
use UsersPeer;
use AppDelegationPeer;
use AppDelayPeer;

class Light
{

    /**
     * Method get list start case
     *
     * @param $userId User id
     * @return array
     * @throws \Exception
     */
    public function getProcessListStartCase($userId)
    {
        $response = null;
        try {
            $oProcess = new \Process();
            $oCase    = new \Cases();

            //Get ProcessStatistics Info
            $start   = 0;
            $limit   = '';
            $proData = $oProcess->getAllProcesses( $start, $limit, null, null, false, true );

            $processListInitial = $oCase->getStartCasesPerType( $userId, 'category' );

            $processList = array ();
            foreach ($processListInitial as $key => $procInfo) {
                if (isset( $procInfo['pro_uid'] )) {
                    if (trim( $procInfo['cat'] ) == "") {
                        $procInfo['cat'] = "_OTHER_";
                    }
                    $processList[$procInfo['catname']][$procInfo['value']] = $procInfo;
                }
            }

            ksort( $processList );
            foreach ($processList as $key => $processInfo) {
                ksort( $processList[$key] );
            }

            foreach ($proData as $key => $proInfo) {
                $proData[$proInfo['PRO_UID']] = $proInfo;
            }

            $task = new \ProcessMaker\BusinessModel\Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid", "stepUid" => "step_uid"));

            foreach ($processList as $key => $processInfo) {
                $tempTreeChildren = array ();
                foreach ($processList[$key] as $keyChild => $processInfoChild) {
                    $tempTreeChild['text']      = htmlentities($keyChild, ENT_QUOTES, 'UTF-8'); //ellipsis ( $keyChild, 50 );
                    $tempTreeChild['processId'] = $processInfoChild['pro_uid'];
                    $tempTreeChild['taskId']    = $processInfoChild['uid'];
                    $forms = $task->getSteps($processInfoChild['uid']);
                    $newForm = array();
                    $c = 0;
                    foreach ($forms as $k => $form) {
                        if ($form['step_type_obj'] == "DYNAFORM") {
                            $newForm[$c]['formId'] = $form['step_uid_obj'];
                            $newForm[$c]['index'] = $c+1;
                            $newForm[$c]['title'] = $form['obj_title'];
                            $newForm[$c]['description'] = $form['obj_description'];
                            $c++;
                        }
                    }
                    $tempTreeChild['forms'] = $newForm;
                    if (isset( $proData[$processInfoChild['pro_uid']] )) {
                        $tempTreeChildren[] = $tempTreeChild;
                    }
                }
                $response = $tempTreeChildren;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * Get counters each type of list
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public function getCounterCase($userId)
    {
        try {
            $userUid = (isset( $userId ) && $userId != '') ? $userId : null;
            $oAppCache = new \AppCacheView();

            $aTypes = Array ();
            $aTypes['to_do']       = 'toDo';
            $aTypes['draft']       = 'draft';
            $aTypes['cancelled']   = 'cancelled';
            $aTypes['sent']        = 'participated';
            $aTypes['paused']      = 'paused';
            $aTypes['completed']   = 'completed';
            $aTypes['selfservice'] = 'unassigned';

            $aCount = $oAppCache->getAllCounters( array_keys( $aTypes ), $userUid );

            $response = Array ();
            foreach ($aCount as $type => $count) {
                $response[$aTypes[$type]] = $count;
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * @param $sAppUid
     * @return Criteria
     */
    public function getTransferHistoryCriteria($sAppUid)
    {
        $c = new Criteria('workflow');
        $c->addAsColumn('TAS_TITLE', 'TAS_TITLE.CON_VALUE');
        $c->addSelectColumn(UsersPeer::USR_FIRSTNAME);
        $c->addSelectColumn(UsersPeer::USR_LASTNAME);
        $c->addSelectColumn(AppDelegationPeer::DEL_DELEGATE_DATE);
        $c->addSelectColumn(AppDelegationPeer::PRO_UID);
        $c->addSelectColumn(AppDelegationPeer::TAS_UID);
        $c->addSelectColumn(AppDelegationPeer::APP_UID);
        $c->addSelectColumn(AppDelegationPeer::DEL_INDEX);
        ///-- $c->addAsColumn('USR_NAME', "CONCAT(USR_LASTNAME, ' ', USR_FIRSTNAME)");
        $sDataBase = 'database_' . strtolower(DB_ADAPTER);
        if (G::LoadSystemExist($sDataBase)) {
            G::LoadSystem($sDataBase);
            $oDataBase = new \database();
            $c->addAsColumn('USR_NAME', $oDataBase->concatString("USR_LASTNAME", "' '", "USR_FIRSTNAME"));
            $c->addAsColumn(
                'DEL_FINISH_DATE', $oDataBase->getCaseWhen("DEL_FINISH_DATE IS NULL", "'-'", AppDelegationPeer::DEL_FINISH_DATE)
            );
            $c->addAsColumn(
                'APP_TYPE', $oDataBase->getCaseWhen("DEL_FINISH_DATE IS NULL", "'IN_PROGRESS'", AppDelayPeer::APP_TYPE)
            );
        }
        $c->addSelectColumn(AppDelegationPeer::DEL_INIT_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_ENABLE_ACTION_DATE);
        $c->addSelectColumn(AppDelayPeer::APP_DISABLE_ACTION_DATE);
        //APP_DELEGATION LEFT JOIN USERS
        $c->addJoin(AppDelegationPeer::USR_UID, UsersPeer::USR_UID, Criteria::LEFT_JOIN);

        //APP_DELAY FOR MORE DESCRIPTION
        //$c->addJoin(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX, Criteria::LEFT_JOIN);
        //$c->addJoin(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID, Criteria::LEFT_JOIN);
        $del = \DBAdapter::getStringDelimiter();
        $app = array();
        $app[] = array(AppDelegationPeer::DEL_INDEX, AppDelayPeer::APP_DEL_INDEX);
        $app[] = array(AppDelegationPeer::APP_UID, AppDelayPeer::APP_UID);
        $c->addJoinMC($app, Criteria::LEFT_JOIN);

        //LEFT JOIN CONTENT TAS_TITLE
        $c->addAlias("TAS_TITLE", 'CONTENT');
        $del = \DBAdapter::getStringDelimiter();
        $appTitleConds = array();
        $appTitleConds[] = array(AppDelegationPeer::TAS_UID, 'TAS_TITLE.CON_ID');
        $appTitleConds[] = array('TAS_TITLE.CON_CATEGORY', $del . 'TAS_TITLE' . $del);
        $appTitleConds[] = array('TAS_TITLE.CON_LANG', $del . SYS_LANG . $del);
        $c->addJoinMC($appTitleConds, Criteria::LEFT_JOIN);

        //WHERE
        $c->add(AppDelegationPeer::APP_UID, $sAppUid);

        //ORDER BY
        $c->clearOrderByColumns();
        $c->addAscendingOrderByColumn(AppDelegationPeer::DEL_DELEGATE_DATE);

        return $c;
    }

    /**
     * GET history of case
     *
     * @param $app_uid
     * @return array
     * @throws \Exception
     */
    public function getCasesListHistory($app_uid)
    {
        G::LoadClass( 'case' );
        G::LoadClass( "BasePeer" );

        //global $G_PUBLISH;
        $c = $this->getTransferHistoryCriteria( $app_uid );

        //$result     = new \stdClass();
        $aProcesses = Array ();

        $rs = \GulliverBasePeer::doSelectRs( $c );
        $rs->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $rs->next();
        for ($j = 0; $j < $rs->getRecordCount(); $j ++) {
            $result               = $rs->getRow();
            $result["ID_HISTORY"] = $result["PRO_UID"] . '_' . $result["APP_UID"] . '_' . $result["TAS_UID"];
            $aProcesses[]         = $result;
            $rs->next();
            $processUid = $result["PRO_UID"];
        }

        $process = new \Process();

        $arrayProcessData = $process->load($processUid);

        //$newDir = '/tmp/test/directory';
        //G::verifyPath( $newDir );

        $result = array();
        $result["processName"] = $arrayProcessData["PRO_TITLE"];
        //$result["PRO_DESCRIPTION"] = $arrayProcessData["PRO_DESCRIPTION"];
        $result['flow'] = $aProcesses;
        return $result;
    }

    /**
     * starting one case
     *
     * @param string $userId
     * @param string $proUid
     * @param string $taskUid
     * @return array
     * @throws \Exception
     */
    public function startCase($userId = '', $proUid = '', $taskUid = '')
    {
        try {
            $oCase = new \Cases();

            $this->lookinginforContentProcess( $proUid );

            $aData = $oCase->startCase( $taskUid, $userId );

            $response = array();
            $response['caseId'] = $aData['APPLICATION'];
            $response['caseIndex'] = $aData['INDEX'];
            $response['caseNumber'] = $aData['CASE_NUMBER'];

        } catch (Exception $e) {
            $response['status'] = 'failure';
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function lookinginforContentProcess ($sproUid)
    {
        $oContent = new \Content();
        ///we are looking for a pro title for this process $sproUid
        $oCriteria = new \Criteria( 'workflow' );
        $oCriteria->add( \ContentPeer::CON_CATEGORY, 'PRO_TITLE' );
        $oCriteria->add( \ContentPeer::CON_LANG, 'en' );
        $oCriteria->add( \ContentPeer::CON_ID, $sproUid );
        $oDataset = \ContentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        $oDataset->next();
        $aRow = $oDataset->getRow();
        if (! is_array( $aRow )) {

            $oC = new \Criteria( 'workflow' );
            $oC->addSelectColumn( \TaskPeer::TAS_UID );
            $oC->add( \TaskPeer::PRO_UID, $sproUid );
            $oDataset1 = \TaskPeer::doSelectRS( $oC );
            $oDataset1->setFetchmode( \ResultSet::FETCHMODE_ASSOC );

            while ($oDataset1->next()) {
                $aRow1 = $oDataset1->getRow();

                $oCriteria1 = new \Criteria( 'workflow' );
                $oCriteria1->add( ContentPeer::CON_CATEGORY, 'TAS_TITLE' );
                $oCriteria1->add( ContentPeer::CON_LANG, SYS_LANG );
                $oCriteria1->add( ContentPeer::CON_ID, $aRow1['TAS_UID'] );
                $oDataset2 = ContentPeer::doSelectRS( $oCriteria1 );
                $oDataset2->setFetchmode( ResultSet::FETCHMODE_ASSOC );
                $oDataset2->next();
                $aRow2 = $oDataset2->getRow();

                Content::insertContent( 'TAS_TITLE', '', $aRow2['CON_ID'], 'en', $aRow2['CON_VALUE'] );
            }
            $oC2 = new Criteria( 'workflow' );
            $oC2->add( ContentPeer::CON_CATEGORY, 'PRO_TITLE' );
            $oC2->add( ContentPeer::CON_LANG, SYS_LANG );
            $oC2->add( ContentPeer::CON_ID, $sproUid );
            $oDataset3 = ContentPeer::doSelectRS( $oC2 );
            $oDataset3->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $oDataset3->next();
            $aRow3 = $oDataset3->getRow();

            Content::insertContent( 'PRO_TITLE', '', $aRow3['CON_ID'], 'en', $aRow3['CON_VALUE'] );

        }
        return 1;

    }

    /**
     * Route Case
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param string $bExecuteTriggersBeforeAssignment
     *
     * return array Return an array with Task Case
     */
    public function updateRouteCase($applicationUid, $userUid, $delIndex)
    {
        try {
            if (!$delIndex) {
                $delIndex = \AppDelegation::getCurrentIndex($applicationUid);
            }
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $fields = $ws->derivateCase($userUid, $applicationUid, $delIndex, $bExecuteTriggersBeforeAssignment = false);
            $array = json_decode(json_encode($fields), true);
            if ($array ["status_code"] != 0) {
                throw (new \Exception($array ["message"]));
            } else {
                unset($array['status_code']);
                unset($array['message']);
                unset($array['timestamp']);
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $fields;
    }

    /**
     * Get user Data
     *
     * @param string $applicationUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param string $bExecuteTriggersBeforeAssignment
     *
     * return array Return an array with Task Case
     */
    public function getUserData($userUid)
    {
        try {
            $direction = PATH_IMAGES_ENVIRONMENT_USERS . $userUid . ".gif";
            if (! file_exists( $direction )) {
                $direction = PATH_HOME . 'public_html/images/user.gif';
            }
            $gestor    = fopen($direction, "r");
            $contenido = fread($gestor, filesize($direction));
            fclose($gestor);
            $oUser    = new \Users();
            $aUserLog = $oUser->loadDetailed($userUid);
            $response['userId']     = $aUserLog['USR_UID'];
            $response['firstName']  = $aUserLog['USR_FIRSTNAME'];
            $response['lastName']   = $aUserLog['USR_LASTNAME'];
            $response['fullName']   = $aUserLog['USR_FIRSTNAME'].' '.$aUserLog['USR_LASTNAME'];
            $response['email']      = $aUserLog['USR_EMAIL'];
            $response['userRole']   = $aUserLog['USR_ROLE_NAME'];
            $response['userPhone']  = $aUserLog['USR_PHONE'];
            $response['updateDate'] = $aUserLog['USR_UPDATE_DATE'];
            $response['userPhoto']  = base64_encode($contenido);
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }

    /**
     * Download files and resize dimensions in file type image
     * if not type image return content file
     *
     * return array Return an array with Task Case
     */
    public function downloadFile($app_uid, $request_data)
    {
        try {
            $oAppDocument = new \AppDocument();
            $arrayFiles = array();
            foreach ($request_data as $key => $fileData) {
                if (! isset( $fileData['version'] )) {
                    //Load last version of the document
                    $docVersion = $oAppDocument->getLastAppDocVersion( $fileData['fileId'] );
                } else {
                    $docVersion = $fileData['version'];
                }
                $oAppDocument->Fields = $oAppDocument->load( $fileData['fileId'], $docVersion );

                $sAppDocUid  = $oAppDocument->getAppDocUid();
                $iDocVersion = $oAppDocument->getDocVersion();
                $info = pathinfo( $oAppDocument->getAppDocFilename() );
                $ext  = (isset($info['extension'])?$info['extension']:'');//BUG fix: must handle files without any extension

                //$app_uid = \G::getPathFromUID($oAppDocument->Fields['APP_UID']);
                $file = \G::getPathFromFileUID($oAppDocument->Fields['APP_UID'], $sAppDocUid);

                $realPath  = PATH_DOCUMENT .  $app_uid . '/' . $file[0] . $file[1] . '_' . $iDocVersion . '.' . $ext;
                $realPath1 = PATH_DOCUMENT . $app_uid . '/' . $file[0] . $file[1] . '.' . $ext;

                $width  = isset($fileData['width']) ? $fileData['width']:null;
                $height = isset($fileData['height']) ? $fileData['height']:null;
                if (file_exists( $realPath )) {
                    switch($ext){
                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                        case 'png':
                            $arrayFiles[$key]['fileId']      = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = base64_encode($this->imagesThumbnails($realPath, $ext, $width, $height));
                            break;
                        default:
                            $fileTmp    = fopen($realPath, "r");
                            $content = fread($fileTmp, filesize($realPath));
                            $arrayFiles[$key]['fileId']      = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = base64_encode($content);
                            fclose($fileTmp);
                            break;
                    }
                } elseif (file_exists( $realPath1 )) {
                    switch($ext){
                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                        case 'png':
                            $arrayFiles[$key]['fileId']      = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = $this->imagesThumbnails($realPath1, $ext, $width, $height);
                            break;
                        default:
                            $fileTmp    = fopen($realPath, "r");
                            $content = fread($fileTmp, filesize($realPath));
                            $arrayFiles[$key]['fileId']      = $fileData['fileId'];
                            $arrayFiles[$key]['fileContent'] = base64_encode($content);
                            fclose($fileTmp);
                            break;
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $arrayFiles;
    }

    /**
     * resize image if send width or height
     *
     * @param $path
     * @param $extensions
     * @param null $newWidth
     * @param null $newHeight
     * @return string
     */
    public function imagesThumbnails($path, $extensions, $newWidth = null, $newHeight = null)
    {
        switch($extensions){
            case 'jpg':
            case 'jpeg':
                $imgTmp = imagecreatefromjpeg($path);
                break;
            case 'gif':
                $imgTmp = imagecreatefromgif($path);
                break;
            case 'png':
                $imgTmp = imagecreatefrompng($path);
                break;
        }

        $width  = imagesx($imgTmp);
        $height = imagesy($imgTmp);

        $ratio     = $width / $height;

        $isThumbnails = false;
        if (isset($newWidth) && !isset($newHeight)) {
            $newwidth  = $newWidth;
            $newheight = round($newwidth / $ratio);
            $isThumbnails = true;
        } elseif (!isset($newWidth) && isset($newHeight)) {
            $newheight = $newHeight;
            $newwidth  = round($newheight / $ratio);
            $isThumbnails = true;
        } elseif (isset($newWidth) && isset($newHeight)) {
            $newwidth  = $newWidth;
            $newheight = $newHeight;
            $isThumbnails = true;
        }

        $thumb = $imgTmp;
        if ($isThumbnails) {
            $thumb = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($thumb, $imgTmp, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        }
        ob_start();
        switch($extensions){
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumb);
                break;
            case 'gif':
                imagegif($thumb);
                break;
            case 'png':
                imagepng($thumb);
                break;
        }
        $image = ob_get_clean();
        imagedestroy($thumb);
        return $image;
    }

    public function logout($oauthAccessTokenId, $refresh)
    {
        $aFields = array();

        if (!isset($_GET['u'])) {
            $aFields['URL'] = '';
        } else {
            $aFields['URL'] = htmlspecialchars(addslashes(stripslashes(strip_tags(trim(urldecode($_GET['u']))))));
        }

        if (!isset($_SESSION['G_MESSAGE'])) {
            $_SESSION['G_MESSAGE'] = '';
        }

        if (!isset($_SESSION['G_MESSAGE_TYPE'])) {
            $_SESSION['G_MESSAGE_TYPE'] = '';
        }

        $msg = $_SESSION['G_MESSAGE'];
        $msgType = $_SESSION['G_MESSAGE_TYPE'];

        if (!isset($_SESSION['FAILED_LOGINS'])) {
            $_SESSION['FAILED_LOGINS'] = 0;
            $_SESSION["USERNAME_PREVIOUS1"] = "";
            $_SESSION["USERNAME_PREVIOUS2"] = "";
        }

        $sFailedLogins = $_SESSION['FAILED_LOGINS'];
        $usernamePrevious1 = $_SESSION["USERNAME_PREVIOUS1"];
        $usernamePrevious2 = $_SESSION["USERNAME_PREVIOUS2"];

        $aFields['LOGIN_VERIFY_MSG'] = G::loadTranslation('LOGIN_VERIFY_MSG');

        //start new session
        @session_destroy();
        session_start();
        session_regenerate_id();

        setcookie("workspaceSkin", SYS_SKIN, time() + 24*60*60, "/sys".SYS_SYS);

        if (strlen($msg) > 0) {
            $_SESSION['G_MESSAGE'] = $msg;
        }
        if (strlen($msgType) > 0) {
            $_SESSION['G_MESSAGE_TYPE'] = $msgType;
        }

        $_SESSION['FAILED_LOGINS'] = $sFailedLogins;
        $_SESSION["USERNAME_PREVIOUS1"] = $usernamePrevious1;
        $_SESSION["USERNAME_PREVIOUS2"] = $usernamePrevious2;

        /*----------------------------------********---------------------------------*/
        if (!class_exists('pmLicenseManager')) {
            G::LoadClass('pmLicenseManager');
        }
        $licenseManager =& \pmLicenseManager::getSingleton();
        if (in_array(md5($licenseManager->result), array('38afd7ae34bd5e3e6fc170d8b09178a3', 'ba2b45bdc11e2a4a6e86aab2ac693cbb'))) {
            $G_PUBLISH = new \Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/licenseExpired', '', array(), 'licenseUpdate');
            G::RenderPage('publish');
            die();
        }
        /*----------------------------------********---------------------------------*/

        try {
            $oatoken = new \OauthAccessTokens();
            $result = $oatoken->remove($oauthAccessTokenId);

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }
        return $response;

    }

    /**
     * Get information for status paused and participated or other status
     *
     * @param $userUid
     * @param $type
     * @param $app_uid
     * @throws \Exception
     */
    public function getInformation($userUid, $type, $app_uid)
    {
        switch ($type) {
            case 'paused':
            case 'participated':
                $oCase = new \Cases();
                $iDelIndex = $oCase->getCurrentDelegationCase( $app_uid );
                $aFields = $oCase->loadCase( $app_uid, $iDelIndex );
                $this->getInfoResume($userUid, $aFields, $type);
                break;
        }
    }

    /**
     * view in html response for status
     *
     * @param $userUid
     * @param $Fields
     * @param $type
     * @throws \Exception
     */
    public function getInfoResume($userUid, $Fields, $type)
    {
        //print_r($Fields);die;
        /* Includes */
        G::LoadClass( 'case' );
        /* Prepare page before to show */
        //$oCase = new \Cases();

//        $participated = $oCase->userParticipatedInCase( $Fields['APP_UID'], $userUid );
//        if ($RBAC->userCanAccess( 'PM_ALLCASES' ) < 0 && $participated == 0) {
//            /*if (strtoupper($Fields['APP_STATUS']) != 'COMPLETED') {
//              $oCase->thisIsTheCurrentUser($_SESSION['APPLICATION'], $_SESSION['INDEX'], $_SESSION['USER_LOGGED'], 'SHOW_MESSAGE');
//            }*/
//            $aMessage['MESSAGE'] = G::LoadTranslation( 'ID_NO_PERMISSION_NO_PARTICIPATED' );
//            $G_PUBLISH = new Publisher();
//            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
//            G::RenderPage( 'publishBlank', 'blank' );
//            die();
//        }

        $objProc = new \Process();
        $aProc = $objProc->load( $Fields['PRO_UID'] );
        $Fields['PRO_TITLE'] = $aProc['PRO_TITLE'];

        $objTask = new \Task();

        if (isset($_SESSION['ACTION']) && ($_SESSION['ACTION'] == 'jump')) {
            $task = explode('-', $Fields['TAS_UID']);
            $Fields['TAS_TITLE'] = '';
            for( $i = 0; $i < sizeof($task)-1; $i ++ ) {
                $aTask = $objTask->load( $task[$i] );
                $Fields['TAS_TITLE'][] = $aTask['TAS_TITLE'];
            }
            $Fields['TAS_TITLE'] = implode(" - ", array_values($Fields['TAS_TITLE']));
        } else {
            $aTask = $objTask->load( $Fields['TAS_UID'] );
            $Fields['TAS_TITLE'] = $aTask['TAS_TITLE'];
        }

        require_once(PATH_GULLIVER .'../thirdparty/smarty/libs/Smarty.class.php');
        $G_PUBLISH = new \Publisher();
        $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'cases/cases_Resume.xml', '', $Fields, '' );
        $G_PUBLISH->RenderContent();
    }
}
