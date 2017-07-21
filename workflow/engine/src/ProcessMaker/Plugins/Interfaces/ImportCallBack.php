<?php

namespace ProcessMaker\Plugins\Interfaces;

class ImportCallBack
{
    public $namespace;
    public $callBackFile;

    /**
     * This function is the constructor of the cronFile class
     * @param string $namespace
     * @param string $callBackFile
     */
    public function __construct($namespace, $callBackFile)
    {
        $this->namespace = $namespace;
        $this->callBackFile = $callBackFile;
    }
}
