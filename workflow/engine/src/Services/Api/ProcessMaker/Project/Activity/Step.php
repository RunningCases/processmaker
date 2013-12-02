<?php
namespace Services\Api\ProcessMaker\Project\Activity;

use \ProcessMaker\Api;
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
     */
    public function doPostActivityStep($activityUid, $projectUid, $request_data = array())
    {
        try {
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
     */
    public function doPutActivityStep($stepUid, $activityUid, $projectUid, $request_data = array())
    {
        try {
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
}

