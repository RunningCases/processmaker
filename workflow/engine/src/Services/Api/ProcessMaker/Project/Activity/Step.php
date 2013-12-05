<?php
namespace Services\Api\ProcessMaker\Project\Activity;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Step Api Controller
 *
 * @protected
 */
class Step extends Api
{
    /**
     * @url GET /:projectUid/activity/:activityUid/step/:stepUid
     */
    public function doGetActivityStep($stepUid, $activityUid, $projectUid)
    {
        try {
            $step = new \BusinessModel\Step();

            $response = $step->getStep($stepUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/activity/:activityUid/step
     *
     * @param string        $activityUid
     * @param string        $projectUid
     * @param StepStructure $request_data
     *
     * @status 201
     */
    public function doPostActivityStep($activityUid, $projectUid, StepStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $step = new \BusinessModel\Step();

            $stepUid = $step->create($activityUid, $projectUid, $request_data);

            $response = array("old_uid" => $request_data["step_uid"], "new_uid" => $stepUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/activity/:activityUid/step/:stepUid
     *
     * @param string        $stepUid
     * @param string        $activityUid
     * @param string        $projectUid
     * @param StepStructure $request_data
     */
    public function doPutActivityStep($stepUid, $activityUid, $projectUid, StepStructure $request_data = null)
    {
        try {
            $request_data = (array)($request_data);

            $step = new \BusinessModel\Step();

            $step->update($stepUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/activity/:activityUid/step/:stepUid
     */
    public function doDeleteActivityStep($stepUid, $activityUid, $projectUid)
    {
        try {
            $step = new \BusinessModel\Step();

            $step->delete($stepUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/activity/:activityUid/step/:stepUid/triggers
     */
    public function doGetActivityStepTriggers($stepUid, $activityUid, $projectUid)
    {
        try {
            $step = new \BusinessModel\Step();

            $response = $step->getTriggers($stepUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/activity/:activityUid/step/:stepUid/available-triggers/:type
     *
     * @param string $stepUid
     * @param string $activityUid
     * @param string $projectUid
     * @param string $type {@from body}{@choice before,after}
     */
    public function doGetActivityStepAvailableTriggers($stepUid, $activityUid, $projectUid, $type)
    {
        try {
            $step = new \BusinessModel\Step();

            $response = $step->getAvailableTriggers($stepUid, strtoupper($type));

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

class StepStructure
{
    /**
     * @var string {@from body}{@min 32}{@max 32}
     */
    public $step_uid;

    /**
     * @var string {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}
     */
    public $step_type_obj;

    /**
     * @var string {@from body}{@min 32}{@max 32}
     */
    public $step_uid_obj;

    /**
     * @var string
     */
    public $step_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $step_position;

    /**
     * @var string {@from body}{@choice EDIT,VIEW}
     */
    public $step_mode;
}

