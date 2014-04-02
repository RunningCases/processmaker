<?php
namespace ProcessMaker\Services\Api\Project;

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
     * @url GET /:prj_uid/web-entry/:tas_uid/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $tas_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetWebEntry($dyn_uid, $tas_uid, $prj_uid)
    {
        try {
            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();
            $webEntry->setFormatFieldNameInUppercase(false);
            $webEntry->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $webEntry->getWebEntry($prj_uid, $tas_uid, $dyn_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:prj_uid/web-entry
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @status 201
     */
    public function doPostWebEntry($prj_uid, $request_data)
    {
        try {
            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();
            $webEntry->setFormatFieldNameInUppercase(false);
            $webEntry->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $webEntry->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/web-entry/:tas_uid/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $tas_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doDeleteWebEntry($dyn_uid, $tas_uid, $prj_uid)
    {
        try {
            $webEntry = new \ProcessMaker\BusinessModel\WebEntry();
            $webEntry->setFormatFieldNameInUppercase(false);
            $webEntry->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $webEntry->delete($prj_uid, $tas_uid, $dyn_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

