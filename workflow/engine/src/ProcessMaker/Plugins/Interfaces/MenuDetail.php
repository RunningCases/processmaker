<?php

namespace ProcessMaker\Plugins\Interfaces;

class MenuDetail
{
    public $sNamespace;
    public $sMenuId;
    public $sFilename;

    /**
     * This function is the constructor of the menuDetail class
     * @param string $sNamespace
     * @param string $sMenuId
     * @param string $sFilename
     */
    public function __construct($sNamespace, $sMenuId, $sFilename)
    {
        $this->sNamespace = $sNamespace;
        $this->sMenuId = $sMenuId;
        $this->sFilename = $sFilename;
    }
}
