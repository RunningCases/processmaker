<?php

namespace ProcessMaker\Util;

class Logger
{
    private static $instance;
    private $logFile;
    private $fp;

    protected function __construct()
    {
        $this->logFile = sys_get_temp_dir() . '/processmaker.log';
        $this->fp = fopen($this->logFile, "a+");
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    public function setLog($data)
    {
        if (! is_string($data)) {
            $data = print_r($data, true);
        }

        fwrite($this->fp, "PM LOG: ".date('Y-m-d H:i:s') . " " . $data . PHP_EOL);
    }

    public static function log($data)
    {
        $me = Logger::getInstance();
        $me->setLog($data);
    }
}