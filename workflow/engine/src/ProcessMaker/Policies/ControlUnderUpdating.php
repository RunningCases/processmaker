<?php

namespace ProcessMaker\Policies;

use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\RestException;

/**
 * ControlUnderUpdating sends an error signal 503 to report that the application 
 * is in update.
 */
class ControlUnderUpdating implements iAuthenticate
{

    /**
     * Access verification method.
     *
     * API access will be denied when this method returns false
     *
     * @return boolean true when api access is allowed false otherwise
     * @throws RestException
     */
    public function __isAllowed()
    {
        $response = true;
        $underUpdating = \Bootstrap::isPMUnderUpdating();
        if ($underUpdating['action']) {
            $sysTemp = true;
            if (defined("SYS_TEMP")) {
                $sysTemp = $underUpdating['workspace'] == SYS_TEMP;
            }
            if ($underUpdating['workspace'] == "true" || $sysTemp) {
                $mesage = 'The server is currently unable to handle the request '
                        . 'due to a temporary overloading or maintenance of the '
                        . 'server (An application update has probably been '
                        . 'performed on the server).';
                throw new RestException(503, $mesage);
            }
        }
        return $response;
    }

    /**
     * Required by interface iAuthenticate 
     * @return string string to be used with WWW-Authenticate header
     * @example Basic
     * @example Digest
     * @example OAuth
     * @return string
     */
    public function __getWWWAuthenticateString()
    {
        return '';
    }

}
