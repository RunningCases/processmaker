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
     * @param string $projectUid
     * @param CaseTrackerObjectPostStructure $request_data
     *
     * @status 201
     */
    public function doPostCaseTrackerObject($projectUid, CaseTrackerObjectPostStructure $request_data = null)
    {
        try {
            $caseTrackerObject = new \BusinessModel\CaseTrackerObject();

            $arrayData = $caseTrackerObject->getArrayDataFromRequestData($request_data);

            $arrayData = $caseTrackerObject->create($projectUid, $arrayData);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/case-tracker/object/:caseTrackerObjectUid
     *
     * @param string $caseTrackerObjectUid
     * @param string $projectUid
     * @param CaseTrackerObjectPutStructure $request_data
     */
    public function doPutCaseTrackerObject($caseTrackerObjectUid, $projectUid, CaseTrackerObjectPutStructure $request_data = null)
    {
        try {
            $caseTrackerObject = new \BusinessModel\CaseTrackerObject();

            $arrayData = $caseTrackerObject->getArrayDataFromRequestData($request_data);

            $arrayData = $caseTrackerObject->update($caseTrackerObjectUid, $arrayData);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/case-tracker/object/:caseTrackerObjectUid
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

class CaseTrackerObjectPostStructure
{
    /**
     * @var string {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}{@required true}
     */
    public $cto_type_obj;

    /**
     * @var string {@from body}{@min 32}{@max 32}{@required true}
     */
    public $cto_uid_obj;

    /**
     * @var string
     */
    public $cto_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $cto_position;
}

class CaseTrackerObjectPutStructure
{
    /**
     * @var string {@from body}{@choice DYNAFORM,INPUT_DOCUMENT,OUTPUT_DOCUMENT}
     */
    public $cto_type_obj;

    /**
     * @var string {@from body}{@min 32}{@max 32}
     */
    public $cto_uid_obj;

    /**
     * @var string
     */
    public $cto_condition;

    /**
     * @var int {@from body}{@min 1}
     */
    public $cto_position;
}

