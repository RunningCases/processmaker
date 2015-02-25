<?php

/**
 * Class for part mobile
 *
 * Created by Dev: Ronald Quenta
 * E-mail: ronald.otn@gmail.com
 */

namespace ProcessMaker\Services\Api;

use \G;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 *
 * Process Api Controller
 *
 * @protected
 */
class Light extends Api
{
    /**
     * Get list counters
     * @return array
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /counters
     */
    public function countersCases ()
    {
        try {
            $oMobile     = new \ProcessMaker\BusinessModel\Light();
            $counterCase = $oMobile->getCounterCase($this->getUserId());
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $counterCase;
    }

    /**
     * Get list process start
     * @return array
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /start-case
     */
    public function getProcessListStartCase ()
    {
        try {
            $oMobile   = new \ProcessMaker\BusinessModel\Light();
            $startCase = $oMobile->getProcessListStartCase($this->getUserId());
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $startCase;
    }

    /**
     * Get list Case To Do
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /todo
     */
    public function doGetCasesListToDo(
        $start = 0,
        $limit = 10,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'todo';
            $dataList['paged']  = true;
            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort']  = $sort;
            $dataList['dir']   = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process']  = $pro_uid;
            $dataList['search']   = $search;

            $oCases   = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            $result   = $this->parserDataTodo($response['data']);
            return $result;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataTodo ($data)
    {
        $structure = array(
            //'app_uid' => 'mongoId',
            'app_uid'           => 'caseId',
            'app_title'         => 'caseTitle',
            'app_number'        => 'caseNumber',
            'app_update_date'   => 'date',
            'del_task_due_date' => 'dueDate',
            //'' => 'status'
            'user' => array(
                'usrcr_usr_uid'       => 'userId',
                'usrcr_usr_firstname' => 'firstName',
                'usrcr_usr_lastname'  => 'lastName',
                'usrcr_usr_username'  => 'fullName',
            ),
            'prevUser' => array(
                'previous_usr_uid'       => 'userId',
                'previous_usr_firstname' => 'firstName',
                'previous_usr_lastname'  => 'lastName',
                'previous_usr_username'  => 'fullName',
            ),
            'process' => array(
                'pro_uid'       => 'processId',
                'app_pro_title' => 'name'
            ),
            'task' => array(
                'tas_uid'       => 'taskId',
                'app_tas_title' => 'name'
            ),
            'inp_doc_uid' => 'documentUid' //Esta opcion es temporal
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    /**
     * Get list Cases Participated
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated
     */
    public function doGetCasesListParticipated(
        $start = 0,
        $limit = 10,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'sent';
            $dataList['paged']  = true;
            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort']  = $sort;
            $dataList['dir']   = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process']  = $pro_uid;
            $dataList['search']   = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            $result = $this->parserDataParticipated($response['data']);
            return $result;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataParticipated ($data)
    {
        $structure = array(
            //'app_uid' => 'mongoId',
            'app_uid'           => 'caseId',
            'app_title'         => 'caseTitle',
            'app_number'        => 'caseNumber',
            'app_update_date'   => 'date',
            'del_task_due_date' => 'dueDate',
            'currentUser' => array(
                'usrcr_usr_uid'       => 'userId',
                'usrcr_usr_firstname' => 'firstName',
                'usrcr_usr_lastname'  => 'lastName',
                'usrcr_usr_username'  => 'fullName',
            ),
            'prevUser' => array(
                'previous_usr_uid'       => 'userId',
                'previous_usr_firstname' => 'firstName',
                'previous_usr_lastname'  => 'lastName',
                'previous_usr_username'  => 'fullName',
            ),
            'process' => array(
                'pro_uid'       => 'processId',
                'app_pro_title' => 'name'
            ),
            'task' => array(
                'tas_uid'       => 'taskId',
                'app_tas_title' => 'name'
            )
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    /**
     * Get list Cases Paused
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused
     */
    public function doGetCasesListPaused(
        $start = 0,
        $limit = 10,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'paused';
            $dataList['paged']  = true;

            $dataList['start']    = $start;
            $dataList['limit']    = $limit;
            $dataList['sort']     = $sort;
            $dataList['dir']      = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process']  = $pro_uid;
            $dataList['search']   = $search;
            $oCases = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            $result = $this->parserDataParticipated($response['data']);
            return $result;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataPaused ($data)
    {
        $structure = array(
            //'app_uid' => 'mongoId',
            'app_uid'           => 'caseId',
            'app_title'         => 'caseTitle',
            'app_number'        => 'caseNumber',
            'app_update_date'   => 'date',
            'del_task_due_date' => 'dueDate',
            'currentUser' => array(
                'usrcr_usr_uid'       => 'userId',
                'usrcr_usr_firstname' => 'firstName',
                'usrcr_usr_lastname'  => 'lastName',
                'usrcr_usr_username'  => 'fullName',
            ),
            'prevUser' => array(
                'previous_usr_uid'       => 'userId',
                'previous_usr_firstname' => 'firstName',
                'previous_usr_lastname'  => 'lastName',
                'previous_usr_username'  => 'fullName',
            ),
            'process' => array(
                'pro_uid'       => 'processId',
                'app_pro_title' => 'name'
            ),
            'task' => array(
                'tas_uid'       => 'taskId',
                'app_tas_title' => 'name'
            )
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    /**
     * Get list Cases Unassigned
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned
     */
    public function doGetCasesListUnassigned(
        $start = 0,
        $limit = 0,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $cat_uid = '',
        $pro_uid = '',
        $search = ''
    ) {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'unassigned';
            $dataList['paged']  = false;

            $dataList['start']    = $start;
            $dataList['limit']    = $limit;
            $dataList['sort']     = $sort;
            $dataList['dir']      = $dir;
            $dataList['category'] = $cat_uid;
            $dataList['process']  = $pro_uid;
            $dataList['search']   = $search;
            $oCases   = new \ProcessMaker\BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            $result   = $this->parserDataUnassigned($response);
            return $result;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataUnassigned ($data)
    {
        $structure = array(
            //'app_uid' => 'mongoId',
            'app_uid'           => 'caseId',
            'app_title'         => 'caseTitle',
            'app_number'        => 'caseNumber',
            'app_update_date'   => 'date',
            'del_task_due_date' => 'dueDate',
            'currentUser' => array(
                'usrcr_usr_uid'       => 'userId',
                'usrcr_usr_firstname' => 'firstName',
                'usrcr_usr_lastname'  => 'lastName',
                'usrcr_usr_username'  => 'fullName',
            ),
            'prevUser' => array(
                'previous_usr_uid'       => 'userId',
                'previous_usr_firstname' => 'firstName',
                'previous_usr_lastname'  => 'lastName',
                'previous_usr_username'  => 'fullName',
            ),
            'process' => array(
                'pro_uid'       => 'processId',
                'app_pro_title' => 'name'
            ),
            'task' => array(
                'tas_uid'       => 'taskId',
                'app_tas_title' => 'name'
            )
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    public function replaceFields ($data, $structure)
    {
        $response = array();
        foreach ($data as $field => $d) {
            if (is_array($d)) {
                $newData = array();
                foreach ($d as $field => $value) {
                    if (array_key_exists($field, $structure)) {
                        $newName           = $structure[$field];
                        $newData[$newName] = $value;
                    } else {
                        foreach ($structure as $name => $str) {
                            if (is_array($str) && array_key_exists($field, $str)) {
                                $newName                  = $str[$field];
                                $newData[$name][$newName] = $value;
                            }
                        }
                    }
                }
                $response[] = $newData;
            } else {
                if (array_key_exists($field, $structure)) {
                    $newName           = $structure[$field];
                    $response[$newName] = $d;
                } else {
                    foreach ($structure as $name => $str) {
                        if (is_array($str) && array_key_exists($field, $str)) {
                            $newName                  = $str[$field];
                            $response[$name][$newName] = $d;
                        }
                    }
                }
            }

        }
        return $response;
    }

    /**
     * Get list History case
     *
     * @copyright Colosa - Bolivia
     *
     * @url GET /history/:app_uid
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetCasesListHistory($app_uid)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light();
            $response         = $oMobile->getCasesListHistory($app_uid);
            $response['flow'] = $this->parserDataHistory($response['flow']);
            $r             = new \stdclass();
            $r->data       = $response;
            $r->totalCount = count($response['flow']);
            return $r;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    public function parserDataHistory ($data)
    {
        $structure = array(
            //'' => 'caseId',
            //'' => 'caseTitle',
            //'' => 'processName',
            //'' => 'ownerFullName',
            //'flow' => array(
                'TAS_TITLE'         => 'taskName',
                //'' => 'userId',
                'USR_NAME'          => 'userFullName',
                'APP_TYPE'          => 'flowStatus', // is null default Router in FE
                'DEL_DELEGATE_DATE' => 'dueDate',
            //)
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    /**
     *
     * @url GET /project/:prj_uid/dynaforms
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForms($prj_uid)
    {
        try {
            $process = new \ProcessMaker\BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getDynaForms($prj_uid);
            $result   = $this->parserDataDynaForm($response);
            foreach ($result as $k => $form) {
                $result[$k]['formContent'] = (isset($form['formContent']) && $form['formContent'] != null)?json_decode($form['formContent']):"";
                $result[$k]['index']       = $k;
            }
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $result;
    }

    /**
     * @url GET /project/:prj_uid/activity/:act_uid/steps
     *
     * @param string $act_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetActivitySteps($act_uid, $prj_uid)
    {
        try {
            $task = new \ProcessMaker\BusinessModel\Task();
            $task->setFormatFieldNameInUppercase(false);
            $task->setArrayParamException(array("taskUid" => "act_uid", "stepUid" => "step_uid"));

            $activitySteps = $task->getSteps($act_uid);

            //$step = new \ProcessMaker\Services\Api\Project\Activity\Step();

            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $response = array();
            for ($i = 0; $i < count($activitySteps); $i++) {
                if ($activitySteps[$i]['step_type_obj'] == "DYNAFORM") {
                    $dataForm = $dynaForm->getDynaForm($activitySteps[$i]['step_uid_obj']);
                    $result   = $this->parserDataDynaForm($dataForm);
                    $result['formContent'] = (isset($result['formContent']) && $result['formContent'] != null)?json_decode($result['formContent']):"";
                    $result['index']       = $i;
                    //$activitySteps[$i]["triggers"] = $step->doGetActivityStepTriggers($activitySteps[$i]["step_uid"], $act_uid, $prj_uid);
                    $response[] = $result;
                }
            }
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $response;
    }

    /**
     * @url GET /project/dynaform/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     */
    public function doGetDynaForm($dyn_uid)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);

            $response = $dynaForm->getDynaForm($dyn_uid);
            $result   = $this->parserDataDynaForm($response);
            $result['formContent'] = (isset($result['formContent']) && $result['formContent'] != null)?json_decode($result['formContent']):"";
            return $result;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /project/dynaforms
     *
     */
    public function doGetDynaFormsId($request_data)
    {
        try {
            $dynaForm = new \ProcessMaker\BusinessModel\DynaForm();
            $dynaForm->setFormatFieldNameInUppercase(false);
            $return = array();
            foreach ($request_data['formId'] as $dyn_uid) {
                $response = $dynaForm->getDynaForm($dyn_uid);
                $result   = $this->parserDataDynaForm($response);
                $result['formContent'] = (isset($result['formContent']) && $result['formContent'] != null)?json_decode($result['formContent']):"";
                $return[] = $result;
            }
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $return;
    }

    public function parserDataDynaForm ($data)
    {
        $structure = array(
            'dyn_uid'         => 'formId',
            'dyn_title'       => 'formTitle',
            'dyn_description' => 'formDescription',
            //'dyn_type'        => 'formType',
            'dyn_content'     => 'formContent'
        );

        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    /**
     * @url POST /process/:pro_uid/task/:task_uid/start-case
     *
     * @param string $pro_uid {@min 32}{@max 32}
     * @param string $task_uid {@min 32}{@max 32}
     */
    public function postStartCase($pro_uid, $task_uid)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light();
            $result  = $oMobile->startCase($this->getUserId(), $pro_uid, $task_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $result;
    }

    /**
     * Route Case
     * @url PUT /cases/:app_uid/route-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $del_index {@from body}
     */
    public function doPutRouteCase($app_uid, $del_index = null)
    {
        try {
            $oMobile  = new \ProcessMaker\BusinessModel\Light();
            $response = $oMobile->updateRouteCase($app_uid, $this->getUserId(), $del_index);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $response;
    }

    /**
     * @url GET /user/data
     */
    public function doGetUserData()
    {
        try {
            $userUid  = $this->getUserId();
            $oMobile  = new \ProcessMaker\BusinessModel\Light();
            $response = $oMobile->getUserData($userUid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url POST /users/data
     */
    public function doGetUsersData($request_data)
    {
        try {
            $response = array();
            $oMobile  = new \ProcessMaker\BusinessModel\Light();
            foreach ($request_data['user']['ids'] as $userUid) {
                $response[] = $oMobile->getUserData($userUid);
            }
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url POST /case/:app_uid/input-document
     *
     * @param string $app_uid         { @min 32}{@max 32}
     * @param string $tas_uid         {@min 32}{@max 32}
     * @param string $app_doc_comment
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     */
    public function doPostInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
            $file = $inputDocument->addCasesInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid, $userUid);
            $response   = $this->parserInputDocument($file);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $response;
    }

    public function parserInputDocument ($data)
    {
        $structure = array(
            'app_doc_uid'      => 'fileId',
            'app_doc_filename' => 'fileName',
            'app_doc_version'  => 'version'
        );
        $response = $this->replaceFields($data, $structure);
        return $response;
    }

    /**
     * @url POST /case/:app_uid/input-document/location
     *
     * @param string $app_uid         { @min 32}{@max 32}
     * @param string $tas_uid         {@min 32}{@max 32}
     * @param string $app_doc_comment
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     * @param float $latitude     {@min -90}{@max 90}
     * @param float $longitude     {@min -180}{@max 180}
     */
    public function postInputDocumentLocation($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid, $latitude, $longitude)
    {
        try {
            $userUid       = $this->getUserId();
            $inputDocument = new \ProcessMaker\BusinessModel\Cases\InputDocument();
            $url           = "http://maps.googleapis.com/maps/api/staticmap?center=".$latitude.','.$longitude."&format=jpg&size=600x600&zoom=15&markers=color:blue%7Clabel:S%7C".$latitude.','.$longitude;
            $imageLocation = imagecreatefromjpeg($url);
            $tmpfname = tempnam("php://temp","pmm");
            imagejpeg($imageLocation, $tmpfname);

            $_FILES["form"]["type"] = "image/jpeg";
            $_FILES["form"]["name"] = 'Location.jpg';
            $_FILES["form"]["tmp_name"] = $tmpfname;
            $_FILES["form"]["error"] = 0;
            $sizes = getimagesize($tmpfname);
            $_FILES["form"]["size"] = ($sizes['0'] * $sizes['1']);
            $file = $inputDocument->addCasesInputDocument($app_uid, $tas_uid, $app_doc_comment, $inp_doc_uid, $userUid);

            $strPathName = PATH_DOCUMENT . G::getPathFromUID($app_uid) . PATH_SEP;
            $strFileName = $file->app_doc_uid . "_" . $file->app_doc_version . ".jpg";
            copy($tmpfname, $strPathName . "/" . $strFileName);
            $response   = $this->parserInputDocument($file);
            unlink($tmpfname);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $response;
    }

    /**
     * @url POST /case/:app_uid/download64
     *
     * @param string $app_uid         {@min 32}{@max 32}
     */
    public function postDownloadFile($app_uid, $request_data)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light();
            $files = $oMobile->downloadFile($app_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $files;
    }

    /**
     * @url POST /logout
     *
     * @param $access
     * @param $refresh
     * @return mixed
     */
    public function postLogout($access, $refresh)
    {
        try {
            $oMobile = new \ProcessMaker\BusinessModel\Light();
            $files = $oMobile->logout($access, $refresh);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
        return $files;
    }
}
