<?php

namespace ProcessMaker\Plugins\Interfaces;

class CronFile
{
    public $namespace;
    public $cronFile;

    /**
     * This function is the constructor of the cronFile class
     * @param string $namespace
     * @param string $cronFile
     */
    public function __construct($namespace, $cronFile)
    {
        $this->namespace = $namespace;
        $this->cronFile = $cronFile;
    }
}
