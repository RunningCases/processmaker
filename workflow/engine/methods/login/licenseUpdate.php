<?php

$aInfoLoadFile = array();
$aInfoLoadFile['name'] = $_FILES['form']['name']['licenseFile'];
$aInfoLoadFile['tmp_name'] = $_FILES['form']['tmp_name']['licenseFile'];
$aux = pathinfo($aInfoLoadFile['name']);

//validating the extention before to upload it
if ($aux['extension'] != 'dat') {
    G::SendTemporalMessage('ID_WARNING_ENTERPRISE_LICENSE_MSG_DAT', 'warning');
} else {
    $dir = PATH_DATA_SITE;
    G::uploadFile($aInfoLoadFile["tmp_name"], $dir, $aInfoLoadFile["name"]);
    //reading the file that was uploaded

    $licenseManager =& pmLicenseManager::getSingleton();
    $response = $licenseManager->installLicense($dir . $aInfoLoadFile["name"], false, false);

    if ($response) {
        $licenseManager = new pmLicenseManager();
        preg_match("/^license_(.*).dat$/", $licenseManager->file, $matches);
        $realId = urlencode($matches[1]);
        $workspace = (isset($licenseManager->workspace)) ? $licenseManager->workspace : 'pmLicenseSrv';

        $addonLocation = "http://{$licenseManager->server}/sys".$workspace."/en/green/services/addonsStore?action=getInfo&licId=$realId";

        ///////
        $cnn = Propel::getConnection("workflow");

        $oCriteriaSelect = new Criteria("workflow");
        $oCriteriaSelect->add(AddonsStorePeer::STORE_ID, $licenseManager->id);

        $oCriteriaUpdate = new Criteria("workflow");
        $oCriteriaUpdate->add(AddonsStorePeer::STORE_ID, $licenseManager->id);
        $oCriteriaUpdate->add(AddonsStorePeer::STORE_LOCATION, $addonLocation);

        BasePeer::doUpdate($oCriteriaSelect, $oCriteriaUpdate, $cnn);

        //plugin.singleton //are all the plugins that are enabled in the SYS_SYS
        $pluginRegistry = &PMPluginRegistry::getSingleton();

        $arrayAddon = array();

        //ee //all plugins enterprise installed in /processmaker/workflow/engine/plugins (no matter if they are enabled/disabled)
        if (file_exists(PATH_DATA_SITE . "ee")) {
            $arrayAddon = unserialize(trim(file_get_contents(PATH_DATA_SITE . "ee")));
        }

        foreach ($arrayAddon as $addon) {
            $sFileName = substr($addon["sFilename"], 0, strpos($addon["sFilename"], "-"));

            if (file_exists(PATH_PLUGINS . $sFileName . ".php")) {
                $addonDetails = $pluginRegistry->getPluginDetails($sFileName . ".php");
                $enabled = 0;

                if ($addonDetails) {
                    $enabled = ($addonDetails->enabled)? 1 : 0;
                }

                if ($enabled == 1 && !in_array($sFileName, $licenseManager->features)) {
                    require_once (PATH_PLUGINS . $sFileName . ".php");

                    $pluginRegistry->disablePlugin($sFileName);
                }
            }
        }

        file_put_contents(PATH_DATA_SITE . "plugin.singleton", $pluginRegistry->serializeInstance());
        G::SendTemporalMessage('ID_NLIC', 'info');
    } else {
        G::SendTemporalMessage('ID_WARNING_ENTERPRISE_LICENSE_MSG', 'warning');
    }
}

G::header('Location: ../login/login');
die();