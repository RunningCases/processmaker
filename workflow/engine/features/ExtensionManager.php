<?php

/**
 * Description of ExtensionManager
 * 
 */
class ExtensionManager
{
    public function registerView($view, ExtensionContainer $container)
    {
        $view = $this->prepareView($view);
        $container->register($view);
    }
    
    public function prepareView($view)
    {
        return $view;
    }
    
}
