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
}

