<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity Api Controller
 *
 * @protected
 */
class Activity extends Api
{
    /**
     * @param string $projectUid {@min 32} {@max 32}
     * @param string $activityUid {@min 32} {@max 32}
     * @param string $filter {@choice definition,,properties}
     *
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
     * @param string $projectUid {@min 32} {@max 32}
     * @param string $activityUid {@min 32} {@max 32}
     * @param ActivityPropertiesStructure $properties {@from body}
     *
     * @url PUT /:projectUid/activity/:activityUid
     */
    public function doPutProjectActivity($projectUid, $activityUid, ActivityPropertiesStructure $properties, $request_data =  array())
    {
        try {
            $task = new \BusinessModel\Task();
            $properties = $task->updateProperties($activityUid, $projectUid, $request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }



    /**
     * @param string $projectUid {@min 32} {@max 32}
     * @param string $activityUid {@min 32} {@max 32}
     *
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

    /**
     * @url GET /:projectUid/activity/:activityUid/steps
     */
    public function doGetActivitySteps($activityUid, $projectUid)
    {
        try {
            $task = new \BusinessModel\Task();

            $response = $task->getSteps($activityUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/activity/:activityUid/available-steps
     */
    public function doGetActivityAvailableSteps($activityUid, $projectUid)
    {
        try {
            $task = new \BusinessModel\Task();

            $response = $task->getAvailableSteps($activityUid, $projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}


class ActivityPropertiesStructure
{
    /**
     * @var string {@from body} {@min 1} {@max 200}
     */
    public $tas_title;

    /**
     * @var string {@from body}
     */
    public $tas_description;

    /**
     * @var string {@from body}
     */
    public $tas_priority_variable;

    /**
     * @var string {@from body}
     */
    public $tas_derivation_screen_tpl;

    /**
     * @var string {@from body} {@choice TRUE,FALSE}
     */
    public $tas_start;

    /**
     * @var string {@from body} {@choice BALANCED,MANUAL,EVALUATE,REPORT_TO,SELF_SERVICE,SELF_SERVICE_EVALUATE}
     */
    public $tas_assign_type;

    /**
     * @var string {@from body}
     */
    public $tas_assign_variable;

    /**
     * @var string {@from body}
     */
    public $tas_group_variable;

    /**
     * @var string {@from body}
     */
    public $tas_selfservice_time;

    /**
     * @var int {@from body} {@choice 1,0}
     */
    public $tas_selfservice_timeout;

    /**
     * @var string {@from body} {@choice DAYS,,HOURS}
     */
    public $tas_selfservice_time_unit;

    /**
     * @var string {@from body} {@min 0} {@max 32}
     */
    public $tas_selfservice_trigger_uid;

    /**
     * @var string {@from body} {@choice TRUE,FALSE}
     */
    public $tas_transfer_fly;

    /**
     * @var int {@from body}
     */
    public $tas_duration;

    /**
     * @var string {@from body} {@choice DAYS,HOURS}
     */
    public $tas_timeunit;

    /**
     * @var string {@from body} {@choice 1,,2}
     */
    public $tas_type_day;

    /**
     * @var string {@from body} {@min 0} {@max 32}
     */
    public $tas_calendar;

    /**
     * @var string {@from body} {@choice NORMAL,ADHOC}
     */
    public $tas_type;

    /**
     * @var string {@from body}
     */
    public $tas_def_title;

    /**
     * @var string {@from body}
     */
    public $tas_def_description;

    /**
     * @var string {@from body} {@choice TRUE,FALSE}
     */
    public $tas_send_last_email;

    /**
     * @var string {@from body}
     */
    public $tas_def_subject_message;

    /**
     * @var string {@from body} {@choice template,text}
     */
    public $tas_def_message_type;

    /**
     * @var string {@from body}
     */
    public $tas_def_message;

    /**
     * @var string {@from body}
     */
    public $tas_def_message_template;
}
