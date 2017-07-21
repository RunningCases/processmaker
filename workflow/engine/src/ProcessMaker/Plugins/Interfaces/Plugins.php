<?php

namespace ProcessMaker\Plugins\Interfaces;

class Plugins
{
    public $_aPluginDetails = array();
    public $_aPlugins = array();
    public $_aMenus = array();
    public $_aFolders = array();
    public $_aTriggers = array();
    public $_aDashlets = array();
    public $_aReports = array();
    public $_aPmFunctions = array();
    public $_aRedirectLogin = array();
    public $_aSteps = array();
    public $_aCSSStyleSheets = array();
    public $_aToolbarFiles = array();
    public $_aCaseSchedulerPlugin = array();
    public $_aTaskExtendedProperties = array();
    public $_aDashboardPages = array();
    public $_aCronFiles = array();
    public $_arrayDesignerMenu = array();
    public $_aMenuOptionsToReplace = array();
    public $_aImportProcessCallbackFile = array();
    public $_aOpenReassignCallback = array();
    public $_arrayDesignerSourcePath = array();

    /**
     * Registry a plugin javascript to include with js core at same runtime
     */
    public $_aJavascripts = array();

    /**
     * Contains all rest services classes from plugins
     */
    public $_restServices = array();

    public $_restExtendServices = array();

    public $_restServiceEnabled = array();

    public static function setter($vars)
    {
        $has = get_object_vars(new static());
        $newObject = new \stdClass();
        foreach ($has as $name => $oldValue) {
            if (isset($vars[$name])) {
                $newObject->{$name} = $vars[$name];
                unset($vars[$name]);
            } else {
                $newObject->{$name} = $oldValue;
            }
        }
        if ($vars) {
            $sClassName = $vars['sClassName'];
            $sNamespace = $vars['sNamespace'];
            $sFilename = $vars['sFilename'];
            $newObjectDetails = new PluginDetail(
                $sNamespace,
                $sClassName,
                $sFilename
            );
            if (class_exists($sClassName)) {
                $oPlugin = new $sClassName($sNamespace, $sFilename);
            } else {
                $oPlugin = $newObjectDetails;
            }
            $newObject->_aPlugins[$sNamespace] = $oPlugin;
            $hasDetails = get_object_vars($newObjectDetails);
            foreach ($hasDetails as $name => $oldValue) {
                if (isset($vars[$name])) {
                    $newObjectDetails->{$name} = $vars[$name];
                    unset($vars[$name]);
                } else {
                    $newObjectDetails->{$name} = $oldValue;
                }
            }
            $newObject->_aPluginDetails[$sNamespace] = $newObjectDetails;
        }
        return $newObject;
    }
}
