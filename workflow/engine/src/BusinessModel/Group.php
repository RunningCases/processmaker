<?php
namespace BusinessModel;

class Group
{
    private $arrayMsgExceptionParam = array();

    /**
     * Set exception messages for parameters
     */
    public function setArrayMsgExceptionParam($arrayData)
    {
        try {
            $this->arrayMsgExceptionParam = $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exist the Group in table GROUP
     *
     * @param string $groupUid Unique id of Group
     *
     * return void Throw exception if doesn't exist the Group in table GROUP
     */
    public function throwExceptionIfNoExistsGroup($groupUid)
    {
        $group = new \Groupwf();

        if (!$group->GroupwfExists($groupUid)) {
            $msg = (isset($this->arrayMsgExceptionParam["groupUid"]))? str_replace(array("{0}"), array($this->arrayMsgExceptionParam["groupUid"]), "Invalid value specified for \"{0}\"") . " / " : "";
            $msg = $msg . str_replace(array("{0}", "{1}"), array($groupUid, "GROUPWF"), "The UID \"{0}\" doesn't exist in table {1}");

            throw (new \Exception($msg));
        }
    }

    /**
     * Verify if exists the title of a Group
     *
     * @param string $title           Title
     * @param string $groupUidExclude Unique id of Group to exclude
     *
     * return bool Return true if exists the title of a Group, false otherwise
     */
    public function existsTitle($title, $groupUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\GroupwfPeer::GRP_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\GroupwfPeer::GRP_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "GRP_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            if ($groupUidExclude != "") {
                $criteria->add(\GroupwfPeer::GRP_UID, $groupUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $title, \Criteria::EQUAL);

            $rsCriteria = \GroupwfPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a Group
     *
     * @param string $title           Title
     * @param string $groupUidExclude Unique id of Group to exclude
     *
     * return void Throw exception if exists the title of a Group
     */
    public function throwExceptionIfExistsTitle($title, $groupUidExclude = "")
    {
        if ($this->existsTitle($title, $groupUidExclude)) {
            $msg = (isset($this->arrayMsgExceptionParam["groupTitle"]))? str_replace(array("{0}"), array($this->arrayMsgExceptionParam["groupTitle"]), "Invalid value specified for \"{0}\"") . " / " : "";
            $msg = $msg . \G::LoadTranslation("ID_MSG_GROUP_NAME_EXISTS");

            throw (new \Exception($msg));
        }
    }

    /**
     * Create Group
     *
     * @param array $arrayData Data
     *
     * return array Return data of the new Group created
     */
    public function create($arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["GRP_UID"]);

            //Verify data
            if (!isset($arrayData["GRP_TITLE"])) {
                throw (new \Exception(str_replace(array("{0}"), array(strtolower("GRP_TITLE")), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["GRP_TITLE"] = trim($arrayData["GRP_TITLE"]);

            if ($arrayData["GRP_TITLE"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array(strtolower("GRP_TITLE")), "The \"{0}\" attribute is empty")));
            }

            if (!isset($arrayData["GRP_STATUS"])) {
                throw (new \Exception(str_replace(array("{0}"), array(strtolower("GRP_STATUS")), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["GRP_STATUS"] = trim($arrayData["GRP_STATUS"]);

            if ($arrayData["GRP_STATUS"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array(strtolower("GRP_STATUS")), "The \"{0}\" attribute is empty")));
            }

            $this->throwExceptionIfExistsTitle($arrayData["GRP_TITLE"]);

            //Create
            $group = new \Groupwf();

            $groupUid = $group->create($arrayData);

            //Return
            $arrayData = array_change_key_case($arrayData, CASE_LOWER);

            unset($arrayData["grp_uid"]);

            return array_merge(array("grp_uid" => $groupUid), $arrayData);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update Group
     *
     * @param string $groupUid  Unique id of Group
     * @param array  $arrayData Data
     *
     * return array Return data of the Group updated
     */
    public function update($groupUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNoExistsGroup($groupUid);

            if (isset($arrayData["GRP_TITLE"])) {
                $arrayData["GRP_TITLE"] = trim($arrayData["GRP_TITLE"]);

                if ($arrayData["GRP_TITLE"] == "") {
                    throw (new \Exception(str_replace(array("{0}"), array(strtolower("GRP_TITLE")), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["GRP_STATUS"])) {
                $arrayData["GRP_STATUS"] = trim($arrayData["GRP_STATUS"]);

                if ($arrayData["GRP_STATUS"] == "") {
                    throw (new \Exception(str_replace(array("{0}"), array(strtolower("GRP_STATUS")), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["GRP_TITLE"])) {
                $this->throwExceptionIfExistsTitle($arrayData["GRP_TITLE"], $groupUid);
            }

            //Update
            $group = new \Groupwf();

            $arrayData["GRP_UID"] = $groupUid;

            $result = $group->update($arrayData);

            //Return
            unset($arrayData["GRP_UID"]);

            return array_change_key_case($arrayData, CASE_LOWER);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return void
     */
    public function delete($groupUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNoExistsGroup($groupUid);

            //Delete
            $group = new \Groupwf();

            $group->remove($groupUid);

            //Delete assignments of tasks
            $criteria = new \Criteria("workflow");

            $criteria->add(\TaskUserPeer::USR_UID, $groupUid);

            \TaskUserPeer::doDelete($criteria);

            //Delete permissions
            $criteria = new \Criteria("workflow");

            $criteria->add(\ObjectPermissionPeer::USR_UID, $groupUid);

            \ObjectPermissionPeer::doDelete($criteria);

            //Delete assignments of supervisors
            $criteria = new \Criteria("workflow");

            $criteria->add(\ProcessUserPeer::USR_UID, $groupUid);
            $criteria->add(\ProcessUserPeer::PU_TYPE, "GROUP_SUPERVISOR");

            \ProcessUserPeer::doDelete($criteria);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for Group
     *
     * return object
     */
    public function getGroupCriteria()
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\GroupwfPeer::GRP_UID);
            $criteria->addSelectColumn(\GroupwfPeer::GRP_STATUS);
            $criteria->addSelectColumn(\GroupwfPeer::GRP_LDAP_DN);
            $criteria->addSelectColumn(\GroupwfPeer::GRP_UX);
            $criteria->addAsColumn("GRP_TITLE", \ContentPeer::CON_VALUE);
            $criteria->addJoin(\GroupwfPeer::GRP_UID, \ContentPeer::CON_ID, \Criteria::LEFT_JOIN);
            $criteria->add(\ContentPeer::CON_CATEGORY, "GRP_TITLE", \Criteria::EQUAL);
            $criteria->add(\ContentPeer::CON_LANG, SYS_LANG, \Criteria::EQUAL);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of total Users by Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return array Return an array with data of total Users by Group
     */
    public function getTotalUsersByGroup($groupUid = "")
    {
        try {
            $arrayData = array();

            //Verif data
            if ($groupUid != "") {
                $this->throwExceptionIfNoExistsGroup($groupUid);
            }

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\GroupUserPeer::GRP_UID);
            $criteria->addSelectColumn("COUNT(" . \GroupUserPeer::GRP_UID . ") AS NUM_REC");
            $criteria->addJoin(\GroupUserPeer::USR_UID, \UsersPeer::USR_UID, \Criteria::INNER_JOIN);

            if ($groupUid != "") {
                $criteria->add(\GroupUserPeer::GRP_UID, $groupUid, \Criteria::EQUAL);
            }

            $criteria->add(\UsersPeer::USR_STATUS, "CLOSED", \Criteria::NOT_EQUAL);
            $criteria->addGroupByColumn(\GroupUserPeer::GRP_UID);

            $rsCriteria = \GroupUserPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[$row["GRP_UID"]] = $row["NUM_REC"];
            }

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of total Tasks by Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return array Return an array with data of total Tasks by Group
     */
    public function getTotalTasksByGroup($groupUid = "")
    {
        try {
            $arrayData = array();

            //Verif data
            if ($groupUid != "") {
                $this->throwExceptionIfNoExistsGroup($groupUid);
            }

            //Get data
            $criteria = new \Criteria("workflow");

            $criteria->addAsColumn("GRP_UID", \TaskUserPeer::USR_UID);
            $criteria->addSelectColumn("COUNT(" . \TaskUserPeer::USR_UID . ") AS NUM_REC");

            if ($groupUid != "") {
                $criteria->add(\TaskUserPeer::USR_UID, $groupUid, \Criteria::EQUAL);
            }

            $criteria->add(\TaskUserPeer::TU_TYPE, 1, \Criteria::EQUAL);
            $criteria->add(\TaskUserPeer::TU_RELATION, 2, \Criteria::EQUAL);
            $criteria->addGroupByColumn(\TaskUserPeer::USR_UID);

            $rsCriteria = \TaskUserPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC );

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayData[$row["GRP_UID"]] = $row["NUM_REC"];
            }

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Group from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data Group
     */
    public function getGroupDataFromRecord($record)
    {
        try {
            return array(
                "grp_uid"    => $record["GRP_UID"],
                "grp_title"  => $record["GRP_TITLE"],
                "grp_status" => $record["GRP_STATUS"],
                "grp_users"  => (int)($record["GRP_USERS"]),
                "grp_tasks"  => (int)($record["GRP_TASKS"])
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Groups
     *
     * @param array  $arrayFilterData Data of the filters
     * @param string $sortField       Field name to sort
     * @param string $sortDir         Direction of sorting (ASC, DESC)
     * @param int    $start           Start
     * @param int    $limit           Limit
     *
     * return array Return an array with all Groups
     */
    public function getGroups($arrayFilterData = null, $sortField = null, $sortDir = null, $start = null, $limit = null)
    {
        try {
            $arrayGroup = array();

            //Verify data
            $ereg = "/^(?:\+|\-)?(?:0|[1-9]\d*)$/";

            if (!is_null($start) && ($start . "" == "" || !preg_match($ereg, $start . "") || (int)($start) < 0)) {
                throw (new \Exception(str_replace(array("{0}"), array("start"), "Invalid value specified for \"{0}\". Expecting positive integer value")));
            }

            if (!is_null($limit) && ($limit . "" == "" || !preg_match($ereg, $limit . "") || (int)($limit) < 0)) {
                throw (new \Exception(str_replace(array("{0}"), array("limit"), "Invalid value specified for \"{0}\". Expecting positive integer value")));
            }

            //Get data
            if (!is_null($limit) && $limit . "" == "0") {
                return $arrayGroup;
            }

            $arrayTotalUsersByGroup = $this->getTotalUsersByGroup();
            $arrayTotalTasksByGroup = $this->getTotalTasksByGroup();

            //SQL
            $criteria = $this->getGroupCriteria();

            if (!is_null($arrayFilterData) && is_array($arrayFilterData) && isset($arrayFilterData["filter"]) && trim($arrayFilterData["filter"]) != "") {
                $criteria->add(\ContentPeer::CON_VALUE, "%" . trim($arrayFilterData["filter"]) . "%", \Criteria::LIKE);
            }

            //Number records total
            $criteriaCount = clone $criteria;

            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn("COUNT(" . \GroupwfPeer::GRP_UID . ") AS NUM_REC");

            $rsCriteriaCount = \GroupwfPeer::doSelectRS($criteriaCount);
            $rsCriteriaCount->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteriaCount->next();
            $row = $rsCriteriaCount->getRow();

            $numRecTotal = $row["NUM_REC"];

            //SQL
            if (!is_null($sortField) && trim($sortField) != "") {
                $sortField = strtoupper($sortField);

                switch ($sortField) {
                    case "GRP_UID":
                    case "GRP_STATUS":
                    case "GRP_LDAP_DN":
                    case "GRP_UX":
                        $sortField = \GroupwfPeer::TABLE_NAME . "." . $sortField;
                        break;
                    default:
                        $sortField = "GRP_TITLE";
                        break;
                }
            } else {
                $sortField = "GRP_TITLE";
            }

            if (!is_null($sortDir) && trim($sortDir) != "" && strtoupper($sortDir) == "DESC") {
                $criteria->addDescendingOrderByColumn($sortField);
            } else {
                $criteria->addAscendingOrderByColumn($sortField);
            }

            if (!is_null($start)) {
                $criteria->setOffset((int)($start));
            }

            if (!is_null($limit)) {
                $criteria->setLimit((int)($limit));
            }

            $rsCriteria = \GroupwfPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $row["GRP_USERS"] = (isset($arrayTotalUsersByGroup[$row["GRP_UID"]]))? $arrayTotalUsersByGroup[$row["GRP_UID"]] : 0;
                $row["GRP_TASKS"] = (isset($arrayTotalTasksByGroup[$row["GRP_UID"]]))? $arrayTotalTasksByGroup[$row["GRP_UID"]] : 0;

                $arrayGroup[] = $this->getGroupDataFromRecord($row);
            }

            //Return
            return $arrayGroup;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a Group
     *
     * @param string $groupUid Unique id of Group
     *
     * return array Return an array with data of a Group
     */
    public function getGroup($groupUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNoExistsGroup($groupUid);

            //Get data
            $arrayTotalUsersByGroup = $this->getTotalUsersByGroup($groupUid);
            $arrayTotalTasksByGroup = $this->getTotalTasksByGroup($groupUid);

            //SQL
            $criteria = $this->getGroupCriteria();

            $criteria->add(\GroupwfPeer::GRP_UID, $groupUid, \Criteria::EQUAL);

            $rsCriteria = \GroupwfPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            $row["GRP_USERS"] = (isset($arrayTotalUsersByGroup[$groupUid]))? $arrayTotalUsersByGroup[$groupUid] : 0;
            $row["GRP_TASKS"] = (isset($arrayTotalTasksByGroup[$groupUid]))? $arrayTotalTasksByGroup[$groupUid] : 0;

            //Return
            return $this->getGroupDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

