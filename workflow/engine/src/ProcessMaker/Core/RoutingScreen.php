<?php

namespace ProcessMaker\Core;


class RoutingScreen extends \Derivation
{
    public function __construct()
    {
        parent::__construct();
        $this->setRegexpTaskTypeToInclude("GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT");
    }

    public function mergeDataDerivation($post, $prepareInformation)
    {
        $aDataMerged = array();
        $flagJumpTask = false;
        foreach ($prepareInformation as $key => $nextTask) {
            $aDataMerged[$key] = $nextTask['NEXT_TASK'];
            unset($aDataMerged[$key]['USER_ASSIGNED']);
            $aDataMerged[$key]['DEL_PRIORITY'] = '';
            foreach ($post as $i => $item) {
                if(isset($post[$i]['SOURCE_UID']) && ($nextTask['NEXT_TASK']['TAS_UID'] === $post[$i]['SOURCE_UID'])){
                    $flagJumpTask = true;
                    if($post[$i]['SOURCE_UID'] === $post[$i]['TAS_UID']){
                        $aDataMerged[$key]['USR_UID'] = $post[$i]['USR_UID'];
                    } else {
                        $aDataMerged[$key]['NEXT_ROUTING'][] = $post[$i];
                    }
                }
            }
        }
        //If flagJumpTask is false the template does not Jump Intermediate Events
        if(!$flagJumpTask){
            $aDataMerged = $post;
        }
        return $aDataMerged;
    }

}