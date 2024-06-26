<?php
/**
 * main.php Cases List main processor
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

use ProcessMaker\Core\System;
use ProcessMaker\Plugins\PluginRegistry;

global $RBAC;
$RBAC->requirePermissions('PM_FACTORY');

$conf = new Configurations();

$pmVersion = (preg_match("/^([\d\.]+).*$/", System::getVersion(), $arrayMatch)) ? $arrayMatch[1] : ""; //Otherwise: Branch master

/*----------------------------------********---------------------------------*/
if (true) {
    $arrayFlagImportFileExtension = array("pm", "pmx", "pmx2", "bpmn");
} else {
    /*----------------------------------********---------------------------------*/
    $arrayFlagImportFileExtension = array("pm", "pmx", "bpmn");
    /*----------------------------------********---------------------------------*/
}
/*----------------------------------********---------------------------------*/

$arrayFlagMenuNewOption = array("pm" => true, "bpmn" => true);

if ($pmVersion != "") {
    $arrayFlagImportFileExtension = (version_compare($pmVersion . "", "3", ">=")) ? $arrayFlagImportFileExtension : array("pm");
    $arrayFlagMenuNewOption = (version_compare($pmVersion . "", "3", ">=")) ? array("bpmn" => true) : array("pm" => true);
}

$oPluginRegistry = PluginRegistry::loadSingleton();

$arrayMenuNewOptionPlugin = array();
$arrayContextMenuOptionPlugin = array();

foreach ($oPluginRegistry->getDesignerMenu() as $value) {
    if (file_exists($value->file)) {
        require_once($value->file);

        $className = "DesignerMenu" . $value->pluginName;

        if (class_exists($className)) {
            $obj = new $className();

            if (method_exists($obj, "getDesignerMenu")) {
                $arrayDesignerMenuData = $obj->getDesignerMenu();

                if (isset($arrayDesignerMenuData["MENU_NEW_OPTION"]) && is_array($arrayDesignerMenuData["MENU_NEW_OPTION"])) {
                    $arrayMenuNewOptionPlugin = array_merge($arrayMenuNewOptionPlugin, $arrayDesignerMenuData["MENU_NEW_OPTION"]);
                }

                if (isset($arrayDesignerMenuData["CONTEXT_MENU_OPTION"]) && is_array($arrayDesignerMenuData["CONTEXT_MENU_OPTION"])) {
                    $arrayContextMenuOptionPlugin = array_merge($arrayContextMenuOptionPlugin, $arrayDesignerMenuData["CONTEXT_MENU_OPTION"]);
                }
            }
        }
    }
}

$oHeadPublisher->addExtJsScript('processes/main', true); //adding a javascript file .js
$oHeadPublisher->addContent('processes/main'); //adding a html file  .html.

$partnerFlag = (defined('PARTNER_FLAG')) ? PARTNER_FLAG : false;
$oHeadPublisher->assign('PARTNER_FLAG', $partnerFlag);
$oHeadPublisher->assign('pageSize', $conf->getEnvSetting('casesListRowNumber'));
$oHeadPublisher->assign("arrayFlagImportFileExtension", $arrayFlagImportFileExtension);
$oHeadPublisher->assign("arrayFlagMenuNewOption", $arrayFlagMenuNewOption);
$oHeadPublisher->assign("arrayMenuNewOptionPlugin", $arrayMenuNewOptionPlugin);
$oHeadPublisher->assign("arrayContextMenuOptionPlugin", $arrayContextMenuOptionPlugin);

$designer = new Designer();
$oHeadPublisher->assign('SYS_SYS', config("system.workspace"));
$oHeadPublisher->assign('SYS_LANG', SYS_LANG);
$oHeadPublisher->assign('SYS_SKIN', SYS_SKIN);
$oHeadPublisher->assign('HTTP_SERVER_HOSTNAME', System::getHttpServerHostnameRequestsFrontEnd());
$oHeadPublisher->assign('credentials', base64_encode(G::json_encode($designer->getCredentials())));

$userUid = (isset($_SESSION['USER_LOGGED']) && $_SESSION['USER_LOGGED'] != '') ? $_SESSION['USER_LOGGED'] : null;
$oHeadPublisher->assign('userUid', $userUid);
$oHeadPublisher->assign('workspace', config('system.workspace'));

$deleteCasesFlag = false;
global $RBAC;
if ($RBAC->userCanAccess('PM_DELETE_PROCESS_CASES') === 1) {
    $deleteCasesFlag = true;
}
$oHeadPublisher->assign('deleteCasesFlag', $deleteCasesFlag);

$oPluginRegistry = PluginRegistry::loadSingleton();
$callBackFile = $oPluginRegistry->getImportProcessCallback();
$file = false;
if (count($callBackFile)) {
    $file = $callBackFile[0]->getCallBackFile() != "" ? $callBackFile[0]->getCallBackFile() : false;
}
$oHeadPublisher->assign("importProcessCallbackFile", $file);

$isGranularFeature = false;
/*----------------------------------********---------------------------------*/
$licensedFeatures = PMLicensedFeatures::getSingleton();
if ($licensedFeatures->verifyfeature('jXsSi94bkRUcVZyRStNVExlTXhEclVadGRRcG9xbjNvTWVFQUF3cklKQVBiVT0=')) {
    $isGranularFeature = true;
}
/*----------------------------------********---------------------------------*/
$oHeadPublisher->assign("isGranularFeature", $isGranularFeature);

G::RenderPage('publish', 'extJs');
