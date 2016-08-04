<?php

namespace ProcessMaker\Core;


class RoutingScreen extends \Derivation
{
    public function __construct()
    {
        parent::__construct();
        $this->setRegexpTaskTypeToInclude("GATEWAYTOGATEWAY|END-MESSAGE-EVENT|END-EMAIL-EVENT|SCRIPT-TASK|INTERMEDIATE-CATCH-TIMER-EVENT|INTERMEDIATE-THROW-EMAIL-EVENT");
    }

    public function mergeDataDerivation($post, $prepareInformation)
    {
        $iPost = count($post);
        $aDataMerged = array();
        $flagJumpTask = false;
        foreach ($prepareInformation as $key => $nextTask) {
            $aDataMerged[$key] = $nextTask['NEXT_TASK'];
            unset($aDataMerged[$key]['USER_ASSIGNED']);
            $aDataMerged[$key]['DEL_PRIORITY'] = '';
            for ($i = 1; $i <= $iPost; $i++) {
                if(isset($post[$i]['SOURCE_UID']) && ($nextTask['NEXT_TASK']['TAS_UID'] === $post[$i]['SOURCE_UID']) && ($post[$i]['SOURCE_UID'] !== $post[$i]['TAS_UID'])){
                    $flagJumpTask = true;
                    $aDataMerged[$key]['NEXT_ROUTING'][] = $post[$i];
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