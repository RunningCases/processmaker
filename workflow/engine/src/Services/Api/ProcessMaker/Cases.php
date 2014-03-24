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
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET
     */
    public function doGetCasesListToDo(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'todo';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases To Do with paged
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paged
     */
    public function doGetCasesListToDoPaged(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'todo';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /draft
     */
    public function doGetCasesListDraft(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'draft';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Draft with paged
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /draft/paged
     */
    public function doGetCasesListDraftPaged(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'draft';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated
     */
    public function doGetCasesListParticipated(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'sent';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Participated with paged
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /participated/paged
     */
    public function doGetCasesListParticipatedPaged(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'sent';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned
     */
    public function doGetCasesListUnassigned(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'unassigned';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Unassigned with paged
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /unassigned/paged
     */
    public function doGetCasesListUnassignedPaged(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'unassigned';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused
     */
    public function doGetCasesListPaused(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'paused';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Paused with paged
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /paused/paged
     */
    public function doGetCasesListPausedPaged(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'paused';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $status {@from path}
     * @param string $user {@from path}
     * @param string $dateFrom {@from path}
     * @param string $dateTo {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /advanced-search
     */
    public function doGetCasesListAdvancedSearch(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $status = '',
        $user = '',
        $dateFrom = '',
        $dateTo = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'search';
            $dataList['paged']  = false;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['status'] = $status;
            $dataList['user'] = $user;
            $dataList['dateFrom'] = $dateFrom;
            $dataList['dateTo'] = $dateTo;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * Get list Cases Advanced Search with Paged
     *
     * @param string $paged {@from path}
     * @param string $start {@from path}
     * @param string $limit {@from path}
     * @param string $dir {@from path}
     * @param string $category {@from path}
     * @param string $process {@from path}
     * @param string $status {@from path}
     * @param string $user {@from path}
     * @param string $dateFrom {@from path}
     * @param string $dateTo {@from path}
     * @param string $search {@from path}
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /advanced-search/paged
     */
    public function doGetCasesListAdvancedSearchPaged(
        $start = 0,
        $limit = 25,
        $sort = 'APP_CACHE_VIEW.APP_NUMBER',
        $dir = 'DESC',
        $category = '',
        $process = '',
        $status = '',
        $user = '',
        $dateFrom = '',
        $dateTo = '',
        $search = ''
    )
    {
        try {
            $dataList['userId'] = $this->getUserId();
            $dataList['action'] = 'search';
            $dataList['paged']  = true;

            $dataList['start'] = $start;
            $dataList['limit'] = $limit;
            $dataList['sort'] = $sort;
            $dataList['dir'] = $dir;
            $dataList['category'] = $category;
            $dataList['process'] = $process;
            $dataList['status'] = $status;
            $dataList['user'] = $user;
            $dataList['dateFrom'] = $dateFrom;
            $dataList['dateTo'] = $dateTo;
            $dataList['search'] = $search;
            $oCases = new \BusinessModel\Cases();
            $response = $oCases->getList($dataList);
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

