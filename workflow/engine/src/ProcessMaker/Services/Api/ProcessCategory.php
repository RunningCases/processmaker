<?php
namespace ProcessMaker\Services\Api;

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
     * @url GET
     */
    public function doGetCategories($filter = null, $start = null, $limit = null)
    {
        try {
            $processCategory = new \ProcessMaker\BusinessModel\ProcessCategory();
            $processCategory->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $response = $processCategory->getCategories(array("filter" => $filter), null, null, $start, $limit);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:cat_uid
     *
     * @param string $cat_uid     {@min 32}{@max 32}
     */
    public function doGetCategory($cat_uid)
    {
        try {
            $processCategory = new \ProcessMaker\BusinessModel\ProcessCategory();
            $processCategory->setFormatFieldNameInUppercase($this->formatFieldNameInUppercase);

            $response = $processCategory->getCategory($cat_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST
     *
     * @param string $cat_name
     *
     */
    public function doPostCategory($cat_name)
    {
        try {
            $processCategory = new \ProcessMaker\BusinessModel\ProcessCategory();
            $response = $processCategory->addCategory($cat_name);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:cat_uid
     *
     * @param string $cat_uid     {@min 32}{@max 32}
     * @param string $cat_name
     *
     */
    public function doPutCategory($cat_uid, $cat_name)
    {
        try {
            $processCategory = new \ProcessMaker\BusinessModel\ProcessCategory();
            $response = $processCategory->updateCategory($cat_uid, $cat_name);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:cat_uid
     *
     * @param string $cat_uid     {@min 32}{@max 32}
     *
     */
    public function doDeleteCategory($cat_uid)
    {
        try {
            $processCategory = new \ProcessMaker\BusinessModel\ProcessCategory();
            $processCategory->deleteCategory($cat_uid);

        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}