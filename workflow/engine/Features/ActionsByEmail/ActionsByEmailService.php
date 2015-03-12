<?php

namespace Features\ActionsByEmail;

/**
 * Description of ActionsByEmailService
 * 
 */
class ActionsByEmailService
{

    public function saveConfiguration($params)
    {
        if (\PMLicensedFeatures
                ::getSingleton()
                ->verifyfeature('zLhSk5TeEQrNFI2RXFEVktyUGpnczV1WEJNWVp6cjYxbTU3R29mVXVZNWhZQT0=')) {
            switch ($params['type']) {
                case 'configuration':
                    require_once 'classes/model/AbeConfiguration.php';
                    $abeConfigurationInstance = new \AbeConfiguration();
                    $noteValues = json_decode($params['fields']['ABE_CASE_NOTE_IN_RESPONSE']);
                    foreach ($noteValues as $value) {
                        $params['fields']['ABE_CASE_NOTE_IN_RESPONSE'] = $value;
                    }
                    $abeConfigurationInstance->createOrUpdate($params['fields']);
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
        set_include_path(PATH_FEATURES . 'ActionsByEmail' . PATH_SEPARATOR . get_include_path());
        require_once 'classes/model/AbeConfiguration.php';

        $criteria = new \Criteria();
        $criteria->add(\AbeConfigurationPeer::PRO_UID, $params['PRO_UID']);
        $criteria->add(\AbeConfigurationPeer::TAS_UID, $params['TAS_UID']);
        $result = \AbeConfigurationPeer::doSelectRS($criteria);
        $result->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
        $result->next();
        $configuration = array();
        if ($configuration = $result->getRow()) {
            $configuration['ABE_EMAIL_FIELD_VALUE'] = $configuration['ABE_EMAIL_FIELD'];
            $configuration['ABE_ACTION_FIELD_VALUE'] = $configuration['ABE_ACTION_FIELD'];
            $configuration['ABE_CASE_NOTE_IN_RESPONSE'] = $configuration['ABE_CASE_NOTE_IN_RESPONSE'] ? '["1"]' : '[]';
        }
        $configuration['feature'] = 'ActionsByEmail';
        $configuration['prefix'] = 'abe';
        $configuration['PRO_UID'] = $params['PRO_UID'];
        $configuration['TAS_UID'] = $params['TAS_UID'];
        $configuration['SYS_LANG'] = SYS_LANG;
        return $configuration;
    }

}
