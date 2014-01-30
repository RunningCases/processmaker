<?php
namespace ProcessMaker\Util;

/**
 * Singleton Class Logger
 *
 * This Utility is usefull to log local messages
 * @package ProcessMaker\Util
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Logger
{
    private static $instance;
    private $logFile;
    private $fp;

    protected function __construct()
    {
        $this->logFile = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'processmaker.log';
        $this->fp = fopen($this->logFile, "a+");
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Logger();
        }

        return self::$instance;
    }

    public function setLog()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            if (! is_string($arg)) {
                $arg = print_r($arg, true);
            }

            fwrite($this->fp, "- " . date('Y-m-d H:i:s') . " " . $arg . PHP_EOL);
        }
        if (count($args) > 1)
            fwrite($this->fp, PHP_EOL);
    }

    public static function log()
    {
        $me = Logger::getInstance();
        $args = func_get_args();

        call_user_func_array(array($me, 'setLog'), $args);
    }
}

