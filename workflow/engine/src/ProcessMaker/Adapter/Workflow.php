<?php
namespace ProcessMaker\Adapter;

use \Process;
use \ProcessMaker\Adapter\Bpmn\Model as BpmnModel;
use \ProcessMaker\Util\Hash;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class Workflow
 *
 * @package ProcessMaker\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Workflow
{
    public static function loadFromBpmnProject($prjUid)
    {
        $project = BpmnModel::getBpmnObjectBy('Project', \BpmnProjectPeer::PRJ_UID, $prjUid);

        $process = array();
        $process['PRO_UID'] = $prjUid;
        $process['PRO_TITLE'] = $project['PRJ_NAME'];
        $process['PRO_DESCRIPTION'] = '';
        $process['PRO_CATEGORY'] = '';

        $process['tasks'] = array();
        $process['routes'] = array();

        $projectActivities = BpmnModel::getBpmnCollectionBy('Activity', \BpmnActivityPeer::PRJ_UID, $prjUid);

        foreach ($projectActivities as $activity) {
            $activityBound = BpmnModel::getBpmnObjectBy('Bound', \BpmnBoundPeer::ELEMENT_UID, $activity['ACT_UID']);

            $process['tasks'][] = array(
                'TAS_UID' => $activity['ACT_UID'],
                'TAS_TITLE' => $activity['ACT_NAME'],
                'TAS_DESCRIPTION' => $activity['ACT_NAME'],
                'TAS_POSX' => $activityBound['BOU_X'],
                'TAS_POSY' => $activityBound['BOU_Y'],
                'TAS_START' => (self::activityIsStartTask($activity['ACT_UID']) ? 'TRUE' : 'FALSE'),
                '_action' => 'CREATE'
            );

            $process['routes'] = array_merge($process['routes'], self::getRoutesFromBpmnFlows($prjUid, $activity['ACT_UID']));
        }

        return $process;
    }

    private static function getRoutesFromBpmnFlows($prjUid, $actUid)
    {
        $flows = BpmnModel::select('*', 'Flow', array(
            \BpmnFlowPeer::FLO_ELEMENT_ORIGIN => $actUid,
            \BpmnFlowPeer::FLO_ELEMENT_ORIGIN_TYPE => 'bpmnActivity'
        ));
        $routes = array();

        foreach ($flows as $flow) {
            $fromUid = $flow['FLO_ELEMENT_ORIGIN'];

            switch ($flow['FLO_TYPE']) {
                case 'SEQUENCE':
                    break;
                default:
                    throw new \LogicException(sprintf(
                        "Unsupported flow type: %s, ProcessMaker only support type '', Given: '%s'",
                        'SEQUENCE', $flow['FLO_TYPE']
                    ));
            }

            switch ($flow['FLO_ELEMENT_DEST_TYPE']) {
                case 'bpmnActivity':
                    // the most easy case, when the flow is connecting a activity with another activity
                    $routes[] = array(
                        'ROU_UID' => $flow['FLO_UID'], //Hash::generateUID(),
                        'PRO_UID' => $prjUid,
                        'TAS_UID' => $fromUid,
                        'ROU_NEXT_TASK' => $flow['FLO_ELEMENT_DEST'],
                        'ROU_TYPE' => 'SEQUENTIAL',
                        '_action' => 'CREATE'
                    );
                    break;
                case 'bpmnGateway':
                    $gatUid = $flow['FLO_ELEMENT_DEST'];
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
                                    'PRO_UID' => $prjUid,
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
                    $evnUid = $flow['FLO_ELEMENT_DEST'];
                    $event = BpmnModel::getBpmnObjectBy('Event', \BpmnEventPeer::EVN_UID, $evnUid);

                    switch ($event['EVN_TYPE']) {
                        case 'END':
                            $routeType = 'SEQUENTIAL';
                            $routes[] = array(
                                'ROU_UID' => $flow['FLO_UID'], //Hash::generateUID(),
                                'PRO_UID' => $prjUid,
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

        return $routes;
    }

    private static function activityIsStartTask($actUid)
    {
        /*
         * 1. find bpmn flows related to target activity
         * 2. verify is the flow_element_origin_type is a BpmnEvent and it have a evn_type=start
         */
        $selection = BpmnModel::select('*', 'Flow', array(
            \BpmnFlowPeer::FLO_ELEMENT_DEST => $actUid,
            \BpmnFlowPeer::FLO_ELEMENT_DEST_TYPE => 'bpmnActivity'
        ));

        foreach ($selection as $elementOrigin) {
            if ($elementOrigin['FLO_ELEMENT_ORIGIN_TYPE'] == 'bpmnEvent') {
                $event = BpmnModel::getBpmnObjectBy('Event', \BpmnEventPeer::EVN_UID, $elementOrigin['FLO_ELEMENT_ORIGIN']);

                if ($event['EVN_TYPE'] == 'START') {
                    return true;
                }
            }
        }

        return false;
    }
}

