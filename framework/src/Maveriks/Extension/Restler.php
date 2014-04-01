<?php
namespace Maveriks\Extension;

use Luracast\Restler\Defaults;

/**
 * Class Restler
 * Extension Restler class to implement in ProcessMaker
 *
 * @package Maveriks\Extension
 */
class Restler extends \Luracast\Restler\Restler
{
    protected function respond()
    {
        $this->dispatch('respond');
        //handle throttling
        if (Defaults::$throttle) {
            $elapsed = time() - $this->startTime;
            if (Defaults::$throttle / 1e3 > $elapsed) {
                usleep(1e6 * (Defaults::$throttle / 1e3 - $elapsed));
            }
        }
        echo $this->responseData;
        $this->dispatch('complete');
    }
}