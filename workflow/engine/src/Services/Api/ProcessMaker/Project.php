<?php
namespace Services\Api\ProcessMaker;

use Luracast\Restler\RestException;
use ProcessMaker\Services\Api;
use ProcessMaker\Adapter\Bpmn\Model as BpmnModel;
use ProcessMaker\Adapter\Workflow;

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
    public function index()
    {
        try {
            $projects = BpmnModel::loadProjects();

            return $projects;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function get($prjUid)
    {
        try {
            $project = BpmnModel::loadProject($prjUid);

            return $project;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @status 201
     */
    public function post($request_data)
    {
        try {
            $config = array();
            $config['project'] = array('replace_uids' => true);

            $bpmnModel = new BpmnModel();
            $result = $bpmnModel->createProject($request_data, $config['project']['replace_uids']);

            if (array_key_exists('prj_uid', $result)) {
                $prjUid = $result['prj_uid'];
            } else {
                $prjUid = $result[0]['new_uid'];
            }

            $wfProcess = Workflow::loadFromBpmnProject($prjUid);

            $process = new \BusinessModel\Process();
            $userUid = $this->getUserId();
            $data = array('process' => $wfProcess);

            $process->createProcess($userUid, $data);

            return $result;
        } catch (\Exception $e) {
            // TODO in case that $process->createProcess($userUid, $data); fails maybe the BPMN project was created successfully
            //      so, we need remove it or change the creation order.

            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function put($prjUid, $request_data)
    {
        try {

            $result = BpmnModel::updateProject($prjUid, $request_data);

            return $result;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function delete($prjUid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->deleteProcess($prjUid);

            BpmnModel::deleteProject($prjUid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:projectUid/dynaforms
     */
    public function doGetDynaForms($projectUid)
    {
        try {
            $process = new \BusinessModel\Process();

            $response = $process->getDynaForms($projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:projectUid/input-documents
     *
     * @param string $projectUid {@min 32}{@max 32}
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

    /**
     * @url GET /:prj_uid/web-entries
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetWebEntries($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();

            $response = $process->getWebEntries($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

