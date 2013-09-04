<?php
namespace ProcessMaker;

class Api
{
    private static $workspace;

    public function __costruct()
    {
        $this->workspace = null;
    }

    public static function setWorkspace($workspace)
    {
        self::$workspace = $workspace;
    }

    public function getWorkspace()
    {
        return self::$workspace;
    }
}

