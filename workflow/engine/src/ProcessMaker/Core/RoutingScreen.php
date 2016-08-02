<?php

namespace ProcessMaker\Core;


class RoutingScreen extends \Derivation
{
    public function __construct()
    {
        parent::__construct();
        $this->setRegexpTaskTypeToInclude("GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT|SCRIPT-TASK|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT");
    }

}