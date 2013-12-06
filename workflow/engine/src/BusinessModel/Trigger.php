<?php
namespace BusinessModel;

class Trigger
{
    /**
     * Get criteria for Trigger
     *
     * return object
     */
    public function getTriggerCriteria()
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\TriggersPeer::TRI_UID);
            $criteria->addAsColumn("TRI_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("TRI_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\TriggersPeer::TRI_TYPE);
            $criteria->addSelectColumn(\TriggersPeer::TRI_WEBBOT);
            $criteria->addSelectColumn(\TriggersPeer::TRI_PARAM);

            $criteria->addAlias("CT", "CONTENT");
            $criteria->addAlias("CD", "CONTENT");

            $arrayCondition = array();
            $arrayCondition[] = array(\TriggersPeer::TRI_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "TRI_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\TriggersPeer::TRI_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "TRI_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an Trigger
     *
     * @param string $triggerUid Unique id of the Trigger
     *
     * return array
     */
    public function getTrigger($triggerUid)
    {
        try {
            //Criteria
            $criteria = $this->getTriggerCriteria();

            $criteria->add(\TriggersPeer::TRI_UID, $triggerUid, \Criteria::EQUAL);

            $rsCriteria = \TriggersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $arrayTrigger = array(
                "tri_uid"   => $row["TRI_UID"],
                "tri_title" => $row["TRI_TITLE"],
                "tri_description" => $row["TRI_DESCRIPTION"],
                "tri_type"   => $row["TRI_TYPE"],
                "tri_webbot" => $row["TRI_WEBBOT"],
                "tri_param"  => $row["TRI_PARAM"]
            );

            return $arrayTrigger;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

