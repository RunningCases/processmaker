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
            $users = new \BusinessModel\ProjectUser();
            $arrayData = $users->getProjectUsers($prjUid);
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
            $startingTasks = new \BusinessModel\ProjectUser();
            $arrayData = $startingTasks->getProjectStartingTasks($prjUid);
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
            $startingTasks = new \BusinessModel\ProjectUser();
            $arrayData = $startingTasks->getProjectStartingTaskUsers($prjUid, $usrUid);
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
     * @param wsUserCanStartTaskStructure $request_data
     *
     * @url POST /:prjUid/ws/user/can-start-task
     */
    public function doPostProjectWsUserCanStartTask($prjUid, wsUserCanStartTaskStructure $request_data =  null)
    {
        try {
            $request_data = (array)($request_data);
            $user = new \BusinessModel\ProjectUser();
            $objectData = $user->postProjectWsUserCanStartTask($prjUid, $request_data);
            //Response
            $response = $objectData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

}

class wsUserCanStartTaskStructure
{   /**
     * @var string {@from body} {@min 32} {@max 32}
     */
    public $act_uid;

    /**
     * @var string {@from body}
     */
    public $username;

    /**
     * @var string {@from body}
     */
    public $password;
}
