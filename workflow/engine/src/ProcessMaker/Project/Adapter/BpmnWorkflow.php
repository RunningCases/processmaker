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

    const BPMN_GATEWAY_COMPLEX = "COMPLEX";
    const BPMN_GATEWAY_PARALLEL = "PARALLEL";
    const BPMN_GATEWAY_INCLUSIVE = "INCLUSIVE";
    const BPMN_GATEWAY_EXCLUSIVE = "EXCLUSIVE";


    /**
     * OVERRIDES
     */

    public static function load($prjUid)
    {

        $parent = parent::load($prjUid);
        //return new BpmnWorkflow();

        $me = new BpmnWorkflow();

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

        $actUid = parent::addActivity($data);
        $taskData["TAS_UID"] = $actUid;

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

        return $actUid;
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

    public function removeGateway($gatUid)
    {
//        $gatewayData = $this->getGateway($gatUid);
//        $flowsDest = \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_DEST, $gatUid);

//        foreach ($flowsDest as $flowDest) {
//            switch ($flowDest->getFloElementOriginType()) {
//                case "bpmnActivity":
//                    $actUid = $flowDest->getFloElementOrigin();
//                    $flowsOrigin = \BpmnFlow::findAllBy(\BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $gatUid);
//
//                    foreach ($flowsOrigin as $flowOrigin) {
//                        switch ($flowOrigin->getFloElementDestType()) {
//                            case "bpmnActivity":
//                                $toActUid = $flowOrigin->getFloElementDest();
//                                $this->wp->removeRouteFromTo($actUid, $toActUid);
//                                break;
//                        }
//                    }
//                    break;
//            }
//        }

        parent::removeGateway($gatUid);
    }

//    public function addFlow($data)
//    {
//        parent::addFlow($data);

        // to add a workflow route
        // - activity -> activity ==> route
        // - activity -> gateway -> activity  ==> selection, evaluation, parallel or parallel by evaluation route
//        $routes = self::mapBpmnFlowsToWorkflowRoute($data, $flows);
//
//        if ($routes !== null) {
//            foreach ($routes as $routeData) {
//                $this->wp->addRoute($routeData["from"], $routeData["to"], $routeData["type"]);
//            }
//
//            return true;
//        }
//
//        // to add start event->activity  as initial or end task
//        switch ($data["FLO_ELEMENT_ORIGIN_TYPE"]) {
//            case "bpmnEvent":
//                switch ($data["FLO_ELEMENT_DEST_TYPE"]) {
//                    case "bpmnActivity":
//                        $event = \BpmnEventPeer::retrieveByPK($data["FLO_ELEMENT_ORIGIN"]);
//
//                        switch ($event && $event->getEvnType()) {
//                            case "START":
//                                // then set that activity/task as "Start Task"
//                                $this->wp->setStartTask($data["FLO_ELEMENT_DEST"]);
//                                break;
//                        }
//                        break;
//                }
//                break;
//        }

//    }

//    public function updateFlow($floUid, $data, $flows)
//    {
//        parent::updateFlow($floUid, $data);
//    }

    public function removeFlow($floUid)
    {
        $flow = \BpmnFlowPeer::retrieveByPK($floUid);
        parent::removeFlow($floUid);

        // verify case: event(start) -> activity
        // => find the corresponding task and unset it as start task
        if ($flow->getFloElementOriginType() == "bpmnEvent" &&
            $flow->getFloElementDestType() == "bpmnActivity"
        ) {
            $event = \BpmnEventPeer::retrieveByPK($flow->getFloElementOrigin());

            if (! is_null($event) && $event->getEvnType() == "START") {
                $activity = \BpmnActivityPeer::retrieveByPK($flow->getFloElementDest());
                $this->wp->setStartTask($activity->getActUid(), false);
            }
        } elseif ($flow->getFloElementOriginType() == "bpmnActivity" &&
            $flow->getFloElementDestType() == "bpmnEvent") {
            // verify case: activity -> event(end)
            // => find the corresponding task and unset it as start task
            $event = \BpmnEventPeer::retrieveByPK($flow->getFloElementDest());

            if (! is_null($event) && $event->getEvnType() == "END") {
                $activity = \BpmnActivityPeer::retrieveByPK($flow->getFloElementOrigin());

                if (! is_null($activity)) {
                    $this->wp->setEndTask($activity->getActUid(), false);
                }
            }
        } else {
            switch ($flow->getFloElementOriginType()) {
                case "bpmnActivity":
                    switch ($flow->getFloElementDestType()) {
                        // activity->activity
                        case "bpmnActivity":
                            $this->wp->removeRouteFromTo($flow->getFloElementOrigin(), $flow->getFloElementDest());
                            break;
                    }
                    break;
            }
        }

        // TODO Complete for other routes, activity->activity, activity->gateway and viceversa
    }

    public function addEvent($data)
    {
        if (! array_key_exists("EVN_TYPE", $data)) {
            throw new \RuntimeException("Required param \"EVN_TYPE\" is missing.");
        }

        parent::addEvent($data);
    }

    public function mapBpmnFlowsToWorkflowRoutes()
    {
        $activities = $this->getActivities();

        foreach ($activities as $activity) {

            $flows = \BpmnFlow::findAllBy(array(
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN => $activity["ACT_UID"],
                \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnActivity"
            ));

            //
            foreach ($flows as $flow) {
                switch ($flow->getFloElementDestType()) {
                    case "bpmnActivity":
                        // (activity -> activity)
                        $this->wp->addRoute($activity["ACT_UID"], $flow->getFloElementDest(), "SEQUENTIAL");
                        break;

                    case "bpmnGateway":
                        // (activity -> gateway)
                        // we must find the related flows: gateway -> <object>
                        $gatUid = $flow->getFloElementDest();
                        $gatewayFlows = \BpmnFlow::findAllBy(array(
                            \BpmnFlowPeer::FLO_ELEMENT_ORIGIN => $gatUid,
                            \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => "bpmnGateway"
                        ));

                        if ($gatewayFlows > 0) {
                            $this->wp->resetTaskRoutes($activity["ACT_UID"]);
                        }

                        foreach ($gatewayFlows as $gatewayFlow) {
                            $gatewayFlow = $gatewayFlow->toArray();

                            switch ($gatewayFlow['FLO_ELEMENT_DEST_TYPE']) {
                                case 'bpmnActivity':
                                    // (gateway -> activity)
                                    $gateway = \BpmnGateway::findOneBy(\BpmnGatewayPeer::GAT_UID, $gatUid)->toArray();

                                    switch ($gateway["GAT_TYPE"]) {
                                        //case 'SELECTION':
                                        case self::BPMN_GATEWAY_COMPLEX:
                                            $routeType = "SELECT";
                                            break;
                                        //case 'EVALUATION':
                                        case self::BPMN_GATEWAY_EXCLUSIVE:
                                            $routeType = "EVALUATE";
                                            break;
                                        //case 'PARALLEL':
                                        case self::BPMN_GATEWAY_PARALLEL:
                                            if ($gateway["GAT_DIRECTION"] == "DIVERGING") {
                                                $routeType = "PARALLEL";
                                            } elseif ($gateway["GAT_DIRECTION"] == "CONVERGING") {
                                                $routeType = "SEC-JOIN";
                                            } else {
                                                throw new \LogicException(sprintf(
                                                    "Invalid Gateway direction, accepted values: [%s|%s], given: %s.",
                                                    "DIVERGING", "CONVERGING", $gateway["GAT_DIRECTION"]
                                                ));
                                            }
                                            break;
                                        //case 'PARALLEL_EVALUATION':
                                        case self::BPMN_GATEWAY_INCLUSIVE:
                                            if ($gateway["GAT_DIRECTION"] == "DIVERGING") {
                                                $routeType = "PARALLEL-BY-EVALUATION";
                                            } elseif ($gateway["GAT_DIRECTION"] == "CONVERGING") {
                                                $routeType = "SEC-JOIN";
                                            } else {
                                                throw new \LogicException(sprintf(
                                                    "Invalid Gateway direction, accepted values: [%s|%s], given: %s.",
                                                    "DIVERGING", "CONVERGING", $gateway["GAT_DIRECTION"]
                                                ));
                                            }
                                            break;
//                                        case 'PARALLEL_JOIN':
//                                            $routeType = 'SEC-JOIN';
//                                            break;
                                        default:
                                            throw new \LogicException(sprintf("Unsupported Gateway type: %s", $gateway['GAT_TYPE']));
                                    }

                                    $this->wp->addRoute($activity["ACT_UID"], $gatewayFlow['FLO_ELEMENT_DEST'], $routeType);
                                    break;
                                default:
                                    // for processmaker is only allowed flows between "gateway -> activity"
                                    // any another flow is considered invalid
                                    throw new \LogicException(sprintf(
                                        "For ProcessMaker is only allowed flows between \"gateway -> activity\" " . PHP_EOL .
                                        "Given: bpmnGateway -> " . $gatewayFlow['FLO_ELEMENT_DEST_TYPE']
                                    ));
                            }
                        }
                        break;
                }
            }
        }
    }

    public static function mapBpmnFlowsToWorkflowRoute2($flow, $flows, $gateways, $events)
    {
        $fromUid = $flow['FLO_ELEMENT_ORIGIN'];
        $result = array();

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
                $result[] = array("from" => $fromUid, "to" => $flow['FLO_ELEMENT_DEST'], "type" => 'SEQUENTIAL');
                break;
            case 'bpmnGateway':
                $gatUid = $flow['FLO_ELEMENT_DEST'];

                // if it is a gateway it can fork one or more routes
                $gatFlows = self::findInArray($gatUid, "FLO_ELEMENT_ORIGIN", $flows);

                foreach ($gatFlows as $gatFlow) {
                    switch ($gatFlow['FLO_ELEMENT_DEST_TYPE']) {
                        case 'bpmnActivity':
                            // getting gateway properties
                            $gateways = self::findInArray($gatUid, "GAT_UID", $gateways);

                            if (! empty($gateways)) {
                                $gateway = $gateways[0];
                                $routeType = "";

                                switch ($gateway['GAT_TYPE']) {
                                    case self::BPMN_GATEWAY_COMPLEX:
                                        $routeType = 'SELECT';
                                        break;
                                    case self::BPMN_GATEWAY_EXCLUSIVE:
                                        $routeType = 'EVALUATE';
                                        break;
                                    case self::BPMN_GATEWAY_INCLUSIVE:
                                        switch ($gateway['GAT_DIRECTION']) {
                                            case "DIVERGING":
                                                $routeType = 'PARALLEL-BY-EVALUATION';
                                                break;
                                            case "CONVERGING":
                                                $routeType = 'SEC-JOIN';
                                                break;
                                            default:
                                                throw new \LogicException(sprintf("Unsupported Gateway direction: %s", $gateway['GAT_DIRECTION']));
                                        }
                                        break;
                                    case self::BPMN_GATEWAY_PARALLEL:
                                        switch ($gateway['GAT_DIRECTION']) {
                                            case "DIVERGING":
                                                $routeType = 'PARALLEL';
                                                break;
                                            case "CONVERGING":
                                                $routeType = 'SEC-JOIN';
                                                break;
                                            default:
                                                throw new \LogicException(sprintf("Unsupported Gateway direction: %s", $gateway['GAT_DIRECTION']));
                                        }
                                        break;
                                    default:
                                        throw new \LogicException(sprintf("Unsupported Gateway type: %s", $gateway['GAT_TYPE']));
                                }

                                $result[] = array("from" => $fromUid, "to" => $gatFlow['FLO_ELEMENT_DEST'], "type" => $routeType);
                            }
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
                $evnUid = $flow['FLO_ELEMENT_DEST'];
                $events = self::findInArray($evnUid, "EVN_UID", $events);


                if (! empty($events)) {
                    $event = $events[0];

                    switch ($event['EVN_TYPE']) {
                        case 'END':
                            $routeType = 'SEQUENTIAL';
                            $result[] = array("from" => $fromUid, "to" => "-1", "type" => $routeType);
                            break;
                        default:
                            throw new \LogicException("Invalid connection to Event object type");
                    }
                }
                break;
        }

        return empty($result) ? null : $result;
    }

    protected static function findInArray($value, $key, $list)
    {
        $result = array();

        foreach ($list as $item) {
            if (array_key_exists($key, $item) && $item[$key] == $value) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function remove()
    {
        parent::remove();
        $this->wp->remove();
    }

}