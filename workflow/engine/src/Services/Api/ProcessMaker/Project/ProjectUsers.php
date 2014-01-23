<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ProjectUsers Api Controller
 *
 * @protected
 */
class ProjectUsers extends Api
{
    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/users
     */
    public function doGetProjectUsers($prjUid)
    {
        try {
            $supervisor = new \BusinessModel\ProjectUser();
            $arrayData = $supervisor->getProjectUsers($prjUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/starting-tasks
     */
    public function doGetProjectStartingTasks($prjUid)
    {
        try {
            $supervisor = new \BusinessModel\ProjectUser();
            $arrayData = $supervisor->getProjectStartingTasks($prjUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $usrUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/user/:usrUid/starting-tasks
     */
    public function doGetProjectStartingTaskUsers($prjUid, $usrUid)
    {
        try {
            $supervisor = new \BusinessModel\ProjectUser();
            $arrayData = $supervisor->getProjectStartingTaskUsers($prjUid, $usrUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }




}
