<?php
namespace Services\Api\ProcessMaker;

class Application extends \ProcessMaker\Api
{
    /**
     * Sample api protected with OAuth2
     *
     * For testing the oAuth token
     *
     * @access protected
     */
    public function get($id)
    {
        $data = array(
            'USSER_LOGGED' => $this->getUserId(),
            "APP_UID" => $id,
            "PRO_UID" => "13885168416038181883131343548151",
            "DUMMY" => "sample data",
            "WS" => $this->getWorkspace()
        );

        return $data;
    }

    public function post($requestData = null)
    {
        $requestData['processed'] = true;

        return $requestData;
    }
}