<?php
/**
 *
 * processes_ImportFile.php
 *
 * If the feature is enable and the code_scanner_scope was enable with the argument import_plugin, will check the code
 * Review when a plugin was enable
 *
 * @link https://wiki.processmaker.com/3.0/Plugins#Import_a_Plugin
 */

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;
use ProcessMaker\Validation\ValidationUploadedFiles;

global $RBAC;
$RBAC->requirePermissions('PM_SETUP_ADVANCE');

try {
    ValidationUploadedFiles::getValidationUploadedFiles()->dispatch(function($validator) {
        throw new Exception($validator->getMessage());
    });
    //load the variables
    if (!isset($_FILES['form']['error']['PLUGIN_FILENAME']) || $_FILES['form']['error']['PLUGIN_FILENAME'] == 1) {
        throw (new Exception(G::loadTranslation('ID_ERROR_UPLOADING_PLUGIN_FILENAME')));
    }

    //save the file
    if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {
        $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];
        $path = PATH_DOCUMENT . 'input' . PATH_SEP;
        $tempName = $_FILES['form']['tmp_name']['PLUGIN_FILENAME'];
        G::uploadFile($tempName, $path, $filename);
    }

    //save the files Enterprise
    if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {
        $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];
        $path = PATH_DOCUMENT . 'input' . PATH_SEP;
        if (strpos($filename, 'enterprise') !== false) {
            $tar = new Archive_Tar($path . $filename);
            $sFileName = substr($filename, 0, strrpos($filename, '.'));
            $sClassName = substr($filename, 0, strpos($filename, '-'));
            $sClassName = !empty($sClassName) ? $sClassName : $sFileName;

            $files = $tar->listContent();
            $licenseName = '';
            $listFiles = array();
            foreach ($files as $key => $val) {
                if (strpos(trim($val['filename']), 'enterprise/data/') !== false) {
                    $listFiles[] = trim($val['filename']);
                }
                if (strpos(trim($val['filename']), 'license_') !== false) {
                    $licenseName = trim($val['filename']);
                }
            }
            $tar->extractList($listFiles, PATH_PLUGINS . 'data');
            $tar->extractList($licenseName, PATH_PLUGINS);

            $pluginRegistry = PluginRegistry::loadSingleton();
            $autoPlugins = glob(PATH_PLUGINS . "data/enterprise/data/*.tar");
            $autoPluginsA = array();

            foreach ($autoPlugins as $filePath) {
                $plName = basename($filePath);
                //if (!(in_array($plName, $def))) {
                if (strpos($plName, 'enterprise') === false) {
                    $autoPluginsA[]["sFilename"] = $plName;
                }
            }

            $aPlugins = $autoPluginsA;
            foreach ($aPlugins as $key => $aPlugin) {
                $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));

                $oTar = new Archive_Tar(PATH_PLUGINS . "data/enterprise/data/" . $aPlugin["sFilename"]);
                $oTar->extract(PATH_PLUGINS);

                if (!(class_exists($sClassName))) {
                    require_once(PATH_PLUGINS . $sClassName . ".php");
                }

                $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");
                $pluginRegistry->installPlugin($pluginDetail->getNamespace()); //error
                $pluginRegistry->savePlugin($pluginDetail->getNamespace());
            }
            $licfile = glob(PATH_PLUGINS . "*.dat");

            if ((isset($licfile[0])) && (is_file($licfile[0]))) {
                $licfilename = basename($licfile[0]);
                @copy($licfile[0], PATH_DATA_SITE . $licfilename);
                @unlink($licfile[0]);
            }

            require_once('classes/model/AddonsStore.php');
            AddonsStore::checkLicenseStore();
            $licenseManager = PmLicenseManager::getSingleton();
            AddonsStore::updateAll(false);

            $message = G::loadTranslation('ID_ENTERPRISE_INSTALLED') . ' ' . G::loadTranslation('ID_LOG_AGAIN');
            G::SendMessageText($message, "INFO");
            $licenseManager = PmLicenseManager::getSingleton();
            die('<script type="text/javascript">parent.parent.location = "../login/login";</script>');
        }
    }

    //save the packages plugins
    if ($_FILES['form']['error']['PLUGIN_FILENAME'] == 0) {
        $filename = $_FILES['form']['name']['PLUGIN_FILENAME'];
        $path = PATH_DOCUMENT . 'input' . PATH_SEP;
        if (strpos($filename, 'plugins-') !== false) {
            $tar = new Archive_Tar($path . $filename);
            $sFileName = substr($filename, 0, strrpos($filename, '.'));
            $sClassName = substr($filename, 0, strpos($filename, '-'));
            $sClassName = !empty($sClassName) ? $sClassName : $sFileName;

            $files = $tar->listContent();
            $licenseName = '';
            $listFiles = array();
            foreach ($files as $key => $val) {
                if (strpos(trim($val['filename']), 'plugins/') !== false) {
                    $listFiles[] = trim($val['filename']);
                }
                if (strpos(trim($val['filename']), 'license_') !== false) {
                    $licenseName = trim($val['filename']);
                }
            }
            $tar->extractList($listFiles, PATH_PLUGINS . 'data');
            $tar->extractList($licenseName, PATH_PLUGINS);

            $pluginRegistry = PluginRegistry::loadSingleton();
            $autoPlugins = glob(PATH_PLUGINS . "data/plugins/*.tar");
            $autoPluginsA = array();

            foreach ($autoPlugins as $filePath) {
                $plName = basename($filePath);
                if (strpos($plName, 'enterprise') === false) {
                    $autoPluginsA[]["sFilename"] = $plName;
                }
            }

            $aPlugins = $autoPluginsA;
            foreach ($aPlugins as $key => $aPlugin) {
                $sClassName = substr($aPlugin["sFilename"], 0, strpos($aPlugin["sFilename"], "-"));

                $oTar = new Archive_Tar(PATH_PLUGINS . "data/plugins/" . $aPlugin["sFilename"]);
                $oTar->extract(PATH_PLUGINS);

                if (!(class_exists($sClassName))) {
                    require_once(PATH_PLUGINS . $sClassName . ".php");
                }

                $pluginDetail = $pluginRegistry->getPluginDetails($sClassName . ".php");
                $pluginRegistry->installPlugin($pluginDetail->getNamespace()); //error
                $pluginRegistry->savePlugin($pluginDetail->getNamespace());
            }

            $licfile = glob(PATH_PLUGINS . "*.dat");

            if ((isset($licfile[0])) && (is_file($licfile[0]))) {
                $licfilename = basename($licfile[0]);
                @copy($licfile[0], PATH_DATA_SITE . $licfilename);
                @unlink($licfile[0]);
            }

            require_once('classes/model/AddonsStore.php');
            AddonsStore::checkLicenseStore();
            $licenseManager = PmLicenseManager::getSingleton();
            AddonsStore::updateAll(false);

            $message = G::loadTranslation('ID_ENTERPRISE_INSTALLED') . ' ' . G::loadTranslation('ID_LOG_AGAIN');
            G::SendMessageText($message, "INFO");
            $licenseManager = PmLicenseManager::getSingleton();
            die('<script type="text/javascript">parent.parent.location = "../login/login";</script>');
        }
    }

    if (!$_FILES['form']['type']['PLUGIN_FILENAME'] == 'application/octet-stream') {
        $pluginFilename = $_FILES['form']['type']['PLUGIN_FILENAME'];
        throw new Exception(G::loadTranslation('ID_FILES_INVALID_PLUGIN_FILENAME', SYS_LANG, array("pluginFilename" => $pluginFilename
        )));
    }


    $tar = new Archive_Tar($path . $filename);
    $sFileName = substr($filename, 0, strrpos($filename, '.'));
    $sClassName = substr($filename, 0, strpos($filename, '-'));
    $sClassName = !empty($sClassName) ? $sClassName : $sFileName;

    $aFiles = $tar->listContent();
    $bMainFile = false;
    $bClassFile = false;
    if (!is_array($aFiles)) {
        throw new Exception(G::loadTranslation('ID_FAILED_IMPORT_PLUGINS', SYS_LANG, array("filename" => $filename
        )));
    }
    foreach ($aFiles as $key => $val) {
        if (trim($val['filename']) == $sClassName . '.php') {
            $bMainFile = true;
        }
        if (trim($val['filename']) == $sClassName . PATH_SEP . 'class.' . $sClassName . '.php') {
            $bClassFile = true;
        }
    }

    $partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;
    if (($sClassName == 'enterprise') && ($partnerFlag)) {
        $pathFileFlag = PATH_DATA . 'flagNewLicence';
        file_put_contents($pathFileFlag, 'New Enterprise');
    }

    $oPluginRegistry = PluginRegistry::loadSingleton();
    $pluginFile = $sClassName . '.php';

    if ($bMainFile && $bClassFile) {
        $sAux = $sClassName . 'Plugin';
        $fVersionOld = 0.0;
        if (file_exists(PATH_PLUGINS . $pluginFile)) {
            if (!class_exists($sAux) && !class_exists($sClassName . 'plugin')) {
                include PATH_PLUGINS . $pluginFile;
            }
            if (!class_exists($sAux)) {
                $sAux = $sClassName . 'plugin';
            }
            $oClass = new $sAux($sClassName);
            $fVersionOld = $oClass->iVersion;
            unset($oClass);
        }
        $res = $tar->extract($path);

        //Check if is enterprise plugin
        if ($oPluginRegistry->isEnterprisePlugin($sClassName, $path)) {
            throw new Exception(G::LoadTranslation('ID_PMPLUGIN_IMPORT_PLUGIN_IS_ENTERPRISE', [$filename]));
        }

        /*----------------------------------********---------------------------------*/
        if (PMLicensedFeatures::getSingleton()->verifyfeature("B0oWlBLY3hHdWY0YUNpZEtFQm5CeTJhQlIwN3IxMEkwaG4=")) {
            //Check disabled code
            $arrayFoundDisabledCode = [];
            $cs = new CodeScanner(config("system.workspace"));
            if (in_array('import_plugin', $cs->getScope())) {
                $arrayFoundDisabledCode = array_merge($cs->checkDisabledCode("FILE", $path . $pluginFile),
                    $cs->checkDisabledCode("PATH", $path . $sClassName));
            }

            if (!empty($arrayFoundDisabledCode)) {
                throw new Exception(G::LoadTranslation("ID_DISABLED_CODE_PLUGIN"));
            }
        }
        /*----------------------------------********---------------------------------*/

        //Get contents of plugin file
        $sContent = file_get_contents($path . $pluginFile);
        $sContent = str_ireplace($sAux, $sAux . '_', $sContent);
        $sContent = str_ireplace('PATH_PLUGINS', "'" . $path . "'", $sContent);
        $sContent = preg_replace("/\\\$oPluginRegistry\s*=\s*&\s*PMPluginRegistry::getSingleton\s*\(\s*\)\s*;/i", null, $sContent);
        $sContent = preg_replace("/\\\$oPluginRegistry->registerPlugin\s*\(\s*[\"\']" . $sClassName . "[\"\']\s*,\s*__FILE__\s*\)\s*;/i", null, $sContent);

        //header('Content-Type: text/plain');var_dump($sClassName, $sContent);die;
        file_put_contents($path . $pluginFile, $sContent);

        $sAux = $sAux . '_';

        include($path . $pluginFile);

        $oClass = new $sAux($sClassName);
        $fVersionNew = $oClass->iVersion;
        if (!isset($oClass->iPMVersion)) {
            $oClass->iPMVersion = 0;
        }
        if ($oClass->iPMVersion > 0) {
            if (System::getVersion() > 0) {
                if ($oClass->iPMVersion > System::getVersion()) {
                    //throw new Exception('This plugin needs version ' . $oClass->iPMVersion . ' or higher of ProcessMaker');
                }
            }
        }
        if (!isset($oClass->aDependences)) {
            $oClass->aDependences = null;
        }
        if (!empty($oClass->aDependences)) {
            foreach ($oClass->aDependences as $aDependence) {
                if (file_exists(PATH_PLUGINS . $aDependence['sClassName'] . '.php')) {
                    require_once PATH_PLUGINS . $aDependence['sClassName'] . '.php';
                    if (!$oPluginRegistry->getPluginDetails($aDependence['sClassName'] . '.php')) {
                        $sDependence = $aDependence['sClassName'];
                        throw new Exception(G::loadTranslation('ID_PLUGIN_DEPENDENCE_PLUGIN', SYS_LANG, array("Dependence" => $sDependence
                        )));
                    }
                } else {
                    $sDependence = $aDependence['sClassName'];
                    throw new Exception(G::loadTranslation('ID_PLUGIN_DEPENDENCE_PLUGIN', SYS_LANG, array("Dependence" => $sDependence
                    )));
                }
            }
        }
        unset($oClass);
        if ($fVersionOld > $fVersionNew) {
            throw new Exception(G::loadTranslation('ID_RECENT_VERSION_PLUGIN'));
        }
        $res = $tar->extract(PATH_PLUGINS);
    } else {
        throw new Exception(G::loadTranslation('ID_FILE_CONTAIN_CLASS_PLUGIN', SYS_LANG, array("filename" => $filename, "className" => $sClassName
        )));
    }

    if (!file_exists(PATH_PLUGINS . $sClassName . '.php')) {
        throw new Exception(G::loadTranslation('ID_FILE_PLUGIN_NOT_EXISTS', SYS_LANG, array("pluginFile" => $pluginFile
        )));
    }

    require_once(PATH_PLUGINS . $pluginFile);

    $oPluginRegistry->registerPlugin($sClassName, PATH_PLUGINS . $sClassName . ".php");

    $details = $oPluginRegistry->getPluginDetails($pluginFile);

    $oPluginRegistry->installPlugin($details->getNamespace());

    $oPluginRegistry->setupPlugins(); //get and setup enabled plugins
    $oPluginRegistry->savePlugin($details->getNamespace());

    $response = $oPluginRegistry->verifyTranslation($details->getNamespace());
    G::auditLog("InstallPlugin", "Plugin Name: " . $details->getNamespace());
    G::header("Location: pluginsMain");
    die();
} catch (Exception $e) {
    $_SESSION['__PLUGIN_ERROR__'] = $e->getMessage();
    G::header('Location: pluginsMain');
    die();
}
