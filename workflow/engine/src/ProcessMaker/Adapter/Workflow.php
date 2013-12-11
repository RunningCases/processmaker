<?php
namespace ProcessMaker\Adapter;

use \Process;
use \ProcessMaker\Adapter\Bpmn\Model as BpmnModel;
use \ProcessMaker\Util\Hash;

/**
 * Class Workflow
 *
 * @package ProcessMaker\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Workflow
{
    public static $bpmnTypesEquiv = array(
        'event' => array(
            'START' => 'START' // to define task start
        ),
        'flow' => array(
            'SEQUENCE' => 'SEQUENTIAL' // to define task start
        )
    );

    public function loadFromBpmnProject($prjUid)
    {
        $bpmnTypesEquiv = array(
            'event' => array(
                'start' => 'start' // to define task start
            ),
            'flow' => array(
                'SEQUENCE' => 'SEQUENTIAL' // to define task start
            )
        );

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

        /*foreach ($diagram['flows'] as $flow) {
            $process['routes'][] = array(
                'ROU_UID' => '',
                'TAS_UID' => self::getTask($activity['act_uid']),
                'ROU_NEXT_TASK' => self::getNextTask($activity['act_uid']),
                'ROU_TYPE' => ''
            );
        }*/

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
            $type = $flow['FLO_TYPE'];

            switch ($type) {
                case 'SEQUENCE':
                    $type = 'SEQUENTIAL';
                    break;
            }

            //$elFlow = BpmnModel::getBpmnObjectBy('Flow', \BpmnFlowPeer::FLO_ELEMENT_DEST, $elementUid);

            switch ($flow['FLO_ELEMENT_DEST_TYPE']) {
                case 'bpmnActivity':
                    // the most easy case, when the flow is connecting a activity with another activity
                    $routes[] = array(
                        'ROU_UID' => Hash::generateUID(),
                        'PRO_UID' => $prjUid,
                        'TAS_UID' => $fromUid,
                        'ROU_NEXT_TASK' => $flow['FLO_ELEMENT_DEST'],
                        'ROU_TYPE' => $type,
                        '_action' => 'CREATE'
                    );
                    break;

                case 'bpmnGateway':
                    // if it is a gateway it can fork one or more routes
                    $gatUid = $flow['FLO_ELEMENT_DEST'];
                    $gatFlows = BpmnModel::getBpmnCollectionBy('Flow', \BpmnFlowPeer::FLO_ELEMENT_ORIGIN, $gatUid);

                    foreach ($gatFlows as $gatFlow) {
                        switch ($gatFlow['FLO_ELEMENT_DEST_TYPE']) {
                            case 'bpmnActivity':
                                // getting gateway properties
                                $gateway = BpmnModel::getBpmnObjectBy('Gateway', \BpmnFlowPeer::GAT_UID, $gatUid);

                                switch ($gateway['GAT_TYPE']) {
                                    //TODO we need to know gateways types to match with routes types of processmaker
                                    case '':
                                        $routeType = '';
                                        break;
                                }

                                $routes[] = array(
                                    'ROU_UID' => Hash::generateUID(),
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
                                throw new \LogicException("For ProcessMaker is only allowed flows between \"gateway -> activity\"");
                        }
                    }
                    break;
            }
        }


        return $routes;
    }
    private static function getRoutesFromBpmnFlows2($flows)
    {
        // get bpmnActivities on flo_element_origin
        $flowsOriginActivities = array();

        foreach ($flows as $i => $flow) {
            if ($flow['flo_element_origin_type'] == 'bpmnActivity') {
                $flowsOriginActivities[] = $flow;
                unset($flows[$i]);
            }
        }


    }


    private static function getTask($actUid)
    {
    }

    private static function getNextTask($actUid)
    {
    }

    private static function activityIsStartTask($actUid)
    {
        /*
         * 1. find bpmn flows related to target activity
         * 2. verify is the flow_element_origin_type is a BpmnEvent and it have a evn_type = start
         */
        $selection = BpmnModel::select('*', 'Flow', array(
            \BpmnFlowPeer::FLO_ELEMENT_DEST => $actUid,
            \BpmnFlowPeer::FLO_ELEMENT_DEST_TYPE => 'bpmnActivity'
        ));

        foreach ($selection as $elementOrigin) {
            if ($elementOrigin['FLO_ELEMENT_ORIGIN_TYPE'] == 'bpmnEvent') {
                $event = BpmnModel::getBpmnObjectBy('Event', \BpmnEventPeer::EVN_UID, $elementOrigin['FLO_ELEMENT_ORIGIN']);

                if ($event['EVN_TYPE'] == self::$bpmnTypesEquiv['event']['START']) {
                    return true;
                }
            }
        }

        return false;
    }
}
