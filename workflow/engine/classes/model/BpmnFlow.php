<?php

require_once 'classes/model/om/BaseBpmnFlow.php';


/**
 * Skeleton subclass for representing a row from the 'BPMN_FLOW' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    classes.model
 */
class BpmnFlow extends BaseBpmnFlow
{
    /**
     * @param $field string coming from \BpmnFlowPeer::<FIELD_NAME>
     * @param $value string
     * @return \BpmnFlow|null
     */
    public static function findOneBy($field, $value)
    {
        $rows = self::findAllBy($field, $value);

        return empty($rows) ? null : $rows[0];
    }

    public static function findAllBy($field, $value)
    {
        $c = new Criteria('workflow');
        $c->add($field, $value, Criteria::EQUAL);

        return BpmnFlowPeer::doSelect($c);
    }

    public static function getAll($prjUid = null, $start = null, $limit = null, $filter = '', $changeCaseTo = CASE_UPPER)
    {
        //TODO implement $start, $limit and $filter
        $c = new Criteria('workflow');

        if (! is_null($prjUid)) {
            $c->add(BpmnFlowPeer::PRJ_UID, $prjUid, Criteria::EQUAL);
        }

        $rs = BpmnFlowPeer::doSelectRS($c);
        $rs->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        $flows = array();

        while ($rs->next()) {
            $flow = $rs->getRow();
            $flow["FLO_STATE"] = @json_decode($flow["FLO_STATE"], true);
            //$flow["FLO_IS_INMEDIATE"] = $flow["FLO_IS_INMEDIATE"] == 1 ? true : false;
            $flow = $changeCaseTo !== CASE_UPPER ? array_change_key_case($flow, CASE_LOWER) : $flow;

            $flows[] = $flow;
        }

        return $flows;
    }

    public static function exists($floUid)
    {
        $c = new Criteria('workflow');
        $c->add(BpmnFlowPeer::FLO_UID, $floUid);

        return BpmnFlowPeer::doCount($c) > 0 ? true : false;
    }

    public function fromArray($data, $type = BasePeer::TYPE_FIELDNAME)
    {
        parent::fromArray($data, $type);
    }

    public function toArray($type = BasePeer::TYPE_FIELDNAME)
    {
        $flow = parent::toArray($type);
        $flow["FLO_STATE"] = @json_decode($flow["FLO_STATE"], true);

        return $flow;
    }

} // BpmnFlow
