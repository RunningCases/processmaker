<?php

namespace Tests\ProcessMaker\Plugins;

use ProcessMaker\Plugins\Interfaces\CssFile;
use ProcessMaker\Plugins\Interfaces\FolderDetail;
use ProcessMaker\Plugins\Interfaces\MenuDetail;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\Interfaces\Plugins;
use ProcessMaker\Plugins\Interfaces\RedirectDetail;
use ProcessMaker\Plugins\Interfaces\StepDetail;
use ProcessMaker\Plugins\Interfaces\TriggerDetail;
use ProcessMaker\Plugins\PluginsRegistry;
use Tests\WorkflowTestCase;

class PluginsRegistryTest extends WorkflowTestCase
{
    /**
     * @var PluginsRegistry $oPluginRegistry
     */
    protected $oPluginRegistry;
    /**
     * This is called before each test method.
     * Initialize and create required objects in DB
     */
    public function setUp()
    {
        // We should call our Parent setUp before we do anything else in setUp method
        parent::setUp();
        $this->oPluginRegistry = new PluginsRegistry();
        $this->oPluginRegistry->setupPlugins();
    }

    /**
     * This is the default method to test, if the class still having
     * the same number of methods.
     */
    public function testNumberOfMethodsInThisClass()
    {
        $methods = get_class_methods('\ProcessMaker\Plugins\PluginsRegistry');
        $this->assertTrue(count($methods) == 82, count($methods));
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::__construct
     */
    public function testConstruct()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('__construct', $methods), 'exists method __construct');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', '__construct');
        $r->getParameters();
    }

    /**
     * Test get Plugins
     */
    public function testGetPlugins()
    {
        $this->assertObjectHasAttribute('Plugins', $this->oPluginRegistry, 'Plugins attribute does not exist');
        $this->assertEquals([], $this->oPluginRegistry->getPlugins(), 'The Plugins attribute is not an array');
    }

    /**
     * Test set Plugins
     */
    public function testSetPlugins()
    {
        $this->assertObjectHasAttribute('Plugins', $this->oPluginRegistry, 'Plugins attribute does not exist');
        $this->oPluginRegistry->setPlugins([]);
        $this->assertEquals([], $this->oPluginRegistry->getPlugins(), 'The Plugins attribute is not an array');
    }

    /**
     * Test load singleton of database
     */
    public function testLoadSingleton()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $this->assertObjectHasAttribute('Plugins', $oPluginRegistry, 'Plugins attribute does not exist');
        $this->assertInstanceOf(Plugins::class, $oPluginRegistry, '');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerPlugin
     */
    public function testRegisterPluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerPlugin', $methods), 'exists method registerPlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerPlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sFilename');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == true);
        $this->assertTrue($params[1]->getDefaultValue() == '');
    }

    /**
     * Test registry plugin
     */
    public function testRegisterPlugin()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'enterprise.php';
        //add the plugin php file
        require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $oPluginRegistry->registerPlugin("enterprise", $pluginFile);
        $this->assertObjectHasAttribute('_aPluginDetails', $oPluginRegistry, 'Plugins attribute does not exist');
        $this->assertInstanceOf(PluginDetail::class, $oPluginRegistry->_aPluginDetails['enterprise'], '');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getPluginDetails
     */
    public function testGetPluginDetailsParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getPluginDetails', $methods), 'exists method getPluginDetails');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getPluginDetails');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sFilename');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test get plugin details
     */
    public function testGetPluginDetails()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'enterprise.php';
        //add the plugin php file
        require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $details = $oPluginRegistry->getPluginDetails($pluginFile);
        $this->assertEquals('enterprise', $details->sNamespace, 'Namespace attribute does not equals');
        $this->assertObjectHasAttribute('sNamespace', $details, 'sNamespace attribute does not exist');
        $this->assertInstanceOf(PluginDetail::class, $details, '');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::enablePlugin
     */
    public function testEnablePluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('enablePlugin', $methods), 'exists method enablePlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'enablePlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test enable plugin
     */
    public function testEnablePlugin()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'enterprise.php';
        //add the plugin php file
        require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $details = $oPluginRegistry->getPluginDetails($pluginFile);
        $this->assertEquals(false, $details->enabled, 'Not disable Plugin');
        $result = $oPluginRegistry->enablePlugin($details->sNamespace);
        $this->assertEquals(true, $result, 'Plugin is enable');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::disablePlugin
     */
    public function testDisablePluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('disablePlugin', $methods), 'exists method disablePlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'disablePlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'eventPlugin');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == true);
        $this->assertTrue($params[1]->getDefaultValue() == '1');
    }

    /**
     * Test disable plugin
     */
    public function testDisablePlugin()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'enterprise.php';
        //add the plugin php file
        require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $details = $oPluginRegistry->getPluginDetails($pluginFile);
        $result = $oPluginRegistry->enablePlugin($details->sNamespace);
        $this->assertEquals(true, $result, 'Plugin is enable');
        $oPluginRegistry->disablePlugin($details->sNamespace);
        $details = $oPluginRegistry->getPluginDetails($pluginFile);
        $this->assertEquals(false, $details->enabled, 'Plugin is enable');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getStatusPlugin
     */
    public function testGetStatusPluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getStatusPlugin', $methods), 'exists method getStatusPlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getStatusPlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'name');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test get status plugin
     */
    public function testGetStatusPlugin()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'enterprise.php';
        //add the plugin php file
        require_once(PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $details = $oPluginRegistry->getPluginDetails($pluginFile);
        $result = $oPluginRegistry->getStatusPlugin($pluginFile);
        $this->assertEquals(false, $result, 'Plugin is enabled');
        $oPluginRegistry->enablePlugin($details->sNamespace);
        $details = $oPluginRegistry->getPluginDetails($pluginFile);
        $this->assertEquals(true, $details->enabled, 'Plugin is disabled');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::installPluginArchive
     */
    public function testInstallPluginArchiveParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('installPluginArchive', $methods), 'exists method installPluginArchive');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'installPluginArchive');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'filename');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'pluginName');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::uninstallPlugin
     */
    public function testUninstallPluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('uninstallPlugin', $methods), 'exists method uninstallPlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'uninstallPlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::uninstallPluginWorkspaces
     */
    public function testUninstallPluginWorkspaces()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('uninstallPluginWorkspaces', $methods), 'exists method uninstallPluginWorkspaces');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'uninstallPluginWorkspaces');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'arrayPlugin');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::installPlugin
     */
    public function testInstallPlugin()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('installPlugin', $methods), 'exists method installPlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'installPlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerMenu
     */
    public function testRegisterMenuParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerMenu', $methods), 'exists method registerMenu');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerMenu');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sMenuId');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sFilename');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    /**
     * Test register menu
     */
    public function testRegisterMenu()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerMenu(
            "enterprise",
            'setup',
            PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php"
        );
        $this->assertEquals(
            'enterprise',
            $oPluginRegistry->_aMenus[0]->sNamespace,
            'Namespace attribute does not equals'
        );
        $this->assertObjectHasAttribute('sMenuId', $oPluginRegistry->_aMenus[0], 'sMenuId attribute does not exist');
        $this->assertInstanceOf(MenuDetail::class, $oPluginRegistry->_aMenus[0], '');
        $oPluginRegistry->registerMenu(
            "enterprise",
            'setup',
            PATH_CORE . "methods" . PATH_SEP . "enterprise" . PATH_SEP . "enterprise.php"
        );
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerDashlets
     */
    public function testRegisterDashletsParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerDashlets', $methods), 'exists method registerDashlets');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerDashlets');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'namespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test register dashlets
     */
    public function testRegisterDashlets()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerDashlets("enterprise");
        $this->assertEquals(
            'enterprise',
            $oPluginRegistry->_aDashlets[0],
            'Namespace attribute does not equals'
        );
        $oPluginRegistry->registerDashlets("enterprise");
        $this->assertEquals('enterprise', $oPluginRegistry->_aDashlets[0], 'sMenuId attribute does not exist');
        $this->assertTrue(is_array($oPluginRegistry->_aDashlets));
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerCss
     */
    public function testRegisterCssParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerCss', $methods), 'exists method registerCss');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerCss');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sCssFile');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
    }

    /**
     * Test register Css
     */
    public function testRegisterCss()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerCss('enterprise', PATH_CORE . 'css' . PATH_SEP . 'test.css');
        $this->assertEquals(
            PATH_CORE . 'css' . PATH_SEP . 'test.css',
            $oPluginRegistry->_aCSSStyleSheets[0]->sCssFile,
            'sCssFile attribute does not equals'
        );
        $oPluginRegistry->registerCss('enterprise', PATH_CORE . 'css' . PATH_SEP . 'test.css');
        $this->assertObjectHasAttribute(
            'sCssFile',
            $oPluginRegistry->_aCSSStyleSheets[0],
            'sCssFile attribute does not exist'
        );
        $this->assertInstanceOf(CssFile::class, $oPluginRegistry->_aCSSStyleSheets[0], '');
    }

    /**
     * Test get registered css
     */
    public function testGetRegisteredCss()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerCss('enterprise', PATH_CORE . 'css' . PATH_SEP . 'test.css');
        $css = $oPluginRegistry->getRegisteredCss();
        $this->assertEquals(
            PATH_CORE . 'css' . PATH_SEP . 'test.css',
            $css[0]->sCssFile,
            'sCssFile attribute does not equals'
        );
        $this->assertObjectHasAttribute(
            'sCssFile',
            $css[0],
            'sCssFile attribute does not exist'
        );
        $this->assertInstanceOf(CssFile::class, $css[0], '');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerJavascript
     */
    public function testRegisterJavascriptParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerJavascript', $methods), 'exists method registerJavascript');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerJavascript');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sCoreJsFile');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'pluginJsFile');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    /**
     * Test register javascript
     */
    public function testRegisterJavascript()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore.js',
            PATH_PLUGINS . 'js' . PATH_SEP . 'test.js'
        );
        $this->assertEquals(
            PATH_PLUGINS . 'js' . PATH_SEP . 'test.js',
            $oPluginRegistry->_aJavascripts[0]->pluginJsFile[0],
            'sCssFile attribute does not equals'
        );
        $js = $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore.js',
            PATH_PLUGINS . 'js' . PATH_SEP . 'test.js'
        );
        $this->assertEquals(
            PATH_PLUGINS . 'js' . PATH_SEP . 'test.js',
            $js->pluginJsFile[0],
            'sCssFile attribute does not equals'
        );
        // Test send an array
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore2.js',
            [PATH_PLUGINS . 'js' . PATH_SEP . 'test2.js']
        );
        $this->assertEquals(
            PATH_PLUGINS . 'js' . PATH_SEP . 'test2.js',
            $oPluginRegistry->_aJavascripts[1]->pluginJsFile[0],
            'sCssFile attribute does not equals'
        );
        $js = $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore2.js',
            [PATH_PLUGINS . 'js' . PATH_SEP . 'test2.js']
        );
        $this->assertEquals(
            PATH_PLUGINS . 'js' . PATH_SEP . 'test2.js',
            $js->pluginJsFile[0],
            'sCssFile attribute does not equals'
        );
    }

    /**
     * Test throw register javascript send array
     */
    public function testRegisterJavascriptThrowArray()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $this->expectException(\Exception::class);
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore.js',
            true
        );
    }

    /**
     * Test throw register javascript send string
     */
    public function testRegisterJavascriptThrowString()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $this->expectException(\Exception::class);
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore3.js',
            true
        );
    }

    /**
     * Test get registered javascript
     */
    public function testGetRegisteredJavascript()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore2.js',
            [PATH_PLUGINS . 'js' . PATH_SEP . 'test2.js']
        );
        $js = $oPluginRegistry->getRegisteredJavascript();
        $this->assertEquals(2, count($js));
        $this->assertObjectHasAttribute(
            'pluginJsFile',
            $js[0],
            'pluginJsFile attribute does not exist'
        );
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getRegisteredJavascriptBy
     */
    public function testGetRegisteredJavascriptByParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getRegisteredJavascriptBy', $methods), 'exists method getRegisteredJavascriptBy');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getRegisteredJavascriptBy');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sCoreJsFile');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sNamespace');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == true);
        $this->assertTrue($params[1]->getDefaultValue() == '');
    }

    /**
     * Test get registered javascript by path
     */
    public function testGetRegisteredJavascriptBy()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore.js',
            [PATH_PLUGINS . 'js' . PATH_SEP . 'test.js']
        );
        $js = $oPluginRegistry->getRegisteredJavascriptBy(PATH_CORE . 'js' . PATH_SEP . 'testCore.js');
        $this->assertEquals(PATH_PLUGINS . 'js' . PATH_SEP . 'test.js', $js[0]);
        $oPluginRegistry->getRegisteredJavascriptBy(PATH_CORE . 'js' . PATH_SEP . 'testCore.js', 'enterprise');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::unregisterJavascripts
     */
    public function testUnregisterJavascriptsParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('unregisterJavascripts', $methods), 'exists method unregisterJavascripts');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'unregisterJavascripts');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sCoreJsFile');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == true);
        $this->assertTrue($params[1]->getDefaultValue() == '');
    }

    /**
     * Test get unregistered javascript
     */
    public function testUnregisterJavascripts()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore.js',
            [PATH_PLUGINS . 'js' . PATH_SEP . 'test.js']
        );
        $oPluginRegistry->unregisterJavascripts('enterprise');
        $this->assertEquals(0, count($oPluginRegistry->_aJavascripts));
        $oPluginRegistry->registerJavascript(
            'enterprise',
            PATH_CORE . 'js' . PATH_SEP . 'testCore.js',
            [PATH_PLUGINS . 'js' . PATH_SEP . 'test.js']
        );
        $oPluginRegistry->unregisterJavascripts('enterprise', PATH_CORE . 'js' . PATH_SEP . 'testCore.js');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerReport
     */
    public function testRegisterReportParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerReport', $methods), 'exists method registerReport');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerReport');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test register report
     */
    public function testRegisterReport()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerReport('enterprise');
        $this->assertEquals(1, count($oPluginRegistry->_aReports));
        $oPluginRegistry->registerReport('enterprise');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerPmFunction
     */
    public function testRegisterPmFunctionParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerPmFunction', $methods), 'exists method registerPmFunction');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerPmFunction');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test register pmfunction
     */
    public function testRegisterPmFunction()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerPmFunction('enterprise');
        $this->assertEquals(1, count($oPluginRegistry->_aPmFunctions));
        $oPluginRegistry->registerPmFunction('enterprise');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerRedirectLogin
     */
    public function testRegisterRedirectLoginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerRedirectLogin', $methods), 'exists method registerRedirectLogin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerRedirectLogin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sRole');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sPathMethod');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    /**
     * Test registered redirect login
     */
    public function testRegisterRedirectLogin()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerRedirectLogin('enterprise', 'TEST', PATH_CORE . 'js' . PATH_SEP . 'testCore.js');
        $this->assertEquals(1, count($oPluginRegistry->_aRedirectLogin));
        $this->assertInstanceOf(RedirectDetail::class, $oPluginRegistry->_aRedirectLogin[0], '');
        $oPluginRegistry->registerRedirectLogin('enterprise', 'TEST', PATH_CORE . 'js' . PATH_SEP . 'testCore.js');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerFolder
     */
    public function testRegisterFolderParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerFolder', $methods), 'exists method registerFolder');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerFolder');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sFolderId');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sFolderName');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    /**
     * Test registered folder
     */
    public function testRegisterFolder()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerFolder('enterprise', '1234', 'TestCore');
        $this->assertEquals(2, count($oPluginRegistry->_aFolders));
        $this->assertInstanceOf(FolderDetail::class, $oPluginRegistry->_aFolders[1], '');
        $oPluginRegistry->registerFolder('enterprise', '1234', 'TestCore');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerStep
     */
    public function testRegisterStepParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerStep', $methods), 'exists method registerStep');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerStep');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sStepId');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sStepName');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
        $this->assertTrue($params[3]->getName() == 'sStepTitle');
        $this->assertTrue($params[3]->isArray() == false);
        $this->assertTrue($params[3]->isOptional() == false);
        $this->assertTrue($params[4]->getName() == 'setupStepPage');
        $this->assertTrue($params[4]->isArray() == false);
        $this->assertTrue($params[4]->isOptional() == true);
        $this->assertTrue($params[4]->getDefaultValue() == '');
    }

    /**
     * Test register step
     */
    public function testRegisterStep()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerStep('enterprise', '1234', 'TestStep', 'StepTitle');
        $this->assertEquals(1, count($oPluginRegistry->_aSteps));
        $this->assertInstanceOf(StepDetail::class, $oPluginRegistry->_aSteps[0], '');
        $oPluginRegistry->registerStep('enterprise', '1234', 'TestStep', 'StepTitle');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::isRegisteredFolder
     */
    public function testIsRegisteredFolderParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('isRegisteredFolder', $methods), 'exists method isRegisteredFolder');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'isRegisteredFolder');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sFolderName');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test is registered folder
     */
    public function testIsRegisteredFolder()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerFolder('charts', '1234', 'charts');
        $step = $oPluginRegistry->isRegisteredFolder('charts');
        $this->assertTrue($step);
        $oPluginRegistry->registerFolder('pmosCommunity', '1234', 'config');
        $step = $oPluginRegistry->isRegisteredFolder('config');
        $this->assertEquals('pmosCommunity', $step);
        $step = $oPluginRegistry->isRegisteredFolder('noExist');
        $this->assertFalse($step);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getMenus
     */
    public function testGetMenusParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getMenus', $methods), 'exists method getMenus');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getMenus');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'menuId');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * Test get menus
     */
    public function testGetMenus()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('enterprise');
        $oPluginRegistry->registerMenu(
            'charts',
            'processmaker',
            PATH_PLUGINS . 'charts.php'
        );
        $oPluginRegistry->getMenus('processmaker');
        $this->assertTrue(file_exists($oPluginRegistry->_aMenus[0]->sFilename));
    }

    /**
     * Test get dashlets
     */
    public function testGetDashlets()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('enterprise');
        $this->assertTrue(is_array($oPluginRegistry->getDashlets()));
    }

    /**
     * Test get reports
     */
    public function testGetReports()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('enterprise');
        $this->assertTrue(is_array($oPluginRegistry->getReports()));
    }

    /**
     * Test get pmfunction
     */
    public function testGetPmFunctions()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('enterprise');
        $this->assertTrue(is_array($oPluginRegistry->getPmFunctions()));
    }

    /**
     * Test get steps
     */
    public function testGetSteps()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('enterprise');
        $this->assertTrue(is_array($oPluginRegistry->getSteps()));
    }

    /**
     * Test get redirect login
     */
    public function testGetRedirectLogins()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('enterprise');
        $this->assertTrue(is_array($oPluginRegistry->getRedirectLogins()));
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerTrigger
     */
    public function testRegisterTriggerParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerTrigger', $methods), 'exists method registerTrigger');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerTrigger');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sTriggerId');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sTriggerName');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    public function testRegisterTrigger()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerTrigger('enterprise', 'test1234', 'TestTrigger');
        $this->assertEquals(
            'TestTrigger',
            $oPluginRegistry->_aTriggers[0]->sTriggerName,
            'sTriggerName attribute does not equals'
        );
        $this->assertObjectHasAttribute(
            'sTriggerId',
            $oPluginRegistry->_aTriggers[0],
            'sTriggerId attribute does not exist'
        );
        $this->assertInstanceOf(TriggerDetail::class, $oPluginRegistry->_aTriggers[0], '');
        $oPluginRegistry->registerTrigger('enterprise', 'test1234', 'TestTrigger');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getTriggerInfo
     */
    public function testGetTriggerInfoParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getTriggerInfo', $methods), 'exists method getTriggerInfo');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getTriggerInfo');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'triggerId');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    public function testGetTriggerInfo()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin('charts');
        $oPluginRegistry->registerFolder('charts', '1234', 'charts');
        $oPluginRegistry->registerTrigger('charts', 'trigger1234', 'TestTrigger');
        $trigger = $oPluginRegistry->getTriggerInfo('trigger1234');
        $this->assertEquals(
            'TestTrigger',
            $trigger->sTriggerName,
            'sTriggerName attribute does not equals'
        );
        $this->assertObjectHasAttribute(
            'sTriggerId',
            $trigger,
            'sTriggerId attribute does not exist'
        );
        $this->assertInstanceOf(TriggerDetail::class, $trigger, '');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::existsTrigger
     */
    public function testExistsTriggerParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('existsTrigger', $methods), 'exists method existsTrigger');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'existsTrigger');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'triggerId');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    public function testExistsTrigger()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->registerFolder('enterprise', '1234', 'charts');
        $oPluginRegistry->registerTrigger('enterprise', 'trigger1234', 'TestTrigger');
        $trigger = $oPluginRegistry->existsTrigger('trigger1234');
        $this->assertTrue($trigger, 'sTriggerName attribute does not equals');
        $trigger = $oPluginRegistry->existsTrigger('NoExist');
        $this->assertFalse($trigger, 'sTriggerId attribute does not exist');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::executeTriggers
     */
    public function testExecuteTriggersParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('executeTriggers', $methods), 'exists method executeTriggers');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'executeTriggers');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'triggerId');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'oData');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
    }

    public function testExecuteTriggers()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'pmosCommunity.php';
        //add the plugin php file
        require_once(PATH_PLUGINS . "pmosCommunity.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $oPluginRegistry->registerPlugin("pmosCommunity", $pluginFile);
        $oPluginRegistry->registerFolder('pmosCommunity', 'Folder1234', 'pmosCommunity');
        $oPluginRegistry->registerTrigger('pmosCommunity', 'Trigger1234', 'getAvailableCharts');
        $trigger = $oPluginRegistry->executeTriggers('Trigger1234', []);
        $this->assertTrue(is_array($trigger), ' attribute does not equals');
        $this->assertContains('ForumWeek', $trigger, 'ForumWeek element does not exist');
        $oPluginRegistry->registerTrigger('enterprise1', 'Trigger4321', 'notExist');
        $trigger = $oPluginRegistry->executeTriggers('Trigger4321', []);
        $this->assertNull($trigger, 'sTriggerId attribute does not exist');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getPlugin
     */
    public function testGetPluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getPlugin', $methods), 'exists method getPlugin');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getPlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    public function testGetPlugin()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $plugin = $oPluginRegistry->getPlugin('enterprise');
        $this->assertEquals(
            'enterprise',
            $plugin->sPluginFolder,
            'sPluginFolder attribute does not equals'
        );
        $this->assertObjectHasAttribute('sNamespace', $plugin, 'sNamespace attribute does not exist');
        $this->assertInstanceOf(\enterprisePlugin::class, $plugin, '');
        $plugin = $oPluginRegistry->getPlugin('namespaceNoExist');
        $this->assertNull($plugin);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::setCompanyLogo
     */
    public function testSetCompanyLogoParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('setCompanyLogo', $methods), 'exists method setCompanyLogo');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'setCompanyLogo');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'filename');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
    }

    public function testSetCompanyLogo()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->setCompanyLogo('enterprise', PATH_PLUGINS . 'testLogo.png');
        $this->assertEquals(
            PATH_PLUGINS . 'testLogo.png',
            $oPluginRegistry->_aPluginDetails['enterprise']->sCompanyLogo,
            'sPluginFolder attribute does not equals'
        );
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getCompanyLogo
     */
    public function testGetCompanyLogoParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getCompanyLogo', $methods), 'exists method getCompanyLogo');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getCompanyLogo');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'default');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    public function testGetCompanyLogo()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $oPluginRegistry->setCompanyLogo('enterprise', PATH_PLUGINS . 'Logo.png');
        $logo = $oPluginRegistry->getCompanyLogo(PATH_PLUGINS . 'testLogo.png');
        $this->assertEquals(
            PATH_PLUGINS . 'Logo.png',
            $logo,
            'sPluginFolder attribute does not equals'
        );
    }

    public function testGetCompanyLogoDefault()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $oPluginRegistry->enablePlugin("enterprise");
        $logo = $oPluginRegistry->getCompanyLogo(PATH_PLUGINS . 'Logo.png');
        $this->assertEquals(
            PATH_PLUGINS . 'Logo.png',
            $logo,
            'sPluginFolder attribute does not equals'
        );
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::executeMethod
     */
    public function testExecuteMethodParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('executeMethod', $methods), 'exists method executeMethod');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'executeMethod');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'methodName');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'oData');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    public function testExecuteMethod()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $pluginFile = 'pmosCommunity.php';
        //add the plugin php file
        require_once(PATH_PLUGINS . "pmosCommunity.php");
        //register mulitenant in the plugin registry singleton, because details are read from this instance
        $oPluginRegistry->registerPlugin("pmosCommunity", $pluginFile);
        $oPluginRegistry->registerFolder('pmosCommunity', 'Folder1234', 'pmosCommunity');
        $oPluginRegistry->registerTrigger('pmosCommunity', 'Trigger1234', 'getAvailableCharts');
        $trigger = $oPluginRegistry->executeMethod('Trigger1234', []);
        $this->assertTrue(is_array($trigger), ' attribute does not equals');
        $this->assertContains('ForumWeek', $trigger, 'ForumWeek element does not exist');
        $oPluginRegistry->registerTrigger('enterprise1', 'Trigger4321', 'notExist');
        $trigger = $oPluginRegistry->executeMethod('Trigger4321', []);
        $this->assertNull($trigger, 'sTriggerId attribute does not exist');
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getFieldsForPageSetup
     */
    public function testGetFieldsForPageSetupParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getFieldsForPageSetup', $methods), 'exists method getFieldsForPageSetup');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getFieldsForPageSetup');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::updateFieldsForPageSetup
     */
    public function testUpdateFieldsForPageSetupParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('updateFieldsForPageSetup', $methods), 'exists method updateFieldsForPageSetup');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'updateFieldsForPageSetup');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'oData');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerToolbarFile
     */
    public function testRegisterToolbarFileParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerToolbarFile', $methods), 'exists method registerToolbarFile');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerToolbarFile');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sToolbarId');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sFilename');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::getToolbarOptions
     */
    public function testGetToolbarOptionsParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('getToolbarOptions', $methods), 'exists method getToolbarOptions');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'getToolbarOptions');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sToolbarId');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerCaseSchedulerPlugin
     */
    public function testRegisterCaseSchedulerPluginParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(
            in_array('registerCaseSchedulerPlugin', $methods),
            'exists method registerCaseSchedulerPlugin'
        );
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerCaseSchedulerPlugin');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sActionId');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sActionForm');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
        $this->assertTrue($params[3]->getName() == 'sActionSave');
        $this->assertTrue($params[3]->isArray() == false);
        $this->assertTrue($params[3]->isOptional() == false);
        $this->assertTrue($params[4]->getName() == 'sActionExecute');
        $this->assertTrue($params[4]->isArray() == false);
        $this->assertTrue($params[4]->isOptional() == false);
        $this->assertTrue($params[5]->getName() == 'sActionGetFields');
        $this->assertTrue($params[5]->isArray() == false);
        $this->assertTrue($params[5]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerTaskExtendedProperty
     */
    public function testRegisterTaskExtendedPropertyParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(
            in_array('registerTaskExtendedProperty', $methods),
            'exists method registerTaskExtendedProperty'
        );
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerTaskExtendedProperty');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sPage');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sName');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
        $this->assertTrue($params[3]->getName() == 'sIcon');
        $this->assertTrue($params[3]->isArray() == false);
        $this->assertTrue($params[3]->isOptional() == false);
    }

    /**
     * @covers \ProcessMaker\Plugins\PluginsRegistry::registerDashboardPage
     */
    public function testRegisterDashboardPageParams()
    {
        $methods = get_class_methods($this->oPluginRegistry);
        $this->assertTrue(in_array('registerDashboardPage', $methods), 'exists method registerDashboardPage');
        $r = new \ReflectionMethod('\ProcessMaker\Plugins\PluginsRegistry', 'registerDashboardPage');
        $params = $r->getParameters();
        $this->assertTrue($params[0]->getName() == 'sNamespace');
        $this->assertTrue($params[0]->isArray() == false);
        $this->assertTrue($params[0]->isOptional() == false);
        $this->assertTrue($params[1]->getName() == 'sPage');
        $this->assertTrue($params[1]->isArray() == false);
        $this->assertTrue($params[1]->isOptional() == false);
        $this->assertTrue($params[2]->getName() == 'sName');
        $this->assertTrue($params[2]->isArray() == false);
        $this->assertTrue($params[2]->isOptional() == false);
        $this->assertTrue($params[3]->getName() == 'sIcon');
        $this->assertTrue($params[3]->isArray() == false);
        $this->assertTrue($params[3]->isOptional() == false);
    }
}
