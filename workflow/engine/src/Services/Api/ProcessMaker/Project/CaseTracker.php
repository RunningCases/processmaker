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
     * @param string $projectUid
     * @param CaseTrackerPutStructure $request_data
     */
    public function doPutCaseTracker($projectUid, CaseTrackerPutStructure $request_data = null)
    {
        try {
            $caseTracker = new \BusinessModel\CaseTracker();

            $arrayData = $caseTracker->getArrayDataFromRequestData($request_data);

            $arrayData = $caseTracker->update($projectUid, $arrayData);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/case-tracker/objects
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

class CaseTrackerPutStructure
{
    /**
     * @var string {@from body}{@choice NONE,PROCESSMAP,STAGES}
     */
    public $map_type;

    /**
     * @var bool {@from body}
     */
    public $routing_history;

    /**
     * @var bool {@from body}
     */
    public $message_history;
}

