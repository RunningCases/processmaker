<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use Illuminate\Support\Facades\DB;
use \Luracast\Restler\RestException;
use \ProcessMaker\Model\TaskScheduler;
use Illuminate\Support\Facades\Log;
use ProcessMaker\BusinessModel\TaskSchedulerBM;
/**
 * TaskScheduler Controller
 *
 * @protected
 */
class Scheduler extends Api
{
    /**
     * @url GET 
     *
     * @param string $category
     */
    public function doGet($category = null) {
        try {
            return TaskSchedulerBM::getSchedule($category);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url POST 
     * @status 200
     *
     * @param array $request_data
     *
     * @return array
     * @throws RestException
     *
     */
    public function doPost(array $request_data) {
        try {
            return TaskSchedulerBM::saveSchedule($request_data);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}
