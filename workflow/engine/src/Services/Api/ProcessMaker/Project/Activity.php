<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity Api Controller
 *
 * @protected
 */
class Activity extends Api
{
    /**    
     * @url GET /:projectUid/activity/:activityUid
     */
    public function doGetProjectActivity($projectUid, $activityUid, $filter = '')
    {
        try {
            $definition = array();
            $properties = array();

            if ($filter == '' || $filter == 'definition') {
                // DEFINITION
                $definition = array();
            }

            if ($filter == '' || $filter == 'properties') {
                // PROPERTIES
                $task = new \BusinessModel\Task();
                $properties = $task->getProperties($activityUid, true, false);
            }

            $response = array(
                'definition' => $definition,
                'properties' => $properties
            );

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::SYSTEM_EXCEPTION_STATUS, $e->getMessage());
        }
    }



    /**    
     * @url PUT /:projectUid/activity/:activityUid
     */
    public function doPutProjectActivity($projectUid, $activityUid, $request_data = array())
    {
        try {
            $task = new \BusinessModel\Task();
            $properties = $task->updateProperties($activityUid, $projectUid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::SYSTEM_EXCEPTION_STATUS, $e->getMessage());
        }
    }



    /**    
     * @url DELETE /:projectUid/activity/:activityUid
     */
    public function doDeleteProjectActivity($projectUid, $activityUid)
    {
        try {
            $task = new \BusinessModel\Task();
            $task->deleteTask($activityUid);
        } catch (\Exception $e) {
            throw new RestException(Api::SYSTEM_EXCEPTION_STATUS, $e->getMessage());
        }
    }
}

