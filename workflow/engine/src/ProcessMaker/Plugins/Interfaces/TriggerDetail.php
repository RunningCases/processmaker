<?php

namespace ProcessMaker\Plugins\Interfaces;

class TriggerDetail
{
    public $sNamespace;
    public $sTriggerId;
    public $sTriggerName;

    /**
     * This function is the constructor of the triggerDetail class
     * @param string $sNamespace
     * @param string $sTriggerId
     * @param string $sTriggerName
     */
    public function __construct($sNamespace, $sTriggerId, $sTriggerName)
    {
        $this->sNamespace = $sNamespace;
        $this->sTriggerId = $sTriggerId;
        $this->sTriggerName = $sTriggerName;
    }
}
