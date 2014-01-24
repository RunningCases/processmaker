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

    /**
     * @url POST /:projectUid/web-entry
     *
     * @param string $projectUid            {@min 32}{@max 32}
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
        $projectUid,
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

            $arrayData = $webEntry->create($projectUid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

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

