<?php

namespace ProcessMaker\Plugins\Interfaces;

class RedirectDetail
{
    public $sNamespace;
    public $sRoleCode;
    public $sPathMethod;

    /**
     * This function is the constructor of the redirectDetail class
     * @param string $sNamespace
     * @param string $sRoleCode
     * @param string $sPathMethod
     */
    public function __construct($sNamespace, $sRoleCode, $sPathMethod)
    {
        $this->sNamespace = $sNamespace;
        $this->sRoleCode = $sRoleCode;
        $this->sPathMethod = $sPathMethod;
    }
}
