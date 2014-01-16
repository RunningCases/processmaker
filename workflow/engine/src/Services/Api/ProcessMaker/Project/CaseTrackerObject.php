<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\CaseTrackerObject Api Controller
 *
 * @protected
 */
class CaseTrackerObject extends Api
{
    /**
     * @url GET /:projectUid/case-tracker/object/:caseTrackerObjectUid
     *
     * @param string $caseTrackerObjectUid {@min 32}{@max 32}
     * @param string $projectUid           {@min 32}{@max 32}
     */
    public function doGetCaseTrackerObject($caseTrackerObjectUid, $projectUid)
    {
        try {
            $caseTrackerObject = new \BusinessModel\CaseTrackerObject();

            $response = $caseTrackerObject->getCaseTrackerObject($caseTrackerObjectUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/case-tracker/object
     *
     * @param string $projectUid    {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $cto_type_obj  {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}{@required true}
     * @param string $cto_uid_obj   {@from body}{@min 32}{@max 32}{@required true}
     * @param string $cto_condition
     * @param int    $cto_position  {@from body}{@min 1}
     *
     * @status 201
     */
    public function doPostCaseTrackerObject(
        $projectUid,
        $request_data,
        $cto_type_obj = "DYNAFORM",
        $cto_uid_obj = "00000000000000000000000000000000",
        $cto_condition = "",
        $cto_position = 1
    ) {
        try {
            $caseTrackerObject = new \BusinessModel\CaseTrackerObject();

            $arrayData = $caseTrackerObject->create($projectUid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/case-tracker/object/:caseTrackerObjectUid
     *
     * @param string $caseTrackerObjectUid {@min 32}{@max 32}
     * @param string $projectUid           {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $cto_type_obj  {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}
     * @param string $cto_uid_obj   {@from body}{@min 32}{@max 32}
     * @param string $cto_condition
     * @param int    $cto_position  {@from body}{@min 1}
     */
    public function doPutCaseTrackerObject(
        $caseTrackerObjectUid,
        $projectUid,
        $request_data,
        $cto_type_obj = "DYNAFORM",
        $cto_uid_obj = "00000000000000000000000000000000",
        $cto_condition = "",
        $cto_position = 1
    ) {
        try {
            $caseTrackerObject = new \BusinessModel\CaseTrackerObject();

            $arrayData = $caseTrackerObject->update($caseTrackerObjectUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/case-tracker/object/:caseTrackerObjectUid
     *
     * @param string $caseTrackerObjectUid {@min 32}{@max 32}
     * @param string $projectUid           {@min 32}{@max 32}
     */
    public function doDeleteCaseTrackerObject($caseTrackerObjectUid, $projectUid)
    {
        try {
            $caseTrackerObject = new \BusinessModel\CaseTrackerObject();

            $caseTrackerObject->delete($caseTrackerObjectUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

