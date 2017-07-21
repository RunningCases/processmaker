<?php

namespace ProcessMaker\Plugins\Interfaces;

class ToolbarDetail
{
    public $sNamespace;
    public $sToolbarId;
    public $sFilename;

    /**
     * This function is the constructor of the menuDetail class
     * @param string $sNamespace
     * @param string $sToolbarId
     * @param string $sFilename
     */
    public function __construct($sNamespace, $sToolbarId, $sFilename)
    {
        $this->sNamespace = $sNamespace;
        $this->sToolbarId = $sToolbarId;
        $this->sFilename = $sFilename;
    }
}
