<?php

namespace ProcessMaker\Plugins\Interfaces;

class FolderDetail
{
    public $sNamespace;
    public $sFolderId;
    public $sFolderName;

    /**
     * This function is the constructor of the folderDetail class
     * @param string $sNamespace
     * @param string $sFolderId
     * @param string $sFolderName
     */
    public function __construct($sNamespace, $sFolderId, $sFolderName)
    {
        $this->sNamespace = $sNamespace;
        $this->sFolderId = $sFolderId;
        $this->sFolderName = $sFolderName;
    }
}
