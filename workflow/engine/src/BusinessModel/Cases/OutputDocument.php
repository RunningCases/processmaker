<?php
namespace BusinessModel\Cases;

class OutputDocument
{
    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     *
     * return array Return an array with data of an OutputDocument
     */
    public function getCasesOutputDocuments($applicationUid, $userUid)
    {
        try {
            \G::LoadClass('case');
            $oCase = new \Cases();
            $fields = $oCase->loadCase( $applicationUid );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCriteria = new \BusinessModel\Cases();
            $oCriteria->getAllGeneratedDocumentsCriteria( $sProcessUID, $applicationUid, $sTaskUID, $userUid);
            $result = array ();
            global $_DBArray;
            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['DOWNLOAD_FILE'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    $result[] = $docrow;
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $userUid
     * @param string $outputDocumentUid
     *
     * return array Return an array with data of an OutputDocument
     */
    public function getCasesOutputDocument($applicationUid, $userUid, $outputDocumentUid)
    {
        try {
            $sApplicationUID = $applicationUid;
            $sUserUID = $userUid;
            \G::LoadClass('case');
            $oCase = new \Cases();
            $fields = $oCase->loadCase( $sApplicationUID );
            $sProcessUID = $fields['PRO_UID'];
            $sTaskUID = '';
            $oCaseRest = new \BusinessModel\Cases();
            $oCaseRest->getAllGeneratedDocumentsCriteria( $sProcessUID, $sApplicationUID, $sTaskUID, $sUserUID );
            $result = array ();
            global $_DBArray;
            foreach ($_DBArray['outputDocuments'] as $key => $row) {
                if (isset( $row['DOC_VERSION'] )) {
                    $docrow = array ();
                    $docrow['app_doc_uid'] = $row['APP_DOC_UID'];
                    $docrow['app_doc_filename'] = $row['DOWNLOAD_FILE'];
                    $docrow['doc_uid'] = $row['DOC_UID'];
                    $docrow['app_doc_version'] = $row['DOC_VERSION'];
                    $docrow['app_doc_create_date'] = $row['CREATE_DATE'];
                    $docrow['app_doc_create_user'] = $row['CREATED_BY'];
                    $docrow['app_doc_type'] = $row['TYPE'];
                    $docrow['app_doc_index'] = $row['APP_DOC_INDEX'];
                    $docrow['app_doc_link'] = 'cases/' . $row['DOWNLOAD_LINK'];
                    if ($docrow['app_doc_uid'] == $outputDocumentUid) {
                        $result = $docrow;
                    }
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete OutputDocument
     *
     * @param string $outputDocumentUid
     *
     * return array Return an array with data of an OutputDocument
     */
    public function removeOutputDocument($outputDocumentUid)
    {
        try {
            $oAppDocument = \AppDocumentPeer::retrieveByPK( $outputDocumentUid, 1 );
            if (is_null( $oAppDocument ) || $oAppDocument->getAppDocStatus() == 'DELETED') {
                throw (new \Exception('This row doesn\'t exist!'));
            }
            \G::LoadClass('wsBase');
            $ws = new \wsBase();
            $ws->removeDocument($outputDocumentUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases OutputDocument
     *
     * @param string $applicationUid
     * @param string $outputDocumentUid
     * @param string $userUid
     *
     * return array Return an array with data of an OutputDocument
     */
    public function addCasesOutputDocument($applicationUid, $outputDocumentUid, $userUid)
    {
        try {
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
