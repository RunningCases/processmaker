<?php
namespace BusinessModel;

class InputDocument
{
    private $arrayFieldDefinition = array(
        "INP_DOC_UID"              => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentUid"),

        "INP_DOC_TITLE"            => array("type" => "string", "required" => true,  "empty" => false, "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentTitle"),
        "INP_DOC_DESCRIPTION"      => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentDescription"),
        "INP_DOC_FORM_NEEDED"      => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("VIRTUAL", "REAL", "VREAL"),      "fieldNameAux" => "inputDocumentFormNeeded"),
        "INP_DOC_ORIGINAL"         => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("ORIGINAL", "COPY", "COPYLEGAL"), "fieldNameAux" => "inputDocumentOriginal"),
        "INP_DOC_PUBLISHED"        => array("type" => "string", "required" => false, "empty" => false, "defaultValues" => array("PRIVATE"),                       "fieldNameAux" => "inputDocumentPublished"),
        "INP_DOC_VERSIONING"       => array("type" => "int",    "required" => false, "empty" => false, "defaultValues" => array(0, 1),                            "fieldNameAux" => "inputDocumentVersioning"),
        "INP_DOC_DESTINATION_PATH" => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentDestinationPath"),
        "INP_DOC_TAGS"             => array("type" => "string", "required" => false, "empty" => true,  "defaultValues" => array(),                                "fieldNameAux" => "inputDocumentTags")
    );

    private $formatFieldNameInUppercase = true;

    private $arrayFieldNameForException = array(
        "processUid" => "PRO_UID"
    );

    /**
     * Constructor of the class
     *
     * return void
     */
    public function __construct()
    {
        try {
            foreach ($this->arrayFieldDefinition as $key => $value) {
                $this->arrayFieldNameForException[$value["fieldNameAux"]] = $key;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set the format of the fields name (uppercase, lowercase)
     *
     * @param bool $flag Value that set the format
     *
     * return void
     */
    public function setFormatFieldNameInUppercase($flag)
    {
        try {
            $this->formatFieldNameInUppercase = $flag;

            $this->setArrayFieldNameForException($this->arrayFieldNameForException);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set exception messages for fields
     *
     * @param array $arrayData Data with the fields
     *
     * return void
     */
    public function setArrayFieldNameForException($arrayData)
    {
        try {
            foreach ($arrayData as $key => $value) {
                $this->arrayFieldNameForException[$key] = $this->getFieldNameByFormatFieldName($value);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get the name of the field according to the format
     *
     * @param string $fieldName Field name
     *
     * return string Return the field name according the format
     */
    public function getFieldNameByFormatFieldName($fieldName)
    {
        try {
            return ($this->formatFieldNameInUppercase)? strtoupper($fieldName) : strtolower($fieldName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a InputDocument
     *
     * @param string $processUid              Unique id of Process
     * @param string $inputDocumentTitle      Title
     * @param string $inputDocumentUidExclude Unique id of InputDocument to exclude
     *
     * return bool Return true if exists the title of a InputDocument, false otherwise
     */
    public function existsTitle($processUid, $inputDocumentTitle, $inputDocumentUidExclude = "")
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "INP_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);

            if ($inputDocumentUidExclude != "") {
                $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUidExclude, \Criteria::NOT_EQUAL);
            }

            $criteria->add("CT.CON_VALUE", $inputDocumentTitle, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if doesn't exists the InputDocument in table INPUT_DOCUMENT
     *
     * @param string $inputDocumentUid      Unique id of InputDocument
     * @param string $processUid            Unique id of Process
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exists the InputDocument in table INPUT_DOCUMENT
     */
    public function throwExceptionIfNotExistsInputDocument($inputDocumentUid, $processUid, $fieldNameForException)
    {
        try {
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);

            if ($processUid != "") {
                $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            }

            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);

            if (!$rsCriteria->next()) {
                $msg = str_replace(array("{0}", "{1}"), array($fieldNameForException, $inputDocumentUid), "The Input Document with {0}: {1} does not exist");

                throw (new \Exception($msg));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Verify if exists the title of a InputDocument
     *
     * @param string $processUid              Unique id of Process
     * @param string $inputDocumentTitle      Title
     * @param string $fieldNameForException   Field name for the exception
     * @param string $inputDocumentUidExclude Unique id of InputDocument to exclude
     *
     * return void Throw exception if exists the title of a InputDocument
     */
    public function throwExceptionIfExistsTitle($processUid, $inputDocumentTitle, $fieldNameForException, $inputDocumentUidExclude = "")
    {
        try {
            if ($this->existsTitle($processUid, $inputDocumentTitle, $inputDocumentUidExclude)) {
                $msg = str_replace(array("{0}", "{1}"), array($fieldNameForException, $inputDocumentTitle), "The Input Document title with {0}: \"{1}\" already exists");

                throw (new \Exception($msg));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create InputDocument for a Process
     *
     * @param string $processUid Unique id of Process
     * @param array  $arrayData  Data
     *
     * return array Return data of the new InputDocument created
     */
    public function create($processUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            unset($arrayData["INP_DOC_UID"]);

            //Verify data
            $process = new \BusinessModel\Process();

            $process->throwExceptionIfNoExistsProcess($processUid, $this->arrayFieldNameForException["processUid"]);

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, true);

            $this->throwExceptionIfExistsTitle($processUid, $arrayData["INP_DOC_TITLE"], $this->arrayFieldNameForException["inputDocumentTitle"]);

            //Flags
            $flagDataDestinationPath = (isset($arrayData["INP_DOC_DESTINATION_PATH"]))? 1 : 0;
            $flagDataTags = (isset($arrayData["INP_DOC_TAGS"]))? 1 : 0;

            //Create
            $inputDocument = new \InputDocument();

            $arrayData["PRO_UID"] = $processUid;

            $arrayData["INP_DOC_DESTINATION_PATH"] = ($flagDataDestinationPath == 1)? $arrayData["INP_DOC_DESTINATION_PATH"] : "";
            $arrayData["INP_DOC_TAGS"] = ($flagDataTags == 1)? $arrayData["INP_DOC_TAGS"] : "";

            $inputDocumentUid = $inputDocument->create($arrayData);

            //Return
            unset($arrayData["PRO_UID"]);

            if ($flagDataDestinationPath == 0) {
                unset($arrayData["INP_DOC_DESTINATION_PATH"]);
            }

            if ($flagDataTags == 0) {
                unset($arrayData["INP_DOC_TAGS"]);
            }

            $arrayData = array_merge(array("INP_DOC_UID" => $inputDocumentUid), $arrayData);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     * @param array  $arrayData        Data
     *
     * return array Return data of the InputDocument updated
     */
    public function update($inputDocumentUid, $arrayData)
    {
        try {
            $arrayData = array_change_key_case($arrayData, CASE_UPPER);

            //Verify data
            $this->throwExceptionIfNotExistsInputDocument($inputDocumentUid, "", $this->arrayFieldNameForException["inputDocumentUid"]);

            //Load InputDocument
            $inputDocument = new \InputDocument();

            $arrayInputDocumentData = $inputDocument->load($inputDocumentUid);

            $processUid = $arrayInputDocumentData["PRO_UID"];

            //Verify data
            $process = new \BusinessModel\Process();

            $process->throwExceptionIfDataNotMetFieldDefinition($arrayData, $this->arrayFieldDefinition, $this->arrayFieldNameForException, false);

            if (isset($arrayData["INP_DOC_TITLE"])) {
                $this->throwExceptionIfExistsTitle($processUid, $arrayData["INP_DOC_TITLE"], $this->arrayFieldNameForException["inputDocumentTitle"], $inputDocumentUid);
            }

            //Update
            $arrayData["INP_DOC_UID"] = $inputDocumentUid;

            $result = $inputDocument->update($arrayData);

            //Return
            unset($arrayData["INP_DOC_UID"]);

            if (!$this->formatFieldNameInUppercase) {
                $arrayData = array_change_key_case($arrayData, CASE_LOWER);
            }

            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return void
     */
    public function delete($inputDocumentUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsInputDocument($inputDocumentUid, "", $this->arrayFieldNameForException["inputDocumentUid"]);

            //Delete
            //StepSupervisor
            $stepSupervisor = new \StepSupervisor();

            $arrayData = $stepSupervisor->loadInfo($inputDocumentUid);
            $result = $stepSupervisor->remove($arrayData["STEP_UID"]);

            //ObjectPermission
            $objectPermission = new \ObjectPermission();

            $arrayData = $objectPermission->loadInfo($inputDocumentUid);

            if (is_array($arrayData)) {
                $result = $objectPermission->remove($arrayData["OP_UID"]);
            }

            //InputDocument
            $inputDocument = new \InputDocument();

            $result = $inputDocument->remove($inputDocumentUid);

            //Step
            $step = new \Step();

            $step->removeStep("INPUT_DOCUMENT", $inputDocumentUid);

            //ObjectPermission
            $objectPermission = new \ObjectPermission();

            $objectPermission->removeByObject("INPUT", $inputDocumentUid);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get criteria for InputDocument
     *
     * return object
     */
    public function getInputDocumentCriteria()
    {
        try {
            $delimiter = \DBAdapter::getStringDelimiter();

            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_UID);
            $criteria->addAsColumn("INP_DOC_TITLE", "CT.CON_VALUE");
            $criteria->addAsColumn("INP_DOC_DESCRIPTION", "CD.CON_VALUE");
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_FORM_NEEDED);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_ORIGINAL);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_PUBLISHED);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_VERSIONING);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_DESTINATION_PATH);
            $criteria->addSelectColumn(\InputDocumentPeer::INP_DOC_TAGS);

            $criteria->addAlias("CT", \ContentPeer::TABLE_NAME);
            $criteria->addAlias("CD", \ContentPeer::TABLE_NAME);

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CT.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_CATEGORY", $delimiter . "INP_DOC_TITLE" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CT.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            $arrayCondition = array();
            $arrayCondition[] = array(\InputDocumentPeer::INP_DOC_UID, "CD.CON_ID", \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_CATEGORY", $delimiter . "INP_DOC_DESCRIPTION" . $delimiter, \Criteria::EQUAL);
            $arrayCondition[] = array("CD.CON_LANG", $delimiter . SYS_LANG . $delimiter, \Criteria::EQUAL);
            $criteria->addJoinMC($arrayCondition, \Criteria::LEFT_JOIN);

            return $criteria;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an InputDocument from a record
     *
     * @param array $record Record
     *
     * return array Return an array with data InputDocument
     */
    public function getInputDocumentDataFromRecord($record)
    {
        try {
            if ($record["INP_DOC_TITLE"] . "" == "") {
                //Load InputDocument
                $inputDocument = new \InputDocument();

                $arrayInputDocumentData = $inputDocument->load($record["INP_DOC_UID"]);

                //There is no transaltion for this Document name, try to get/regenerate the label
                $record["INP_DOC_TITLE"] = $arrayInputDocumentData["INP_DOC_TITLE"];
                $record["INP_DOC_DESCRIPTION"] = $arrayInputDocumentData["INP_DOC_DESCRIPTION"];
            }

            return array(
                $this->getFieldNameByFormatFieldName("INP_DOC_UID")              => $record["INP_DOC_UID"],
                $this->getFieldNameByFormatFieldName("INP_DOC_TITLE")            => $record["INP_DOC_TITLE"],
                $this->getFieldNameByFormatFieldName("INP_DOC_DESCRIPTION")      => $record["INP_DOC_DESCRIPTION"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_FORM_NEEDED")      => $record["INP_DOC_FORM_NEEDED"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_ORIGINAL")         => $record["INP_DOC_ORIGINAL"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_PUBLISHED")        => $record["INP_DOC_PUBLISHED"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_VERSIONING")       => (int)($record["INP_DOC_VERSIONING"]),
                $this->getFieldNameByFormatFieldName("INP_DOC_DESTINATION_PATH") => $record["INP_DOC_DESTINATION_PATH"] . "",
                $this->getFieldNameByFormatFieldName("INP_DOC_TAGS")             => $record["INP_DOC_TAGS"] . ""
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of an InputDocument
     *
     * @param string $inputDocumentUid Unique id of InputDocument
     *
     * return array Return an array with data of an InputDocument
     */
    public function getInputDocument($inputDocumentUid)
    {
        try {
            //Verify data
            $this->throwExceptionIfNotExistsInputDocument($inputDocumentUid, "", $this->arrayFieldNameForException["inputDocumentUid"]);

            //Get data
            $criteria = $this->getInputDocumentCriteria();

            $criteria->add(\InputDocumentPeer::INP_DOC_UID, $inputDocumentUid, \Criteria::EQUAL);

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            $rsCriteria->next();

            $row = $rsCriteria->getRow();

            return $this->getInputDocumentDataFromRecord($row);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get data of Cases InputDocument
     *
     * @param string $caseUid
     * @param string $userUid
     *
     * return array Return an array with data of an InputDocument
     */
    public function getCasesInputDocument($caseUid, $userUid)
    {
        try {

            ///
            global $G_PUBLISH;
            $defaultEndpoint = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/sys' . SYS_SYS . '/' . SYS_LANG . '/classic/services/wsdl2';

            $endpoint = isset( $_SESSION['END_POINT'] ) ? $_SESSION['END_POINT'] : $defaultEndpoint;

            $sessionId = isset( $_SESSION['SESSION_ID'] ) ? $_SESSION['SESSION_ID'] : '';

            //Apply proxy settings
            $proxy = array ();
            $sysConf = \System::getSystemConfiguration();
            if ($sysConf['proxy_host'] != '') {
                $proxy['proxy_host'] = $sysConf['proxy_host'];
                if ($sysConf['proxy_port'] != '') {
                    $proxy['proxy_port'] = $sysConf['proxy_port'];
                }
                if ($sysConf['proxy_user'] != '') {
                    $proxy['proxy_login'] = $sysConf['proxy_user'];
                }
                if ($sysConf['proxy_pass'] != '') {
                    $proxy['proxy_password'] = $sysConf['proxy_pass'];
                }
            }

            @$client = new \SoapClient( $endpoint, $proxy );
            ///


            $caseId = $caseUid;
            $sessionId = $userUid;
            $params = array ('sessionId' => $sessionId,'caseId' => $caseId);

            $wsResponse = $client->__SoapCall( 'InputDocumentList', array ($params));

            //g::pr($wsResponse);
            $result = \G::PMWSCompositeResponse( $wsResponse, 'documents' );

            $G_PUBLISH = new \Publisher();
            $rows[] = array ('guid' => 'char','name' => 'char','processId' => 'char');

            if (is_array( $result )) {
                foreach ($result as $key => $item) {
                    if (isset( $item->item )) {
                        foreach ($item->item as $index => $val) {
                            if ($val->key == 'guid') {
                                $guid = $val->value;
                            }
                            if ($val->key == 'filename') {
                                $filename = $val->value;
                            }
                            if ($val->key == 'docId') {
                                $docId = $val->value;
                            }
                            if ($val->key == 'version') {
                                $version = $val->value;
                            }
                            if ($val->key == 'createDate') {
                                $createDate = $val->value;
                            }
                            if ($val->key == 'createBy') {
                                $createBy = $val->value;
                            }
                            if ($val->key == 'type') {
                                $type = $val->value;
                            }
                            if ($val->key == 'link') {
                                $link = $val->value;
                            }
                        }
                    } elseif (is_array( $item )) {
                        foreach ($item as $index => $val) {
                            if ($val->key == 'guid') {
                                $guid = $val->value;
                            }
                            if ($val->key == 'filename') {
                                $filename = $val->value;
                            }
                            if ($val->key == 'docId') {
                                $docId = $val->value;
                            }
                            if ($val->key == 'version') {
                                $version = $val->value;
                            }
                            if ($val->key == 'createDate') {
                                $createDate = $val->value;
                            }
                            if ($val->key == 'createBy') {
                                $createBy = $val->value;
                            }
                            if ($val->key == 'type') {
                                $type = $val->value;
                            }
                            if ($val->key == 'link') {
                                $link = $val->value;
                            }
                        }
                    } else {
                        if (isset( $item->guid )) {
                            $guid = $item->guid;
                        }
                        if (isset( $item->filename )) {
                            $filename = $item->filename;
                        }
                        if (isset( $item->docId )) {
                            $docId = $item->docId;
                        }
                        if (isset( $item->version )) {
                            $version = $item->version;
                        }
                        if (isset( $item->createDate )) {
                            $createDate = $item->createDate;
                        }
                        if (isset( $item->createBy )) {
                            $createBy = $item->createBy;
                        }
                        if (isset( $item->type )) {
                            $type = $item->type;
                        }
                        if (isset( $item->link )) {
                            $link = $item->link;
                        }
                    }
                    $rows[] = array ('guid' => $guid,'filename' => $filename,'docId' => $docId,'version' => $version,'createDate' => $createDate,'createBy' => $createBy,'type' => $type,'link' => $link);
                }
            }
            return $rows;
            /* testing//
                global $_DBArray;
                $_DBArray = (isset( $_SESSION['_DBArray'] ) ? $_SESSION['_DBArray'] : '');
                $_DBArray['inputDocument'] = $rows;
                $documentArray = array ();
                $documentArray[] = array ('guid' => 'char','filename' => 'char'
                );
                if (isset( $_DBArray['inputDocument'] )) {
                    foreach ($_DBArray['inputDocument'] as $key => $val) {
                        if ($key != 0 && isset( $val['filename'] )) {
                            $documentArray[] = array ('guid' => $val['guid'],'filename' => $val['filename']
                            );
                        }
                    }
                }
                if (isset( $_DBArray['outputDocument'] )) {
                    foreach ($_DBArray['outputDocument'] as $key => $val) {
                        if ($key != 0 && isset( $val['filename'] )) {
                            $documentArray[] = array ('guid' => $val['guid'],'filename' => $val['filename']
                            );
                        }
                    }
                }
                $_DBArray['documents'] = $documentArray;
                $_DBArray['WS_TMP_CASE_UID'] = $frm["CASE_ID"];
                $_SESSION['_DBArray'] = $_DBArray;

                G::LoadClass( 'ArrayPeer' );
                $c = new Criteria( 'dbarray' );
                $c->setDBArrayTable( 'inputDocument' );
                $c->addAscendingOrderByColumn( 'name' );
                $G_PUBLISH->AddContent( 'propeltable', 'paged-table', 'setup/wsrInputDocumentList', $c );

            } elseif (is_object( $result )) {
                $_SESSION['WS_SESSION_ID'] = '';
                $fields['status_code'] = $result->status_code;
                $fields['message'] = $result->message;
                $fields['time_stamp'] = date( "Y-m-d H:i:s" );
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'setup/wsShowResult', null, $fields );
            }
            G::RenderPage( 'publish', 'raw' );
            break;*/

        } catch (\Exception $e) {
            throw $e;
        }
    }

}

