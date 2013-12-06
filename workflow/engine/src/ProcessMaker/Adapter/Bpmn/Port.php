<?php
namespace ProcessMaker\Adapter\Bpmn;

use \Process;

/**
 * Class Port
 *
 * @package ProcessMaker\Adapter\Bpmn
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Port
{
    public function convertBpmnProjectToPmWorkflow($bpmnProject)
    {
        $proUid = $bpmnProject['prj_uid'];

        $process = array();
        $process['PRO_UID'] = $proUid;
        $process['PRO_TITLE'] = $bpmnProject['prj_name'];
        $process['PRO_DESCRIPTION'] = '';
        $process['PRO_CATEGORY'] = '';
        $process['PRO_UID'] = $proUid;
        $process['PRO_UID'] = $proUid;
        $process['tasks'] = array();

        $diagram = $bpmnProject['prj_name']['diagrams'][0];

        foreach ($diagram['activities'] as $activity) {
            $process['tasks'][] = array(
                'TAS_UID' => $activity['act_uid'],
                'TAS_TITLE' => $activity['act_name'],
                'TAS_DESCRIPTION' => $activity['act_name'],
                'TAS_POSX' => $activity['bou_x'],
                'TAS_POSY' => $activity['bou_y'],
                'TAS_START' => $activity['act_uid']
            );
        }

    }
}
