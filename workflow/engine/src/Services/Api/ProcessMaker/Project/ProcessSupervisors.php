<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ProcessSupervisors Api Controller
 *
 * @protected
 */
class ProcessSupervisors extends Api
{
    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     *
     * @url GET /:prjUid/supervisors
     */
    public function doGetProjectSupervisors($prjUid, $filter = '', $start = null, $limit = null)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->getSupervisors($prjUid, $filter, $start, $limit);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/inputdocument-supervisor
     */
    public function doGetProjectInputDocumentSupervisor($prjUid)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->getInputDocumentSupervisor($prjUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @url GET /:prjUid/dynaform-supervisor
     */
    public function doGetProjectDynaformSupervisor($prjUid)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->getDynaformSupervisor($prjUid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url DELETE /:prjUid/supervisor/:supUid
     *
     * @param string $prjUid
     * @param string $supUid
     *
     */
    public function doDeleteSupervisor($prjUid, $supUid)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->removeProcessSupervisor($prjUid, $supUid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url DELETE /:prjUid/dynaform-supervisor/:dynUid
     *
     * @param string $prjUid
     * @param string $dynUid
     *
     */
    public function doDeleteDynaformSupervisor($prjUid, $dynUid)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->removeDynaformSupervisor($prjUid, $dynUid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @url DELETE /:prjUid/inputdocument-supervisor/:inputDocUid
     *
     * @param string $prjUid
     * @param string $inputDocUid
     *
     */
    public function doDeleteInputDocumentSupervisor($prjUid, $inputDocUid)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->removeInputDocumentSupervisor($prjUid, $inputDocUid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

}
