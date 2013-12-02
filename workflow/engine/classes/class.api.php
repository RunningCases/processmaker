<?php
namespace ProcessMaker;

class Api
{
    private static $workspace;
    private static $userId;

    const STAT_CREATED = 201;
    const STAT_APP_EXCEPTION = 400;

    public function __costruct()
    {
        self::$workspace = null;
    }

    public static function setWorkspace($workspace)
    {
        self::$workspace = $workspace;
    }

    public function getWorkspace()
    {
        return self::$workspace;
    }

    public static function setUserId($userId)
    {
        self::$userId = $userId;
    }

    public function getUserId()
    {
        //return self::$userId;

        return \Api\OAuth2\Server::getUserId();
    }
}

