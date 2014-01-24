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
    function index()
    {
        try {
            $projects = BpmnModel::loadProjects();

            return $projects;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @status 201
     */
    function post($request_data)
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

    function get($prjUid)
    {
        try {
            $project = BpmnModel::loadProject($prjUid);

            return $project;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    function put($prjUid, $request_data)
    {
        try {

            $project = BpmnModel::updateProject($prjUid, $request_data);

            return $project;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    function put22($prjUid, $request_data)
    {
        try {
            $project = BpmnModel::loadProject($prjUid);


            $projectUpdated = $project;
            $projectUpdated['diagrams'][0]['activities'][] = array(
                "act_uid" => "befd5b20508822592122970978652b05",
                "prj_uid" => "27289719452b05bef821d30070436486",
                "pro_uid" => "67771289152b05bef952892039062017",
                "act_name" => "Task # x",
                "act_type" => "TASK",
                "act_is_for_compensation" => 0,
                "act_start_quantity" => "1",
                "act_completion_quantity" => 0,
                "act_task_type" => "EMPTY",
                "act_implementation" => "",
                "act_instantiate" => 0,
                "act_script_type" => "",
                "act_script" => "",
                "act_loop_type" => "NONE",
                "act_test_before" => "",
                "act_loop_maximum" => 0,
                "act_loop_condition" => "",
                "act_loop_cardinality" => 0,
                "act_loop_behavior" => "",
                "act_is_adhoc" => 0,
                "act_is_collapsed" => 0,
                "act_completion_condition" => "",
                "act_ordering" => "",
                "act_cancel_remaining_instances" => 1,
                "act_protocol" => "",
                "act_method" =>"",
                "act_is_global" => 0,
                "act_referer" => "",
                "act_default_flow" => 0,
                "act_master_diagram" => "",
                "bou_uid" => "65717999352b05befddcaf6007443642",
                "dia_uid" => "12117099152b05bef8d4c66069408293",
                "element_uid" => "22970978652b05befd5b205088225921",
                "bou_element" => "pm_canvas",
                "bou_element_type" => "bpmnActivity",
                "bou_x" => 467,
                "bou_y" => 331,
                "bou_width" => 100,
                "bou_height" => 50,
                "bou_rel_position" => 0,
                "bou_size_identical" => 0,
                "bou_container" => "bpmnDiagram"
            );



            echo 'updated: ' . $projectUpdated['diagrams'][0]['activities'][0]['act_uid'] . PHP_EOL;
            echo 'deleted: ' . $projectUpdated['diagrams'][0]['activities'][1]['act_uid'] . PHP_EOL;
            echo 'deleted: ' . $projectUpdated['diagrams'][0]['events'][0]['evn_uid'] . PHP_EOL;
            echo 'deleted: ' . $projectUpdated['diagrams'][0]['events'][1]['evn_uid'] . PHP_EOL;

            unset($projectUpdated['diagrams'][0]['activities'][1]);
            unset($projectUpdated['diagrams'][0]['events'][0]);
            unset($projectUpdated['diagrams'][0]['events'][1]);

            $projectUpdated['diagrams'][0]['activities'][0]['act_name'] = 'changed name';


            $diff = \ProcessMaker\Adapter\Bpmn\Model::getDiffFromProjects($project, $projectUpdated);
            return $diff;

            //$WorkflowProces = \ProcessMaker\Adapter\Workflow::loadFromBpmnProject($prjUid);
            //return $WorkflowProces;
            return $project;
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
     * @url GET /:projectUid/web-entries
     *
     * @param string $projectUid {@min 32}{@max 32}
     */
    public function doGetWebEntries($projectUid)
    {
        try {
            $process = new \BusinessModel\Process();

            $response = $process->getWebEntries($projectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

