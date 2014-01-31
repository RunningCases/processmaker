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

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param string $path
     *
     * @url GET /:prjUid/process-file-manager-download
     */
    public function doGetProcessFilesManagerDownload($prjUid, $path = '')
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $arrayData = $filesManager->getProcessFilesManagerDownload($prjUid);
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
     * @url POST /:prjUid/process-file-manager-upload
     */
    public function doPostProcessFilesManagerUpload($prjUid)
    {
        try {
            $userUid = $this->getUserId();
            $filesManager = new \BusinessModel\FilesManager();
            $arrayData = $filesManager->uploadProcessFilesManager($prjUid, $userUid);
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
     * @param ProcessFilesManagerStructure1 $request_data
     *
     * @url PUT /:prjUid/process-file-manager1
     */
    public function doPutProcessFilesManager($prjUid, ProcessFilesManagerStructure1 $request_data, $path)
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
            $arrayData = $filesManager->deleteProcessFilesManager($prjUid, $path);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
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

class ProcessFilesManagerStructure1
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