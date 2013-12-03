<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Services\Api;
use \Luracast\Restler\RestException;

class Test3 extends Api
{

    function hello3()
    {
        return 'Hello #3';
    }

    /**
     * @status 201
     */
    function post()
    {
        return array('success' => true);
    }
}
