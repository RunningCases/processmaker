<?php

namespace ProcessMaker\Plugins\Interfaces;

class OpenReassignCallback
{
    public $callBackFile;

    /**
     * This function is the constructor of the cronFile class
     * @param string $callBackFile
     */
    public function __construct($callBackFile)
    {
        $this->callBackFile = $callBackFile;
    }
}
