<?php

require_once 'classes/model/om/BaseBpmnActivity.php';


/**
 * Skeleton subclass for representing a row from the 'BPMN_ACTIVITY' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class BpmnActivity extends BaseBpmnActivity
{
    public function create($data, $generateUid = true)
    {
        // validate foreign keys, they must be present into data array

        if (! array_key_exists('PRJ_UID', $data)) {
            throw new PropelException("Error, required param 'PRJ_UID' is missing!");
        }

        if (! array_key_exists('PRO_UID', $data)) {
            throw new PropelException("Error, required param 'PRO_UID' is missing!");
        }

        $this->fromArray($data, BasePeer::TYPE_FIELDNAME);

        if ($generateUid) {
            $this->setActUid(\ProcessMaker\Util\Hash::generateUID());
        }

        $this->save();
        $process = BpmnProcessPeer::retrieveByPK($data['PRO_UID']);

        // create related bound
        $bound = new Bound();
        $bound->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $bound->setBouUid(\ProcessMaker\Util\Hash::generateUID());
        $bound->setPrjUid($this->getPrjUid());
        $bound->setDiaUid($process->getDiaUid());
        $bound->setElementUid($this->getActUid());
        $bound->setBouElementType('bpmnActivity');
        $bound->setBouElement('pm_canvas');
        $bound->setBouContainer('bpmnDiagram');
        $bound->save();
    }

    public function update($data)
    {
        $this->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->save();

        // update related bound
        $bound = BpmnBound::findOneBy(BpmnBoundPeer::ELEMENT_UID, $this->getActUid());
        $bound->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $bound->save();
    }
} // BpmnActivity
