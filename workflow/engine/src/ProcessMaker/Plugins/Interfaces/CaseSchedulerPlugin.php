<?php

namespace ProcessMaker\Plugins\Interfaces;

class CaseSchedulerPlugin
{
    public $sNamespace;
    public $sActionId;
    public $sActionForm;
    public $sActionSave;
    public $sActionExecute;
    public $sActionGetFields;

    /**
     * This function is the constructor of the caseSchedulerPlugin class
     * @param string $sNamespace
     * @param string $sActionId
     * @param string $sActionForm
     * @param string $sActionSave
     * @param string $sActionExecute
     * @param string $sActionGetFields
     */
    public function __construct($sNamespace, $sActionId, $sActionForm, $sActionSave, $sActionExecute, $sActionGetFields)
    {
        $this->sNamespace       = $sNamespace;
        $this->sActionId        = $sActionId;
        $this->sActionForm      = $sActionForm;
        $this->sActionSave      = $sActionSave;
        $this->sActionExecute   = $sActionExecute;
        $this->sActionGetFields = $sActionGetFields;
    }
}
