<?php

namespace Services\Api\ProcessMaker;

class Application
{
    public function get($id)
    {
        $data = array(
            "APP_UID" => $id,
            "PRO_UID" => "13885168416038181883131343548151",
            "DUMMY" => "sample data"
        );

        return $data;
    }

    public function post($requestData = null)
    {
        $requestData;
        $requestData['processed'] = true;

        return $requestData;
    }
}