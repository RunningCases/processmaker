<?php

namespace Tests\ProcessMaker\Plugins;

use ProcessMaker\Plugins\Interfaces\CssFile;
use ProcessMaker\Plugins\Interfaces\MenuDetail;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\Interfaces\Plugins;
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
        $this->assertEquals(1, count($js));
        $this->assertObjectHasAttribute(
            'pluginJsFile',
            $js[0],
            'pluginJsFile attribute does not exist'
        );
    }
}
