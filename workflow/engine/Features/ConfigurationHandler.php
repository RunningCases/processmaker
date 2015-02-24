<?php
namespace Features;
/**
 * Description of ConfigurationManager
 * 
 */
class ConfigurationHandler
{
    public function retrieveFeatureConfiguration($feature)
    {
        $configuration = new stdClass();
        $configuration->views = $this->retrieveViews($feature);
    }
    
    public function retrieveViews($feature, $type = 'configuration')
    {
        require_once $feature->path . PATH_SEPARATOR . $feature->name;
    }
    
    public function getViewList($feature)
    {
        $forbiddenFolders = array();
        $views = glob($feature->path.'/*', GLOB_ONLYDIR);
        $viewFiles = array();
        foreach ($views as $directory) {
            $feature = new stdClass();
            if (in_array($directory, $forbiddenFolders)) {
                continue;
            }
            $feature->path = PATH_FEATURES . PATH_SEP . $directory;
            $feature->name = $directory;
            $viewFiles[] = $feature;
        }
        return $viewFiles;
    }
}
