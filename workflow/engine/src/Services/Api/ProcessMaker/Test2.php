<?php
namespace Services\Api\ProcessMaker;

use \ProcessMaker\Api;
use \Luracast\Restler\RestException;

class Test2 extends Api
{

    function hello2()
    {
        return 'Hello #2';
    }

    /**
     * @url GET /getHello
     */
    function helloworld($param = '')
    {
        return 'Greetings, from a overridden url ' . $param;
    }

    /**
     * @url GET /sample/other/large/:name
     */
    function sampleOther($name)
    {
        return 'Name: ' . $name;
    }
}
