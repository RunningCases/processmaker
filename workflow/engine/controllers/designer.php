<?php

/**
 * Designer Controller
 *
 * @inherits Controller
 * @access public
 */

class Designer extends Controller
{
    public function __construct ()
    {

    }

    /**
     * Index Action
     *
     * @param string $httpData (opional)
     */
    public function index($httpData)
    {
        $proUid = isset($httpData->pro_uid) ? $httpData->pro_uid : '';

        $this->setVar('pro_uid', $proUid);
        $this->setView('designer/index');

        $this->render();
    }
}

