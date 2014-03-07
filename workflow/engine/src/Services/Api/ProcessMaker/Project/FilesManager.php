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
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $path
     *
     * @url GET /:prj_uid/file-manager
     */
    public function doGetProcessFilesManager($prj_uid, $path = '')
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            if ($path != '') {
                $arrayData = $filesManager->getProcessFilesManagerPath($prj_uid, $path);
            } else {
                $arrayData = $filesManager->getProcessFilesManager($prj_uid);
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
     * @param string $prj_uid {@min 32} {@max 32}
     * @param ProcessFilesManagerStructure $request_data
     *
     * @url POST /:prj_uid/file-manager
     */
    public function doPostProcessFilesManager($prj_uid, ProcessFilesManagerStructure $request_data)
    {
        try {
            $userUid = $this->getUserId();
            $request_data = (array)($request_data);
            $filesManager = new \BusinessModel\FilesManager();
            $arrayData = $filesManager->addProcessFilesManager($prj_uid, $userUid, $request_data);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $prf_uid {@min 32} {@max 32}
     *
     * @url POST /:prj_uid/file-manager/:prf_uid/upload
     */
    public function doPostProcessFilesManagerUpload($prj_uid, $prf_uid)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $sData = $filesManager->uploadProcessFilesManager($prj_uid, $prf_uid);
            //Response
            $response = $sData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param ProcessFilesManagerStructure $request_data
     * @param string $prf_uid {@min 32} {@max 32}
     *
     * @url PUT /:prj_uid/file-manager/:prf_uid
     */
    public function doPutProcessFilesManager($prj_uid, ProcessFilesManagerStructure $request_data, $prf_uid)
    {
        try {
            $userUid = $this->getUserId();
            $request_data = (array)($request_data);
            $filesManager = new \BusinessModel\FilesManager();
            $arrayData = $filesManager->updateProcessFilesManager($prj_uid, $userUid, $request_data, $prf_uid);
            //Response
            $response = $arrayData;
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
        return $response;
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $prf_uid {@min 32} {@max 32}
     *
     * @url DELETE /:prj_uid/file-manager/:prf_uid
     */
    public function doDeleteProcessFilesManager($prj_uid, $prf_uid)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $filesManager->deleteProcessFilesManager($prj_uid, $prf_uid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $prf_uid {@min 32} {@max 32}
     *
     * @url GET /:prj_uid/file-manager/:prf_uid/download
     */
    public function doGetProcessFilesManagerDownload($prj_uid, $prf_uid)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $filesManager->downloadProcessFilesManager($prj_uid, $prf_uid);
        } catch (\Exception $e) {
            //response
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @param string $prj_uid {@min 32} {@max 32}
     * @param string $path
     *
     * @url DELETE /:prj_uid/file-manager/folder
     */
    public function doDeleteFolderProcessFilesManager($prj_uid, $path)
    {
        try {
            $filesManager = new \BusinessModel\FilesManager();
            $filesManager->deleteFolderProcessFilesManager($prj_uid, $path);
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

