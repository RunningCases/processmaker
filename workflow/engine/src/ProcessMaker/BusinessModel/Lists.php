<?php
namespace ProcessMaker\BusinessModel;

use \G;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Lists {

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
    public function getList($listName = 'inbox', $dataList = array(), $total = false)
    {
        Validator::isArray($dataList, '$dataList');
        if (!isset($dataList["userId"])) {
            throw (new \Exception(\G::LoadTranslation("ID_USER_NOT_EXIST", array('userId',''))));
        } else {
            Validator::usrUid($dataList["userId"], "userId");
        }

        $userUid = $dataList["userId"];
        $filters["paged"]    = isset( $dataList["paged"] ) ? $dataList["paged"] : true;
        $filters['count']    = isset( $dataList['count'] ) ? $dataList['count'] : true;

        $filters["category"] = isset( $dataList["category"] ) ? $dataList["category"] : "";
        $filters["process"]  = isset( $dataList["process"] ) ? $dataList["process"] : "";
        $filters["search"]   = isset( $dataList["search"] ) ? $dataList["search"] : "";
        $filters["filter"]   = isset( $dataList["filter"] ) ? $dataList["filter"] : "";
        $filters["dateFrom"] = (!empty( $dataList["dateFrom"] )) ? substr( $dataList["dateFrom"], 0, 10 ) : "";
        $filters["dateTo"]   = (!empty( $dataList["dateTo"] )) ? substr( $dataList["dateTo"], 0, 10 ) : "";

        $filters["start"]    = isset( $dataList["start"] ) ? $dataList["start"] : "0";
        $filters["limit"]    = isset( $dataList["limit"] ) ? $dataList["limit"] : "25";
        $filters["sort"]     = isset( $dataList["sort"] ) ? $dataList["sort"] : "";
        $filters["dir"]      = isset( $dataList["dir"] ) ? $dataList["dir"] : "DESC";

        // Select list
        switch ($listName) {
            case 'inbox':
                $list = new \ListInbox();
                $listpeer = 'ListInboxPeer';
                break;
            case 'participated_history':
                $list = new \ListParticipatedHistory();
                $listpeer = 'ListParticipatedHistoryPeer';
                break;
            case 'participated_last':
                $list = new \ListParticipatedLast();
                $listpeer = 'ListParticipatedLastPeer';
                break;
            case 'completed':
                $list = new \ListCompleted();
                $listpeer = 'ListCompletedPeer';
                break;
            case 'my_inbox':
                $list = new \ListMyInbox();
                $listpeer = 'ListMyInboxPeer';
                break;
            case 'unassigned':
                $list = new \ListUnassigned();
                $listpeer = 'ListUnassignedPeer';
                break;
        }


        // Validate filters
        $filters["start"] = (int)$filters["start"];
        $filters["start"] = abs($filters["start"]);
        if ($filters["start"] != 0) {
            $filters["start"]--;
        }

        $filters["limit"] = (int)$filters["limit"];
        $filters["limit"] = abs($filters["limit"]);
        if ($filters["limit"] == 0) {
            G::LoadClass("configuration");
            $conf = new \Configurations();
            $generalConfCasesList = $conf->getConfiguration('ENVIRONMENT_SETTINGS', '');
            if (isset($generalConfCasesList['casesListRowNumber'])) {
                $filters["limit"] = (int)$generalConfCasesList['casesListRowNumber'];
            } else {
                $filters["limit"] = 25;
            }
        } else {
            $filters["limit"] = (int)$filters["limit"];
        }

        $filters["sort"] = G::toUpper($filters["sort"]);
        $columnsList = $listpeer::getFieldNames(\BasePeer::TYPE_FIELDNAME);
        if (!(in_array($filters["sort"], $columnsList))) {
            $filters["sort"] = '';
        }

        $filters["dir"] = G::toUpper($filters["dir"]);
        if (!($filters["dir"] == 'DESC' || $filters["dir"] == 'ASC')) {
            $filters["dir"] = 'DESC';
        }
        if ($filters["process"] != '') {
            Validator::proUid($filters["process"], '$pro_uid');
        }
        if ($filters["category"] != '') {
            Validator::catUid($filters["category"], '$cat_uid');
        }
        if ($filters["dateFrom"] != '') {
            Validator::isDate($filters["dateFrom"], 'Y-m-d', '$date_from');
        }
        if ($filters["dateTo"] != '') {
            Validator::isDate($filters["dateTo"], 'Y-m-d', '$date_to');
        }

        if ($total) {
            $total = $list->countTotal($userUid, $filters);
            return $total;
        }

        $result = $list->loadList($userUid, $filters);
        if (!empty($result)) {
            foreach ($result as &$value) {
                $value = array_change_key_case($value, CASE_LOWER);
            }
        }

        $response = array();
        if ($filters["paged"]) {
            $filtersData = array();
            $filtersData['start']       = $filters["start"]+1;
            $filtersData['limit']       = $filters["limit"];
            $filtersData['sort']        = G::toLower($filters["sort"]);
            $filtersData['dir']         = G::toLower($filters["dir"]);
            $filtersData['cat_uid']     = $filters["category"];
            $filtersData['pro_uid']     = $filters["process"];
            $filtersData['search']      = $filters["search"];
            $filtersData['date_from']   = $filters["dateFrom"];
            $filtersData['date_to']     = $filters["dateTo"];
            $response['filters']        = $filtersData;
            $response['data']           = $result;
        } else {
            $response = $result;
        }

        return $response;
    }
}