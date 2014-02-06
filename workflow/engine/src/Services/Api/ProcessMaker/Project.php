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
    public function index()
    {
        try {
            $start = null;
            $limit = null;
            $filter = "";

            $projects = \ProcessMaker\Project\Adapter\BpmnWorkflow::getList($start, $limit, $filter, CASE_LOWER);

            return $projects;

        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function get($prjUid)
    {
        try {
            $bwp = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($prjUid);

            $project = array_change_key_case($bwp->getProject(), CASE_LOWER);
            $diagram = $bwp->getDiagram();

            if (! is_null($diagram)) {
                $diagram = array_change_key_case($diagram, CASE_LOWER);
                $diagram["activities"] = $bwp->getActivities(array("changeCaseTo" => CASE_LOWER));
                $diagram["events"] = $bwp->getEvents();
                $diagram["flows"] = $bwp->getFlows();
                $diagram["artifacts"] = $bwp->getArtifacts();
                $diagram["laneset"] = $bwp->getLanesets();
                $diagram["lanes"] = $bwp->getLanes();

                $project["diagrams"][] = $diagram;
            }

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
// NEED REFACTOR
//            $config = array();
//            $config['project'] = array('replace_uids' => true);
//
//            $bpmnModel = new BpmnModel();
//            $result = $bpmnModel->createProject($request_data, $config['project']['replace_uids']);
//
//            if (array_key_exists('prj_uid', $result)) {
//                $prjUid = $result['prj_uid'];
//            } else {
//                $prjUid = $result[0]['new_uid'];
//            }
//
//            $wfProcess = Workflow::loadFromBpmnProject($prjUid);
//
//            $process = new \BusinessModel\Process();
//            $userUid = $this->getUserId();
//            $data = array('process' => $wfProcess);
//
//            $process->createProcess($userUid, $data);

//            return $result;
        } catch (\Exception $e) {
            // TODO in case that $process->createProcess($userUid, $data); fails maybe the BPMN project was created successfully
            //      so, we need remove it or change the creation order.

            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function put($prjUid, $request_data)
    {
        try {

            //$result = BpmnModel::updateProject($prjUid, $request_data);


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
     * @url GET /:prj_uid/process
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProcess($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getProcess($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/process
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutProcess($prj_uid, $request_data)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $process->update($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
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

