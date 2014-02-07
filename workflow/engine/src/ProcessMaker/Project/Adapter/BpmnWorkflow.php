<?php
namespace ProcessMaker\Project\Adapter;

use ProcessMaker\Project;
use ProcessMaker\Util\Hash;

/**
 * Class BpmnWorkflow
 * @package ProcessMaker\Project\Adapter
 */
class BpmnWorkflow extends Project\Bpmn
{
    /**
     * @var \ProcessMaker\Project\Workflow
     */
    protected $wp;

    public function __construct()
    {
        $this->wp = new Project\Workflow();
    }


    /**
     * OVERRIDES
     */

    public static function load($prjUid)
    {
        $parent = parent::load($prjUid);

        $me = new self();

        $me->project = $parent->project;
        $me->prjUid = $parent->project->getPrjUid();
        $me->wp = Project\Workflow::load($me->prjUid);

        return $me;
    }

    public function create($data)
    {
        try {
            parent::create($data);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Can't create Bpmn Project." . PHP_EOL . $e->getMessage()));
        }

        try {
            $wpData = array();
            $wpData["PRO_UID"] = $this->getUid();

            if (array_key_exists("PRJ_NAME", $data)) {
                $wpData["PRO_TITLE"] = $data["PRJ_NAME"];
            }
            if (array_key_exists("PRJ_DESCRIPTION", $data)) {
                $wpData["PRO_DESCRIPTION"] = $data["PRJ_DESCRIPTION"];
            }
            if (array_key_exists("PRJ_AUTHOR", $data)) {
                $wpData["PRO_CREATE_USER"] = $data["PRJ_AUTHOR"];
            }

            $wp = new Project\Workflow();
            $wp->create($wpData);

        } catch (\Exception $e) {
            $prjUid = $this->getUid();
            $this->remove();

            throw new \RuntimeException(sprintf(
                "Can't create Bpmn Project with prj_uid: %s, workflow creation fails." . PHP_EOL . $e->getMessage()
                , $prjUid
            ));
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        $bpmnProjects = parent::getList($start, $limit, $filter, $changeCaseTo);
        $workflowProjects = Project\Workflow::getList($start, $limit, "", "");

        $workflowProjectsUids = array();

        foreach ($workflowProjects as $workflowProject) {
            $workflowProjectsUids[] = $workflowProject["PRO_UID"];
        }

        $prjUidKey = $changeCaseTo == CASE_UPPER ? "PRJ_UID" : "prj_uid";
        $list = array();

        foreach ($bpmnProjects as $bpmnProject) {
            if (in_array($bpmnProject[$prjUidKey], $workflowProjectsUids)) {
                $list[] = $bpmnProject;
            }
        }

        return $list;
    }

    public function addActivity($data)
    {
        parent::addActivity($data);

        $taskData = array();
        $taskData["TAS_UID"] = $data["ACT_UID"];

        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_TITLE"] = $data["ACT_NAME"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSX"] = $data["BOU_X"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSY"] = $data["BOU_Y"];
        }

        $this->wp->addTask($taskData);
    }

    public function updateActivity($actUid, $data)
    {
        parent::updateActivity($actUid, $data);

        $taskData = array();

        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_TITLE"] = $data["ACT_NAME"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSX"] = $data["BOU_X"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSY"] = $data["BOU_Y"];
        }

        $this->wp->updateTask($actUid, $taskData);
    }

    public function removeActivity($actUid)
    {
        parent::removeActivity($actUid);
        $this->wp->removeTask($actUid);
    }

    public function addFlow($data)
    {
        parent::addFlow($data);

        $fromUid = $data['FLO_ELEMENT_ORIGIN'];

        if ($data['FLO_TYPE'] != 'SEQUENCE') {
            throw new \LogicException(sprintf(
                "Unsupported flow type: %s, ProcessMaker only support type '', Given: '%s'",
                'SEQUENCE', $data['FLO_TYPE']
            ));
        }

        switch ($data['FLO_ELEMENT_DEST_TYPE']) {
            case 'bpmnActivity':
                // the most easy case, when the flow is connecting a activity with another activity
                /*$data = array(
                    'ROU_UID' => $data['FLO_UID'], //Hash::generateUID(),
                    'PRO_UID' => $this->getUid(),
                    'TAS_UID' => $fromUid,
                    'ROU_NEXT_TASK' => $data['FLO_ELEMENT_DEST'],
                    'ROU_TYPE' => 'SEQUENTIAL'
                );*/
                $this->wp->addRoute($fromUid, $data['FLO_ELEMENT_DEST'], 'SEQUENTIAL');
                break;
            case 'bpmnGateway':
                $gatUid = $data['FLO_ELEMENT_DEST'];
                // if it is a gateway it can fork one or more routes
                $gatFlows = BpmnModel::getBpmnCollectionBy('Flow', \BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $gatUid);

                foreach ($gatFlows as $gatFlow) {
                    switch ($gatFlow['FLO_ELEMENT_DEST_TYPE']) {
                        case 'bpmnActivity':
                            // getting gateway properties
                            $gateway = BpmnModel::getBpmnObjectBy('Gateway', \BpmnGatewayPeer::GAT_UID, $gatUid);

                            switch ($gateway['GAT_TYPE']) {
                                case 'SELECTION':
                                    $routeType = 'SELECT';
                                    break;
                                case 'EVALUATION':
                                    $routeType = 'EVALUATE';
                                    break;
                                case 'PARALLEL':
                                    $routeType = 'PARALLEL';
                                    break;
                                case 'PARALLEL_EVALUATION':
                                    $routeType = 'PARALLEL-BY-EVALUATION';
                                    break;
                                case 'PARALLEL_JOIN':
                                    $routeType = 'SEC-JOIN';
                                    break;
                                default:
                                    throw new \LogicException(sprintf("Unsupported Gateway type: %s", $gateway['GAT_TYPE']));
                            }

                            $routes[] = array(
                                'ROU_UID' => $gatFlow['FLO_UID'], //Hash::generateUID(),
                                'PRO_UID' => $this->getUid(),
                                'TAS_UID' => $fromUid,
                                'ROU_NEXT_TASK' => $gatFlow['FLO_ELEMENT_DEST'],
                                'ROU_TYPE' => $routeType,
                                '_action' => 'CREATE'
                            );
                            break;
                        default:
                            // for processmaker is only allowed flows between "gateway -> activity"
                            // any another flow is considered invalid
                            throw new \LogicException(sprintf(
                                "For ProcessMaker is only allowed flows between \"gateway -> activity\" " . PHP_EOL .
                                "Given: bpmnGateway -> " . $gatFlow['FLO_ELEMENT_DEST_TYPE']
                            ));
                    }
                }
                break;
            case 'bpmnEvent':
                $evnUid = $data['FLO_ELEMENT_DEST'];
                $event = BpmnModel::getBpmnObjectBy('Event', \BpmnEventPeer::EVN_UID, $evnUid);

                switch ($event['EVN_TYPE']) {
                    case 'END':
                        $routeType = 'SEQUENTIAL';
                        $routes[] = array(
                            'ROU_UID' => $data['FLO_UID'], //Hash::generateUID(),
                            'PRO_UID' => $this->getUid(),
                            'TAS_UID' => $fromUid,
                            'ROU_NEXT_TASK' => '-1',
                            'ROU_TYPE' => $routeType,
                            '_action' => 'CREATE'
                        );
                        break;
                    default:
                        throw new \LogicException("Invalid connection to Event object type");
                }

                break;
        }


    }
}