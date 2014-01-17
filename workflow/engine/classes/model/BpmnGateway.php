<?php

require_once 'classes/model/om/BaseBpmnGateway.php';


/**
 * Skeleton subclass for representing a row from the 'BPMN_GATEWAY' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class BpmnGateway extends BaseBpmnGateway
{
    private $bound;

    public function __construct($generateUid = true)
    {
        $this->bound = new BpmnBound();
        $this->bound->setBouElementType(lcfirst(str_replace(__NAMESPACE__, '', __CLASS__)));
        $this->bound->setBouElement('pm_canvas');
        $this->bound->setBouContainer('bpmnDiagram');
    }

    // OVERRIDES

    public function fromArray($data)
    {
        parent::fromArray($data, BasePeer::TYPE_FIELDNAME);

        // try resolve the related bound
        if (array_key_exists('BOU_UID', $data)) {
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

} // BpmnGateway
