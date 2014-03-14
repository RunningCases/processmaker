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
     * @url GET /draft
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
     * @url GET /draft
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
     * @url GET /draft
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
     * @url GET /draft
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
}

