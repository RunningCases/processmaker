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
            $uids = $bpmnModel->createProject($request_data);

            $wfProcess = \ProcessMaker\Adapter\Workflow::loadFromBpmnProject($uids[0]['new_uid']);

            $process = new \BusinessModel\Process();
            $userUid = $this->getUserId();
            $data = array('process' => $wfProcess);
            $process->createProcess($userUid, $data);

            return $uids;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    function get($prjUid)
    {
        try {
            $project = BpmnModel::loadProject($prjUid);

            //$WorkflowProces = \ProcessMaker\Adapter\Workflow::loadFromBpmnProject($prjUid);
            //return $WorkflowProces;
            return $project;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:projectUid/input-documents
     */
    public function doGetInputDocuments($projectUid)
    {
        try {
            $process = new \BusinessModel\Process();

            $response = $process->getInputDocuments($projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

