<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\ProjectUsers Api Controller
 *
 * @protected
 */
class FilesManager extends Api
{
    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $path {@choice templates,folder,}
     *
     * @url GET /:prjUid/process-file-manager
     */
    public function doGetProcessFilesManager($prjUid, $path = '')
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            if ($path != '') { 
                if ($path == 'templates') {
                    $path = 'mailTemplates';
                } else {
                    $path = 'public';
                }
                $arrayData = $filesManager->getProcessFilesManagerPath($prjUid, $path);
            } else {
                $arrayData = $filesManager->getProcessFilesManager($prjUid);
            }            
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

}
