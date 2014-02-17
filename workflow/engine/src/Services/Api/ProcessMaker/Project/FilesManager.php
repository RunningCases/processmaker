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
     * @param string $path
     *
     * @url GET /:prjUid/process-file-manager
     */
    public function doGetProcessFilesManager($prjUid, $path = '')
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            if ($path != '') {
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

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param ProcessFilesManagerStructure $request_data
     *
     * @url POST /:prjUid/process-file-manager
     */
    public function doPostProcessFilesManager($prjUid, ProcessFilesManagerStructure $request_data)
    {
        try {
            $userUid = $this->getUserId();
            $request_data = (array)($request_data);

            $filesManager = new \BusinessModel\FilesManager();
            $arrayData = $filesManager->addProcessFilesManager($prjUid, $userUid, $request_data);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     *
     * @header Accept: application/octet-stream
     * @url POST /:prjUid/process-file-manager/upload
     */
    public function doPostProcessFilesManagerUpload($prjUid)
    {
        try {
            require_once (PATH_TRUNK . "workflow" . PATH_SEP . "engine" . PATH_SEP . "src" . PATH_SEP . "Extension" . PATH_SEP . "Restler" . PATH_SEP . "UploadFormat.php");
            $userUid = $this->getUserId();
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param ProcessFilesManagerStructurePut $request_data
     * @param string $path
     *
     * @url PUT /:prjUid/process-file-manager
     */
    public function doPutProcessFilesManager($prjUid, ProcessFilesManagerStructurePut $request_data, $path)
    {
        try {
            $userUid = $this->getUserId();
            $request_data = (array)($request_data);
            $filesManager = new \BusinessModel\FilesManager();
            $arrayData = $filesManager->updateProcessFilesManager($prjUid, $userUid, $request_data, $path);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $path
     *
     * @url DELETE /:prjUid/process-file-manager
     */
    public function doDeleteProcessFilesManager($prjUid, $path)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $filesManager->deleteProcessFilesManager($prjUid, $path);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $path
     *
     * @url GET /:prjUid/process-file-manager/download
     */
    public function doGetProcessFilesManagerDownload($prjUid, $path)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $filesManager->downloadProcessFilesManager($prjUid, $path);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }
}

class ProcessFilesManagerStructure
{
    /**
     * @var string {@from body}
     */
    public $file_name;

    /**
     * @var string {@from body}
     */
    public $path;

    /**
     * @var string {@from body}
     */
    public $content;
}


class ProcessFilesManagerStructurePut
{
    /**
     * @var string {@from body}
     */
    public $file_name;

    /**
     * @var string {@from body}
     */
    public $content;
}

class ProcessFilesManagerStructureUpload
{
    /**
     * @var string {@from body}
     */
    public $url;

}
