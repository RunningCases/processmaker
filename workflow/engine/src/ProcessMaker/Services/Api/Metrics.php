<?php

namespace ProcessMaker\Services\Api;

use Exception;
use ProcessMaker\BusinessModel\Cases\Draft;
use ProcessMaker\BusinessModel\Cases\Inbox;
use ProcessMaker\BusinessModel\Cases\Paused;
use ProcessMaker\BusinessModel\Cases\Unassigned;
use ProcessMaker\Services\Api;
use RBAC;

class Metrics extends Api
{
    /**
     * Constructor of the class
     * Defines the $RBAC definition
     */
    public function __construct()
    {
        global $RBAC;
        if (!isset($RBAC)) {
            $RBAC = RBAC::getSingleton(PATH_DATA, session_id());
            $RBAC->sSystem = 'PROCESSMAKER';
            $RBAC->initRBAC();
            $RBAC->loadUserRolePermission($RBAC->sSystem, $this->getUserId());
        }
    }

    /**
     * Get total cases per process
     * 
     * @url /process-total-cases
     * 
     * @param string $caseList
     * @param int $category
     * @param bool $topTen
     * @param array $processes
     * 
     * @return array
     * 
     * @throws RestException
     * 
     * @class AccessControl {@permission TASK_METRICS_VIEW}
     */
    public function getProcessTotalCases($caseList, $category = null, $topTen = false, $processes = [])
    {
        try {
            switch ($caseList) {
                case 'inbox':
                    $list = new Inbox();
                    break;
                case 'draft':
                    $list = new Draft();
                    break;
                case 'paused':
                    $list = new Paused();
                    break;
                case 'unassigned':
                    $list = new Unassigned();
                    break;
            }
            $result = $list->getCountersByProcesses($category, $topTen, $processes);
            return $result;
        } catch (Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
