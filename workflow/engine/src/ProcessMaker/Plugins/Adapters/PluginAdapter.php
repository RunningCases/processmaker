<?php

namespace ProcessMaker\Plugins\Adapters;

use PMPluginRegistry;
use ProcessMaker\Plugins\Interfaces\Plugins;
use ProcessMaker\Plugins\PluginsRegistry;

class PluginAdapter
{
    protected $pluginRegistry;
    /**
     * @var array $aliasNameAttributes
     */
    private $aliasNameAttributes = [
        'sNamespace' => 'PLUGIN_NAMESPACE',
        'sDescription' => 'PLUGIN_DESCRIPTION',
        'sClassName' => 'CLASS_NAME',
        'sFriendlyName' => 'FRIENDLY_NAME',
        'sFilename' => 'FILE_NAME',
        'sPluginFolder' => 'PLUGIN_FOLDER',
        'iVersion' => 'PLUGIN_VERSION',
        'enabled' => 'PLUGIN_ENABLE',
        'bPrivate' => 'PLUGIN_PRIVATE',
        '_aMenus' => 'PLUGIN_MENUS',
        '_aFolders' => 'PLUGIN_FOLDERS',
        '_aTriggers' => 'PLUGIN_TRIGGERS',
        '_aPmFunctions' => 'PLUGIN_PM_FUNCTIONS',
        '_aRedirectLogin' => 'PLUGIN_REDIRECT_LOGIN',
        '_aSteps' => 'PLUGIN_STEPS',
        '_aCSSStyleSheets' => 'PLUGIN_CSS',
        '_aJavascripts' => 'PLUGIN_JS',
        '_restServices' => 'PLUGIN_REST_SERVICE',
    ];

    /**
     * @param PMPluginRegistry|PluginsRegistry $pluginsSingleton
     */
    public function save($pluginsSingleton)
    {
        $this->pluginRegistry = \G::json_decode(\G::json_encode($pluginsSingleton->iterateVisible()));
        foreach ($this->pluginRegistry->_aPluginDetails as $nameSpace => $value) {
            $this->savePluginMigrate($nameSpace, $this->pluginRegistry);
        }
    }

    public function savePluginMigrate($sNamespace, $pluginRegistry)
    {
        $structurePlugin = $this->getOldPluginStructure($sNamespace, $pluginRegistry);
        $plugin = $this->diffFieldTable($structurePlugin);
        if ($plugin['PLUGIN_NAMESPACE']) {
            $fieldPlugin = \PluginsRegistry::loadOrCreateIfNotExists(md5($plugin['PLUGIN_NAMESPACE']), $plugin);
            \PluginsRegistry::update($fieldPlugin);
        }
    }

    public function savePlugin($sNamespace, $pluginRegistry)
    {
        $structurePlugin = $this->getPluginStructure($sNamespace, $pluginRegistry);
        $plugin = $this->diffFieldTable($structurePlugin);
        if ($plugin['PLUGIN_NAMESPACE']) {
            $fieldPlugin = \PluginsRegistry::loadOrCreateIfNotExists(md5($plugin['PLUGIN_NAMESPACE']), $plugin);
            \PluginsRegistry::update($fieldPlugin);
        }
    }

    /**
     * @param string $nameSpace
     * @param object $pluginsRegistry
     * @return array
     */
    public function getOldPluginStructure($nameSpace, $pluginsRegistry)
    {
        $pluginRegistry = clone $pluginsRegistry;
        $structurePlugins =  $pluginRegistry->_aPluginDetails->{$nameSpace};
        unset($pluginRegistry->_aPluginDetails);
        $aPlugins = isset($pluginRegistry->_aPlugins->{$nameSpace}) ? $pluginRegistry->_aPlugins->{$nameSpace} : [];
        $structurePlugins = array_merge((array)$structurePlugins, (array)$aPlugins);
        unset($pluginRegistry->_aPlugins);
        foreach ($pluginRegistry as $propertyName => $propertyValue) {
            foreach ($propertyValue as $key => $plugin) {
                if (is_object($plugin) &&
                    (
                        (property_exists($plugin, 'sNamespace') && $plugin->sNamespace == $nameSpace) ||
                        (!is_int($key) && $key == $nameSpace)
                    )
                ) {
                    $structurePlugins[$propertyName][] = $plugin;
                } elseif (is_object($plugin) &&
                    property_exists($plugin, 'pluginName') &&
                    $plugin->pluginName == $nameSpace
                ) {
                    $structurePlugins[$propertyName][] = $plugin;
                } elseif (is_string($plugin) && $plugin == $nameSpace) {
                    $structurePlugins[$propertyName][] = $plugin;
                }
            }
        }
        return $structurePlugins;
    }

    /**
     * @param string $nameSpace
     * @param object $pluginsRegistry
     * @return array
     */
    public function getPluginStructure($nameSpace, $pluginsRegistry)
    {
        $pluginRegistry = clone $pluginsRegistry;
        $structurePlugins =  $pluginRegistry->_aPluginDetails[$nameSpace];
        unset($pluginRegistry->_aPluginDetails);
        $aPlugins = isset($pluginRegistry->_aPlugins[$nameSpace]) ? $pluginRegistry->_aPlugins[$nameSpace] : [];
        $structurePlugins = array_merge((array)$structurePlugins, get_object_vars($aPlugins));
        unset($pluginRegistry->_aPlugins);
        foreach ($pluginRegistry as $propertyName => $propertyValue) {
            foreach ($propertyValue as $key => $plugin) {
                if (is_object($plugin) &&
                    (
                        (property_exists($plugin, 'sNamespace') && $plugin->sNamespace == $nameSpace) ||
                        (!is_int($key) && $key == $nameSpace)
                    )
                ) {
                    $structurePlugins[$propertyName][] = $plugin;
                } elseif (is_object($plugin) &&
                    property_exists($plugin, 'pluginName') &&
                    $plugin->pluginName == $nameSpace
                ) {
                    $structurePlugins[$propertyName][] = $plugin;
                } elseif (is_array($plugin) && $key == $nameSpace) {
                    $structurePlugins[$propertyName][$key] = $plugin;
                } elseif (is_bool($plugin) && $key == $nameSpace) {
                    $structurePlugins[$propertyName][$key] = $plugin;
                } elseif (is_string($plugin) && $plugin == $nameSpace) {
                    $structurePlugins[$propertyName][] = $plugin;
                }
            }
        }
        return $structurePlugins;
    }

    /**
     * @param $plugin
     * @return array
     */
    public function diffFieldTable($plugin)
    {
        $fields = [];
        $map = \PluginsRegistryPeer::getTableMap();
        $columns = $map->getColumns();
        $attributes = array_diff_key((array)$plugin, $this->aliasNameAttributes);
        $fieldsTMP = array_intersect_key((array)$plugin, $this->aliasNameAttributes);
        foreach ($this->aliasNameAttributes as $name => $nameTable) {
            if (array_key_exists($name, $fieldsTMP)) {
                switch (gettype($fieldsTMP[$name])) {
                    case 'string':
                        $valueField = array_key_exists($name, $fieldsTMP) ? $fieldsTMP[$name] : '';
                        break;
                    case 'array':
                        $valueField = array_key_exists($name, $fieldsTMP) ? $fieldsTMP[$name] : [];
                        $valueField = \G::json_encode($valueField);
                        break;
                    case 'integer':
                        $valueField = array_key_exists($name, $fieldsTMP) ? $fieldsTMP[$name] : 0;
                        break;
                    case 'boolean':
                        $valueField = array_key_exists($name, $fieldsTMP) ? ($fieldsTMP[$name] ? true : false ): false;
                        break;
                    case 'NULL':
                    default:
                        $valueField = array_key_exists($name, $fieldsTMP) ?
                            $fieldsTMP[$name] :
                            $this->getDefaultValueType($columns[$nameTable]->getType());
                        break;
                }
            } else {
                $valueField = $this->getDefaultValueType($columns[$nameTable]->getType());
            }
            $fields[$nameTable] = $valueField;
        }
        $fields['PLUGIN_ATTRIBUTES'] = \G::json_encode($attributes);
        return $fields;
    }

    public function getDefaultValueType($var)
    {
        switch ($var) {
            case 'string':
                $response = '';
                break;
            case 'int':
                $response = 0;
                break;
            case 'boolean':
                $response = false;
                break;
            default:
                $response = '';
                break;
        }
        return $response;
    }

    /**
     * @param PluginsRegistry $oPlugins
     * @return mixed
     */
    public function getPluginsDefinition($oPlugins)
    {
        $oldStructure = $this->convertArrayStructure();
        $oPlugins->setPlugins($oldStructure);
        $oPlugins = $this->populateAttributes($oPlugins, $oldStructure);
        return $oPlugins;
    }

    public function convertArrayStructure()
    {
        $invertAlias = array_flip($this->aliasNameAttributes);
        $plugins = \PluginsRegistry::loadPlugins();
        $pluginsRegistry = [];
        foreach ($plugins as $index => $plugin) {
            $namePlugin = $plugin['PLUGIN_NAMESPACE'];
            $pluginsRegistry[$namePlugin] = new \stdClass();
            array_walk($plugin, function ($value, $key) use ($invertAlias, &$pluginsRegistry, $namePlugin) {
                if (array_key_exists($key, $invertAlias)) {
                    $pluginsRegistry[$namePlugin]->{$invertAlias[$key]} = !is_null($data = \G::json_decode($value)) ?
                        $data :
                        (!empty($value) ? $value : []);
                }
            });
            $moreAttributes = \G::json_decode($plugin['PLUGIN_ATTRIBUTES']);
            $pluginsRegistry[$namePlugin] = Plugins::setter(array_merge(
                (array)$pluginsRegistry[$namePlugin],
                $moreAttributes ? (array)$moreAttributes : []
            ));
        }
        return $pluginsRegistry;
    }

    public function populateAttributes($oPlugins, $structures)
    {
        foreach ($structures as $namePlugin => $plugin) {
            foreach ($plugin as $nameAttribute => $detail) {
                if ($detail &&
                    property_exists($oPlugins, $nameAttribute) &&
                    $plugin->_aPluginDetails[$namePlugin]->enabled
                ) {
                    $oPlugins->{$nameAttribute} = array_merge($oPlugins->{$nameAttribute}, (array)$detail);
                }
            }
        }
        return $oPlugins;
    }
}
