<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Group Api Controller
 *
 * @protected
 */
class Group extends Api
{
    /**
     * @url GET
     */
    public function index($filter = null, $start = null, $limit = null)
    {
        try {
            $group = new \BusinessModel\Group();

            $response = $group->getGroups(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:grp_uid
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doGet($grp_uid)
    {
        try {
            $group = new \BusinessModel\Group();
            $group->setArrayMsgExceptionParam(array("groupUid" => "grp_uid"));

            $response = $group->getGroup($grp_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param array  $request_data
     * @param string $grp_title    {@from body}{@required true}
     * @param string $grp_status   {@from body}{@choice ACTIVE,INACTIVE}{@required true}
     *
     * @status 201
     */
    public function doPost(
        $request_data,
        $grp_title = "",
        $grp_status = "ACTIVE"
    ) {
        try {
            $group = new \BusinessModel\Group();
            $group->setArrayMsgExceptionParam(array("groupTitle" => "grp_title"));

            $arrayData = $group->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:grp_uid
     *
     * @param string $grp_uid      {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $grp_title    {@from body}
     * @param string $grp_status   {@from body}{@choice ACTIVE,INACTIVE}
     */
    public function doPut(
        $grp_uid,
        $request_data,
        $grp_title = "",
        $grp_status = "ACTIVE"
    ) {
        try {
            $group = new \BusinessModel\Group();
            $group->setArrayMsgExceptionParam(array("groupUid" => "grp_uid", "groupTitle" => "grp_title"));

            $arrayData = $group->update($grp_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:grp_uid
     *
     * @param string $grp_uid {@min 32}{@max 32}
     */
    public function doDelete($grp_uid)
    {
        try {
            $group = new \BusinessModel\Group();
            $group->setArrayMsgExceptionParam(array("groupUid" => "grp_uid"));

            $group->delete($grp_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

