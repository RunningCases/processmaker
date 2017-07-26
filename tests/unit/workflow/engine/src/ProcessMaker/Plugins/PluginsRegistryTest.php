<?php

namespace Tests\ProcessMaker\Plugins;

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
     * @test
     */
    public function getPlugins()
    {
        $this->assertObjectHasAttribute('Plugins', $this->oPluginRegistry, 'Plugins attribute does not exist');
        $this->assertEquals([], $this->oPluginRegistry->getPlugins(), 'The Plugins attribute is not an array');
    }

    /**
     * @test
     */
    public function setPlugins()
    {
        $this->assertObjectHasAttribute('Plugins', $this->oPluginRegistry, 'Plugins attribute does not exist');
        $this->oPluginRegistry->setPlugins([]);
        $this->assertEquals([], $this->oPluginRegistry->getPlugins(), 'The Plugins attribute is not an array');
    }

    /**
     * @test
     */
    public function loadSingleton()
    {
        $oPluginRegistry = PluginsRegistry::loadSingleton();
        $this->assertObjectHasAttribute('Plugins', $oPluginRegistry, 'Plugins attribute does not exist');
        $this->assertInstanceOf(Plugins::class, $oPluginRegistry, '');
    }

    /**
     * @test
     */
    public function registerPlugin()
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
     * @test
     */
    public function getPluginDetails()
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
     * @test
     */
    public function enablePlugin()
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
     * @test
     */
    public function disablePlugin()
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
}
