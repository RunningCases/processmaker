<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Group Api Controller
 *
 * @protected
 */
class Calendar extends Api
{
    private $formatFieldNameInUppercase = false;

    /**
     * @url GET
     */
    public function index($filter = null, $start = null, $limit = null)
    {
        try {
            $calendar = new \BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $response = $calendar->getCalendars(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:cal_uid
     *
     * @param string $cal_uid {@min 32}{@max 32}
     */
    public function doGet($cal_uid)
    {
        try {
            $calendar = new \BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $response = $calendar->getCalendar($cal_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param array $request_data
     *
     * @status 201
     */
    public function doPost($request_data)
    {
        try {
            $calendar = new \BusinessModel\Calendar();
            $calendar->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $arrayData = $calendar->create($request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

