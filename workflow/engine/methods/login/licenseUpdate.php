<?php

use ProcessMaker\Plugins\PluginRegistry;

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

    $licenseManager = PmLicenseManager::getSingleton();
    $response = $licenseManager->installLicense($dir . $aInfoLoadFile["name"], false, false);

    if ($response) {
        $licenseManager = new PmLicenseManager();
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

        //are all the plugins that are enabled in the workspace
        $pluginRegistry = PluginRegistry::loadSingleton();
        /** @var \ProcessMaker\Plugins\Interfaces\PluginDetail $plugin */
        foreach ($pluginRegistry->getAllPluginsDetails() as $plugin) {
            if ($plugin->isEnabled() && !in_array($plugin->getNamespace(), $licenseManager->features)) {
                $pluginRegistry->disablePlugin($plugin->getNamespace());
                // In order to keep the custom plugins state, it is required to set the attribute before saving the info
                $plugin->setEnabled(true);
                $pluginRegistry->savePlugin($plugin->getNamespace());
            }
        }

        G::SendTemporalMessage('ID_NLIC', 'info');
    } else {
        G::SendTemporalMessage('ID_WARNING_ENTERPRISE_LICENSE_MSG', 'warning');
    }
}

G::header('Location: ../login/login');
die();
