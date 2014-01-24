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

    public function getBound()
    {
        return $this->bound;
    }


    // OVERRIDES

    public function setActUid($actUid)
    {
        parent::setActUid($actUid);
        $this->bound->setElementUid($this->getActUid());
    }

    public function setPrjUid($prjUid)
    {
        parent::setPrjUid($prjUid);
        $this->bound->setPrjUid($this->getPrjUid());
    }

    public function setProUid($proUid)
    {
        parent::setProUid($proUid);

        $process = BpmnProcessPeer::retrieveByPK($this->getProUid());
        $this->bound->setDiaUid($process->getDiaUid());
    }

    public function save($con = null)
    {
        parent::save($con);

        if (is_object($this->bound) && get_class($this->bound) == 'BpmnBound') {
            $this->bound->save($con);
        }
    }

    public function delete($con = null)
    {
        // first, delete the related bound object
        if (is_object($this->bound) && get_class($this->bound) == 'BpmnBound') {
            $this->bound->delete($con);
        }

        parent::delete($con);
    }

	public function fromArray($data)
    {
        parent::fromArray($data, BasePeer::TYPE_FIELDNAME);

        // try resolve the related bound
        $bound = BpmnBound::findByElement('Activity', $this->getActUid());

        //if (array_key_exists('BOU_UID', $data)) {
        if (is_object($bound)) {
            //$bound = BpmnBoundPeer::retrieveByPK($data['BOU_UID']);
            $this->bound = $bound;
        }

        $this->bound->fromArray($data, BasePeer::TYPE_FIELDNAME);
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
