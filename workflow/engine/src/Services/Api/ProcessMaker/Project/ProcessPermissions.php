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

