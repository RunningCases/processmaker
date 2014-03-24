<?php
namespace Services\Api\ProcessMaker\Cases;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Cases\OutputDocument Api Controller
 *
 * @protected
 */
class OutputDocument extends Api
{
    /**
     * @url GET /:cas_uid/output-documents
     *
     * @param string $cas_uid     {@min 32}{@max 32}
     */
    public function doGetOutputDocuments($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $outputDocument = new \BusinessModel\Cases\OutputDocument();
            $response = $outputDocument->getCasesOutputDocuments($cas_uid, $userUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:cas_uid/output-document/:out_doc_uid
     *
     * @param string $cas_uid     {@min 32}{@max 32}
     * @param string $out_doc_uid     {@min 32}{@max 32}
     */
    public function doGetOutputDocument($cas_uid, $out_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $outputDocument = new \BusinessModel\Cases\OutputDocument();
            $response = $outputDocument->getCasesOutputDocument($cas_uid, $userUid, $out_doc_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:cas_uid/output-document/:out_doc_uid
     *
     * @param string $cas_uid     {@min 32}{@max 32}
     * @param string $out_doc_uid     {@min 32}{@max 32}
     */
    public function doDeleteOutputDocument($cas_uid, $out_doc_uid)
    {
        try {
            $outputDocument = new \BusinessModel\Cases\OutputDocument();
            $outputDocument->removeOutputDocument($out_doc_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    /**
     * @url POST /:cas_uid/output-document
     *
     * @param string $cas_uid         {@min 32}{@max 32}
     * @param string $out_doc_uid     {@min 32}{@max 32}
     */
    public function doPostOutputDocument($cas_uid, $out_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $outputDocument = new \BusinessModel\Cases\OutputDocument();
            $response = $outputDocument->addCasesOutputDocument($cas_uid, $out_doc_uid, $userUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}