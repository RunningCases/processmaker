<?php

/**
 * XmlForm_Field_popupOption - XmlForm_Field_popupOption class
 *
 * @package workflow.engine.ProcessMaker
 */
class XmlForm_Field_PopupOption extends XmlForm_Field
{
    public $launch = '';

    /**
     * Get Events
     *
     * @return string
     */
    public function getEvents()
    {
        $script = '{name:"' . $this->name . '",text:"' . addcslashes($this->label, '\\"') . '", launch:leimnud.closure({Function:function(target){' . $this->launch . '}, args:target})}';
        return $script;
    }
}
