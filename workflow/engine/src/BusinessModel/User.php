<?php
namespace BusinessModel;

class User
{
    /**
     * Set exception messages for parameters
     *
     * @param array $arrayData Data with the params
     *
     * return void
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
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
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
     * return bool Return true if exists the title of a Group, false otherwise
     */
    public function existsTitle($groupTitle, $groupUidExclude = "")
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

            $criteria->add("CT.CON_VALUE", $groupTitle, \Criteria::EQUAL);

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
     * Verify if status has invalid value
     *
     * @param string $groupStatus Status
     *
     * return void Throw exception if status has invalid value
     */
    public function throwExceptionIfHaveInvalidValueInStatus($groupStatus)
    {
        if (!in_array($groupStatus, array("ACTIVE", "INACTIVE"))) {
            $field = $this->getFieldNameByFormatFieldName("GRP_STATUS");

            throw (new \Exception(str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"")));
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
    public function throwExceptionIfExistsTitle($groupTitle, $groupUidExclude = "")
    {
        if ($this->existsTitle($groupTitle, $groupUidExclude)) {
            $field = $this->getFieldNameByFormatFieldName("GRP_TITLE");

            $msg = str_replace(array("{0}"), array($field), "Invalid value specified for \"{0}\"") . " / ";
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
                throw (new \Exception(str_replace(array("{0}"), array($this->getFieldNameByFormatFieldName("GRP_TITLE")), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["GRP_TITLE"] = trim($arrayData["GRP_TITLE"]);

            if ($arrayData["GRP_TITLE"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array($this->getFieldNameByFormatFieldName("GRP_TITLE")), "The \"{0}\" attribute is empty")));
            }

            if (!isset($arrayData["GRP_STATUS"])) {
                throw (new \Exception(str_replace(array("{0}"), array($this->getFieldNameByFormatFieldName("GRP_STATUS")), "The \"{0}\" attribute is not defined")));
            }

            $arrayData["GRP_STATUS"] = trim($arrayData["GRP_STATUS"]);

            if ($arrayData["GRP_STATUS"] == "") {
                throw (new \Exception(str_replace(array("{0}"), array($this->getFieldNameByFormatFieldName("GRP_STATUS")), "The \"{0}\" attribute is empty")));
            }

            $this->throwExceptionIfHaveInvalidValueInStatus($arrayData["GRP_STATUS"]);

            $this->throwExceptionIfExistsTitle($arrayData["GRP_TITLE"]);

            //Create
            $group = new \Groupwf();

            $groupUid = $group->create($arrayData);

            //Return
            $arrayData = array_merge(array("GRP_UID" => $groupUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
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
                    throw (new \Exception(str_replace(array("{0}"), array($this->getFieldNameByFormatFieldName("GRP_TITLE")), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["GRP_STATUS"])) {
                $arrayData["GRP_STATUS"] = trim($arrayData["GRP_STATUS"]);

                if ($arrayData["GRP_STATUS"] == "") {
                    throw (new \Exception(str_replace(array("{0}"), array($this->getFieldNameByFormatFieldName("GRP_STATUS")), "The \"{0}\" attribute is empty")));
                }
            }

            if (isset($arrayData["GRP_STATUS"])) {
                $this->throwExceptionIfHaveInvalidValueInStatus($arrayData["GRP_STATUS"]);
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

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
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
                $this->getFieldNameByFormatFieldName("GRP_UID")    => $record["GRP_UID"],
                $this->getFieldNameByFormatFieldName("GRP_TITLE")  => $record["GRP_TITLE"],
                $this->getFieldNameByFormatFieldName("GRP_STATUS") => $record["GRP_STATUS"],
                $this->getFieldNameByFormatFieldName("GRP_USERS")  => (int)($record["GRP_USERS"]),
                $this->getFieldNameByFormatFieldName("GRP_TASKS")  => (int)($record["GRP_TASKS"])
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Users
     *
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     *
     * return array Return an array with all Users
     */
    public function getUsers($filter, $start, $limit)
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oCriteria = new \Criteria();
            if ($filter != '') {
                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
            }
            if ($start) {
                if ($start < 0) {
                    throw (new \Exception( 'invalid value specified for `start`.'));
                } else {
                    $oCriteria->setOffset( $start );
                }
            }
            if (isset($limit)) {
                if ($limit < 0) {
                    throw (new \Exception( 'invalid value specified for `limit`.'));
                } else {
                    if ($limit == 0) {
                        return $aUsers;
                    } else {
                        $oCriteria->setLimit( $limit );
                    }
                }
            }
            $oDataset = \UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $aRow1 = $oDataset->getRow();
                $aRow1 = array_change_key_case($aRow1, CASE_LOWER);
                $aUserInfo[] = $aRow1;
            }
            //Return
            return $aUserInfo;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of a User
     *
     * @param string $userUid Unique id of Group
     *
     * return array Return an array with data of a User
     */
    public function getUser($userUid)
    {
        try {
            $oUser = \UsersPeer::retrieveByPK($userUid);
            if (is_null($oUser)) {
                throw (new \Exception( 'This id for `usr_uid`: '. $userUid .' do not correspond to a registered user'));
            }
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "classes" . PATH_SEP . "model" . PATH_SEP . "Users.php");
            $oCriteria = new \Criteria();
            if ($filter != '') {
                $oCriteria->add( $oCriteria->getNewCriterion( \UsersPeer::USR_USERNAME, "%$filter%", \Criteria::LIKE )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_FIRSTNAME, "%$filter%", \Criteria::LIKE ) )->addOr( $oCriteria->getNewCriterion( \UsersPeer::USR_LASTNAME, "%$filter%", \Criteria::LIKE ) ) );
            }
            $oCriteria->add(\UsersPeer::USR_UID, $userUid);
            $oDataset = \UsersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $aRow1 = $oDataset->getRow();
                $aRow1 = array_change_key_case($aRow1, CASE_LOWER);
                $aUserInfo = $aRow1;
            }
            //Return
            return $aUserInfo;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

