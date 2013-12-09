<?php
namespace ProcessMaker\Adapter;

use \Process;
use \ProcessMaker\Adapter\Bpmn\Model as BpmnModel;

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

    public function loadFromBpmnProject($bpmnProject)
    {
        $proUid = $bpmnProject['prj_uid'];
        $bpmnTypesEquiv = array(
            'event' => array(
                'start' => 'start' // to define task start
            ),
            'flow' => array(
                'SEQUENCE' => 'SEQUENTIAL' // to define task start
            )
        );


        $process = array();
        $process['PRO_UID'] = $proUid;
        $process['PRO_TITLE'] = $bpmnProject['prj_name'];
        $process['PRO_DESCRIPTION'] = '';
        $process['PRO_CATEGORY'] = '';
        $process['PRO_UID'] = $proUid;
        $process['PRO_UID'] = $proUid;
        $process['tasks'] = array();

        $diagram = $bpmnProject['diagrams'][0];

        foreach ($diagram['activities'] as $activity) {
            $process['tasks'][] = array(
                'TAS_UID' => $activity['act_uid'],
                'TAS_TITLE' => $activity['act_name'],
                'TAS_DESCRIPTION' => $activity['act_name'],
                'TAS_POSX' => $activity['bou_x'],
                'TAS_POSY' => $activity['bou_y'],
                'TAS_START' => (self::activityIsStartTask($activity['act_uid']) ? 'TRUE' : 'FALSE'),
                '_action' => 'CREATE'
            );
        }

        $process['routes'] = array();

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
