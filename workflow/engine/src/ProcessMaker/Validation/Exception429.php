<?php

namespace ProcessMaker\Validation;

use Exception;

class Exception429 extends Exception
{
    /**
     * Status code: too many requests.
     * @var int 
     */
    private $status = 429;

    /**
     * Get status code.
     * @return int
     */
    function getStatus()
    {
        return $this->status;
    }
}
