<?php

require_once 'classes/model/om/BaseBpmnProcess.php';


/**
 * Skeleton subclass for representing a row from the 'BPMN_PROCESS' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class BpmnProcess extends BaseBpmnProcess
{
    public static function findAllByProUid($prjUid)
    {
        $c = new Criteria("workflow");
        $c->add(BpmnProcessPeer::PRJ_UID, $prjUid);

        return BpmnProcessPeer::doSelect($c);
    }


    // Overrides

    public function toArray($type = BasePeer::TYPE_FIELDNAME)
    {
        return parent::toArray($type);
    }
} // BpmnProcess
