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
    public function doGetSupervisors($prjUid, $filter = '', $start = null, $limit = null)
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
     * @param string $filter
     * @param int    $start
     * @param int    $limit
     *
     * @url GET /:prjUid/available-supervisors
     */
    public function doGetAvailableSupervisors($prjUid, $filter = '', $start = null, $limit = null)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $arrayData = $supervisor->getAvailableSupervisors($prjUid, $filter, $start, $limit);
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
    public function doGetInputDocumentSupervisor($prjUid)
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
    public function doGetDynaformSupervisor($prjUid)
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

    /**
     * @url POST /:prjUid/supervisor
     *
     * @param string $prjUid
     * @param string $sup_uid
     * @param string $sup_type {@choice user,group}
     *
     * @status 201
     */
    public function doPostSupervisors($prjUid, $sup_uid, $sup_type)
    {
        try {
            $supervisor = new \BusinessModel\ProcessSupervisor();
            $sup_type=ucwords($sup_type);
            $arrayData = $supervisor->addSupervisor($prjUid, $sup_uid, $sup_type);
        } catch (\Exception $e) {
            //Response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }



}
