<?php
/**
 * We will send a case note in the actions by email
 * @param object $httpData
 * @return void
 */
function postNote($httpData)
{
    $appUid = (isset($httpData->appUid)) ? $httpData->appUid : '';
    $usrUid = (isset($httpData->usrUid)) ? $httpData->usrUid : '';
    $appNotes = new AppNotes();
    $noteContent = addslashes($httpData->noteText);
    $result = $appNotes->postNewNote($appUid, $usrUid, $noteContent, false);

    //send the response to client
    @ini_set('implicit_flush', 1);
    ob_start();
    @ob_flush();
    @flush();
    @ob_end_flush();
    ob_implicit_flush(1);

    //send notification in background
    $noteRecipientsList = array();
    $oCase = new Cases();
    $p = $oCase->getUsersParticipatedInCase($appUid);
    foreach ($p['array'] as $key => $userParticipated) {
        $noteRecipientsList[] = $key;
    }

    $noteRecipients = implode(",", $noteRecipientsList);
    $appNotes->sendNoteNotification($appUid, $usrUid, $noteContent, $noteRecipients);
}

/**
 * We will get to the abeRequest data from actions by email
 * @param string $AbeRequestsUid
 * @return array $abeRequests
 */
function loadAbeRequest($AbeRequestsUid)
{
    $criteria = new Criteria();
    $criteria->add(AbeRequestsPeer::ABE_REQ_UID, $AbeRequestsUid);
    $resultRequests = AbeRequestsPeer::doSelectRS($criteria);
    $resultRequests->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $resultRequests->next();
    $abeRequests = $resultRequests->getRow();

    return $abeRequests;
}

/**
 * We will get the AbeConfiguration by actions by email
 * @param string $AbeConfigurationUid
 * @return array $abeConfiguration
 */
function loadAbeConfiguration($AbeConfigurationUid)
{
    $criteria = new Criteria();
    $criteria->add(AbeConfigurationPeer::ABE_UID, $AbeConfigurationUid);
    $result = AbeConfigurationPeer::doSelectRS($criteria);
    $result->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $result->next();
    $abeConfiguration = $result->getRow();

    return $abeConfiguration;
}

/**
 * We will update the request by actions by email
 * @param array $data
 * @return void
 * @throws Exception
 */
function uploadAbeRequest($data)
{
    try {
        $abeRequestsInstance = new AbeRequests();
        $abeRequestsInstance->createOrUpdate($data);
    } catch (Exception $error) {
        throw $error;
    }
}

/**
 * Function getDynaformsVars
 *
 * @access public
 * @param eter string $sProcessUID
 * @param eter boolean $bSystemVars
 * @return array
 */
function getDynaformsVars($sProcessUID, $typeVars = 'all', $bIncMulSelFields = 0)
{
    $aFields = array();
    $aFieldsNames = array();
    if ($typeVars == 'system' || $typeVars == 'all') {
        $aAux = G::getSystemConstants();
        foreach ($aAux as $sName => $sValue) {
            $aFields[] = array('sName' => $sName, 'sType' => 'system', 'sLabel' => G::LoadTranslation('ID_TINY_SYSTEM_VARIABLES'));
        }
        //we're adding the pin variable to the system list
        $aFields[] = array('sName' => 'PIN', 'sType' => 'system', 'sLabel' => G::LoadTranslation('ID_TINY_SYSTEM_VARIABLES'));

        //we're adding the app_number variable to the system list
        $aFields[] = array('sName' => 'APP_NUMBER', 'sType' => 'system', 'sLabel' => G::LoadTranslation('ID_TINY_SYSTEM_VARIABLE'), 'sUid' => '');
    }

    $aInvalidTypes = array("title", "subtitle", "file", "button", "reset", "submit", "javascript", "pmconnection");
    $aMultipleSelectionFields = array("listbox", "checkgroup");

    if ($bIncMulSelFields != 0) {
        $aInvalidTypes = array_merge($aInvalidTypes, $aMultipleSelectionFields);
    }
    // getting bpmn projects
    $oCriteria = new Criteria('workflow');
    $oCriteria->addSelectColumn(BpmnProjectPeer::PRJ_UID);
    $oCriteria->add(BpmnProjectPeer::PRJ_UID, $sProcessUID);
    $oDataset = ProcessPeer::doSelectRS($oCriteria, Propel::getDbConnection('workflow_ro'));
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    $row = $oDataset->getRow();
    if (isset($row["PRJ_UID"])) {
        if ($typeVars == 'process' || $typeVars == 'all') {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn(ProcessVariablesPeer::VAR_UID);
            $oCriteria->addSelectColumn(ProcessVariablesPeer::VAR_NAME);
            $oCriteria->addSelectColumn(ProcessVariablesPeer::VAR_FIELD_TYPE);
            $oCriteria->add(ProcessVariablesPeer::PRJ_UID, $sProcessUID);
            $oDataset = ProcessVariablesPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            while ($oDataset->next()) {
                $row = $oDataset->getRow();
                array_push($aFields, array(
                    "sName" => $row["VAR_NAME"],
                    "sType" => $row["VAR_FIELD_TYPE"],
                    "sLabel" => $row["VAR_FIELD_TYPE"]
                ));
            }
        }
        if ($typeVars == 'grid' || $typeVars == 'all') {
            $oC = new Criteria('workflow');
            $oC->addSelectColumn(DynaformPeer::DYN_CONTENT);
            $oC->add(DynaformPeer::PRO_UID, $sProcessUID);
            $oC->add(DynaformPeer::DYN_TYPE, 'xmlform');
            $oData = DynaformPeer::doSelectRS($oC);
            $oData->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oData->next();
            while ($aRowd = $oData->getRow()) {
                $dynaform = G::json_decode($aRowd['DYN_CONTENT'], true);
                if (is_array($dynaform) && sizeof($dynaform)) {
                    $items = $dynaform['items'][0]['items'];
                    foreach ($items as $key => $val) {
                        if (isset($val[0]['type']) && $val[0]['type'] == 'grid') {
                            if (sizeof($val[0]['columns'])) {
                                $columns = $val[0]['columns'];
                                foreach ($columns as $column) {
                                    array_push($aFields, array(
                                        "sName" => $column['name'],
                                        "sType" => $column['type'],
                                        "sLabel" => $column['type']
                                    ));
                                }
                            }
                        }
                    }
                }
                $oData->next();
            }
        }

    } else {
        require_once 'classes/model/Dynaform.php';
        $oCriteria = new Criteria('workflow');
        $oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
        $oCriteria->add(DynaformPeer::PRO_UID, $sProcessUID);
        $oCriteria->add(DynaformPeer::DYN_TYPE, 'xmlform');
        $oDataset = DynaformPeer::doSelectRS($oCriteria);
        $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $oDataset->next();
        while ($aRow = $oDataset->getRow()) {
            if (file_exists(PATH_DYNAFORM . PATH_SEP . $aRow['DYN_FILENAME'] . '.xml')) {
                $G_FORM = new Form($aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG);
                if (($G_FORM->type == 'xmlform') || ($G_FORM->type == '')) {
                    foreach ($G_FORM->fields as $k => $v) {
                        if (!in_array($v->type, $aInvalidTypes)) {
                            if (!in_array($k, $aFieldsNames)) {
                                $aFields[] = array('sName' => $k, 'sType' => $v->type, 'sLabel' => ($v->type != 'grid' ? $v->label : '[ ' . G::LoadTranslation('ID_GRID') . ' ]')
                                );
                                $aFieldsNames[] = $k;
                            }
                        }
                    }
                }
            }
            $oDataset->next();
        }
    }
    return $aFields;
}

/**
 * Function getGridsVars
 *
 * @access public
 * @param eter string $sProcessUID
 * @return array
 */
function getGridsVars($sProcessUID)
{
    $aFields = array();
    $aFieldsNames = array();

    require_once 'classes/model/Dynaform.php';
    $oCriteria = new Criteria('workflow');
    $oCriteria->addSelectColumn(DynaformPeer::DYN_FILENAME);
    $oCriteria->add(DynaformPeer::PRO_UID, $sProcessUID);
    $oDataset = DynaformPeer::doSelectRS($oCriteria);
    $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
    $oDataset->next();
    while ($aRow = $oDataset->getRow()) {
        $G_FORM = new Form($aRow['DYN_FILENAME'], PATH_DYNAFORM, SYS_LANG);
        if ($G_FORM->type == 'xmlform') {
            foreach ($G_FORM->fields as $k => $v) {
                if ($v->type == 'grid') {
                    if (!in_array($k, $aFieldsNames)) {
                        $aFields[] = array('sName' => $k, 'sXmlForm' => str_replace($sProcessUID . '/', '', $v->xmlGrid));
                        $aFieldsNames[] = $k;
                    }
                }
            }
        }
        $oDataset->next();
    }
    return $aFields;
}

/**
 * Function getVarsGrid returns all variables of Grid
 *
 * @access public
 * @param string proUid process ID
 * @param string dynUid dynaform ID
 * @return array
 */

function getVarsGrid($proUid, $dynUid)
{
    G::LoadClass('dynaformhandler');
    G::LoadClass('AppSolr');

    $dynaformFields = array();

    if (is_file(PATH_DATA . '/sites/' . SYS_SYS . '/xmlForms/' . $proUid . '/' . $dynUid . '.xml') && filesize(PATH_DATA . '/sites/' . SYS_SYS . '/xmlForms/' . $proUid . '/' . $dynUid . '.xml') > 0) {
        $dyn = new dynaFormHandler(PATH_DATA . '/sites/' . SYS_SYS . '/xmlForms/' . $proUid . '/' . $dynUid . '.xml');
        $dynaformFields[] = $dyn->getFields();
    }

    $dynaformFieldTypes = array();

    foreach ($dynaformFields as $aDynFormFields) {
        foreach ($aDynFormFields as $field) {

            if ($field->getAttribute('validate') == 'Int') {
                $dynaformFieldTypes[$field->nodeName] = 'Int';
            } elseif ($field->getAttribute('validate') == 'Real') {
                $dynaformFieldTypes[$field->nodeName] = 'Real';
            } else {
                $dynaformFieldTypes[$field->nodeName] = $field->getAttribute('type');
            }
        }
    }
    return $dynaformFieldTypes;
}
