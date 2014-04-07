<?php
namespace BusinessModel;

use \G;
use \UsersPeer;
use \CasesPeer;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Cases
{
    /**
     * Get list for Cases
     *
     * @access public
     * @param array $dataList, Data for list
     * @return array
     *
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     */
    public function getList($dataList = array())
    {
        Validator::isArray($dataList, '$dataList');
        if (!isset($dataList["userId"])) {
            throw (new \Exception("The user with userId: '' does not exist."));
        } else {
            Validator::usrUid($dataList["userId"], "userId");
        }

        G::LoadClass("applications");
        $solrEnabled = false;
        $userUid = $dataList["userId"];
        $callback = isset( $dataList["callback"] ) ? $dataList["callback"] : "stcCallback1001";
        $dir = isset( $dataList["dir"] ) ? $dataList["dir"] : "DESC";
        $sort = isset( $dataList["sort"] ) ? $dataList["sort"] : "APP_CACHE_VIEW.APP_NUMBER";
        $start = isset( $dataList["start"] ) ? $dataList["start"] : "0";
        $limit = isset( $dataList["limit"] ) ? $dataList["limit"] : "25";
        $filter = isset( $dataList["filter"] ) ? $dataList["filter"] : "";
        $process = isset( $dataList["process"] ) ? $dataList["process"] : "";
        $category = isset( $dataList["category"] ) ? $dataList["category"] : "";
        $status = isset( $dataList["status"] ) ? strtoupper( $dataList["status"] ) : "";
        $user = isset( $dataList["user"] ) ? $dataList["user"] : "";
        $search = isset( $dataList["search"] ) ? $dataList["search"] : "";
        $action = isset( $dataList["action"] ) ? $dataList["action"] : "todo";
        $type = "extjs";
        $dateFrom = isset( $dataList["dateFrom"] ) ? substr( $dataList["dateFrom"], 0, 10 ) : "";
        $dateTo = isset( $dataList["dateTo"] ) ? substr( $dataList["dateTo"], 0, 10 ) : "";
        $first = isset( $dataList["first"] ) ? true :false;

        $valuesCorrect = array('todo', 'draft', 'paused', 'sent', 'selfservice', 'unassigned', 'search');
        if (!in_array($action, $valuesCorrect)) {
            throw (new \Exception('The value for $action is incorrect.'));
        }

        if ($action == 'search' || $action == 'to_reassign') {
            $userUid = ($user == "CURRENT_USER") ? $userUid : $user;
            if ($first) {
                $result = array();
                $result['totalCount'] = 0;
                $result['data'] = array();
                return $result;
            }
        }

        if ((
                $action == "todo" || $action == "draft" || $action == "paused" || $action == "sent" ||
                $action == "selfservice" || $action == "unassigned" || $action == "search"
            ) &&
            (($solrConf = \System::solrEnv()) !== false)
        ) {
            G::LoadClass("AppSolr");

            $ApplicationSolrIndex = new \AppSolr(
                $solrConf["solr_enabled"],
                $solrConf["solr_host"],
                $solrConf["solr_instance"]
            );

            if ($ApplicationSolrIndex->isSolrEnabled() && $solrConf['solr_enabled'] == true) {
                //Check if there are missing records to reindex and reindex them
                $ApplicationSolrIndex->synchronizePendingApplications();
                $solrEnabled = true;
            }
        }


        if ($solrEnabled) {
            $result = $ApplicationSolrIndex->getAppGridData(
                $userUid,
                $start,
                $limit,
                $action,
                $filter,
                $search,
                $process,
                $status,
                $type,
                $dateFrom,
                $dateTo,
                $callback,
                $dir,
                $sort,
                $category
            );
        } else {
            G::LoadClass("applications");
            $apps = new \Applications();
            $result = $apps->getAll(
                $userUid,
                $start,
                $limit,
                $action,
                $filter,
                $search,
                $process,
                $status,
                $type,
                $dateFrom,
                $dateTo,
                $callback,
                $dir,
                (strpos($sort, ".") !== false)? $sort : "APP_CACHE_VIEW." . $sort,
                $category
            );
        }
        if (!empty($result['data'])) {
            foreach ($result['data'] as &$value) {
                $value = array_change_key_case($value, CASE_LOWER);
            }
        }
        return $result;
    }

    /**
     * Get data of a Case
     *
     * @param string $caseUid Unique id of Case
     * @param string $userUid Unique id of User
     *
     * return array Return an array with data of Case Info
     */
    public function getCaseInfo($caseUid, $userUid)
    {
        try {
            $solrEnabled = 0;
            if (($solrEnv = \System::solrEnv()) !== false) {
                \G::LoadClass("AppSolr");
                $appSolr = new \AppSolr(
                    $solrEnv["solr_enabled"],
                    $solrEnv["solr_host"],
                    $solrEnv["solr_instance"]
                );
                if ($appSolr->isSolrEnabled() && $solrEnv["solr_enabled"] == true) {
                    //Check if there are missing records to reindex and reindex them
                    $appSolr->synchronizePendingApplications();
                    $solrEnabled = 1;
                }
            }
            if ($solrEnabled == 1) {
                try {
                    \G::LoadClass("searchIndex");
                    $arrayData = array();
                    $delegationIndexes = array();
                    $columsToInclude = array("APP_UID");
                    $solrSearchText = null;
                    //Todo
                    $solrSearchText = $solrSearchText . (($solrSearchText != null)? " OR " : null) . "(APP_STATUS:TO_DO AND APP_ASSIGNED_USERS:" . $userUid . ")";
                    $delegationIndexes[] = "APP_ASSIGNED_USER_DEL_INDEX_" . $userUid . "_txt";
                    //Draft
                    $solrSearchText = $solrSearchText . (($solrSearchText != null)? " OR " : null) . "(APP_STATUS:DRAFT AND APP_DRAFT_USER:" . $userUid . ")";
                    //Index is allways 1
                    $solrSearchText = "($solrSearchText)";
                    //Add del_index dynamic fields to list of resulting columns
                    $columsToIncludeFinal = array_merge($columsToInclude, $delegationIndexes);
                    $solrRequestData = \Entity_SolrRequestData::createForRequestPagination(
                        array(
                            "workspace"  => $solrEnv["solr_instance"],
                            "startAfter" => 0,
                            "pageSize"   => 1000,
                            "searchText" => $solrSearchText,
                            "numSortingCols" => 1,
                            "sortCols" => array("APP_NUMBER"),
                            "sortDir"  => array(strtolower("DESC")),
                            "includeCols"  => $columsToIncludeFinal,
                            "resultFormat" => "json"
                        )
                    );
                    //Use search index to return list of cases
                    $searchIndex = new \BpmnEngine_Services_SearchIndex($appSolr->isSolrEnabled(), $solrEnv["solr_host"]);
                    //Execute query
                    $solrQueryResult = $searchIndex->getDataTablePaginatedList($solrRequestData);
                    //Get the missing data from database
                    $arrayApplicationUid = array();
                    foreach ($solrQueryResult->aaData as $i => $data) {
                        $arrayApplicationUid[] = $data["APP_UID"];
                    }
                    $aaappsDBData = $appSolr->getListApplicationDelegationData($arrayApplicationUid);
                    foreach ($solrQueryResult->aaData as $i => $data) {
                        //Initialize array
                        $delIndexes = array(); //Store all the delegation indexes
                        //Complete empty values
                        $applicationUid = $data["APP_UID"]; //APP_UID
                        //Get all the indexes returned by Solr as columns
                        for($i = count($columsToInclude); $i <= count($data) - 1; $i++) {
                            if (is_array($data[$columsToIncludeFinal[$i]])) {
                                foreach ($data[$columsToIncludeFinal[$i]] as $delIndex) {
                                    $delIndexes[] = $delIndex;
                                }
                            }
                        }
                        //Verify if the delindex is an array
                        //if is not check different types of repositories
                        //the delegation index must always be defined.
                        if (count($delIndexes) == 0) {
                            $delIndexes[] = 1; // the first default index
                        }
                        //Remove duplicated
                        $delIndexes = array_unique($delIndexes);
                        //Get records
                        foreach ($delIndexes as $delIndex) {
                            $aRow = array();
                            //Copy result values to new row from Solr server
                            $aRow["APP_UID"] = $data["APP_UID"];
                            //Get delegation data from DB
                            //Filter data from db
                            $indexes = $appSolr->aaSearchRecords($aaappsDBData, array(
                                "APP_UID" => $applicationUid,
                                "DEL_INDEX" => $delIndex
                            ));
                            foreach ($indexes as $index) {
                                $row = $aaappsDBData[$index];
                            }
                            if(!isset($row))
                            {
                                continue;
                            }
                            $aRow["APP_NUMBER"] = $row["APP_NUMBER"];
                            $aRow["APP_STATUS"] = $row["APP_STATUS"];
                            $aRow["PRO_UID"]    = $row["PRO_UID"];
                            $aRow["DEL_INDEX"]  = $row["DEL_INDEX"];
                            $arrayData[] = array(
                                "guid" => $aRow["APP_UID"],
                                "name" => $aRow["APP_NUMBER"],
                                "status" => $aRow["APP_STATUS"],
                                "delIndex" => $aRow["DEL_INDEX"],
                                "processId" => $aRow["PRO_UID"]
                            );
                        }
                    }
                    $case = array();
                    for ($i = 0; $i<=count($arrayData)-1; $i++) {
                        if ($arrayData[$i]["guid"] == $caseUid) {
                            $case = $arrayData[$i];
                        }
                    }
                    return $case;
                } catch (\InvalidIndexSearchTextException $e) {
                    $arrayData = array();
                    $arrayData[] = array (
                        "guid" => $e->getMessage(),
                        "name" => $e->getMessage(),
                        "status" => $e->getMessage(),
                        "delIndex" => $e->getMessage(),
                        "processId" => $e->getMessage()
                    );
                    return $arrayData;
                }
            } else {
                $arrayData = array();
                $criteria = new \Criteria("workflow");
                $criteria->addSelectColumn(\AppCacheViewPeer::APP_UID);
                $criteria->addSelectColumn(\AppCacheViewPeer::DEL_INDEX);
                $criteria->addSelectColumn(\AppCacheViewPeer::APP_NUMBER);
                $criteria->addSelectColumn(\AppCacheViewPeer::APP_STATUS);
                $criteria->addSelectColumn(\AppCacheViewPeer::PRO_UID);
                $criteria->add(\AppCacheViewPeer::USR_UID, $userUid);
                $criteria->add(\AppCacheViewPeer::APP_UID, $caseUid);
                $criteria->add(
                //ToDo - getToDo()
                    $criteria->getNewCriterion(\AppCacheViewPeer::APP_STATUS, "TO_DO", \CRITERIA::EQUAL)->addAnd(
                        $criteria->getNewCriterion(\AppCacheViewPeer::DEL_FINISH_DATE, null, \Criteria::ISNULL))->addAnd(
                            $criteria->getNewCriterion(\AppCacheViewPeer::APP_THREAD_STATUS, "OPEN"))->addAnd(
                            $criteria->getNewCriterion(\AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN"))
                )->addOr(
                    //Draft - getDraft()
                        $criteria->getNewCriterion(\AppCacheViewPeer::APP_STATUS, "DRAFT", \CRITERIA::EQUAL)->addAnd(
                            $criteria->getNewCriterion(\AppCacheViewPeer::APP_THREAD_STATUS, "OPEN"))->addAnd(
                                $criteria->getNewCriterion(\AppCacheViewPeer::DEL_THREAD_STATUS, "OPEN"))
                    );
                $criteria->addDescendingOrderByColumn(\AppCacheViewPeer::APP_NUMBER);
                $rsCriteria = \AppCacheViewPeer::doSelectRS($criteria);
                $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
                while ($rsCriteria->next()) {
                    $row = $rsCriteria->getRow();
                    $arrayData[] = array(
                        "guid" => $row["APP_UID"],
                        "name" => $row["APP_NUMBER"],
                        "status" => $row["APP_STATUS"],
                        "delIndex" => $row["DEL_INDEX"],
                        "processId" => $row["PRO_UID"]
                    );
                }
                return $arrayData;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data Task Case
     *
     * @param string $caseUid Unique id of Case
     *
     * return array Return an array with Task Case
     */
    public function getTaskCase($caseUid)
    {
        try {
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $fields = $ws->taskCase($caseUid);
            //Return
            return $fields;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add New Case
     *
     * @param string $prjUid Unique id of Project
     * @param string $actUid Unique id of Activity
     * @param string $caseUid Unique id of Case
     * @param array $variables
     *
     * return array Return an array with Task Case
     */
    public function addCase($prjUid, $actUid, $userUid, $variables)
    {
        try {
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            if ($variables) {
                $variables = array_shift($variables);
            }
            $fields = $ws->newCase($prjUid, $userUid, $actUid, $variables);
            //Return
            return $fields;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add New Case Impersonate
     *
     * @param string $prjUid Unique id of Project
     * @param string $usrUid Unique id of User
     * @param string $caseUid Unique id of Case
     * @param array $variables
     *
     * return array Return an array with Task Case
     */
    public function addCaseImpersonate($prjUid, $usrUid, $caseUid, $variables)
    {
        try {
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $fields = $ws->newCaseImpersonate($prjUid, $usrUid, $variables, '1352844695225ff5fe54de2005407079');
            //Return
            return $fields;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Reassign Case
     *
     * @param string $caseUid Unique id of Case
     * @param string $userUid Unique id of User
     * @param string $delIndex
     * @param string $userUidSource Unique id of User Source
     * @param string $userUid $userUidTarget id of User Target
     *
     * return array Return an array with Task Case
     */

    public function updateReassignCase($caseUid, $userUid, $delIndex, $userUidSource, $userUidTarget)
    {
        try {
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $fields = $ws->reassignCase($userUid, $caseUid, $delIndex, $userUidSource, $userUidTarget);
            //Return
            return $fields;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
