<?php

namespace ProcessMaker\Plugins\Interfaces;

class StepDetail
{
    public $sNamespace;
    public $sStepId;
    public $sStepName;
    public $sStepTitle;
    public $sSetupStepPage;

    /**
     * This function is the constructor of the stepDetail class
     * @param string $sNamespace
     * @param string $sStepId
     * @param string $sStepName
     * @param string $sStepTitle
     * @param string $sSetupStepPage
     */
    public function __construct($sNamespace, $sStepId, $sStepName, $sStepTitle, $sSetupStepPage)
    {
        $this->sNamespace     = $sNamespace;
        $this->sStepId        = $sStepId;
        $this->sStepName      = $sStepName;
        $this->sStepTitle     = $sStepTitle;
        $this->sSetupStepPage = $sSetupStepPage;
    }
}
