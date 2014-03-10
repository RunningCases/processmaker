<?php
namespace ProcessMaker\Importer;

class WorkflowImporter extends Importer
{
    public function validateSource()
    {
        return true;
    }

    public function targetExists()
    {
        return false;
    }

    public function import($option = self::IMPORT_OPTION_CREATE_NEW)
    {
        switch ($option) {
            case self::IMPORT_OPTION_CREATE_NEW:
                $this->prepare();
                $this->createNewProject();
                break;
            case self::IMPORT_OPTION_DISABLE_AND_CREATE_NEW:
                break;
            case self::IMPORT_OPTION_OVERWRITE:
                break;
        }
    }

    public function createNewProject()
    {

    }

    public function updateProject()
    {

    }

    public function disableCurrentProject()
    {

    }
}