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
        $count = 1;
        $iPost = count($post);
        $aDataMerged = array();
        $flag = false;
        foreach ($prepareInformation as $key => $nextTask) {
            for ($i = 1; $i <= $iPost; $i++) {
                if(isset($post[$i]['SOURCE_UID']) && $nextTask['NEXT_TASK']['TAS_UID'] === $post[$i]['SOURCE_UID']){
                    if($post[$i]['SOURCE_UID'] !== $post[$i]['TAS_UID']){
                        $aDataMerged[$count] = $nextTask['NEXT_TASK'];
                        unset($aDataMerged[$count]['USER_ASSIGNED']);
                        $aDataMerged[$count]['DEL_PRIORITY'] = '';
                        $aDataMerged[$count]['NEXT_ROUTING'] = $post[$i];
                        $count++;
                        $flag = true;
                    } else {
                        $aDataMerged[$count] = \G::array_merges($nextTask['NEXT_TASK'],$post[$i]);
                        unset($aDataMerged[$count]['USER_ASSIGNED']);
                        $count++;
                        $flag = true;
                        break;
                    }
                }
            }
        }
        if(!$flag){
            $aDataMerged = $post;
        }
        return $aDataMerged;
    }

}