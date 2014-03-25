<?php
namespace Services\Api\ProcessMaker\Cases;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

/**
 * Cases\InputDocument Api Controller
 *
 * @protected
 */
class InputDocument extends Api
{
    /**
     * @url GET /:cas_uid/input-documents
     *
     * @param string $cas_uid     {@min 32}{@max 32}
     */
    public function doGetInputDocuments($cas_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \BusinessModel\Cases\InputDocument();
            $response = $inputDocument->getCasesInputDocuments($cas_uid, $userUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:cas_uid/input-document/:inp_doc_uid
     *
     * @param string $cas_uid     {@min 32}{@max 32}
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     */
    public function doGetInputDocument($cas_uid, $inp_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \BusinessModel\Cases\InputDocument();
            $response = $inputDocument->getCasesInputDocument($cas_uid, $userUid, $inp_doc_uid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url DELETE /:cas_uid/input-document/:inp_doc_uid
     *
     * @param string $cas_uid     {@min 32}{@max 32}
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     */
    public function doDeleteInputDocument($cas_uid, $inp_doc_uid)
    {
        try {
            $inputDocument = new \BusinessModel\Cases\InputDocument();
            $inputDocument->removeInputDocument($inp_doc_uid);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
    /**
     * @url POST /:cas_uid/input-document
     *
     * @param string $cas_uid         {@min 32}{@max 32}
     * @param string $inp_doc_uid     {@min 32}{@max 32}
     */
    public function doPostInputDocument($cas_uid, $inp_doc_uid)
    {
        try {
            $userUid = $this->getUserId();
            $inputDocument = new \BusinessModel\Cases\InputDocument();
            $response = $inputDocument->addCasesInputDocument($cas_uid, $inp_doc_uid, $userUid);
            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}


