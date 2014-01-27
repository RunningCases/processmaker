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
     * @url GET /:prj_uid/web-entry/:tas_uid/:dyn_uid
     *
     * @param string $dyn_uid {@min 32}{@max 32}
     * @param string $tas_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetWebEntry($dyn_uid, $tas_uid, $prj_uid)
    {
        try {
            $webEntry = new \BusinessModel\WebEntry();

            $response = $webEntry->getWebEntry($prj_uid, $tas_uid, $dyn_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:prj_uid/web-entry
     *
     * @param string $prj_uid               {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $tas_uid               {@from body}{@min 32}{@max 32}{@required true}
     * @param string $dyn_uid               {@from body}{@min 32}{@max 32}{@required true}
     * @param string $method                {@from body}{@choice WS,HTML}{@required true}
     * @param int    $input_document_access {@from body}{@choice 0,1}{@required true}
     * @param string $usr_username          {@from body}
     * @param string $usr_password          {@from body}
     *
     * @status 201
     */
    public function doPostWebEntry(
        $prj_uid,
        $request_data,
        $tas_uid = "00000000000000000000000000000000",
        $dyn_uid = "00000000000000000000000000000000",
        $method = "WS",
        $input_document_access = 0,
        $usr_username = "",
        $usr_password = ""
    ) {
        try {
            $webEntry = new \BusinessModel\WebEntry();

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
            $webEntry = new \BusinessModel\WebEntry();

            $webEntry->delete($prj_uid, $tas_uid, $dyn_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

