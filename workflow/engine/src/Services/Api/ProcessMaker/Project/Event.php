<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Step\Event Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Event extends Api
{
    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $filter {@choice message,conditional,,multiple}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:projectUid/events
     */
    public function doGetEvents($projectUid, $filter = '')
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid', 'evn_max_attempts'
            );
            $event = new \BusinessModel\Event();
            $response = $event->getEvents($projectUid, $filter);
            foreach ($response as &$eventData) {
                foreach ($eventData as $key => $value) {
                    if (in_array($key, $hiddenFields)) {
                        unset($eventData[$key]);
                    }
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $EventUid {@min 1} {@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:projectUid/event/:EventUid
     */
    public function doGetEvent($projectUid, $EventUid)
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid', 'evn_max_attempts'
            );
            $event = new \BusinessModel\Event();
            $response = $event->getEvents($projectUid, '', $EventUid);
            foreach ($response as $key => $eventData) {
                if (in_array($key, $hiddenFields)) {
                    unset($response[$key]);
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $evn_description {@from body} {@min 1}
     * @param string $evn_status {@from body} {@choice ACTIVE,INACTIVE}
     * @param string $evn_action {@from body} {@choice SEND_MESSAGE,EXECUTE_CONDITIONAL_TRIGGER,EXECUTE_TRIGGER}
     * @param string $evn_related_to {@from body} {@choice SINGLE,MULTIPLE}
     * @param string $tas_uid {@from body} {@min 1}
     * @param string $evn_tas_uid_from {@from body} {@min 1}
     * @param string $evn_tas_estimated_duration {@from body} {@min 1}
     * @param string $evn_time_unit {@from body} {@choice DAYS,HOURS}
     * @param string $evn_when {@from body} {@type float}
     * @param string $evn_when_occurs {@from body} {@choice AFTER_TIME,TASK_STARTED}
     * @param string $tri_uid {@from body} {@min 1}
     * @param string $evn_tas_uid_to {@from body}
     * @param string $evn_conditions {@from body}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url POST /:projectUid/event
     */
    public function doPostEvent($projectUid, $request_data, $evn_description, $evn_status, $evn_action,
        $evn_related_to, $tas_uid, $evn_tas_uid_from, $evn_tas_estimated_duration,
        $evn_time_unit, $evn_when, $evn_when_occurs, $tri_uid, $evn_tas_uid_to = '', $evn_conditions = '')
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid', 'evn_max_attempts'
            );
            $event = new \BusinessModel\Event();
            $response = $event->saveEvents($projectUid, $request_data, true);
            foreach ($response as $key => $eventData) {
                if (in_array($key, $hiddenFields)) {
                    unset($response[$key]);
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $EventUid {@min 1} {@max 32}
     * @return void
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     *
     * @url DELETE /:projectUid/event/:eventUid
     */
    public function doDeleteEvent($projectUid, $eventUid)
    {
        try {
            $event = new \BusinessModel\Event();
            $response = $event->deleteEvent($eventUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

