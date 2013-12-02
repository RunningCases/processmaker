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
            $hiddenFields = array('pro_uid', 'tas_uid', 'tas_delay_type', 'tas_temporizer', 'tas_alert',
                'tas_mi_instance_variable', 'tas_mi_complete_variable', 'tas_assign_location',
                'tas_assign_location_adhoc', 'tas_last_assigned', 'tas_user', 'tas_can_upload', 'tas_view_upload',
                'tas_view_additional_documentation', 'tas_can_cancel', 'tas_owner_app', 'tas_can_pause',
                'tas_can_send_message', 'tas_can_delete_docs', 'tas_self_service', 'tas_to_last_user',
                'tas_derivation', 'tas_posx', 'tas_posy', 'tas_width', 'tas_height', 'tas_color', 'tas_evn_uid',
                'tas_boundary', 'tas_def_proc_code', 'stg_uid'
            );
            $definition = array();
            $properties = array();

            if ($filter == '' || $filter == 'definition') {
                // DEFINITION
                $definition = array();
                $response['definition'] = $definition;
            }

            if ($filter == '' || $filter == 'properties') {
                // PROPERTIES
                $task = new \BusinessModel\Task();
                $properties = $task->getProperties($activityUid, true, false);
                foreach ($properties as $key => $value) {
                    if (in_array($key, $hiddenFields)) {
                        unset($properties[$key]);
                    }
                }
                $response['properties'] = $properties;
            }

            return $response;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
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
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
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
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

