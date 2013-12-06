<?php
namespace Services\Api\ProcessMaker;

use Luracast\Restler\RestException;
use ProcessMaker\Services\Api;
use ProcessMaker\Adapter\Bpmn\Model as BpmnModel;

/**
 * Class Project
 *
 * @package Services\Api\ProcessMaker
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 *
 * @protected
 */
class Project extends Api
{
    function index()
    {
        try {
            $projects = BpmnModel::loadProjects();

            return $projects;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    function post($request_data)
    {
        try {
            $bpmnModel = new BpmnModel();

            return $bpmnModel->createProject($request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    function get($prjUid)
    {
        try {
            $project = BpmnModel::loadProject($prjUid);

            return $project;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
