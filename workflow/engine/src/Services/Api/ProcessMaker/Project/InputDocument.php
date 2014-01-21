<?php
namespace Services\Api\ProcessMaker\Project;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Project\InputDocument Api Controller
 *
 * @protected
 */
class InputDocument extends Api
{
    /**
     * @url GET /:projectUid/input-document/:inputDocumentUid
     *
     * @param string $inputDocumentUid {@min 32}{@max 32}
     * @param string $projectUid       {@min 32}{@max 32}
     */
    public function doGetInputDocument($inputDocumentUid, $projectUid)
    {
        try {
            $inputdoc = new \BusinessModel\InputDocument();

            $response = $inputdoc->getInputDocument($inputDocumentUid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url POST /:projectUid/input-document
     *
     * @param string $projectUid          {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $inp_doc_title       {@from body}
     * @param string $inp_doc_description {@from body}
     * @param string $inp_doc_form_needed {@from body}{@choice VIRTUAL,REAL,VREAL}
     * @param string $inp_doc_original    {@from body}{@choice ORIGINAL,COPY}
     * @param string $inp_doc_published   {@from body}{@choice PRIVATE}
     * @param int    $inp_doc_versioning  {@from body}{@choice 0,1}
     * @param string $inp_doc_destination_path {@from body}
     * @param string $inp_doc_tags             {@from body}
     *
     * @status 201
     */
    public function doPostInputDocument(
        $projectUid,
        $request_data,
        $inp_doc_title = "",
        $inp_doc_description = "",
        $inp_doc_form_needed = "VIRTUAL",
        $inp_doc_original = "ORIGINAL",
        $inp_doc_published = "PRIVATE",
        $inp_doc_versioning = 0,
        $inp_doc_destination_path = "",
        $inp_doc_tags = ""
    ) {
        try {
            $inputdoc = new \BusinessModel\InputDocument();

            $arrayData = $inputdoc->create($projectUid, $request_data);

            $response = $arrayData;

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:projectUid/input-document/:inputDocumentUid
     *
     * @param string $inputDocumentUid    {@min 32}{@max 32}
     * @param string $projectUid          {@min 32}{@max 32}
     * @param array  $request_data
     * @param string $inp_doc_title       {@from body}
     * @param string $inp_doc_description {@from body}
     * @param string $inp_doc_form_needed {@from body}{@choice VIRTUAL,REAL,VREAL}
     * @param string $inp_doc_original    {@from body}{@choice ORIGINAL,COPY}
     * @param string $inp_doc_published   {@from body}{@choice PRIVATE}
     * @param int    $inp_doc_versioning  {@from body}{@choice 0,1}
     * @param string $inp_doc_destination_path {@from body}
     * @param string $inp_doc_tags             {@from body}
     */
    public function doPutInputDocument(
        $inputDocumentUid,
        $projectUid,
        $request_data,
        $inp_doc_title = "",
        $inp_doc_description = "",
        $inp_doc_form_needed = "VIRTUAL",
        $inp_doc_original = "ORIGINAL",
        $inp_doc_published = "PRIVATE",
        $inp_doc_versioning = 0,
        $inp_doc_destination_path = "",
        $inp_doc_tags = ""
    ) {
        try {
            $inputdoc = new \BusinessModel\InputDocument();

            $arrayData = $inputdoc->update($inputDocumentUid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:projectUid/input-document/:inputDocumentUid
     *
     * @param string $inputDocumentUid {@min 32}{@max 32}
     * @param string $projectUid       {@min 32}{@max 32}
     */
    public function doDeleteInputDocument($inputDocumentUid, $projectUid)
    {
        try {
            $inputdoc = new \BusinessModel\InputDocument();

            $inputdoc->delete($inputDocumentUid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

