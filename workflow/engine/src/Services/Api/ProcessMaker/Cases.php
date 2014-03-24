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
     * @url GET /:app_uid
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
    public function doGetCaseInfo($app_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $oData = $cases->getCaseInfo($app_uid, $userUid);
            return $oData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:app_uid/current-task
     *
     * @param string $app_uid {@min 32}{@max 32}
     */
        public function doGetTaskCase($app_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $oData = $cases->getTaskCase($app_uid, $userUid);
            return $oData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param string $pro_uid {@from body} {@min 32}{@max 32}
     * @param string $tas_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     *
     */
    public function doPostCase($pro_uid, $tas_uid, $variables=null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $oData = $cases->addCase($pro_uid, $tas_uid, $userUid, $variables);
            return $oData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /impersonate
     *
     * @param string $pro_uid {@from body} {@min 32}{@max 32}
     * @param string $usr_uid {@from body} {@min 32}{@max 32}
     * @param string $tas_uid {@from body} {@min 32}{@max 32}
     * @param array $variables {@from body}
     *
     */
    public function doPostCaseImpersonate($pro_uid, $usr_uid, $tas_uid, $variables=null)
    {
        try {
            $cases = new \BusinessModel\Cases();
            $oData = $cases->addCaseImpersonate($pro_uid, $usr_uid, $tas_uid, $variables);
            return $oData;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:app_uid/reassign-case
     *
     * @param string $app_uid {@min 32}{@max 32}
     * @param string $usr_uid_source {@from body} {@min 32}{@max 32}
     * @param string $usr_uid_target {@from body} {@min 32}{@max 32}
     * @param string $del_index {@from body}
     */
    public function doPutReassignCase($app_uid, $usr_uid_source, $usr_uid_target, $del_index = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $cases->updateReassignCase($app_uid, $userUid, $del_index, $usr_uid_source, $usr_uid_target);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:app_uid/route-case
     *
     * @param string $app_uid {@from body} {@min 32}{@max 32}
     * @param string $del_index {@from body}
     */
    public function doPutRouteCase($app_uid, $del_index = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $cases->updateRouteCase($app_uid, $userUid, $del_index);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Cancel Case
     *
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/cancel-case
     */
    public function doPutCancelCase($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $cases->putCancelCase($cas_uid, $userUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Pause Case
     *
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $unpaused_date {@from body}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/pause-case
     */
    public function doPutPauseCase($cas_uid, $unpaused_date = null)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            if ($unpaused_date == null) {
                $cases->putPauseCase($cas_uid, $userUid);
            } else {
                $cases->putPauseCase($cas_uid, $userUid, false, $unpaused_date);
            }
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Unpause Case
     *
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/unpause-case
     */
    public function doPutUnpauseCase($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $cases->putUnpauseCase($cas_uid, $userUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Unpause Case
     *
     * @param string $cas_uid {@min 1}{@max 32}
     * @param string $tri_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url PUT /:cas_uid/execute-trigger/:tri_uid
     */
    public function doPutExecuteTriggerCase($cas_uid, $tri_uid)
    {
        try {
            $userUid = $this->getUserId();
            $cases = new \BusinessModel\Cases();
            $cases->putExecuteTriggerCase($cas_uid, $tri_uid, $userUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Delete Case
     *
     * @param string $cas_uid {@min 1}{@max 32}
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url DELETE /:cas_uid
     */
    public function doDeleteCase($cas_uid)
    {
        try {
            $cases = new \BusinessModel\Cases();
            $cases->deleteCase($cas_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

