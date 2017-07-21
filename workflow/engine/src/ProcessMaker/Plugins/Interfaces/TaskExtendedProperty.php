<?php

namespace ProcessMaker\Plugins\Interfaces;

class TaskExtendedProperty
{
    public $sNamespace;
    public $sPage;
    public $sName;
    public $sIcon;

    /**
     * This function is the constructor of the taskExtendedProperty class
     * @param string $sNamespace
     * @param string $sPage
     * @param string $sName
     * @param string $sIcon
     */
    public function __construct($sNamespace, $sPage, $sName, $sIcon)
    {
        $this->sNamespace = $sNamespace;
        $this->sPage = $sPage;
        $this->sName = $sName;
        $this->sIcon = $sIcon;
    }
}
