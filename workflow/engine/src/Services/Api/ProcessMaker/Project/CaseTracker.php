<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\CaseTracker Api Controller
 *
 * @protected
 */
class CaseTracker extends Api
{
    /**
     * @url GET /:projectUid/case-tracker/property
     *
     * @param string $projectUid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerProperty($projectUid)
    {
        try {
            $caseTracker = new \BusinessModel\CaseTracker();

            $response = $caseTracker->getCaseTracker($projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/case-tracker/property
     *
     * @param string $projectUid      {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $map_type        {@from body}{@choice NONE,PROCESSMAP,STAGES}
     * @param bool   $routing_history {@from body}
     * @param bool   $message_history {@from body}
     */
    public function doPutCaseTracker(
        $projectUid,
        $request_data,
        $map_type = "NONE",
        $routing_history = false,
        $message_history = false
    ) {
        try {
            $caseTracker = new \BusinessModel\CaseTracker();

            $arrayData = $caseTracker->update($projectUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/case-tracker/objects
     *
     * @param string $projectUid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerObjects($projectUid)
    {
        try {
            $caseTracker = new \BusinessModel\CaseTracker();

            $response = $caseTracker->getCaseTrackerObjects($projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/case-tracker/available-objects
     *
     * @param string $projectUid {@min 32}{@max 32}
     */
    public function doGetCaseTrackerAvailableObjects($projectUid)
    {
        try {
            $caseTracker = new \BusinessModel\CaseTracker();

            $response = $caseTracker->getAvailableCaseTrackerObjects($projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

