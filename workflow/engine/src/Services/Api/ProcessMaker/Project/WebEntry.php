<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\WebEntry Api Controller
 *
 * @protected
 */
class WebEntry extends Api
{
    /**
     * @url GET /:projectUid/web-entry/:activityUid/:dynaFormUid
     *
     * @param string $dynaFormUid {@min 32}{@max 32}
     * @param string $activityUid {@min 32}{@max 32}
     * @param string $projectUid  {@min 32}{@max 32}
     */
    public function doGetWebEntry($dynaFormUid, $activityUid, $projectUid)
    {
        try {
            $webEntry = new \BusinessModel\WebEntry();

            $response = $webEntry->getWebEntry($projectUid, $activityUid, $dynaFormUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    ///**
    // * @url POST /:projectUid/case-tracker/object
    // *
    // * @param string $projectUid    {@min 32}{@max 32}
    // * @param array  $request_data
    // * @param string $cto_type_obj  {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}{@required true}
    // * @param string $cto_uid_obj   {@from body}{@min 32}{@max 32}{@required true}
    // * @param string $cto_condition
    // * @param int    $cto_position  {@from body}{@min 1}
    // *
    // * @status 201
    // */
    //public function doPostWebEntry(
    //    $projectUid,
    //    $request_data,
    //    $cto_type_obj = "DYNAFORM",
    //    $cto_uid_obj = "00000000000000000000000000000000",
    //    $cto_condition = "",
    //    $cto_position = 1
    //) {
    //    try {
    //        $caseTrackerObject = new \BusinessModel\WebEntry();
    //
    //        $arrayData = $caseTrackerObject->create($projectUid, $request_data);
    //
    //        $response = $arrayData;
    //
    //        return $response;
    //    } catch (\Exception $e) {
    //        throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    //    }
    //}

    /**
     * @url DELETE /:projectUid/web-entry/:activityUid/:dynaFormUid
     *
     * @param string $dynaFormUid {@min 32}{@max 32}
     * @param string $activityUid {@min 32}{@max 32}
     * @param string $projectUid  {@min 32}{@max 32}
     */
    public function doDeleteWebEntry($dynaFormUid, $activityUid, $projectUid)
    {
        try {
            $webEntry = new \BusinessModel\WebEntry();

            $webEntry->delete($projectUid, $activityUid, $dynaFormUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

