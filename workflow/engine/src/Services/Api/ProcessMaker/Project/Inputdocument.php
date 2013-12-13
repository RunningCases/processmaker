<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\Inputdocument Api Controller
 *
 * @protected
 */
class Inputdocument extends Api
{
    /**
     * @url GET /:projectUid/input-document/:inputDocumentUid
     */
    public function doGetInputDocument($inputDocumentUid, $projectUid)
    {
        try {
            $inputdoc = new \BusinessModel\Inputdocument();

            $response = $inputdoc->getInputDocument($inputDocumentUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/input-document
     *
     * @param string $projectUid
     * @param InputdocumentPostStructure $request_data
     *
     * @status 201
     */
    public function doPostInputDocument($projectUid, InputdocumentPostStructure $request_data = null)
    {
        try {
            $inputdoc = new \BusinessModel\Inputdocument();

            $arrayData = $inputdoc->getArrayDataFromRequestData($request_data);

            $arrayData = $inputdoc->create($projectUid, $arrayData);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/input-document/:inputDocumentUid
     *
     * @param string $inputDocumentUid
     * @param string $projectUid
     * @param InputdocumentPutStructure $request_data
     */
    public function doPutInputDocument($inputDocumentUid, $projectUid, InputdocumentPutStructure $request_data = null)
    {
        try {
            $inputdoc = new \BusinessModel\Inputdocument();

            $arrayData = $inputdoc->getArrayDataFromRequestData($request_data);

            $arrayData = $inputdoc->update($inputDocumentUid, $arrayData);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/input-document/:inputDocumentUid
     */
    public function doDeleteInputDocument($inputDocumentUid, $projectUid)
    {
        try {
            $inputdoc = new \BusinessModel\Inputdocument();

            $inputdoc->delete($inputDocumentUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

class InputdocumentPostStructure
{
    /**
     * @var string {@from body}{@required true}
     */
    public $inp_doc_title;

    /**
     * @var string {@from body}
     */
    public $inp_doc_description;

    /**
     * @var string {@from body}{@choice VIRTUAL,REAL,VREAL}
     */
    public $inp_doc_form_needed;

    /**
     * @var string {@from body}{@choice ORIGINAL,COPY}
     */
    public $inp_doc_original;

    /**
     * @var string {@from body}{@choice PRIVATE}
     */
    public $inp_doc_published;

    /**
     * @var int {@from body}{@choice 0,1}
     */
    public $inp_doc_versioning;

    /**
     * @var string {@from body}
     */
    public $inp_doc_destination_path;

    /**
     * @var string {@from body}
     */
    public $inp_doc_tags;
}

class InputdocumentPutStructure
{
    /**
     * @var string {@from body}
     */
    public $inp_doc_title;

    /**
     * @var string {@from body}
     */
    public $inp_doc_description;

    /**
     * @var string {@from body}{@choice VIRTUAL,REAL,VREAL}
     */
    public $inp_doc_form_needed;

    /**
     * @var string {@from body}{@choice ORIGINAL,COPY}
     */
    public $inp_doc_original;

    /**
     * @var string {@from body}{@choice PRIVATE}
     */
    public $inp_doc_published;

    /**
     * @var int {@from body}{@choice 0,1}
     */
    public $inp_doc_versioning;

    /**
     * @var string {@from body}
     */
    public $inp_doc_destination_path;

    /**
     * @var string {@from body}
     */
    public $inp_doc_tags;
}

