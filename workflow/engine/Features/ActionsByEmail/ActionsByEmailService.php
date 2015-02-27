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
        switch ($params['type']) {
            case 'configuration':
                require_once 'classes/model/AbeConfiguration.php';
                $abeConfigurationInstance = new \AbeConfiguration();
                $abeConfigurationInstance->createOrUpdate($params['fields']);
                break;
            default:
                break;
        }
    }
}
