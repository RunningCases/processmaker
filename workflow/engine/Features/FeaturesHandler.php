<?php
namespace Features;
/**
 * Description of FeatureManager
 * 
 */
class FeaturesHandler
{
    public function loadConfiguration($params)
    {
        $features = $this->getFeatureList();
        $configurations = array();
        foreach ($features as $feature) {
            $service = $this->getFeatureService(array('name' => $feature->name));
            $configurations[] = $service->loadConfiguration($params);
        }
        return array_filter($configurations);
    }

    public function saveConfiguration($configurationForms)
    {
        foreach ($configurationForms as $feature => $form) {
            $service = $this->getFeatureService(array('name' => $feature));
            $service->saveConfiguration($form);
        }
        return true;
    }

    public function getFeatureList()
    {
        $invalidFolders = array('ViewContainers');
        $featuresFolders = glob(PATH_FEATURES.'/*', GLOB_ONLYDIR);
        $features = array();
        foreach ($featuresFolders as $directory) {
            $feature = new \stdClass();
            $featureName = basename($directory);
            if (in_array($featureName, $invalidFolders)) {
                continue;
            }
            $feature->path = PATH_FEATURES . $featureName;
            $feature->name = $featureName;
            $features[] = $feature;
        }
        return $features;
    }

    public function getFeatureService($params)
    {
        $features = $this->getFeatureList();
        foreach ($features as $feature) {
            if ($params['name'] == $feature->name) {
                $className = $feature->name . 'Service';
                $namespace = '\\Features\\' . $feature->name . '\\' . $className;
                require_once $feature->path . PATH_SEP . $className . '.php';
                $service = new $namespace();
                return $service;
            }
        }
    }
}
