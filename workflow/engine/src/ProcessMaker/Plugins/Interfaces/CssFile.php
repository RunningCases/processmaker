<?php

namespace ProcessMaker\Plugins\Interfaces;

class CssFile
{
    public $sNamespace;
    public $sCssFile;

    /**
     * This function is the constructor of the cssFile class
     * @param string $sNamespace
     * @param string $sCssFile
     */
    public function __construct($sNamespace, $sCssFile)
    {
        $this->sNamespace = $sNamespace;
        $this->sCssFile = $sCssFile;
    }
}
