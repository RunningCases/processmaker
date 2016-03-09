<?php

namespace ProcessMaker\BusinessModel;

/**
 * Description of ActionsByEmailService
 *
 */
class ActionsByEmail
{

    public function saveConfiguration($params)
    {
        if (\PMLicensedFeatures
                ::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
            $feature = $params['ActionsByEmail'];
            switch ($feature['type']) {
                case 'configuration':
                    require_once 'classes/model/AbeConfiguration.php';
                    $abeConfigurationInstance = new \AbeConfiguration();
                    if(isset($feature['fields']['ABE_CASE_NOTE_IN_RESPONSE'])){
                        $noteValues = json_decode($feature['fields']['ABE_CASE_NOTE_IN_RESPONSE']);
                        foreach ($noteValues as $value) {
                            $feature['fields']['ABE_CASE_NOTE_IN_RESPONSE'] = $value;
                        }
                    }
                    $abeConfigurationInstance->createOrUpdate($feature['fields']);
                    break;
                default:
                    break;
            }
        }
    }

    public function loadConfiguration($params)
    {
        if ($params['type'] != 'activity'
            || !\PMLicensedFeatures
                ::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0='))
        {
            return false;
        }
        require_once 'classes/model/AbeConfiguration.php';

        $criteria = new \Criteria();
        $criteria->add(\AbeConfigurationPeer::PRO_UID, $params['PRO_UID']);
        $criteria->add(\AbeConfigurationPeer::TAS_UID, $params['TAS_UID']);
        $result = \AbeConfigurationPeer::doSelectRS($criteria);
        $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $configuration = array();
        if ($configuration = $result->getRow()) {
            $configuration['ABE_UID'] = $configuration['ABE_UID'];
            $configuration['ABE_TYPE'] = $configuration['ABE_TYPE'];
            $configuration['DYN_UID'] = $configuration['DYN_UID'];
            $configuration['ABE_TEMPLATE'] = $configuration['ABE_TEMPLATE'];
            $configuration['ABE_SUBJECT_FIELD'] = $configuration['ABE_SUBJECT_FIELD'];
            $configuration['ABE_EMAIL_FIELD'] = $configuration['ABE_EMAIL_FIELD'];
            $configuration['ABE_ACTION_FIELD'] = $configuration['ABE_ACTION_FIELD'];
            $configuration['ABE_MAILSERVER_OR_MAILCURRENT'] = $configuration['ABE_MAILSERVER_OR_MAILCURRENT'];
            $configuration['ABE_CASE_NOTE_IN_RESPONSE'] = $configuration['ABE_CASE_NOTE_IN_RESPONSE'] ? '["1"]' : '[]';
            $configuration['ABE_CUSTOM_GRID'] = unserialize($configuration['ABE_CUSTOM_GRID']);
        }
        $configuration['feature'] = 'ActionsByEmail';
        $configuration['prefix'] = 'abe';
        $configuration['PRO_UID'] = $params['PRO_UID'];
        $configuration['TAS_UID'] = $params['TAS_UID'];
        $configuration['SYS_LANG'] = SYS_LANG;
        return $configuration;
    }

    public function editTemplate(array $arrayData)
    {
        //Action Validations
        if (!isset($arrayData['TEMPLATE'])) {
            $arrayData['TEMPLATE'] = '';
        }

        if ($arrayData['TEMPLATE'] == '') {
            throw new Exception(\G::LoadTranslation('ID_TEMPLATE_PARAMETER_EMPTY'));
        }

        $data = array(
            'CONTENT' => file_get_contents(
                PATH_DATA_MAILTEMPLATES . $arrayData['PRO_UID'] . PATH_SEP . $arrayData['TEMPLATE']
            ),
            'TEMPLATE' => $arrayData['TEMPLATE'],
        );

        global $G_PUBLISH;

        $G_PUBLISH = new \Publisher();
        $G_PUBLISH->AddContent('xmlform', 'xmlform', 'actionsByEmail/actionsByEmail_FileEdit', '', $data);

        \G::RenderPage('publish', 'raw');
        die();
    }

    public function updateTemplate(array $arrayData)
    {
        //Action Validations
        if (!isset($arrayData['TEMPLATE'])) {
            $arrayData['TEMPLATE'] = '';
        }

        if (!isset($arrayData['CONTENT'])) {
            $arrayData['CONTENT'] = '';
        }

        if ($arrayData['TEMPLATE'] == '') {
            throw new Exception(\G::LoadTranslation('ID_TEMPLATE_PARAMETER_EMPTY'));
        }

        $templateFile = fopen(PATH_DATA_MAILTEMPLATES . $arrayData['PRO_UID'] . PATH_SEP . $arrayData['TEMPLATE'], 'w');
        $content = stripslashes($arrayData['CONTENT']);
        $content = str_replace('@amp@', '&', $content);
        $content = base64_decode($content);

        fwrite($templateFile, $content);
        fclose($templateFile);
    }

    public function loadFields(array $arrayData)
    {
        if (!isset($arrayData['DYN_UID'])) {
            $arrayData['DYN_UID'] = '';
        }

        if (!isset($arrayData['PRO_UID'])) {
            $arrayData['PRO_UID'] = '';
        }

        $response->emailFields = array();
        $response->actionFields = array();

        if ($arrayData['PRO_UID'] != '' && $arrayData['DYN_UID']) {
            $dynaform = new Form($arrayData['PRO_UID'] . PATH_SEP . $arrayData['DYN_UID'], PATH_DYNAFORM, SYS_LANG, false);

            foreach ($dynaform->fields as $fieldName => $data) {
                switch ($data->type) {
                    case 'text':
                    case 'suggest':
                    case 'hidden':
                    case 'textarea':
                        $response->emailFields[] = array('value' => $data->name, 'label' => $data->label . ' (@@' . $data->name . ')');
                        break;
                    case 'dropdown':
                    case 'radiogroup':
                    case 'yesno':
                    case 'checkbox':
                        $response->actionFields[] = array('value' => $data->name, 'label' => $data->label . ' (@@' . $data->name . ')');
                        break;
                }
            }
        }

        //Return
        return $response;
    }

    public function saveConfiguration2(array $arrayData)
    {
        if (!isset($arrayData['ABE_UID'])) {
            $arrayData['ABE_UID'] = '';
        }

        if (!isset($arrayData['PRO_UID'])) {
            $arrayData['PRO_UID'] = '';
        }

        if (!isset($arrayData['TAS_UID'])) {
            $arrayData['TAS_UID'] = '';
        }

        if (!isset($arrayData['ABE_TYPE'])) {
            $arrayData['ABE_TYPE'] = '';
        }

        if (!isset($arrayData['ABE_TEMPLATE'])) {
            $arrayData['ABE_TEMPLATE'] = '';
        }

        if (!isset($arrayData['DYN_UID'])) {
            $arrayData['DYN_UID'] = '';
        }

        if (!isset($arrayData['ABE_EMAIL_FIELD'])) {
            $arrayData['ABE_EMAIL_FIELD'] = '';
        }

        if (!isset($arrayData['ABE_ACTION_FIELD'])) {
            $arrayData['ABE_ACTION_FIELD'] = '';
        }

        if (!isset($arrayData['ABE_CASE_NOTE_IN_RESPONSE'])) {
            $arrayData['ABE_CASE_NOTE_IN_RESPONSE'] = 0;
        }

        if ($arrayData['PRO_UID'] == '') {
            throw new Exception(\G::LoadTranslation('ID_PRO_UID_PARAMETER_IS_EMPTY'));
        }

        if ($arrayData['TAS_UID'] == '') {
            throw new Exception(\G::LoadTranslation('ID_TAS_UID_PARAMETER_IS_EMPTY'));
        }

        $abeConfigurationInstance = new \AbeConfiguration();

        if ($arrayData['ABE_TYPE'] != '') {
            if ($arrayData['DYN_UID'] == '') {
                throw new Exception(\G::LoadTranslation('ID_DYN_UID_PARAMETER_IS_EMPTY'));
            }

            try {
                $response->ABE_UID = $abeConfigurationInstance->createOrUpdate($arrayData);
            } catch (\Exception $error) {
                throw $error;
            }
        } else {
            try {
                $abeConfigurationInstance->deleteByTasUid($arrayData['TAS_UID']);
                $response->ABE_UID = '';
            } catch (\Exception $error) {
                throw $error;
            }
        }

        //Return
        return $response;
    }

    public function loadActionByEmail(array $arrayData)
    {
        $criteria = new \Criteria();
        $criteria->addSelectColumn('COUNT(*)');

        $criteria->addJoin(\AbeConfigurationPeer::ABE_UID, \AbeRequestsPeer::ABE_UID);
        $criteria->addJoin(\AppDelegationPeer::APP_UID, \AbeRequestsPeer::APP_UID);
        $criteria->addJoin(\AppDelegationPeer::DEL_INDEX, \AbeRequestsPeer::DEL_INDEX);
        $result = \AbeConfigurationPeer::doSelectRS($criteria);
        $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $totalCount = $result->getRow();
        $totalCount = $totalCount['COUNT(*)'];

        $criteria = new \Criteria();
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::PRO_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::TAS_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_UPDATE_DATE);
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_TEMPLATE);
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_ACTION_FIELD);
        $criteria->addSelectColumn(\AbeConfigurationPeer::DYN_UID);

        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_UID);
        $criteria->addSelectColumn(\AbeRequestsPeer::APP_UID);
        $criteria->addSelectColumn(\AbeRequestsPeer::DEL_INDEX);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_SENT_TO);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_STATUS);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_SUBJECT);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_ANSWERED);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_BODY);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_DATE);

        $criteria->addSelectColumn(\ApplicationPeer::APP_NUMBER);

        $criteria->addSelectColumn(\AppDelegationPeer::DEL_PREVIOUS);

        $criteria->addJoin(\AbeConfigurationPeer::ABE_UID, \AbeRequestsPeer::ABE_UID);
        $criteria->addJoin(\ApplicationPeer::APP_UID, \AbeRequestsPeer::APP_UID);

        $criteria->addJoin(\AppDelegationPeer::APP_UID, \AbeRequestsPeer::APP_UID);
        $criteria->addJoin(\AppDelegationPeer::DEL_INDEX, \AbeRequestsPeer::DEL_INDEX);
        $criteria->addDescendingOrderByColumn(\AbeRequestsPeer::ABE_REQ_DATE);
        $criteria->setLimit($arrayData['limit']);
        $criteria->setOffset($arrayData['start']);
        $result = \AbeConfigurationPeer::doSelectRS($criteria);
        $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $data = Array();
        $arrayPro = Array();
        $arrayTAS = Array();
        $index = 0;

        while ($result->next()) {
            $data[] = $result->getRow();
            $criteriaRes = new \Criteria();

            $criteriaRes->addSelectColumn(\AbeResponsesPeer::ABE_RES_UID);
            $criteriaRes->addSelectColumn(\AbeResponsesPeer::ABE_RES_CLIENT_IP);
            $criteriaRes->addSelectColumn(\AbeResponsesPeer::ABE_RES_DATA);
            $criteriaRes->addSelectColumn(\AbeResponsesPeer::ABE_RES_STATUS);
            $criteriaRes->addSelectColumn(\AbeResponsesPeer::ABE_RES_MESSAGE);

            $criteriaRes->add(\AbeResponsesPeer::ABE_REQ_UID, $data[$index]['ABE_REQ_UID']);

            $resultRes = \AbeResponsesPeer::doSelectRS($criteriaRes);
            $resultRes->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $resultRes->next();
            $dataRes = Array();

            if ($dataRes = $resultRes->getRow()) {
                $data[$index]['ABE_RES_UID'] = $dataRes['ABE_RES_UID'];
                $data[$index]['ABE_RES_CLIENT_IP'] = $dataRes['ABE_RES_CLIENT_IP'];
                $data[$index]['ABE_RES_DATA'] = $dataRes['ABE_RES_DATA'];
                $data[$index]['ABE_RES_STATUS'] = $dataRes['ABE_RES_STATUS'];
                $data[$index]['ABE_RES_MESSAGE'] = $dataRes['ABE_RES_MESSAGE'];
            } else {
                $data[$index]['ABE_RES_UID'] = '';
                $data[$index]['ABE_RES_CLIENT_IP'] = '';
                $data[$index]['ABE_RES_DATA'] = '';
                $data[$index]['ABE_RES_STATUS'] = '';
                $data[$index]['ABE_RES_MESSAGE'] = '';
            }

            $criteriaRes = new \Criteria();

            $criteriaRes->addSelectColumn(\AppDelegationPeer::USR_UID);
            $criteriaRes->addSelectColumn(\UsersPeer::USR_FIRSTNAME);
            $criteriaRes->addSelectColumn(\UsersPeer::USR_LASTNAME);

            $criteria->addJoin(\AppDelegationPeer::APP_UID, $data[$index]['APP_UID']);
            $criteria->addJoin(\AppDelegationPeer::DEL_INDEX, $data[$index]['DEL_PREVIOUS']);
            $criteria->addJoin(\AppDelegationPeer::USR_UID, \UsersPeer::USR_UID);
            $resultRes = \AppDelegationPeer::doSelectRS($criteriaRes);
            $resultRes->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $resultRes->next();

            if ($dataRes = $resultRes->getRow()) {
                $data[$index]['USER'] = $dataRes['USR_FIRSTNAME'] . ' ' . $dataRes['USR_LASTNAME'];
            } else {
                $data[$index]['USER'] = '';
            }

            $data[$index]['ABE_REQ_ANSWERED'] = ($data[$index]['ABE_REQ_ANSWERED'] == 1) ? 'YES' : 'NO';
            $index++;
        }

        $response = array();
        $response['totalCount'] = $totalCount;
        $response['data'] = $data;

        //Return
        return $response;
    }

    public function forwardMail(array $arrayData)
    {
        if (!isset($arrayData['REQ_UID'])) {
            $arrayData['REQ_UID'] = '';
        }

        $criteria = new \Criteria();
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::PRO_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::TAS_UID);

        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_UID);
        $criteria->addSelectColumn(\AbeRequestsPeer::APP_UID);
        $criteria->addSelectColumn(\AbeRequestsPeer::DEL_INDEX);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_SENT_TO);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_SUBJECT);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_BODY);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_ANSWERED);
        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_STATUS);

        $criteria->addSelectColumn(\AppDelegationPeer::DEL_FINISH_DATE);

        $criteria->add(\AbeRequestsPeer::ABE_REQ_UID, $arrayData['REQ_UID']);
        $criteria->addJoin(\AbeRequestsPeer::ABE_UID, \AbeConfigurationPeer::ABE_UID);
        $criteria->addJoin(\AppDelegationPeer::APP_UID, \AbeRequestsPeer::APP_UID);
        $criteria->addJoin(\AppDelegationPeer::DEL_INDEX, \AbeRequestsPeer::DEL_INDEX);
        $resultRes = AbeRequestsPeer::doSelectRS($criteria);
        $resultRes->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        $resultRes->next();
        $dataRes = Array();

        if ($dataRes = $resultRes->getRow()) {
            if (is_null($dataRes['DEL_FINISH_DATE'])) {
                \G::LoadClass('spool');

                $configuration = new \Configuration();
                $sDelimiter = \DBAdapter::getStringDelimiter();
                $criteria = new \Criteria('workflow');
                $criteria->add(\ConfigurationPeer::CFG_UID, 'Emails');
                $criteria->add(\ConfigurationPeer::OBJ_UID, '');
                $criteria->add(\ConfigurationPeer::PRO_UID, '');
                $criteria->add(\ConfigurationPeer::USR_UID, '');
                $criteria->add(\ConfigurationPeer::APP_UID, '');

                if (\ConfigurationPeer::doCount($criteria) == 0) {
                    $configuration->create(array('CFG_UID' => 'Emails', 'OBJ_UID' => '', 'CFG_VALUE' => '', 'PRO_UID' => '', 'USR_UID' => '', 'APP_UID' => ''));
                    $newConfiguration = array();
                } else {
                    $newConfiguration = $configuration->load('Emails', '', '', '', '');

                    if ($newConfiguration['CFG_VALUE'] != '') {
                        $newConfiguration = unserialize($newConfiguration['CFG_VALUE']);
                    } else {
                        $newConfiguration = array();
                    }
                }

                $spool = new \spoolRun();
                $spool->setConfig(array(
                    'MESS_ENGINE' => $newConfiguration['MESS_ENGINE'],
                    'MESS_SERVER' => $newConfiguration['MESS_SERVER'],
                    'MESS_PORT' => $newConfiguration['MESS_PORT'],
                    'MESS_ACCOUNT' => $newConfiguration['MESS_ACCOUNT'],
                    'MESS_PASSWORD' => $newConfiguration['MESS_PASSWORD'],
                    'SMTPAuth' => $newConfiguration['MESS_RAUTH']
                ));

                $spool->create(array(
                    'msg_uid' => '',
                    'app_uid' => $dataRes['APP_UID'],
                    'del_index' => $dataRes['DEL_INDEX'],
                    'app_msg_type' => 'TEST',
                    'app_msg_subject' => $dataRes['ABE_REQ_SUBJECT'],
                    'app_msg_from' => $newConfiguration['MESS_ACCOUNT'],
                    'app_msg_to' => $dataRes['ABE_REQ_SENT_TO'],
                    'app_msg_body' => $dataRes['ABE_REQ_BODY'],
                    'app_msg_cc' => '',
                    'app_msg_bcc' => '',
                    'app_msg_attach' => '',
                    'app_msg_template' => '',
                    'app_msg_status' => 'pending'
                ));

                if ($spool->sendMail()) {
                    $dataRes['ABE_REQ_STATUS'] = 'SENT';

                    $message = G::LoadTranslation('ID_EMAIL_RESENT_TO') . ': '. $dataRes['ABE_REQ_SENT_TO'];
                } else {
                    $dataRes['ABE_REQ_STATUS'] = 'ERROR';
                    $message = G::LoadTranslation('ID_THERE_PROBLEM_SENDING_EMAIL') . ': '. $dataRes['ABE_REQ_SENT_TO'] . ', ' . G::LoadTranslation('ID_PLEASE_TRY_LATER');
                }

                try {
                    $abeRequestsInstance = new \AbeRequests();
                    $abeRequestsInstance->createOrUpdate($dataRes);
                } catch (\Exception $error) {
                    throw $error;
                }
            } else {
                $message =  \G::LoadTranslation('ID_UNABLE_TO_SEND_EMAIL');
            }
        } else {
             $message = \G::LoadTranslation('ID_UNEXPECTED_ERROR_OCCURRED_PLEASE');
        }

        //Return
        return $message;
    }

    public function viewForm(array $arrayData)
    {
        //coment
        if (!isset($arrayData['REQ_UID'])) {
            $arrayData['REQ_UID'] = '';
        }

        $criteria = new \Criteria();
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::PRO_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::TAS_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::DYN_UID);
        $criteria->addSelectColumn(\AbeConfigurationPeer::ABE_ACTION_FIELD);

        $criteria->addSelectColumn(\AbeRequestsPeer::ABE_REQ_UID);
        $criteria->addSelectColumn(\AbeRequestsPeer::APP_UID);
        $criteria->addSelectColumn(\AbeRequestsPeer::DEL_INDEX);

        $criteria->addSelectColumn(\AbeResponsesPeer::ABE_RES_UID);
        $criteria->addSelectColumn(\AbeResponsesPeer::ABE_RES_DATA);

        $criteria->add(\AbeRequestsPeer::ABE_REQ_UID, $arrayData['REQ_UID']);
        $criteria->addJoin(\AbeRequestsPeer::ABE_UID, \AbeConfigurationPeer::ABE_UID);
        $criteria->addJoin(\AbeResponsesPeer::ABE_REQ_UID, \AbeRequestsPeer::ABE_REQ_UID);
        $resultRes = AbeRequestsPeer::doSelectRS($criteria);
        $resultRes->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        $resultRes->next();
        $dataRes = Array();
        $message = \G::LoadTranslation('ID_USER_NOT_RESPONDED_REQUEST');

        if ($dataRes = $resultRes->getRow()) {
            $_SESSION['CURRENT_DYN_UID'] = trim($dataRes['DYN_UID']);
            $dynaform = new \Form($dataRes['PRO_UID'] . PATH_SEP . trim($dataRes['DYN_UID']), PATH_DYNAFORM, SYS_LANG, false);
            $dynaform->mode = 'view';

            if ($dataRes['ABE_RES_DATA'] != '') {
                $value = unserialize($dataRes['ABE_RES_DATA']);

                if (is_array($value)) {
                    $dynaform->values = $value;

                    foreach ($dynaform->fields as $fieldName => $field) {
                        if ($field->type == 'submit') {
                            unset($dynaform->fields[$fieldName]);
                        }
                    }

                    $message = $dynaform->render(PATH_CORE . 'templates/xmlform.html', $scriptCode);
                } else {
                    $response = $dynaform->render(PATH_CORE . 'templates/xmlform.html', $scriptCode);
                    $field = $dynaform->fields[$dataRes['ABE_ACTION_FIELD']];
                    $message = '<b>Type:   </b>' . $field->type . '<br>';

                    switch ($field->type) {
                        case 'dropdown':
                        case 'radiogroup':
                            $message .=$field->label . ' - ';
                            $message .= $field->options[$value];
                            break;
                        case 'yesno':
                            $message .= '<b>' . $field->label . ' </b>- ';
                            $message .= ($value == 1) ? 'Yes' : 'No';
                            break;
                        case 'checkbox':
                            $message .= '<b>' . $field->label . '</b> - ';
                            $message .= ($value == 'On') ? 'Check' : 'Uncheck';
                            break;
                    }
                }
            }
        }

        //Return
        return $message;
    }
}
