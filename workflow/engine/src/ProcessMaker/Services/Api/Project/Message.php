<?php
namespace ProcessMaker\Services\Api\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;
/**
 * Project\Message Api Controller
 *
 * @protected
 */
class Message extends Api
{
    /**
     * @url GET /:prj_uid/messages
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetMessages($prj_uid)
    {
        try {
            $message = new \ProcessMaker\BusinessModel\Message();

            $response = $message->getMessages($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/message/:mes_uid
     *
     * @param string $mes_uid {@min 32}{@max 32}
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetMessage($mes_uid, $prj_uid)
    {
        try {
            $message = new \ProcessMaker\BusinessModel\Message();

            $response = $message->getMessage($prj_uid, $mes_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:prj_uid/message
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     *
     * @status 201
     */
    public function doPostMessage($prj_uid, $request_data)
    {
        try {
            $request_data = (array)($request_data);
            $message = new \ProcessMaker\BusinessModel\Message();

            $arrayData = $message->create($prj_uid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/message/:mes_uid
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param string $mes_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutMessage($prj_uid, $mes_uid, array $request_data)
    {
        try {
            $request_data = (array)($request_data);
            $message = new \ProcessMaker\BusinessModel\Message();

            $message->update($prj_uid, $mes_uid, $request_data);

        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:prj_uid/message/:mes_uid
     *
     * @param string $prj_uid {@min 32}{@max 32}
     * @param string $mes_uid {@min 32}{@max 32}
     */
    public function doDeleteMessage($prj_uid, $mes_uid)
    {
        try {
            $message = new \ProcessMaker\BusinessModel\Message();

            $message->delete($prj_uid, $mes_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

