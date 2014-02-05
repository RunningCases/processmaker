<?php

require_once 'classes/model/om/BaseBpmnBound.php';


/**
 * Skeleton subclass for representing a row from the 'BPMN_BOUND' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class BpmnBound extends BaseBpmnBound
{
    public static function findOneBy($field, $value)
    {
        $rows = self::findAllBy($field, $value);

        return empty($rows) ? null : $rows[0];
    }

    public static function findAllBy($field, $value)
    {
        $c = new Criteria('workflow');
        $c->add($field, $value, Criteria::EQUAL);

        return BpmnBoundPeer::doSelect($c);
    }
	
    public static function findByElement($type, $uid)
    {
		$bouElementType = 'bpmn' . ucfirst(strtolower($type));

        $c = new Criteria('workflow');
        $c->add(BpmnBoundPeer::ELEMENT_UID, $uid, CRITERIA::EQUAL);
		$c->add(BpmnBoundPeer::BOU_ELEMENT_TYPE, $bouElementType, CRITERIA::EQUAL);

        return BpmnBoundPeer::doSelectOne($c);
    }
} // BpmnBound
