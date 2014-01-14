<?php
namespace Services\Api\ProcessMaker\Project\Activity\Step;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Step\Trigger Api Controller
 *
 * @protected
 */
class Trigger extends Api
{
    /**
     * @url GET /:projectUid/activity/:activityUid/step/:stepUid/trigger/:triggerUid/:type
     *
     * @param string $triggerUid
     * @param string $stepUid
     * @param string $activityUid
     * @param string $projectUid
     * @param string $type {@from body}{@choice before,after}
     */
    public function doGetActivityStepTrigger($triggerUid, $stepUid, $activityUid, $projectUid, $type)
    {
        try {
            $stepTrigger = new \BusinessModel\Step\Trigger();

            $response = $stepTrigger->getTrigger($stepUid, strtoupper($type), $activityUid, $triggerUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/activity/:activityUid/step/:stepUid/trigger
     *
     * @param string $stepUid
     * @param string $activityUid
     * @param string $projectUid
     * @param StepTriggerPostStructure $request_data
     *
     * @status 201
     */
    public function doPostActivityStepTrigger($stepUid, $activityUid, $projectUid, StepTriggerPostStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->create($stepUid, $request_data["st_type"], $activityUid, $request_data["tri_uid"], $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/activity/:activityUid/step/:stepUid/trigger/:triggerUid
     *
     * @param string $triggerUid
     * @param string $stepUid
     * @param string $activityUid
     * @param string $projectUid
     * @param StepTriggerPutStructure $request_data
     */
    public function doPutActivityStepTrigger($triggerUid, $stepUid, $activityUid, $projectUid, StepTriggerPutStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->update($stepUid, $request_data["st_type"], $activityUid, $triggerUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/activity/:activityUid/step/:stepUid/trigger/:triggerUid/:type
     *
     * @param string $triggerUid
     * @param string $stepUid
     * @param string $activityUid
     * @param string $projectUid
     * @param string $type {@from body}{@choice before,after}
     */
    public function doDeleteActivityStepTrigger($triggerUid, $stepUid, $activityUid, $projectUid, $type)
    {
        try {
            $stepTrigger = new \BusinessModel\Step\Trigger();

            $stepTrigger->delete($stepUid, strtoupper($type), $activityUid, $triggerUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    //Step "Assign Task"

    /**
     * @url GET /:projectUid/activity/:activityUid/step/trigger/:triggerUid/:type
     *
     * @param string $triggerUid
     * @param string $activityUid
     * @param string $projectUid
     * @param string $type {@from body}{@choice before-assignment,before-routing,after-routing}
     */
    public function doGetActivityStepAssignTaskTrigger($triggerUid, $activityUid, $projectUid, $type)
    {
        try {
            $stepTrigger = new \BusinessModel\Step\Trigger();

            $response = $stepTrigger->getTrigger("", strtoupper(str_replace("-", "_", $type)), $activityUid, $triggerUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/activity/:activityUid/step/trigger
     *
     * @param string $activityUid
     * @param string $projectUid
     * @param StepAssignTaskTriggerPostStructure $request_data
     *
     * @status 201
     */
    public function doPostActivityStepAssignTaskTrigger($activityUid, $projectUid, StepAssignTaskTriggerPostStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->create("", $request_data["st_type"], $activityUid, $request_data["tri_uid"], $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/activity/:activityUid/step/trigger/:triggerUid
     *
     * @param string $triggerUid
     * @param string $activityUid
     * @param string $projectUid
     * @param StepAssignTaskTriggerPutStructure $request_data
     */
    public function doPutActivityStepAssignTaskTrigger($triggerUid, $activityUid, $projectUid, StepAssignTaskTriggerPutStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $stepTrigger = new \BusinessModel\Step\Trigger();

            $arrayData = $stepTrigger->update("", $request_data["st_type"], $activityUid, $triggerUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/activity/:activityUid/step/trigger/:triggerUid/:type
     *
     * @param string $triggerUid
     * @param string $activityUid
     * @param string $projectUid
     * @param string $type {@from body}{@choice before-assignment,before-routing,after-routing}
     */
    public function doDeleteActivityStepAssignTaskTrigger($triggerUid, $activityUid, $projectUid, $type)
    {
        try {
            $stepTrigger = new \BusinessModel\Step\Trigger();

            $stepTrigger->delete("", strtoupper(str_replace("-", "_", $type)), $activityUid, $triggerUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

class StepTriggerPostStructure
{
    /**
     * @var string {@from body}{@choice BEFORE,AFTER}{@required true}
     */
    public $st_type;

    /**
     * @var string {@from body}{@min 32}{@max 32}{@required true}
     */
    public $tri_uid;

    /**
     * @var string
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $st_position;
}

class StepTriggerPutStructure
{
    /**
     * @var string {@from body}{@choice BEFORE,AFTER}{@required true}
     */
    public $st_type;

    /**
     * @var string
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $st_position;
}

class StepAssignTaskTriggerPostStructure
{
    /**
     * @var string {@from body}{@choice BEFORE_ASSIGNMENT,BEFORE_ROUTING,AFTER_ROUTING}{@required true}
     */
    public $st_type;

    /**
     * @var string {@from body}{@min 32}{@max 32}{@required true}
     */
    public $tri_uid;

    /**
     * @var string
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $st_position;
}

class StepAssignTaskTriggerPutStructure
{
    /**
     * @var string {@from body}{@choice BEFORE_ASSIGNMENT,BEFORE_ROUTING,AFTER_ROUTING}{@required true}
     */
    public $st_type;

    /**
     * @var string
     */
    public $st_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $st_position;
}

