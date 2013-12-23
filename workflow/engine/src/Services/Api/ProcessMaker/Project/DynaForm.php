<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\DynaForm Api Controller
 *
 * @protected
 */
class DynaForm extends Api
{
    /**
     * @url GET /:projectUid/dynaform/:dynaFormUid
     */
    public function doGetDynaForm($dynaFormUid, $projectUid)
    {
        try {
            $dynaForm = new \BusinessModel\DynaForm();

            $response = $dynaForm->getDynaForm($dynaFormUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/dynaform
     *
     * @param string $projectUid
     * @param DynaFormPostStructure $request_data
     *
     * @status 201
     */
    public function doPostDynaForm($projectUid, DynaFormPostStructure $request_data = null)
    {
        try {
            $dynaForm = new \BusinessModel\DynaForm();

            $arrayData = $dynaForm->getArrayDataFromRequestData($request_data);

            $arrayData = $dynaForm->defineCreate($projectUid, $arrayData);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/dynaform/:dynaFormUid
     *
     * @param string $dynaFormUid
     * @param string $projectUid
     * @param DynaFormPutStructure $request_data
     */
    public function doPutDynaForm($dynaFormUid, $projectUid, DynaFormPutStructure $request_data = null)
    {
        try {
            $dynaForm = new \BusinessModel\DynaForm();

            $arrayData = $dynaForm->getArrayDataFromRequestData($request_data);

            $arrayData = $dynaForm->update($dynaFormUid, $arrayData);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/dynaform/:dynaFormUid
     */
    public function doDeleteDynaForm($dynaFormUid, $projectUid)
    {
        try {
            $dynaForm = new \BusinessModel\DynaForm();

            $dynaForm->delete($dynaFormUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

class DynaFormPostStructure
{
    /**
     * @var string {@from body}{@required true}
     */
    public $dyn_title;

    /**
     * @var string {@from body}
     */
    public $dyn_description;

    /**
     * @var string {@from body}{@choice xmlform,grid}{@required true}
     */
    public $dyn_type;

    /**
     * @var array {@from body}{@type associative}
     */
    public $copy_import;

    /**
     * @var array {@from body}{@type associative}
     */
    public $pmtable;
}

class DynaFormPutStructure
{
    /**
     * @var string {@from body}
     */
    public $dyn_title;

    /**
     * @var string {@from body}
     */
    public $dyn_description;

    /**
     * @var string {@from body}{@choice xmlform,grid}{@required true}
     */
    public $dyn_type;
}

