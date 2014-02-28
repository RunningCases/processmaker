<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Group Api Controller
 *
 * @protected
 */
class ProcessCategory extends Api
{
    private $formatFieldNameInUppercase = false;

    /**
     * @url GET /categories
     */
    public function doGetCategories($filter = null, $start = null, $limit = null)
    {
        try {
            $processCategory = new \BusinessModel\ProcessCategory();
            $processCategory->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $response = $processCategory->getCategories(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

