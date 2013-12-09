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
     * @url GET /:projectUid/activity/:activityUid/step/:stepUid/trigger/:triggerUid
     */
    public function doGetActivityStepTrigger($triggerUid, $stepUid, $activityUid, $projectUid)
    {
        try {
            $trigger = new \BusinessModel\Trigger();

            $response = $trigger->getTrigger($triggerUid);

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

            $stepTrigger->create($stepUid, $request_data["tri_uid"], $request_data["st_type"], $request_data);
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

            $stepTrigger->update($stepUid, $triggerUid, $request_data["st_type"], $request_data);
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

            $stepTrigger->delete($stepUid, $triggerUid, strtoupper($type));
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

class StepTriggerPostStructure
{
    /**
     * @var string {@from body}{@min 32}{@max 32}{@required true}
     */
    public $tri_uid;

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

class StepTriggerPutStructure
{
    /**
     * @var string {@from body}{@choice BEFORE,AFTER}
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

