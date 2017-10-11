<?php
namespace ProcessMaker\Services\Api;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * File Api Controller
 *
 * @protected
 */
class File extends Api
{
    /**
     * @url POST /upload
     * @access protected
     * @class AccessControl {@permission PM_FACTORY}
     * @param array  $request_data
     */
    public function doPostFilesUpload($request_data)
    {
        try {
            $request_data = (array)($request_data);
            $files = new \ProcessMaker\BusinessModel\File();
            $sData = $files->uploadFile($request_data);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

}
