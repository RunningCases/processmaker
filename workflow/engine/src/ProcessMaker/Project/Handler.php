<?php
namespace ProcessMaker\Project;

use ProcessMaker\Util\Logger;

abstract class Handler
{
    public static function load($uid)
    {   // This method must be implemented on children classes, this is not declared abstract since PHP 5.3.x
        // don't allow any more static abstract methods.
        return null;
    }

    public abstract function create($data);
    //public abstract function update();
    public abstract function remove();

    /**
     * Log in ProcessMaker Standard Output if debug mode is enabled.
     *
     * @author Erik Amaru Ortiz <aortiz.erik at icloud dot com>
     * @internal param $args this method receives N-Arguments dynamically with any type, string, array, object, etc
     *                       it means that you ca use it by example:
     *
     * self::log("Beginning transaction");
     * self::log("Method: ", __METHOD__, 'Returns: ', $result);
     *
     */
    public static function log()
    {
        if (System::isDebugMode()) {

            $me = Logger::getInstance();
            $args = func_get_args();
            //array_unshift($args, 'Class '.__CLASS__.' ');

            call_user_func_array(array($me, 'setLog'), $args);
        }
    }
}