<?php

namespace Tests\ProcessMaker\Plugins;

use ProcessMaker\Plugins\Interfaces\CssFile;
use ProcessMaker\Plugins\Interfaces\FolderDetail;
use ProcessMaker\Plugins\Interfaces\MenuDetail;
use ProcessMaker\Plugins\Interfaces\PluginDetail;
use ProcessMaker\Plugins\Interfaces\Plugins;
use ProcessMaker\Plugins\Interfaces\RedirectDetail;
use ProcessMaker\Plugins\Interfaces\StepDetail;
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
        $this->assertEquals(2, count($js));
        $this->assertObjectHasAttribute(
            'pluginJsFile',
            $js[0],
            'pluginJsFile attribute does not exist'
        );
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
}
