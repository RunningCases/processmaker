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
     * @url GET /:prjUid/file-manager
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
     * @url POST /:prjUid/file-manager
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
     * @param string $prfUid {@min 32} {@max 32}
     *
     * @url POST /:prjUid/file-manager/:prfUid/upload
     */
    public function doPostProcessFilesManagerUpload($prjUid, $prfUid)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $filesManager->uploadProcessFilesManager($prjUid, $prfUid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prjUid {@min 32} {@max 32}
     * @param ProcessFilesManagerStructurePut $request_data
     * @param string $path
     *
     * @url PUT /:prjUid/file-manager
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
     * @url DELETE /:prjUid/file-manager
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
     * @url GET /:prjUid/file-manager/download
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
    public $prf_filename;

    /**
     * @var string {@from body}
     */
    public $prf_path;
    
    /**
     * @var string {@from body}
     */
    public $prf_content;
}


class ProcessFilesManagerStructurePut
{
    /**
     * @var string {@from body}
     */
    public $prf_filename;

    /**
     * @var string {@from body}
     */
    public $prf_content;
}

