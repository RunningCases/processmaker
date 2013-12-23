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
        $c = new Criteria('workflow');

        $c->add($field, $value, CRITERIA::EQUAL );

        $rs = ContentPeer::doSelectRS($c);
        //$rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $rs->next();

        return $rs->getRow();
    }
} // BpmnBound
