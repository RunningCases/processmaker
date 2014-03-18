<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;


/**
 * Cases Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Cases extends Api
{
    /**
     * Get list Cases To Do
     *
     * @access public
     * @param array $request_data , Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET
     */
    public function doGetCasesListToDo($request_data = array())
    {
        try {
            $request_data['action'] = 'todo';
            $request_data['userId'] = $this->getUserId();
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft
     *
     * @access public
     * @param array $request_data , Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /draft
     */
    public function doGetCasesListDraft($request_data = array())
    {
        try {
            $request_data['action'] = 'draft';
            $request_data['userId'] = $this->getUserId();
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated
     *
     * @access public
     * @param array $request_data , Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated
     */
    public function doGetCasesListParticipated($request_data = array())
    {
        try {
            $request_data['action'] = 'sent';
            $request_data['userId'] = $this->getUserId();
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned
     *
     * @access public
     * @param array $request_data , Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned
     */
    public function doGetCasesListUnassigned($request_data = array())
    {
        try {
            $request_data['action'] = 'unassigned';
            $request_data['userId'] = $this->getUserId();
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused
     *
     * @access public
     * @param array $request_data , Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused
     */
    public function doGetCasesListPaused($request_data = array())
    {
        try {
            $request_data['action'] = 'paused';
            $request_data['userId'] = $this->getUserId();
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search
     *
     * @access public
     * @param array $request_data , Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /advanced-search
     */
    public function doGetCasesListAdvancedSearch($request_data = array())
    {
        try {
            $request_data['action'] = 'search';
            $request_data['userId'] = $this->getUserId();
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:cas_uid
     *
     * @param string $cas_uid {@min 32}{@max 32}
     */
    public function doGetCaseInfo($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $arrayData = $cases->getCaseInfo($cas_uid, $userUid);
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:cas_uid/current-task
     *
     * @param string $cas_uid {@min 32}{@max 32}
     */
        public function doGetTaskCase($cas_uid)
    {
        try {
            $cases = new \BusinessModel\Cases();
            $arrayData = $cases->getTaskCase($cas_uid);
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param string $prj_uid {@from body} {@min 32}{@max 32}
     * @param string $act_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     *
     */
    public function doPostCase($prj_uid, $act_uid, $variables=null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $arrayData = $cases->addCase($prj_uid, $act_uid, $userUid, $variables);
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /impersonate
     *
     * @param string $prj_uid {@from body} {@min 32}{@max 32}
     * @param string $usr_uid {@from body} {@min 32}{@max 32}
     * @param string $act_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     */
    public function doPostCaseImpersonate($prj_uid, $usr_uid, $act_uid, $variables=null)
    {
        try {
            $cases = new \BusinessModel\Cases();
            $arrayData = $cases->addCaseImpersonate($prj_uid, $usr_uid, $act_uid, $variables);
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:cas_uid/reassign-case
     *
     * @param string $del_index {@from body}
     * @param string $usr_uid_source {@from body} {@min 32}{@max 32}
     * @param string $usr_uid_target {@from body} {@min 32}{@max 32}
     */
    public function doPutReassignCase($cas_uid, $del_index, $usr_uid_source, $usr_uid_target)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $arrayData = $cases->updateReassignCase($cas_uid, $userUid, $del_index, $usr_uid_source, $usr_uid_target);
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:cas_uid/route-case
     *
     * @param string $del_index {@from body}
     * @param string $usr_uid_source {@from body} {@min 32}{@max 32}
     * @param string $usr_uid_target {@from body} {@min 32}{@max 32}
     */
    public function doPutRouteCase($cas_uid, $del_index)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $arrayData = $cases->updateRouteCase($cas_uid, $userUid, $del_index);
            return $arrayData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

