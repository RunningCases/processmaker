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
     * @param string $op_obj_type {@from body} {@choice ANY,DYNAFORM,INPUT,OUTPUT,CASES_NOTES,MSGS_HISTORY}
     * @param string $op_participate {@from body} {@choice 0,1}
     * @param string $op_action {@from body} {@choice VIEW,BLOCK,DELETE}
     * @param string $op_case_status {@from body} {@choice ALL,DRAFT,TO_DO,PAUSED,COMPLETED}
     * @param string $tas_uid {@from body}
     * @param string $group_user {@from body}
     * @param string $dynaforms {@from body}
     * @param string $inputs {@from body}
     * @param string $outputs {@from body}
     * @param string $op_task_source {@from body}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url POST /:projectUid/process-permission/
     */
    public function doPostProcessPermission($projectUid, $request_data, $op_obj_type, $op_participate, $op_action,
        $op_case_status, $tas_uid = '', $group_user = '', $dynaforms = '', $inputs = '', $outputs = '', $op_task_source = '')
    {
        try {
            $request_data['pro_uid'] = $projectUid;
            $processPermissions = new \BusinessModel\ProcessPermissions();
            $response = $processPermissions->saveProcessPermission($request_data);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @param string $projectUid {@min 1} {@max 32}
     * @param string $objectPermissionUid {@min 1} {@max 32}
     * @param array $request_data
     * @param string $op_obj_type {@from body} {@choice ANY,DYNAFORM,INPUT,OUTPUT,CASES_NOTES,MSGS_HISTORY}
     * @param string $op_participate {@from body} {@choice 0,1}
     * @param string $op_action {@from body} {@choice VIEW,BLOCK,DELETE}
     * @param string $op_case_status {@from body} {@choice ALL,DRAFT,TO_DO,PAUSED,COMPLETED}
     * @param string $tas_uid {@from body}
     * @param string $group_user {@from body}
     * @param string $dynaforms {@from body}
     * @param string $inputs {@from body}
     * @param string $outputs {@from body}
     * @param string $op_task_source {@from body}
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url PUT /:projectUid/process-permission/:objectPermissionUid
     */
    public function doPutProcessPermission($projectUid, $objectPermissionUid, $request_data, $op_obj_type,
        $op_participate, $op_action, $op_case_status, $tas_uid = '', $group_user = '', $dynaforms = '', $inputs = '',
        $outputs = '', $op_task_source = '')
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

