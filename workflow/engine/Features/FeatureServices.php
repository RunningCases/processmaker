<?php
namespace Features;

use Features\ActionsByEmail\views\ActivityConfigurationView;
/**
 * Description of FeaturesService
 * 
 */
class FeatureServices
{
    public function retrieveView($params)
    {
        $viewsFolder = 'views';
        $className = ucfirst($params['type']) . ucfirst($params['view']) . 'View';
        require_once $viewsFolder.DS.$className.'.php';
        $view = new $className();
        return $view->retrieveView();
    }
}
