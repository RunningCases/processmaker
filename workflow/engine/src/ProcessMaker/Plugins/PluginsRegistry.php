<?php

namespace ProcessMaker\Plugins;

use ProcessMaker\Plugins\Adapters\PluginAdapter;
use ProcessMaker\Plugins\Interfaces\CaseSchedulerPlugin;
use ProcessMaker\Plugins\Interfaces\CronFile;
use ProcessMaker\Plugins\Interfaces\CssFile;
use ProcessMaker\Plugins\Interfaces\DashboardPage;
use ProcessMaker\Plugins\Interfaces\FolderDetail;
use ProcessMaker\Plugins\Interfaces\ImportCallBack;
use ProcessMaker\Plugins\Interfaces\MenuDetail;
use ProcessMaker\Plugins\Interfaces\OpenReassignCallback;
use ProcessMaker\Plugins\Interfaces\Plugins;
use Maveriks\Util\ClassLoader;
use Maveriks\WebApplication;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\Interfaces\RedirectDetail;
use ProcessMaker\Plugins\Interfaces\StepDetail;
use ProcessMaker\Plugins\Interfaces\TaskExtendedProperty;
use ProcessMaker\Plugins\Interfaces\ToolbarDetail;
use ProcessMaker\Plugins\Interfaces\TriggerDetail;

/**
 * Class PluginRegistry
 * @package ProcessMaker\Plugins
 */
class PluginsRegistry extends Plugins
{
    /**
     * Instance of de object PluginRegistry
     * @var PluginsRegistry
     */
    private static $instance = null;

    public $pluginAdapter;
    /**
     * Array of all plugins
     * @var array
     */
    private $Plugins = array();

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->Plugins;
    }

    /**
     * @param array $Plugins
     */
    public function setPlugins($Plugins)
    {
        $this->Plugins = $Plugins;
    }

    /**
     * PluginRegistry constructor.
     */
    public function __construct()
    {
        $this->pluginAdapter = new PluginAdapter();
        $oPlugins = $this->pluginAdapter->getPluginsDefinition($this);
    }

    /**
     * Load the singleton instance from a serialized stored file
     *
     * @return PluginsRegistry|static
     * @throws \Exception
     */
    public static function loadSingleton()
    {
        if (self::$instance == null) {
            self::$instance = new PluginsRegistry();
            if (!is_object(self::$instance) || get_class(self::$instance) != "ProcessMaker\Plugins\PluginsRegistry") {
                throw new \Exception("Can't load main PluginRegistry object.");
            }
        }
        return self::$instance;
    }

    /**
     * This function is instancing to this class
     * param
     *
     * @return object
     * @deprecated
     */
    public static function getSingleton()
    {
        if (self::$instance == null) {
            self::$instance = new PluginsRegistry();
        }
        return self::$instance;
    }

    /**
     * Register the plugin in the singleton
     *
     * @param string $sNamespace
     * @param string $sFilename
     */
    public function registerPlugin($sNamespace, $sFilename = null)
    {
        //require_once ($sFilename);
        $sClassName = $sNamespace . "plugin";
        $plugin = new $sClassName($sNamespace, $sFilename);

        if (isset($this->_aPluginDetails[$sNamespace])) {
            $this->_aPluginDetails[$sNamespace]->iVersion = $plugin->iVersion;
            return;
        }

        $detail = new PluginDetail(
            $sNamespace,
            $sClassName,
            $sFilename,
            $plugin->sFriendlyName,
            $plugin->sPluginFolder,
            $plugin->sDescription,
            $plugin->sSetupPage,
            $plugin->iVersion
        );

        if (isset($plugin->aWorkspaces)) {
            $detail->aWorkspaces = $plugin->aWorkspaces;
        }

        if (isset($plugin->bPrivate)) {
            $detail->bPrivate = $plugin->bPrivate;
        }

        //if (isset($this->_aPluginDetails[$sNamespace])){
        //    $detail->enabled = $this->_aPluginDetails[$sNamespace]->enabled;
        //}

        $this->_aPluginDetails[$sNamespace] = $detail;
    }

    /**
     * get the plugin details, by filename
     *
     * @param string $sFilename
     * @return null
     */
    public function getPluginDetails($sFilename)
    {
        foreach ($this->_aPluginDetails as $key => $row) {
            if ($sFilename == baseName($row->sFilename)) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Enable the plugin in the singleton
     *
     * @param string $sNamespace
     * @return bool
     * @throws \Exception
     */
    public function enablePlugin($sNamespace)
    {
        if ($currentPlugin = $this->_aPluginDetails[$sNamespace]) {
            $this->registerFolder($sNamespace, $sNamespace, $currentPlugin->sPluginFolder);
            //register the default directory, later we can have more
            $currentPlugin->enabled = true;
            /** @var enterprisePlugin $oPlugin */
            if (class_exists($currentPlugin->sClassName)) {
                $oPlugin = new $currentPlugin->sClassName(
                    $currentPlugin->sNamespace,
                    $currentPlugin->sFilename
                );
            } else {
                $oPlugin = $currentPlugin;
            }
            $this->_aPlugins[$currentPlugin->sNamespace] = $oPlugin;
            if (method_exists($oPlugin, 'enable')) {
                $oPlugin->enable();
            }

            /*
             * 1. register <plugin-dir>/src directory for autoloading
             * 2. verify if rest service is enabled
             * 3. register rest service directory
             */
            $pluginSrcDir = PATH_PLUGINS . $currentPlugin->sNamespace . PATH_SEP . 'src';

            if (is_dir($pluginSrcDir)) {
                //Bootstrap::registerDir($detail->sNamespace.'/src', $pluginSrcDir);
                $loader = ClassLoader::getInstance();
                $loader->add($pluginSrcDir);
            }

            if (array_key_exists($currentPlugin->sNamespace, $this->_restServiceEnabled)
                && $this->_restServiceEnabled[$currentPlugin->sNamespace] == true
            ) {
                $oPlugin->registerRestService();
            }
            return true;
        } else {
            throw new \Exception("Unable to enable plugin '$sNamespace' (plugin not found)");
        }
    }

    /**
     * disable the plugin in the singleton
     *
     * @param string $sNamespace
     * @param int $eventPlugin
     * @throws \Exception
     */
    public function disablePlugin($sNamespace, $eventPlugin = 1)
    {
        if ($currentPlugin = $this->_aPluginDetails[$sNamespace]) {
            //unset($currentPlugin->_aPluginDetails[$sNamespace]);
            $currentPlugin->enabled = false;
            if ($eventPlugin == 1) {
                //$currentPlugin->_aPlugins[$currentPlugin->sNamespace] = $currentPlugin;
                // If plugin class exists check if disable method exist,
                // otherwise use default plugin details
                if (class_exists($currentPlugin->sClassName)) {
                    $plugin = new $currentPlugin->sClassName(
                        $currentPlugin->sNamespace,
                        $currentPlugin->sFilename
                    );
                } else {
                    throw new \Exception("Unable to disable plugin '$sNamespace' (plugin not found)");
                }
                if (method_exists($plugin, "disable")) {
                    $plugin->disable();
                }
            }
        } else {
            throw new \Exception("Unable to disable plugin '$sNamespace' (plugin not found)");
        }
    }

    /**
     * Get status plugin in the singleton
     * @param string $name Plugin name
     * @return int|string Return a string with status plugin, 0 otherwise
     * @throws \Exception
     */
    public function getStatusPlugin($name)
    {
        try {
            return (isset($this->_aPluginDetails[$name])) ?
                (($this->_aPluginDetails[$name]->enabled) ? "enabled" : "disabled") :
                0;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Install a plugin archive.
     * If pluginName is specified, the archive will
     * only be installed if it contains this plugin.
     *
     * @param $filename
     * @param $pluginName
     * return bool true if enabled, false otherwise
     * @throws \Exception
     */
    public function installPluginArchive($filename, $pluginName)
    {
        \G::LoadThirdParty("pear/Archive", "Tar");
        $tar = new \Archive_Tar($filename);
        $files = $tar->listContent();
        $plugins = array();
        $namePlugin = array();
        foreach ($files as $f) {
            if (preg_match("/^([\w\.]*).ini$/", $f["filename"], $matches)) {
                //if (preg_match( "/^(.*pluginConfig)\.ini$/", $f["filename"], $matches )) {
                $plugins[] = $matches[1];
            }
            if (preg_match("/^.*($pluginName)\.php$/", $f["filename"], $matches)) {
                $namePlugin[] = $matches[1];
            }
        }

        if (count($plugins) > 1) {
            throw new \Exception("Multiple plugins in one archive are not supported currently");
        }

        //if (isset($pluginName) && !in_array($pluginName, $plugins)) {
        if (isset($pluginName) && !in_array($pluginName, $namePlugin)) {
            throw new \Exception("Plugin '$pluginName' not found in archive");
        }

        //$pluginName = $plugins[0];
        $pluginFile = "$pluginName.php";

        $res = $tar->extract(PATH_PLUGINS);
        if (!file_exists(PATH_PLUGINS . $pluginFile)) {
            throw (new \Exception("File \"$pluginFile\" doesn't exist"));
        }
//        $filter = new \InputFilter();
        $path = PATH_PLUGINS . $pluginFile;
//        $path = $filter->validateInput($path, 'path');
        require_once($path);
        /** @var PluginDetail $details */
        $details = $this->getPluginDetails($pluginFile);

        $this->installPlugin($details->sNamespace);
//        $this->setupPlugins();

        $this->enablePlugin($details->sNamespace);
    }

    public function uninstallPlugin($sNamespace)
    {
        $pluginFile = $sNamespace . ".php";

        if (!file_exists(PATH_PLUGINS . $pluginFile)) {
            throw (new \Exception("File \"$pluginFile\" doesn't exist"));
        }

        ///////
        $path = PATH_PLUGINS . $pluginFile;
        $filter = new \InputFilter();
        $path = $filter->validateInput($path, 'path');
        require_once($path);

        foreach ($this->_aPluginDetails as $namespace => $detail) {
            if ($namespace == $sNamespace) {
                $this->enablePlugin($detail->sNamespace);
                $this->disablePlugin($detail->sNamespace);

                ///////
                $plugin = new $detail->sClassName($detail->sNamespace, $detail->sFilename);
                $this->_aPlugins[$detail->sNamespace] = $plugin;

                if (method_exists($plugin, "uninstall")) {
                    $plugin->uninstall();
                }

                ///////
                $this->pluginAdapter->savePlugin($detail->sNamespace, $this);
                ///////
                $pluginDir = PATH_PLUGINS . $detail->sPluginFolder;

                if (isset($detail->sFilename) && !empty($detail->sFilename) && file_exists($detail->sFilename)) {
                    unlink($detail->sFilename);
                }

                if (isset($detail->sPluginFolder) && !empty($detail->sPluginFolder) && file_exists($pluginDir)) {
                    \G::rm_dir($pluginDir);
                }

                ///////
                $this->uninstallPluginWorkspaces(array($sNamespace));
                ///////
                break;
            }
        }
    }

    /**
     * @param $arrayPlugin
     */
    public function uninstallPluginWorkspaces($arrayPlugin)
    {
        $workspace = \System::listWorkspaces();

        foreach ($workspace as $indexWS => $ws) {
            $pluginRegistry =& PluginsRegistry::loadSingleton();
            $attributes = $pluginRegistry->getAttributes();

            foreach ($arrayPlugin as $index => $value) {
                if (isset($attributes["_aPluginDetails"][$value])) {
                    $pluginRegistry->disablePlugin($value, 0);
                }
            }

        }
    }

    /**
     * install the plugin
     *
     * @param string $sNamespace
     */
    public function installPlugin($sNamespace)
    {
        try {
            foreach ($this->_aPluginDetails as $namespace => $detail) {
                if ($sNamespace == $namespace) {
                    $oPlugin = new $detail->sClassName($detail->sNamespace, $detail->sFilename);
                    $this->_aPlugins[$detail->sNamespace] = $oPlugin;

                    $this->pluginAdapter->savePlugin($detail->sNamespace, $this);

                    $oPlugin->install();
                }
            }
        } catch (\Exception $e) {
            global $G_PUBLISH;
            $aMessage['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new \Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage);
            \G::RenderPage('publish');
            die();
        }
    }

    /**
     * Register a menu in the singleton
     *
     * @param string $sNamespace
     * @param string $sMenuId
     * @param string $sFilename
     */
    public function registerMenu($sNamespace, $sMenuId, $sFilename)
    {
        $found = false;
        foreach ($this->_aMenus as $row => $detail) {
            if ($sMenuId == $detail->sMenuId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }
        if (!$found) {
            $menuDetail = new MenuDetail($sNamespace, $sMenuId, $sFilename);
            $this->_aMenus[] = $menuDetail;
        }
    }

    /**
     * Register a dashlet class in the singleton
     *
     * @param string $namespace
     */
    public function registerDashlets($namespace)
    {
        $found = false;
        foreach ($this->_aDashlets as $row => $detail) {
            if ($namespace == $detail) {
                $found = true;
            }
        }
        if (!$found) {
            $this->_aDashlets[] = $namespace;
        }
    }

    /**
     * Register a stylesheet in the singleton
     *
     * @param string $sNamespace
     * @param string $sCssFile
     */
    public function registerCss($sNamespace, $sCssFile)
    {
        $found = false;
        foreach ($this->_aCSSStyleSheets as $row => $detail) {
            if ($sCssFile == $detail->sCssFile && $sNamespace == $detail->sNamespace) {
                $detail->sCssFile = $sCssFile;
                $found = true;
            }
        }
        if (!$found) {
            $cssFile = new CssFile($sNamespace, $sCssFile);
            $this->_aCSSStyleSheets[] = $cssFile;
        }
    }

    /**
     * return all css
     *
     * @return array
     */
    public function getRegisteredCss()
    {
        return $this->_aCSSStyleSheets;
    }

    /**
     * Register a plugin javascript to run with core js script at same runtime
     *
     * @param string $sNamespace
     * @param string $sCoreJsFile
     * @param array /string $pluginJsFile
     * @return mixed
     * @throws \Exception
     */
    public function registerJavascript($sNamespace, $sCoreJsFile, $pluginJsFile)
    {
        foreach ($this->_aJavascripts as $i => $js) {
            if ($sCoreJsFile == $js->sCoreJsFile && $sNamespace == $js->sNamespace) {
                if (is_string($pluginJsFile)) {
                    if (!in_array($pluginJsFile, $this->_aJavascripts[$i]->pluginJsFile)) {
                        $this->_aJavascripts[$i]->pluginJsFile[] = $pluginJsFile;
                    }
                } elseif (is_array($pluginJsFile)) {
                    $this->_aJavascripts[$i]->pluginJsFile = array_unique(
                        array_merge($pluginJsFile, $this->_aJavascripts[$i]->pluginJsFile)
                    );
                } else {
                    throw new \Exception(
                        'Invalid third param, $pluginJsFile should be a string or array - ' .
                        gettype($pluginJsFile) .
                        ' given.'
                    );
                }
                return $this->_aJavascripts[$i];
            }
        }

        $js = new \stdClass();
        $js->sNamespace = $sNamespace;
        $js->sCoreJsFile = $sCoreJsFile;
        $js->pluginJsFile = array();

        if (is_string($pluginJsFile)) {
            $js->pluginJsFile[] = $pluginJsFile;
        } elseif (is_array($pluginJsFile)) {
            $js->pluginJsFile = array_merge($js->pluginJsFile, $pluginJsFile);
        } else {
            throw new \Exception(
                'Invalid third param, $pluginJsFile should be a string or array - ' .
                gettype($pluginJsFile) .
                ' given.'
            );
        }

        $this->_aJavascripts[] = $js;
    }

    /**
     * return all plugin javascripts
     *
     * @return array
     */
    public function getRegisteredJavascript()
    {
        return $this->_aJavascripts;
    }

    /**
     * return all plugin javascripts given a core js file, from all namespaces or a single namespace
     *
     * @param string $sCoreJsFile
     * @param string $sNamespace
     * @return array
     */
    public function getRegisteredJavascriptBy($sCoreJsFile, $sNamespace = '')
    {
        $scripts = array();

        if ($sNamespace == '') {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sCoreJsFile == $js->sCoreJsFile) {
                    $scripts = array_merge($scripts, $this->_aJavascripts[$i]->pluginJsFile);
                }
            }
        } else {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sCoreJsFile == $js->sCoreJsFile && $sNamespace == $js->sNamespace) {
                    $scripts = array_merge($scripts, $this->_aJavascripts[$i]->pluginJsFile);
                }
            }
        }
        return $scripts;
    }

    /**
     * unregister all javascripts from a namespace or a js core file given
     *
     * @param string $sNamespace
     * @param string $sCoreJsFile
     * @return array
     */
    public function unregisterJavascripts($sNamespace, $sCoreJsFile = '')
    {
        if ($sCoreJsFile == '') {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sNamespace == $js->sNamespace) {
                    unset($this->_aJavascripts[$i]);
                }
            }
            // Re-index when all js were unregistered
            $this->_aJavascripts = array_values($this->_aJavascripts);
        } else {
            foreach ($this->_aJavascripts as $i => $js) {
                if ($sCoreJsFile == $js->sCoreJsFile && $sNamespace == $js->sNamespace) {
                    unset($this->_aJavascripts[$i]);
                    // Re-index for each js that was unregistered
                    $this->_aJavascripts = array_values($this->_aJavascripts);
                }
            }
        }
    }

    /**
     * Register a reports class in the singleton
     *
     * @param string $sNamespace
     */
    public function registerReport($sNamespace)
    {
        $found = false;
        foreach ($this->_aReports as $row => $detail) {
            if ($sNamespace == $detail) {
                $found = true;
            }
        }
        if (!$found) {
            $this->_aReports[] = $sNamespace;
        }
    }

    /**
     * Register a PmFunction class in the singleton
     *
     * @param string $sNamespace
     */
    public function registerPmFunction($sNamespace)
    {
        $found = false;
        foreach ($this->_aPmFunctions as $row => $detail) {
            if ($sNamespace == $detail) {
                $found = true;
            }
        }
        if (!$found) {
            $this->_aPmFunctions[] = $sNamespace;
        }
    }

    /**
     * Register a redirectLogin class in the singleton
     *
     * @param string $sNamespace
     * @param string $sRole
     * @param string $sPathMethod
     */
    public function registerRedirectLogin($sNamespace, $sRole, $sPathMethod)
    {
        $found = false;
        foreach ($this->_aRedirectLogin as $row => $detail) {
            if (($sNamespace == $detail->sNamespace) && ($sRole == $detail->sRoleCode)) {
                //Filters based on Workspace and Role Code
                $found = true;
            }
        }
        if (!$found) {
            $this->_aRedirectLogin[] = new RedirectDetail($sNamespace, $sRole, $sPathMethod);
        }
    }

    /**
     * Register a folder for methods
     *
     * @param string $sNamespace
     * @param string $sFolderId
     * @param string $sFolderName
     */
    public function registerFolder($sNamespace, $sFolderId, $sFolderName)
    {
        $found = false;
        foreach ($this->_aFolders as $row => $detail) {
            if ($sFolderId == $detail->sFolderId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }

        if (!$found) {
            $this->_aFolders[] = new FolderDetail($sNamespace, $sFolderId, $sFolderName);
        }
    }

    /**
     * Register a step for process
     *
     * @param $sNamespace
     * @param $sStepId
     * @param $sStepName
     * @param $sStepTitle
     * @param string $setupStepPage
     */
    public function registerStep($sNamespace, $sStepId, $sStepName, $sStepTitle, $setupStepPage = '')
    {
        $found = false;
        foreach ($this->_aSteps as $row => $detail) {
            if ($sStepId == $detail->sStepId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }

        if (!$found) {
            $this->_aSteps[] = new StepDetail($sNamespace, $sStepId, $sStepName, $sStepTitle, $setupStepPage);
        }
    }

    /**
     * Return true if the $sFolderName is registered in the singleton
     *
     * @param string $sFolderName
     * @return bool
     */
    public function isRegisteredFolder($sFolderName)
    {
        foreach ($this->_aFolders as $row => $folder) {
            if ($sFolderName == $folder->sFolderName && is_dir(PATH_PLUGINS . $folder->sFolderName)) {
                return true;
            } elseif ($sFolderName == $folder->sFolderName &&
                is_dir(PATH_PLUGINS . $folder->sNamespace . PATH_SEP . $folder->sFolderName)) {
                return $folder->sNamespace;
            }
        }
        return false;
    }

    /**
     * Return all menus related to a menuId
     *
     * @param string $menuId
     */
    public function getMenus($menuId)
    {
        foreach ($this->_aMenus as $row => $detail) {
            if ($menuId == $detail->sMenuId && file_exists($detail->sFilename)) {
                include($detail->sFilename);
            }
        }
    }

    /**
     * return all dashlets classes registered
     *
     * @return array
     */
    public function getDashlets()
    {
        return $this->_aDashlets;
    }

    /**
     * This function returns all reports registered
     *
     * @return array
     */
    public function getReports()
    {
        return $this->_aReports;
//        $report = array();
//        foreach ($this->_aReports as $row => $detail) {
//            $sClassName = str_replace('plugin', 'class', $this->_aPluginDetails[$detail]->sClassName);
//            $report[] = $sClassName;
//        }
//        return $report;
    }

    /**
     * This function returns all pmFunctions registered
     *
     * @return array
     */
    public function getPmFunctions()
    {
        return $this->_aPmFunctions;
//        $pmf = array();
//        foreach ($this->_aPmFunctions as $row => $detail) {
//            $sClassName = str_replace('plugin', 'class', $this->_aPluginDetails[$detail]->sClassName);
//            $pmf[] = $sClassName;
//        }
//        return $pmf;
    }

    /**
     * This function returns all steps registered
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_aSteps;
    }

    /**
     * This function returns all redirect registered
     *
     * @return array
     */
    public function getRedirectLogins()
    {
        return $this->_aRedirectLogin;
    }

    /**
     * execute all triggers related to a triggerId
     *
     * TODO
     *
     * @param string $triggerId
     * @param $oData
     * @return mixed
     */
    public function executeTriggers($triggerId, $oData)
    {
        \G::LoadThirdParty("pear", "PEAR");
        foreach ($this->_aTriggers as $row => $detail) {
            if ($triggerId == $detail->sTriggerId) {
                //review all folders registered for this namespace
                $found = false;
                $classFile = '';

                foreach ($this->_aFolders as $row => $folder) {
                    $fname = $folder->sNamespace == 'enterprise' ?
                        PATH_CORE . 'classes' . PATH_SEP . 'class.' . $folder->sFolderName . '.php' :
                        PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName . '.php';
                    if ($detail->sNamespace == $folder->sNamespace && file_exists($fname)) {
                        $found = true;
                        $classFile = $fname;
                    }
                }
                if ($found) {
                    require_once($classFile);
                    $sClassName = substr($this->_aPluginDetails[$detail->sNamespace]->sClassName, 0, 1) .
                        str_replace(
                            'plugin',
                            'class',
                            substr($this->_aPluginDetails[$detail->sNamespace]->sClassName, 1)
                        );
                    $obj = new $sClassName();
                    $methodName = $detail->sTriggerName;
                    $response = $obj->{$methodName}($oData);
                    if (\PEAR::isError($response)) {
                        print $response->getMessage();
                        return;
                    }
                    return $response;
                } else {
                    print "error in call method " . $detail->sTriggerName;
                }
            }
        }
    }

    /**
     * verify if exists triggers related to a triggerId
     *
     * @param string $triggerId
     * @return bool
     */
    public function existsTrigger($triggerId)
    {
        $found = false;
        foreach ($this->_aTriggers as $row => $detail) {
            if ($triggerId == $detail->sTriggerId) {
                //review all folders registered for this namespace
                foreach ($this->_aFolders as $row => $folder) {
                    $fname = $folder->sNamespace == 'enterprise' ?
                        PATH_CORE . 'classes' . PATH_SEP . 'class.' . $folder->sFolderName . '.php' :
                        PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName . '.php';
                    if ($detail->sNamespace == $folder->sNamespace && file_exists($fname)) {
                        $found = true;
                    }
                }

            }
        }
        return $found;
    }

    /**
     * Return info related to a triggerId
     *
     * @param int $triggerId
     * @return object
     */
    public function getTriggerInfo($triggerId)
    {
        $found = null;
        foreach ($this->_aTriggers as $row => $detail) {
            if ($triggerId == $detail->sTriggerId) {
                //review all folders registered for this namespace
                foreach ($this->_aFolders as $row => $folder) {
                    $fname = PATH_PLUGINS . $folder->sFolderName . PATH_SEP . 'class.' . $folder->sFolderName . '.php';
                    if ($detail->sNamespace == $folder->sNamespace && file_exists($fname)) {
                        $found = $detail;
                    }
                }
            }
        }
        return $found;
    }

    /**
     * Register a trigger in the Singleton
     *
     * @param string $sNamespace
     * @param string $sTriggerId
     * @param string $sTriggerName
     */
    public function registerTrigger($sNamespace, $sTriggerId, $sTriggerName)
    {
        $found = false;
        foreach ($this->_aTriggers as $row => $detail) {
            if ($sTriggerId == $detail->sTriggerId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }
        if (!$found) {
            $triggerDetail = new TriggerDetail($sNamespace, $sTriggerId, $sTriggerName);
            $this->_aTriggers[] = $triggerDetail;
        }
    }

    /**
     * Get plugin
     *
     * @param string $sNamespace
     * @return mixed
     */
    public function &getPlugin($sNamespace)
    {
        $oPlugin = null;
        if (array_key_exists($sNamespace, $this->_aPlugins)) {
            $oPlugin = $this->_aPlugins[$sNamespace];
        }
        return $oPlugin;
    }

    /**
     * Set company logo
     *
     * @param string $sNamespace
     * @param string $filename
     * @return void
     */
    public function setCompanyLogo($sNamespace, $filename)
    {
        foreach ($this->_aPluginDetails as $row => $detail) {
            if ($sNamespace == $detail->sNamespace) {
                $this->_aPluginDetails[$sNamespace]->sCompanyLogo = $filename;
            }
        }
    }

    /**
     * Get company logo
     *
     * @param $default
     * @return mixed
     */
    public function getCompanyLogo($default)
    {
        $sCompanyLogo = $default;
        foreach ($this->_aPluginDetails as $row => $detail) {
            if (trim($detail->sCompanyLogo) != '') {
                $sCompanyLogo = $detail->sCompanyLogo;
            }
        }
        return $sCompanyLogo;
    }

    /**
     * get setup Plugins
     *
     * @param unknown_type $default
     * @return void
     */
    /**
     * @return int
     */
    public function setupPlugins()
    {
        try {
            require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
//            require_once("class.serverConfiguration.php");
            $iPlugins = 0;
//            $oServerConf =& \serverConf::getSingleton();
//            $oServerConf->addPlugin(SYS_SYS, $this->_aPluginDetails);
            foreach ($this->_aPluginDetails as $namespace => $detail) {
                if (isset($detail->enabled) && $detail->enabled) {
                    if (!empty($detail->sFilename) && file_exists($detail->sFilename)) {
                        $arrayFileInfo = pathinfo($detail->sFilename);
                        $sFilename = (($detail->sNamespace == "enterprise") ?
                                PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP :
                                PATH_PLUGINS
                            ) . $arrayFileInfo["basename"];
                        if (!file_exists($sFilename)) {
                            continue;
                        }
                        require_once $sFilename;
                        if (class_exists($detail->sClassName)) {
                            /** @var \enterprisePlugin|\PMPlugin $oPlugin */
                            $oPlugin = new $detail->sClassName($detail->sNamespace, $detail->sFilename);
                            $this->_aPlugins[$detail->sNamespace] = $oPlugin;
                            $iPlugins++;
                            $oPlugin->setup();
                        }
                    }
                }
            }
            return $iPlugins;
        } catch (\Exception $e) {
            global $G_PUBLISH;
            $aMessage['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new \Publisher();
            $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $aMessage);
            \G::RenderPage('publish');
            die();
        }
    }

    /**
     * this function execute a Method
     *
     * @param string $sNamespace
     * @param string $methodName
     * @param object $oData
     * @return mixed
     * @throws \Exception
     */
    public function executeMethod($sNamespace, $methodName, $oData)
    {
        $response = null;
        try {
            $details = $this->_aPluginDetails[$sNamespace];
            $pluginFolder = $details->sPluginFolder;
            $className = $details->sClassName;
            $classFile = PATH_PLUGINS . $pluginFolder . PATH_SEP . 'class.' . $pluginFolder . '.php';
            if (file_exists($classFile)) {
                $sClassName = substr_replace($className, "class", -6, 6);
                //$sClassName = str_replace ( 'plugin', 'class', $className );
                if (!class_exists($sClassName)) {
                    require_once $classFile;
                }
                $obj = new $sClassName();
                if (!in_array($methodName, get_class_methods($obj))) {
                    throw (new \Exception("The method '$methodName' doesn't exist in class '$sClassName' "));
                }
                $obj->sNamespace = $details->sNamespace;
                $obj->sClassName = $details->sClassName;
                $obj->sFilename = $details->sFilename;
                $obj->iVersion = $details->iVersion;
                $obj->sFriendlyName = $details->sFriendlyName;
                $obj->sPluginFolder = $details->sPluginFolder;
                $response = $obj->{$methodName}($oData);
            }
            return $response;
        } catch (\Exception $e) {
            throw ($e);
        }
    }

    /**
     * this function gets Fields For Page on Setup
     *
     * @param string $sNamespace
     * @return object
     */
    public function getFieldsForPageSetup($sNamespace)
    {
        $oData = null;
        return $this->executeMethod($sNamespace, 'getFieldsForPageSetup', $oData);
    }

    /**
     * this function updates Fields For Page on Setup
     *
     * @param string $sNamespace
     * @return mixed
     * @throws \Exception
     */
    public function updateFieldsForPageSetup($sNamespace, $oData)
    {
        if (!isset($this->_aPluginDetails[$sNamespace])) {
            throw (new \Exception("The namespace '$sNamespace' doesn't exist in plugins folder."));
        }
        return $this->executeMethod($sNamespace, 'updateFieldsForPageSetup', $oData);
    }

    /**
     * @deprecated
     */
    public function eevalidate()
    {
//        $fileL = PATH_DATA_SITE . 'license.dat';
//        $fileS = PATH_DATA . 'license.dat';
//        if ((file_exists($fileL)) || (file_exists($fileS))) {
//            //Found a License
//            if (class_exists('pmLicenseManager')) {
//                $sSerializedFile = PATH_DATA_SITE . 'lmn.singleton';
//                $pmLicenseManagerO = &\pmLicenseManager::getSingleton();
//                if (file_exists($sSerializedFile)) {
//                    $pmLicenseManagerO->unSerializeInstance(file_get_contents($sSerializedFile));
//                }
//            }
//        }
    }

    /**
     * Register a toolbar for dynaform editor in the singleton
     *
     * @param string $sNamespace
     * @param string $sToolbarId
     * @param string $sFilename
     */
    public function registerToolbarFile($sNamespace, $sToolbarId, $sFilename)
    {
        $found = false;
        foreach ($this->_aToolbarFiles as $row => $detail) {
            if ($sToolbarId == $detail->sToolbarId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }
        if (!$found) {
            $toolbarDetail = new ToolbarDetail($sNamespace, $sToolbarId, $sFilename);
            $this->_aToolbarFiles[] = $toolbarDetail;
        }
    }

    /**
     * return all toolbar files related to a sToolbarId
     *
     * @param string $sToolbarId (NORMAL, GRID)
     */
    public function getToolbarOptions($sToolbarId)
    {
        foreach ($this->_aToolbarFiles as $row => $detail) {
            if ($sToolbarId == $detail->sToolbarId && file_exists($detail->sFilename)) {
                include($detail->sFilename);
            }
        }
    }

    /**
     * Register a Case Scheduler Plugin
     *
     * @param $sNamespace
     * @param $sActionId
     * @param $sActionForm
     * @param $sActionSave
     * @param $sActionExecute
     * @param $sActionGetFields
     */
    public function registerCaseSchedulerPlugin(
        $sNamespace,
        $sActionId,
        $sActionForm,
        $sActionSave,
        $sActionExecute,
        $sActionGetFields
    )
    {
        $found = false;
        foreach ($this->_aCaseSchedulerPlugin as $row => $detail) {
            if ($sActionId == $detail->sActionId && $sNamespace == $detail->sNamespace) {
                $found = true;
            }
        }

        if (!$found) {
            $this->_aCaseSchedulerPlugin[] = new CaseSchedulerPlugin(
                $sNamespace,
                $sActionId,
                $sActionForm,
                $sActionSave,
                $sActionExecute,
                $sActionGetFields
            );
        }
    }

    /**
     * This function returns all Case Scheduler Plugins registered
     *
     * @return array
     */
    public function getCaseSchedulerPlugins()
    {
        return $this->_aCaseSchedulerPlugin;
    }

    /**
     * Register a Task Extended property page in the singleton
     *
     * @param string $sNamespace
     * @param $sPage
     * @param $sName
     * @param $sIcon
     */
    public function registerTaskExtendedProperty($sNamespace, $sPage, $sName, $sIcon)
    {
        $found = false;
        foreach ($this->_aTaskExtendedProperties as $row => $detail) {
            if ($sPage == $detail->sPage && $sNamespace == $detail->sNamespace) {
                $detail->sName = $sName;
                $detail->sIcon = $sIcon;
                $found = true;
            }
        }
        if (!$found) {
            $taskExtendedProperty = new TaskExtendedProperty($sNamespace, $sPage, $sName, $sIcon);
            $this->_aTaskExtendedProperties[] = $taskExtendedProperty;
        }
    }

    /**
     * Register a dashboard page for cases in the singleton
     *
     * @param string $sNamespace
     * @param string $sPage
     * @param string $sName
     * @param string $sIcon
     */
    public function registerDashboardPage($sNamespace, $sPage, $sName, $sIcon)
    {
        $found = false;
        foreach ($this->_aDashboardPages as $row => $detail) {
            if ($sPage == $detail->sPage && $sNamespace == $detail->sNamespace) {
                $detail->sName = $sName;
                $detail->sIcon = $sIcon;
                $found = true;
            }
        }
        if (!$found) {
            $dashboardPage = new DashboardPage($sNamespace, $sPage, $sName, $sIcon);
            $this->_aDashboardPages[] = $dashboardPage;
        }
    }

    /**
     * Register a rest service class from a plugin to be served by processmaker
     *
     * @param string $sNamespace The namespace for the plugin
     * @return bool
     */
    public function registerRestService($sNamespace)
    {
        $baseSrcPluginPath = PATH_PLUGINS . $sNamespace . PATH_SEP . "src";
        $apiPath = PATH_SEP . "Services" . PATH_SEP . "Api" . PATH_SEP . ucfirst($sNamespace);
        $classesList = (new \Bootstrap())->rglob('*', 0, $baseSrcPluginPath . $apiPath);

        foreach ($classesList as $classFile) {
            if (pathinfo($classFile, PATHINFO_EXTENSION) === 'php') {
                $ns = str_replace(
                    '/',
                    '\\',
                    str_replace('.php', '', str_replace($baseSrcPluginPath, '', $classFile))
                );

                // Ensure that is registering only existent classes.
                if (class_exists($ns)) {
                    $this->_restServices[$sNamespace][] = array(
                        "filepath" => $classFile,
                        "namespace" => $ns
                    );
                }
            }
        }

        WebApplication::purgeRestApiCache(basename(PATH_DATA_SITE));

        return true;
    }

    /**
     * Register a extend rest service class from a plugin to be served by processmaker
     *
     * @param string $sNamespace The namespace for the plugin
     * @param string $className The service (api) class name
     */
    public function registerExtendsRestService($sNamespace, $className)
    {
        $baseSrcPluginPath = PATH_PLUGINS . $sNamespace . PATH_SEP . "src";
        $apiPath = PATH_SEP . "Services" . PATH_SEP . "Ext" . PATH_SEP;
        $classFile = $baseSrcPluginPath . $apiPath . 'Ext' . $className . '.php';
        if (file_exists($classFile)) {
            $this->_restExtendServices[$sNamespace][$className] = array(
                "filePath" => $classFile,
                "classParent" => $className,
                "classExtend" => 'Ext' . $className
            );
        }
    }

    /**
     * Get a extend rest service class from a plugin to be served by processmaker
     *
     * @param string $className The service (api) class name
     * @return array
     */
    public function getExtendsRestService($className)
    {
        $responseRestExtendService = array();
        foreach ($this->_restExtendServices as $sNamespace => $restExtendService) {
            if (isset($restExtendService[$className])) {
                $responseRestExtendService = $restExtendService[$className];
                break;
            }
        }
        return $responseRestExtendService;
    }

    /**
     * Remove a extend rest service class from a plugin to be served by processmaker
     *
     * @param string $sNamespace
     * @param string $className The service (api) class name
     * @return bool
     */
    public function disableExtendsRestService($sNamespace, $className = '')
    {
        if (isset($this->_restExtendServices[$sNamespace][$className]) && !empty($className)) {
            unset($this->_restExtendServices[$sNamespace][$className]);
        } elseif (empty($className)) {
            unset($this->_restExtendServices[$sNamespace]);
        }
    }

    /**
     * Unregister a rest service class of a plugin
     *
     * @param string $sNamespace The namespace for the plugin
     */
    public function unregisterRestService($sNamespace)
    {
        if ($this->_restServices) {
            unset($this->_restServices[$sNamespace]);
            WebApplication::purgeRestApiCache(basename(PATH_DATA_SITE));
        }
    }

    public function getRegisteredRestServices()
    {
        return $this->_restServices;
    }

    /**
     * return all dashboard pages
     *
     * @return array
     */
    public function getDashboardPages()
    {
        return $this->_aDashboardPages;
    }

    /**
     * return all tasl extended properties
     *
     * @return array
     */
    public function getTaskExtendedProperties()
    {
        return $this->_aTaskExtendedProperties;
    }

    public function registerDashboard()
    {
        // Dummy function for backwards compatibility
    }

    public function getAttributes()
    {
        return get_object_vars($this);
    }

    public function verifyTranslation($namePlugin)
    {
        $language = new \Language();
        $pathPluginTranslations = PATH_PLUGINS . $namePlugin . PATH_SEP . 'translations' . PATH_SEP;
        if (file_exists($pathPluginTranslations . 'translations.php')) {
            if (!file_exists($pathPluginTranslations . $namePlugin . '.' . SYS_LANG . '.po')) {
                $language->createLanguagePlugin($namePlugin, SYS_LANG);
            }
            $language->updateLanguagePlugin($namePlugin, SYS_LANG);
        }
    }

    /**
     * Register a cron file in the singleton
     *
     * @param string $namespace
     * @param string $cronFile
     */
    public function registerCronFile($namespace, $cronFile)
    {
        $found = false;
        foreach ($this->_aCronFiles as $row => $detail) {
            if ($cronFile == $detail->cronFile && $namespace == $detail->namespace) {
                $detail->cronFile = $cronFile;
                $found = true;
            }
        }
        if (!$found) {
            $cronFile = new CronFile($namespace, $cronFile);
            $this->_aCronFiles[] = $cronFile;
        }
    }

    /**
     * Function to enable rest service for plugins
     * @param string $sNamespace
     * @param bool $enable
     */
    public function enableRestService($sNamespace, $enable)
    {
        $this->_restServiceEnabled[$sNamespace] = $enable;
    }

    /**
     * Return all cron files registered
     *
     * @return array
     */
    public function getCronFiles()
    {
        return $this->_aCronFiles;
    }

    /**
     * Update the plugin attributes in all workspaces
     *
     * @param string $pluginName Plugin name
     * @throws \Exception
     */
    public function updatePluginAttributesInAllWorkspaces($pluginName)
    {
        try {
            \G::LoadClass("wsTools");

            //Set variables
            $pluginFileName = $pluginName . ".php";

            //Verify data
            if (!file_exists(PATH_PLUGINS . $pluginFileName)) {
                throw new \Exception("Error: The plugin not exists");
            }

            //Update plugin attributes
            require_once(PATH_PLUGINS . $pluginFileName);

            $pmPluginRegistry =& \PMPluginRegistry::getSingleton();

            $pluginDetails = $pmPluginRegistry->getPluginDetails($pluginFileName);

            if (isset($pluginDetails->aWorkspaces) &&
                is_array($pluginDetails->aWorkspaces) &&
                count($pluginDetails->aWorkspaces) > 0
            ) {
                $arrayWorkspace = array();

                foreach (\System::listWorkspaces() as $value) {
                    $workspaceTools = $value;

                    $arrayWorkspace[] = $workspaceTools->name;
                }

                $arrayWorkspaceAux = array_diff($arrayWorkspace, $pluginDetails->aWorkspaces); //Workspaces to update
                $strWorkspaceNoWritable = "";

                $arrayWorkspace = array();

                foreach ($arrayWorkspaceAux as $value) {
                    $workspace = $value;

                    $workspacePathDataSite = PATH_DATA . "sites" . PATH_SEP . $workspace . PATH_SEP;

                    if (file_exists($workspacePathDataSite . "plugin.singleton")) {
                        $pmPluginRegistry = PMPluginRegistry::loadSingleton(
                            $workspacePathDataSite . "plugin.singleton"
                        );

                        if (isset($pmPluginRegistry->_aPluginDetails[$pluginName])) {
                            if (!is_writable($workspacePathDataSite . "plugin.singleton")) {
                                $strWorkspaceNoWritable .= (($strWorkspaceNoWritable != "") ? ", " : "") . $workspace;
                            }

                            $arrayWorkspace[] = $workspace;
                        }
                    }
                }

                //Verify data
                if ($strWorkspaceNoWritable != "") {
                    throw new \Exception(
                        'Error: The workspaces ' .
                        $strWorkspaceNoWritable .
                        ' has problems of permissions of write in file "plugin.singleton", solve this problem'
                    );
                }

                //Update plugin attributes
                foreach ($arrayWorkspace as $value) {
                    $workspace = $value;

                    $workspacePathDataSite = PATH_DATA . "sites" . PATH_SEP . $workspace . PATH_SEP;

                    $pmPluginRegistry = \PMPluginRegistry::loadSingleton($workspacePathDataSite . "plugin.singleton");

                    $pmPluginRegistry->disablePlugin($pluginName);

                    file_put_contents(
                        $workspacePathDataSite . "plugin.singleton",
                        $pmPluginRegistry->serializeInstance()
                    );
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Register designer menu file
     *
     * @param string $pluginName Plugin name
     * @param string $file Designer menu file
     * @throws \Exception
     */
    public function registerDesignerMenu($pluginName, $file)
    {
        try {
            $flagFound = false;

            foreach ($this->_arrayDesignerMenu as $value) {
                if ($value->pluginName == $pluginName && $value->file == $file) {
                    $flagFound = true;
                    break;
                }
            }

            if (!$flagFound) {
                $obj = new \stdClass();
                $obj->pluginName = $pluginName;
                $obj->file = $file;

                $this->_arrayDesignerMenu[] = $obj;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return all designer menu files registered
     *
     * @return array
     * @throws \Exception
     */
    public function getDesignerMenu()
    {
        try {
            return $this->_arrayDesignerMenu;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Replace new options to menu
     *
     * @param string $namespace
     * @param array $from
     * @param array $options
     * @return void
     */
    public function registerMenuOptionsToReplace($namespace, $from, $options)
    {
        if (isset($from["section"]) && isset($from["menuId"])) {
            $section = $from["section"];
            $oMenuFromPlugin = $this->_aMenuOptionsToReplace;
            if (array_key_exists($section, $oMenuFromPlugin)) {
                unset($this->_aMenuOptionsToReplace[$from["section"]]);
            }
            $this->_aMenuOptionsToReplace[$from["section"]][$from["menuId"]][] = $options;
        }
    }

    /**
     * Return all menu Options from a specific section
     *
     * @param string $strMenuName
     * @return array
     */
    public function getMenuOptionsToReplace($strMenuName)
    {
        $oMenuFromPlugin = $this->_aMenuOptionsToReplace;
        if (sizeof($oMenuFromPlugin)) {
            if (array_key_exists($strMenuName, $oMenuFromPlugin)) {
                return $oMenuFromPlugin[$strMenuName];
            }
        }
    }

    /**
     * Register a callBackFile in the singleton
     *
     * @param string $namespace
     * @param string $callBackFile
     * @throws \Exception
     */
    public function registerImportProcessCallback($namespace, $callBackFile)
    {
        try {
            $found = false;
            foreach ($this->_aImportProcessCallbackFile as $row => $detail) {
                if ($callBackFile == $detail->callBackFile && $namespace == $detail->namespace) {
                    $detail->callBackFile = $callBackFile;
                    $found = true;
                }
            }
            if (!$found) {
                $callBackFile = new ImportCallBack($namespace, $callBackFile);
                $this->_aImportProcessCallbackFile[] = $callBackFile;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return all callBackFiles registered
     *
     * @return array
     */
    public function getImportProcessCallback()
    {
        return $this->_aImportProcessCallbackFile;
    }

    /**
     * Register a callBackFile in the singleton
     *
     * @param string $callBackFile
     * @throws \Exception
     */
    public function registerOpenReassignCallback($callBackFile)
    {
        try {
            $found = false;
            foreach ($this->_aOpenReassignCallback as $row => $detail) {
                if ($callBackFile == $detail->callBackFile) {
                    $detail->callBackFile = $callBackFile;
                    $found = true;
                }
            }
            if (!$found) {
                $callBackFile = new OpenReassignCallback($callBackFile);
                $this->_aOpenReassignCallback[] = $callBackFile;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return all callBackFiles registered
     *
     * @return array
     */
    public function getOpenReassignCallback()
    {
        return $this->_aOpenReassignCallback;
    }

    public function getPluginsData()
    {
        return $this->_aPlugins;
    }

    /**
     * The following function method extracts the plugin if exists one
     * with the same uppercase characters, this is required for the
     *
     * @param $code
     * @return array|object
     */
    public function getPluginByCode($code)
    {
        $plugin = new \stdClass();
        foreach ($this->_aPlugins as $plugin) {
            $plugin = (array)$plugin;
            if (strtoupper($plugin['sNamespace']) == $code) {
                return (object)$plugin;
            }
        }
        return $plugin;
    }

    /**
     * Checks if the plugin name is Enterprise Plugin
     *
     * @param string $pluginName Plugin name
     * @param string $path Path to plugin
     *
     * @return bool Returns TRUE when plugin name is Enterprise Plugin, FALSE otherwise
     */
    public function isEnterprisePlugin($pluginName, $path = null)
    {
        $path = (!is_null($path) && $path != '') ? rtrim($path, '/\\') . PATH_SEP : PATH_PLUGINS;
        $pluginFile = $pluginName . '.php';

        //Return
        return preg_match(
            '/^.*class\s+' . $pluginName . 'Plugin\s+extends\s+(?:enterprisePlugin)\s*\{.*$/i',
            str_replace(["\n", "\r", "\t"], ' ', file_get_contents($path . $pluginFile))
        );
    }

    /**
     * Registry in an array routes for js or css files.
     * @param string $pluginName
     * @param string $pathFile
     * @throws \Exception
     */
    public function registerDesignerSourcePath($pluginName, $pathFile)
    {
        try {
            $flagFound = false;

            foreach ($this->_arrayDesignerSourcePath as $value) {
                if ($value->pluginName == $pluginName && $value->pathFile == $pathFile) {
                    $flagFound = true;
                    break;
                }
            }

            if (!$flagFound) {
                $obj = new \stdClass();
                $obj->pluginName = $pluginName;
                $obj->pathFile = $pathFile;

                $this->_arrayDesignerSourcePath[] = $obj;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * You obtain an array containing the routes recorded by the
     * function registerDesignerSourcePath.
     * @return array
     * @throws \Exception
     */
    public function getDesignerSourcePath()
    {
        try {
            return $this->_arrayDesignerSourcePath;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function iterateVisible()
    {
        $response = array();
        foreach ($this as $key => $value) {
            $response[$key] = $value;
        }
        return $response;
    }
}
