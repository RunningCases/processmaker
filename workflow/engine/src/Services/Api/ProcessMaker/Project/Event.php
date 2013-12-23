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
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     * @return array
     *
     * @url GET /:projectUid/events
     */
    public function doGetEvents($projectUid, $filter = '')
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid'
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
     * @return array
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @url GET /:projectUid/event/:EventUid
     */
    public function doGetEvent($projectUid, $EventUid)
    {
        try {
            $hiddenFields = array('pro_uid', 'evn_action_parameters',
                'evn_posx', 'evn_posy', 'evn_type', 'tas_evn_uid'
            );
            $event = new \BusinessModel\Event();
            $response = $event->getEvents($projectUid, '', $EventUid);
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
     * @return void
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
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

