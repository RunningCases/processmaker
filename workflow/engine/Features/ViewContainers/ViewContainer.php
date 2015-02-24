<?php
namespace Features\ViewContainers;
/**
 * Description of Container
 * 
 */
class ViewContainer
{
    protected $viewList = array();

    public function register($view)
    {
        $this->viewList[] = $view;
    }

    public function getView($id)
    {
        return $this->viewList[$id];
    }

    public function getAllViews()
    {
        return $this->viewList;
    }
}
