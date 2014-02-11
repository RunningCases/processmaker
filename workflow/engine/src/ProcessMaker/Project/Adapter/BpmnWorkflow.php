<?php
namespace ProcessMaker\Project\Adapter;

use ProcessMaker\Project;
use ProcessMaker\Util\Hash;

/**
 * Class BpmnWorkflow
 *
 * @package ProcessMaker\Project\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class BpmnWorkflow extends Project\Bpmn
{
    /**
     * @var \ProcessMaker\Project\Workflow
     */
    protected $wp;

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

            $this->wp = new Project\Workflow();
            $this->wp->create($wpData);

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
        $taskData = array();

        $taskData["TAS_UID"] = parent::addActivity($data);

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

    public function addFlow($data, $flows, $gateways, $events)
    {
        parent::addFlow($data);

        // to add a workflow route
        // - activity -> activity ==> route
        // - activity -> gateway -> activity  ==> selection, evaluation, parallel or parallel by evaluation route
        $routeData = self::mapBpmnFlowsToWorkflowRoute($data, $flows, $gateways, $events);

        if ($routeData !== null) {
            $this->wp->addRoute($routeData["from"], $routeData["to"], $routeData["type"]);

            return true;
        }

        // to add start event->activity  as initial or end task
        switch ($data["FLO_ELEMENT_ORIGIN_TYPE"]) {
            case "bpmnEvent":
                switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
                    case "bpmnActivity":
                        $event = \BpmnEventPeer::retrieveByPK($data["FLO_ELEMENT_ORIGIN"]);

                        switch ($event && $event->getEvnType()) {
                            case "START":
                                self::log("Setting Task:" . $data["FLO_ELEMENT_DEST"] . " as STARTING TASK");

                                // then set that activity/task as "Start Task"
                                $this->wp->updateTask($data["FLO_ELEMENT_DEST"], array("TAS_START" => "TRUE"));

                                self::log("Setting as \"Stating Task\" Success!");
                                break;
                        }
                        break;
                }
                break;
        }
    }


    public function addEvent($data)
    {
        if (! array_key_exists("EVN_TYPE", $data)) {
            throw new \RuntimeException("Required param \"EVN_TYPE\" is missing.");
        }

        parent::addEvent($data);
    }

    public function removeEvent($evnUid)
    {
        $flow = \BpmnFlowPeer::retrieveByPK($evnUid);

        if (! is_null($flow)) {
            $data = $flow->toArray();

            // to add start event->activity  as initial or end task
            switch ($data["FLO_ELEMENT_ORIGIN_TYPE"]) {
                case "bpmnEvent":
                    switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
                        case "bpmnActivity":
                            $event = \BpmnEventPeer::retrieveByPK($data["FLO_ELEMENT_ORIGIN"]);

                            switch ($event && $event->getEvnType()) {
                                case "START":
                                    self::log("Unset Task:" . $data["FLO_ELEMENT_DEST"] . " as NOT STARTING TASK");

                                    // then set that activity/task as "Start Task"
                                    $this->wp->updateTask($data["FLO_ELEMENT_DEST"], array("TAS_START" => "FALSE"));

                                    self::log("Unset as \"Stating Task\" Success!");
                                    break;
                            }
                            break;
                    }
                    break;
            }
        }

        parent::removeEvent($evnUid);
    }

    public static function mapBpmnFlowsToWorkflowRoute($flow, $flows, $gateways, $events)
    {
        $fromUid = $flow['FLO_ELEMENT_ORIGIN'];

        if ($flow['FLO_ELEMENT_ORIGIN_TYPE'] != "bpmnActivity") {
            // skip flows that comes from a element that is not an Activity
            self::log("Skip map FlowsToWorkflowRoute for -> flow with FLO_UID: {$flow['FLO_UID']}, that have FLO_ELEMENT_ORIGIN: {$flow['FLO_ELEMENT_ORIGIN_TYPE']}:$fromUid");
            return null;
        }

        if ($flow['FLO_TYPE'] != 'SEQUENCE') {
            throw new \LogicException(sprintf(
                "Unsupported flow type: %s, ProcessMaker only support type '', Given: '%s'",
                'SEQUENCE', $flow['FLO_TYPE']
            ));
        }

        switch ($flow['FLO_ELEMENT_DEST_TYPE']) {
            case 'bpmnActivity':
                // the most easy case, when the flow is connecting a activity with another activity
                $result = array("from" => $fromUid, "to" => $flow['FLO_ELEMENT_DEST'], "type" => 'SEQUENTIAL');
                break;
            case 'bpmnGateway':
                $gatUid = $flow['FLO_ELEMENT_DEST'];

                // if it is a gateway it can fork one or more routes
                $gatFlow = self::findInArray($gatUid, "FLO_ELEMENT_ORIGIN", $flows);

                //foreach ($gatFlows as $gatFlow) {
                switch ($gatFlow['FLO_ELEMENT_DEST_TYPE']) {
                    case 'bpmnActivity':
                        // getting gateway properties
                        $gateway = self::findInArray($gatUid, "GAT_UID", $gateways);

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

                        $result = array("from" => $fromUid, "to" => $gatFlow['FLO_ELEMENT_DEST'], "type" => $routeType);
                        break;
                    default:
                        // for processmaker is only allowed flows between "gateway -> activity"
                        // any another flow is considered invalid
                        throw new \LogicException(sprintf(
                            "For ProcessMaker is only allowed flows between \"gateway -> activity\" " . PHP_EOL .
                            "Given: bpmnGateway -> " . $gatFlow['FLO_ELEMENT_DEST_TYPE']
                        ));
                }
                //}
                break;
            case 'bpmnEvent':
                $evnUid = $flow['FLO_ELEMENT_DEST'];
                $event = self::findInArray($evnUid, "EVN_UID", $events);

                switch ($event['EVN_TYPE']) {
                    case 'END':
                        $routeType = 'SEQUENTIAL';
                        $result = array("from" => $fromUid, "to" => "-1", "type" => $routeType);
                        break;
                    default:
                        throw new \LogicException("Invalid connection to Event object type");
                }

                break;
        }

        return $result;

    }

    protected static function findInArray($value, $key, $list)
    {
        foreach ($list as $item) {
            if (! array_key_exists($key, $item)) {
                throw new \Exception("Error: key: $key does not exist in array: " . print_r($item, true));
            }
            if ($item[$key] == $value) {
                return $item;
            }
        }

        return null;
    }

    public function remove()
    {
        parent::remove();
        $this->wp->remove();
    }
}