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
    private $bound;

    public function __construct($generateUid = true)
    {
        $this->bound = new BpmnBound();
        $this->bound->setBouElementType(lcfirst(str_replace(__NAMESPACE__, '', __CLASS__)));
        $this->bound->setBouElement('pm_canvas');
        $this->bound->setBouContainer('bpmnDiagram');
    }

    /* DEPRECATED, IT WILL BE REMOVED SOON
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
    }*/


    // OVERRIDES

	public function fromArray($data)
    {
        parent::fromArray($data, BasePeer::TYPE_FIELDNAME);

        // try resolve the related bound
        if (array_key_exists('BOU_UID', $data)) {
            //$bound = BpmnBound::findByElement('Activity', $this->getActUid());
            $bound = BpmnBoundPeer::retrieveByPK($data['BOU_UID']);

            if (is_object($bound)) {
                $this->bound = $bound;
            }
        }

        $this->bound->fromArray($data, BasePeer::TYPE_FIELDNAME);
    }

    public function save($con = null)
    {
        parent::save($con);

        if (is_object($this->bound) && get_class($this->bound) == 'BpmnBound') {
            $this->bound->save($con);
        }
    }

    public function toArray($keyType = BasePeer::TYPE_PHPNAME)
    {
        $data = parent::toArray($keyType);

        if (is_object($this->bound) && get_class($this->bound) == 'BpmnBound') {
            $data = array_merge($data, $this->bound->toArray($keyType));
        }

        return $data;
    }

} // BpmnActivity
