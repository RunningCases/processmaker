<?php
namespace Services\Api\ProcessMaker\Project\Activity;

//use \ProcessMaker\Api;
use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Assignee Api Controller
 *
 * @protected
 */
class Assignee extends Api
{
    /**
     * @url GET /:prj_uid/activity/:act_uid/assignee
     */
    public function doGetActivityAssignees($prj_uid, $act_uid, $filter = '', $start = '', $limit = '')
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->getTaskAssignees($prj_uid, $act_uid, $filter, $start, $limit);

            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/available-assignee
     */
    public function doGetActivityAvailableAssignee($prj_uid, $act_uid, $filter = '', $start = '', $limit = '')
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->getTaskAvailableAssignee($prj_uid, $act_uid);

            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/assignee/:aas_uid
     */
    public function doGetActivityAssignee($prj_uid, $act_uid, $aas_uid)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->getTaskAssignee($prj_uid, $act_uid, $aas_uid);

            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url POST /:prj_uid/activity/:act_uid/assignee
     * @status 201
     */
    public function doPostActivityAssignee($prj_uid, $act_uid, $assignee_uid, $tu_relation)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->postTaskAssignee($prj_uid, $act_uid, $assignee_uid, $tu_relation);

            //Response
            $response ["success"] = true;
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url DELETE /:prj_uid/activity/:act_uid/assignee/:aas_uid/relation/:tu_relation
     */
    public function doDeleteActivityAssignee($prj_uid, $act_uid, $aas_uid, $tu_relation)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->deleteTaskAssignee($prj_uid, $act_uid, $aas_uid, $tu_relation);

        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/adhoc-assignee
     */
    public function doGetActivityAdhocAssignees($prj_uid, $act_uid, $filter = '', $start = '', $limit = '')
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->getTaskAdhocAssignees($prj_uid, $act_uid, $filter, $start, $limit);

            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/available-adhoc-assignee
     */
    public function doGetActivityAvailableAdhocAssignee($prj_uid, $act_uid, $filter = '', $start = '', $limit = '')
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->getTaskAvailableAdhocAssignee($prj_uid, $act_uid);

            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:prj_uid/activity/:act_uid/adhoc-assignee/:aas_uid
     */
    public function doGetActivityAdhocAssignee($prj_uid, $act_uid, $aas_uid)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->getTaskAdhocAssignee($prj_uid, $act_uid, $aas_uid);

            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url POST /:prj_uid/activity/:act_uid/adhoc-assignee
     * @status 201
     */
    public function doPostActivityAdhocAssignee($prj_uid, $act_uid, $assignee_uid, $tu_relation)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->postTaskAdhocAssignee($prj_uid, $act_uid, $assignee_uid, $tu_relation);

            //Response
            $response ["success"] = true;
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url DELETE /:prj_uid/activity/:act_uid/adhoc-assignee/:aas_uid/relation/:tu_relation
     */
    public function doDeleteActivityAdhocAssignee($prj_uid, $act_uid, $aas_uid, $tu_relation)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();
            $arrayData = $task->deleteTaskAdhocAssignee($prj_uid, $act_uid, $aas_uid, $tu_relation);

        } catch (\Exception $e) {
            //response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

}

