<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\TriggerWizard Api Controller
 *
 * @protected
 */
class TriggerWizard extends Api
{
    /**
     * @url GET /:prj_uid/trigger-wizard/:lib_name
     * @url GET /:prj_uid/trigger-wizard/:lib_name/:fn_name
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $lib_name
     * @param string $fn_name
     */
    public function doGetTriggerWizard($prj_uid, $lib_name, $fn_name = "")
    {
        try {
            $triggerWizard = new \BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase(false);
            $triggerWizard->setArrayFieldNameForException(array("processUid" => "prj_uid", "libraryName" => "lib_name", "methodName" => "fn_name"));

            $response = ($fn_name == "")? $triggerWizard->getLibrary($lib_name) : $triggerWizard->getMethod($lib_name, $fn_name);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/trigger-wizard/:lib_name/:fn_name/:tri_uid
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     * @param string $lib_name
     * @param string $fn_name
     * @param string $tri_uid  {@min 32}{@max 32}
     */
    public function doGetTriggerWizardTrigger($prj_uid, $lib_name, $fn_name, $tri_uid)
    {
        try {
            $triggerWizard = new \BusinessModel\TriggerWizard();
            $triggerWizard->setFormatFieldNameInUppercase(false);
            $triggerWizard->setArrayFieldNameForException(array("processUid" => "prj_uid", "libraryName" => "lib_name", "methodName" => "fn_name"));

            $response = $triggerWizard->getTrigger($lib_name, $fn_name, $tri_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    ///**
    // * @url POST /:prj_uid/dynaform
    // *
    // * @param string $prj_uid      {@min 32}{@max 32}
    // * @param array  $request_data
    // *
    // * @status 201
    // */
    //public function doPostDynaForm($prj_uid, $request_data)
    //{
    //    try {
    //        return $response;
    //    } catch (\Exception $e) {
    //        throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    //    }
    //}
    //
    ///**
    // * @url PUT /:prj_uid/dynaform/:dyn_uid
    // *
    // * @param string $dyn_uid      {@min 32}{@max 32}
    // * @param string $prj_uid      {@min 32}{@max 32}
    // * @param array  $request_data
    // */
    //public function doPutDynaForm($dyn_uid, $prj_uid, $request_data)
    //{
    //    try {
    //        //
    //    } catch (\Exception $e) {
    //        throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
    //    }
    //}
}

