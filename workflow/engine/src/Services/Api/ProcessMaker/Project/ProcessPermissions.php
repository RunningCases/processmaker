<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Activity\Step\ProcessPermissions Api Controller
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class ProcessPermissions extends Api
{
    /**
     * @param string $projectUid {@min 1} {@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:projectUid/process-permissions
     */
    public function doGetProcessPermissions($projectUid)
    {
        try {
            $processPermissions = new \BusinessModel\ProcessPermissions();
            $response = $processPermissions->getProcessPermissions($projectUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $objectPermissionUid {@min 1} {@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET /:projectUid/process-permission/:objectPermissionUid
     */
    public function doGetProcessPermission($projectUid, $objectPermissionUid)
    {
        try {
            $processPermissions = new \BusinessModel\ProcessPermissions();
            $response = $processPermissions->getProcessPermissions($projectUid, $objectPermissionUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $usr_uid {@from body} {@min 1} {@max 32}
     * @param string $op_user_relation {@from body} {@choice 1,2}
     * @param string $op_case_status {@from body} {@choice ALL,DRAFT,TO_DO,PAUSED,COMPLETED}
     * @param string $op_participate {@from body} {@choice 0,1}
     * @param string $op_obj_type {@from body} {@choice ANY,DYNAFORM,INPUT,OUTPUT,CASES_NOTES,MSGS_HISTORY}
     * @param string $op_action {@from body} {@choice VIEW,BLOCK,DELETE}
     * @param string $tas_uid {@from body}
     * @param string $op_task_source {@from body}
     * @param string $dynaforms {@from body}
     * @param string $inputs {@from body}
     * @param string $outputs {@from body}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url POST /:projectUid/process-permission/
     */
    public function doPostProcessPermission($projectUid, $request_data, $usr_uid, $op_user_relation, $op_case_status,
        $op_participate, $op_obj_type, $op_action, $tas_uid = '', $op_task_source = '', $dynaforms = '', $inputs = '',
        $outputs = '')
    {
        try {
            $hiddenFields = array('task_target', 'group_user', 'task_source',
                'object_type', 'object', 'participated', 'action'
            );
            $request_data['pro_uid'] = $projectUid;
            $processPermissions = new \BusinessModel\ProcessPermissions();
            $response = $processPermissions->saveProcessPermission($request_data);
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
     * @param string $objectPermissionUid {@min 1} {@max 32}
     * @param array $request_data
     *
     * @param string $usr_uid {@from body} {@min 1} {@max 32}
     * @param string $op_user_relation {@from body} {@choice 1,2}
     * @param string $op_case_status {@from body} {@choice ALL,DRAFT,TO_DO,PAUSED,COMPLETED}
     * @param string $op_participate {@from body} {@choice 0,1}
     * @param string $op_obj_type {@from body} {@choice ANY,DYNAFORM,INPUT,OUTPUT,CASES_NOTES,MSGS_HISTORY}
     * @param string $op_action {@from body} {@choice VIEW,BLOCK,DELETE}
     * @param string $tas_uid {@from body}
     * @param string $op_task_source {@from body}
     * @param string $dynaforms {@from body}
     * @param string $inputs {@from body}
     * @param string $outputs {@from body}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url PUT /:projectUid/process-permission/:objectPermissionUid
     */
    public function doPutProcessPermission($projectUid, $objectPermissionUid, $request_data, $usr_uid,
        $op_user_relation, $op_case_status, $op_participate, $op_obj_type, $op_action, $tas_uid = '',
        $op_task_source = '', $dynaforms = '', $inputs = '', $outputs = '')
    {
        try {
            $request_data['pro_uid'] = $projectUid;
            $processPermissions = new \BusinessModel\ProcessPermissions();
            $response = $processPermissions->saveProcessPermission($request_data, $objectPermissionUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $objectPermissionUid {@min 1} {@max 32}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     *
     * @url DELETE /:projectUid/process-permission/:objectPermissionUid
     */
    public function doDeleteProcessPermission($projectUid, $objectPermissionUid)
    {
        try {
            $processPermissions = new \BusinessModel\ProcessPermissions();
            $response = $processPermissions->deleteProcessPermission($objectPermissionUid, $projectUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

