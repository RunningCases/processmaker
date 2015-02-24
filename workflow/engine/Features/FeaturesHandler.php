<?php

namespace Features;
/**
 * Description of FeatureManager
 * 
 */
class FeaturesHandler
{
    public function retrieveConfigurations($params)
    {
        foreach ($this->getFeatureList() as $feature) {
            
        }
    }

    public function getFeatureList()
    {
        $invalidFolders = array('ViewContainers');
        $featuresFolders = glob(PATH_FEATURES.'/*', GLOB_ONLYDIR);
        $features = array();
        foreach ($featuresFolders as $directory) {
            $feature = new stdClass();
            if (in_array($directory, $invalidFolders)) {
                continue;
            }
            $feature->path = PATH_FEATURES . PATH_SEP . $directory;
            $feature->name = $directory;
            $features[] = $feature;
        }
        return $features;
    }
}
