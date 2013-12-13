<?php
namespace Services\Api\ProcessMaker\Project;

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
     * @param string $projectUid {@min 1} {@max 32}
     *
     * @url GET /:projectUid/triggers
     */
    public function doGetTriggers($projectUid)
    {
        try {
            $trigger = new \BusinessModel\Trigger();
            $response = $trigger->getTriggersCriteria($projectUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $triggerUid {@min 1} {@max 32}
     *
     * @url GET /:projectUid/trigger/:triggerUid
     */
    public function doGetTrigger($projectUid, $triggerUid)
    {
        try {
            $trigger = new \BusinessModel\Trigger();
            $response = $trigger->getDataTrigger($triggerUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $tri_title {@from body} {@min 1}
     * @param string $tri_description {@from body}
     * @param string $tri_type {@from body} {@choice SCRIPT}
     * @param string $tri_webbot {@from body}
     * @param string $tri_param {@from body}
     *
     * @url POST /:projectUid/trigger
     */
    public function doPostTrigger($projectUid, $request_data, $tri_title, $tri_description = '', $tri_type = 'SCRIPT', $tri_webbot = '', $tri_param = '')
    {
        try {
            $trigger = new \BusinessModel\Trigger();
            $response = $trigger->saveTrigger($projectUid, $request_data, true);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $triggerUid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $tri_title {@from body}
     * @param string $tri_description {@from body}
     * @param string $tri_type {@from body} {@choice SCRIPT}
     * @param string $tri_webbot {@from body}
     * @param string $tri_param {@from body}
     *
     * @url PUT /:projectUid/trigger/:triggerUid
     */
    public function doPutTrigger($projectUid, $triggerUid, $request_data, $tri_title = '', $tri_description = '', $tri_type = 'SCRIPT', $tri_webbot = '', $tri_param = '')
    {
        try {
            $request_data['tri_uid'] = $triggerUid;
            $trigger = new \BusinessModel\Trigger();
            $trigger->saveTrigger($projectUid, $request_data, false, $triggerUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $triggerUid {@min 1} {@max 32}
     * 
     * @url DELETE /:projectUid/trigger/:triggerUid
     */
    public function doDeleteTrigger($projectUid, $triggerUid)
    {
        try {
            $trigger = new \BusinessModel\Trigger();
            $response = $trigger->deleteTrigger($triggerUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

