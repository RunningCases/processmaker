<?php
namespace ProcessMaker\Project\Adapter;

use ProcessMaker\Project;
use ProcessMaker\Util\Common;

/**
 * Class WorkflowBpmn
 *
 * @package ProcessMaker\Project\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class WorkflowBpmn extends Project\Workflow
{
    /**
     * @var \ProcessMaker\Project\Bpmn
     */
    protected $bp;

    /**
     * OVERRIDES
     */

    public static function load($prjUid)
    {
        $parent = parent::load($prjUid);

        $me = new self();

        $me->process = $parent->process;
        $me->proUid = $parent->proUid;
        $me->bp = Project\Bpmn::load($prjUid);

        return $me;
    }

    public function create($data)
    {
        try {
            parent::create($data);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Can't create Workflow Project." . PHP_EOL . $e->getMessage()));
        }

        try {
            $bpData = array();
            $bpData["PRJ_UID"] = $this->getUid();

            if (array_key_exists("PRO_TITLE", $data)) {
                $bpData["PRJ_NAME"] = $data["PRO_TITLE"];
            }
            if (array_key_exists("PRO_DESCRIPTION", $data)) {
                $bpData["PRJ_DESCRIPTION"] = $data["PRO_DESCRIPTION"];
            }
            if (array_key_exists("PRO_CREATE_USER", $data)) {
                $bpData["PRJ_AUTHOR"] = $data["PRO_CREATE_USER"];
            } elseif (array_key_exists("USR_UID", $data)) {
                $bpData["PRJ_AUTHOR"] = $data["USR_UID"];
            }

            $bp = new Project\Bpmn();
            $bp->create($bpData);

            // At this time we will add a default diagram and process
            $bp->addDiagram();
            $bp->addProcess();

        } catch (\Exception $e) {
            $prjUid = $this->getUid();
            $this->remove();

            throw new \RuntimeException(sprintf(
                "Can't create Project with prj_uid: %s, workflow creation fails." . PHP_EOL . $e->getMessage()
                , $prjUid
            ));
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        return parent::getList($start, $limit, $filter, $changeCaseTo);
    }

    public function remove()
    {
        parent::remove();
        $this->bp->remove();
    }

    public function generateBpmnDataEvent(
        $projectUid,
        $processUid,
        $objectBpmnType,
        $objectUid,
        $objectBouX,
        $objectBouY,
        $objectBouWidth,
        $objectBouHeight,
        $eventType,
        $i
    ) {
        try {
            $eventBouWidth  = 35;
            $eventBouHeight = $eventBouWidth;

            $eventBouWidth2  = (int)($eventBouWidth / 2);
            $eventBouHeight2 = (int)($eventBouHeight / 2);

            $eventBouHeight12 = (int)($eventBouWidth / 12);

            //
            $objectBouWidth2 = (int)($objectBouWidth / 2);
            $objectBouWidth4 = (int)($objectBouWidth / 4);

            //Event
            $eventUid = \ProcessMaker\Util\Common::generateUID();

            if ($objectBpmnType == "bpmnGateway" && $eventType == "END") {
                //Gateway
                $eventBouX = $objectBouX + $objectBouWidth + $objectBouWidth4;
                $eventBouY = $objectBouY + (int)($objectBouHeight / 2) - $eventBouHeight2;
            } else {
                //Activity
                $eventBouX = $objectBouX + $objectBouWidth2 - $eventBouWidth2;
                $eventBouY = ($eventType == "START")? $objectBouY - $eventBouHeight - $eventBouHeight2 : $objectBouY + $objectBouHeight + $eventBouHeight2 + $eventBouHeight12;
            }

            $arrayEvent = array(
                "evn_uid"    => $eventUid,
                "prj_uid"    => $projectUid,
                "pro_uid"    => $processUid,
                //"evn_name"   => \G::LoadTranslation(($eventType == "START")? "ID_BPMN_EVENT_START_NAME" : "ID_BPMN_EVENT_END_NAME", array($i)),
                "evn_name"   => "",
                "evn_type"   => $eventType,
                "evn_marker" => "EMPTY",
                "bou_x"      => $eventBouX,
                "bou_y"      => $eventBouY,
                "bou_width"  => $eventBouWidth,
                "bou_height" => $eventBouHeight
            );

            //Flow
            if ($objectBpmnType == "bpmnGateway" && $eventType == "END") {
                //Gateway
                $flowX1 = $objectBouX + $objectBouWidth;
                $flowY1 = $objectBouY + (int)($objectBouHeight / 2);
                $flowX2 = $eventBouX;
                $flowY2 = $eventBouY + $eventBouHeight2;
            } else {
                //Activity
                $flowX1 = $objectBouX + $objectBouWidth2;
                $flowY1 = ($eventType == "START")? $objectBouY - $eventBouHeight + $eventBouHeight2 : $objectBouY + $objectBouHeight;
                $flowX2 = $flowX1;
                $flowY2 = ($eventType == "START")? $objectBouY : $objectBouY + $objectBouHeight + $eventBouHeight2 + $eventBouHeight12;
            }

            $arrayFlow = array(
                "flo_uid"                 => \ProcessMaker\Util\Common::generateUID(),
                "prj_uid"                 => $projectUid,
                "pro_uid"                 => $processUid,
                "flo_type"                => "SEQUENCE",
                "flo_element_origin"      => ($eventType == "START")? $eventUid : $objectUid,
                "flo_element_origin_type" => ($eventType == "START")? "bpmnEvent" : $objectBpmnType,
                "flo_element_dest"        => ($eventType == "START")? $objectUid : $eventUid,
                "flo_element_dest_type"   => ($eventType == "START")? $objectBpmnType : "bpmnEvent",
                "flo_is_inmediate"        => 1,
                "flo_x1"                  => $flowX1,
                "flo_y1"                  => $flowY1,
                "flo_x2"                  => $flowX2,
                "flo_y2"                  => $flowY2,
                "flo_state"               => json_encode(
                    array(
                        array("x" => $flowX1, "y" => $flowY1),
                        array("x" => $flowX1, "y" => $flowY2 - 5),
                        array("x" => $flowX2, "y" => $flowY2 - 5),
                        array("x" => $flowX2, "y" => $flowY2)
                    )
                )
            );

            //Return
            return array($arrayEvent, $arrayFlow);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generateBpmnDataGateway(
        $projectUid,
        $processUid,
        $objectBpmnType,
        $objectUid,
        $objectBouX,
        $objectBouY,
        $objectBouWidth,
        $objectBouHeight,
        $gatewayType,
        $gatewayName,
        $gatewayDirection
    ) {
        try {
            $gatewayBouWidth  = 45;
            $gatewayBouHeight = $gatewayBouWidth;

            $gatewayBouWidth2  = (int)($gatewayBouWidth / 2);
            $gatewayBouHeight2 = (int)($gatewayBouHeight / 2);

            //
            $objectBouWidth2 = (int)($objectBouWidth / 2);
            $objectBouHeight2 = (int)($objectBouHeight / 2);

            //Gateway
            $gatewayUid = \ProcessMaker\Util\Common::generateUID();
            $gatewayBouX = $objectBouX + $objectBouWidth2 - $gatewayBouWidth2;
            $gatewayBouY = ($gatewayDirection == "DIVERGING")? $objectBouY + $objectBouHeight + $gatewayBouHeight2 : $objectBouY - $gatewayBouHeight - $gatewayBouHeight2;

            $arrayGateway = array(
                "gat_uid"          => $gatewayUid,
                "prj_uid"          => $projectUid,
                "pro_uid"          => $processUid,
                "gat_type"         => $gatewayType,
                "gat_name"         => $gatewayName,
                "gat_direction"    => $gatewayDirection,
                "gat_default_flow" => "0",
                "bou_x"            => $gatewayBouX,
                "bou_y"            => $gatewayBouY,
                "bou_width"        => $gatewayBouWidth,
                "bou_height"       => $gatewayBouHeight
            );

            //Flow
            if ($gatewayDirection == "DIVERGING") {
                $flowX1 = $objectBouX + $objectBouWidth2;
                $flowY1 = $objectBouY + $objectBouHeight;
                $flowX2 = $flowX1;
                $flowY2 = $gatewayBouY;
            } else {
                $flowX1 = $objectBouX + $objectBouWidth2;
                $flowY1 = $gatewayBouY + $gatewayBouHeight;
                $flowX2 = $flowX1;
                $flowY2 = $objectBouY;
            }

            $arrayFlow = array(
                "flo_uid"                 => \ProcessMaker\Util\Common::generateUID(),
                "prj_uid"                 => $projectUid,
                "pro_uid"                 => $processUid,
                "flo_type"                => "SEQUENCE",
                "flo_element_origin"      => ($gatewayDirection == "DIVERGING")? $objectUid : $gatewayUid,
                "flo_element_origin_type" => ($gatewayDirection == "DIVERGING")? $objectBpmnType : "bpmnGateway",
                "flo_element_dest"        => ($gatewayDirection == "DIVERGING")? $gatewayUid : $objectUid,
                "flo_element_dest_type"   => ($gatewayDirection == "DIVERGING")? "bpmnGateway" : $objectBpmnType,
                "flo_is_inmediate"        => 1,
                "flo_x1"                  => $flowX1,
                "flo_y1"                  => $flowY1,
                "flo_x2"                  => $flowX2,
                "flo_y2"                  => $flowY2,
                "flo_state"               => json_encode(
                    array(
                        array("x" => $flowX1, "y" => $flowY1),
                        array("x" => $flowX1, "y" => $flowY2 - 5),
                        array("x" => $flowX2, "y" => $flowY2 - 5),
                        array("x" => $flowX2, "y" => $flowY2)
                    )
                )
            );

            //Return
            return array($arrayGateway, $arrayFlow);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generateBpmnDataFlow(
        $projectUid,
        $processUid,
        $objectOriginBpmnType,
        $objectOriginUid,
        $objectOriginBouX,
        $objectOriginBouY,
        $objectOriginBouWidth,
        $objectOriginBouHeight,
        $objectDestBpmnType,
        $objectDestUid,
        $objectDestBouX,
        $objectDestBouY,
        $objectDestBouWidth,
        $objectDestBouHeight
    ) {
        try {
            $objectOriginBouWidth2  = (int)($objectOriginBouWidth / 2);
            $objectDestBouWidth2  = (int)($objectDestBouWidth / 2);

            $x1 = $objectOriginBouX + $objectOriginBouWidth2;
            $y1 = $objectOriginBouY + $objectOriginBouHeight;
            $x2 = $objectDestBouX + $objectDestBouWidth2;
            $y2 = $objectDestBouY;

            //Flow
            $arrayFlow = array(
                "flo_uid"                 => \ProcessMaker\Util\Common::generateUID(),
                "prj_uid"                 => $projectUid,
                "pro_uid"                 => $processUid,
                "flo_type"                => "SEQUENCE",
                "flo_element_origin"      => $objectOriginUid,
                "flo_element_origin_type" => $objectOriginBpmnType,
                "flo_element_dest"        => $objectDestUid,
                "flo_element_dest_type"   => $objectDestBpmnType,
                "flo_is_inmediate"        => 1,
                "flo_x1"                  => $x1,
                "flo_y1"                  => $y1,
                "flo_x2"                  => $x2,
                "flo_y2"                  => $y2,
                "flo_state"               => json_encode(array())
            );

            //Return
            return $arrayFlow;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generateBpmnData($processUid)
    {
        try {
            $arrayData = array();

            //Generate workflow data
            list($arrayWorkflowData, $arrayWorkflowFile) = $this->getData($processUid);

            $arrayWorkflowData["groupwfs"] = array(); //Empty Groups

            $projectUid = $processUid;
            $bpmnDiagramUid = \ProcessMaker\Util\Common::generateUID();
            $bpmnProcessUid = \ProcessMaker\Util\Common::generateUID();

            //Generate BPMN data
            $arrayBpmnData = array();

            $arrayBpmnData["project"][] = array(
                "prj_uid"         => $projectUid,
                "prj_name"        => $arrayWorkflowData["process"][0]["PRO_TITLE"],
                "prj_description" => $arrayWorkflowData["process"][0]["PRO_DESCRIPTION"],
                "prj_create_date" => $arrayWorkflowData["process"][0]["PRO_CREATE_DATE"],
                "prj_update_date" => $arrayWorkflowData["process"][0]["PRO_UPDATE_DATE"],
                "prj_author"      => $arrayWorkflowData["process"][0]["PRO_CREATE_USER"]
            );

            $arrayBpmnData["diagram"][] = array(
                "dia_uid"  => $bpmnDiagramUid,
                "prj_uid"  => $projectUid,
                "dia_name" => $arrayWorkflowData["process"][0]["PRO_TITLE"]
            );

            $arrayBpmnData["process"][] = array(
                "pro_uid"  => $bpmnProcessUid,
                "prj_uid"  => $projectUid,
                "dia_uid"  => $bpmnDiagramUid,
                "pro_name" => $arrayWorkflowData["process"][0]["PRO_TITLE"]
            );

            $eventStartCount = 0;
            $eventEndCount = 0;

            $arrayActivityType = array(
                "NORMAL"     => "TASK",
                "ADHOC"      => "TASK",
                "SUBPROCESS" => "SUB_PROCESS"
            );

            $arrayBpmnData["activity"] = array(); //Activity
            $arrayBpmnData["event"] = array();    //Event
            $arrayBpmnData["flow"] = array();     //Flow

            $arrayTaskData = array();

            foreach ($arrayWorkflowData["tasks"] as $value) {
                $arrayTask = $value;

                $arrayTaskData[$arrayTask["TAS_UID"]] = $arrayTask;

                //Activity
                $activityUid = $arrayTask["TAS_UID"];
                $activityBouX = (int)($arrayTask["TAS_POSX"]);
                $activityBouY = (int)($arrayTask["TAS_POSY"]);
                $activityBouWidth  = (int)($arrayTask["TAS_WIDTH"]);
                $activityBouHeight = (int)($arrayTask["TAS_HEIGHT"]);

                $arrayBpmnData["activity"][] = array(
                    "act_uid"    => $activityUid,
                    "prj_uid"    => $projectUid,
                    "pro_uid"    => $bpmnProcessUid,
                    "act_name"   => $arrayTask["TAS_TITLE"],
                    "act_type"   => $arrayActivityType[$arrayTask["TAS_TYPE"]],
                    "bou_x"      => $activityBouX,
                    "bou_y"      => $activityBouY,
                    "bou_width"  => $activityBouWidth,
                    "bou_height" => $activityBouHeight
                );

                if ($arrayTask["TAS_START"] == "TRUE") {
                    $eventStartCount = $eventStartCount + 1;

                    list($arrayEvent, $arrayFlow) = $this->generateBpmnDataEvent(
                        $projectUid,
                        $bpmnProcessUid,
                        "bpmnActivity",
                        $activityUid,
                        $activityBouX,
                        $activityBouY,
                        $activityBouWidth,
                        $activityBouHeight,
                        "START",
                        $eventStartCount
                    );

                    //Event - START
                    $arrayBpmnData["event"][] = $arrayEvent;

                    //Flow
                    $arrayBpmnData["flow"][] = $arrayFlow;
                }
            }

            $arrayWorkflowDataRouteSecJoin = array();

            $arrayGatewayDivergingData = array();
            $arrayGatewayDivergingNextActivityData = array();

            $arrayGatewayInfo = array(
                "EVALUATE"               => array("type" => "EXCLUSIVE", "translationUid" => "ID_BPMN_GATEWAY_NAME_EXCLUSIVE", "count" => 0),
                "SELECT"                 => array("type" => "COMPLEX",   "translationUid" => "ID_BPMN_GATEWAY_NAME_COMPLEX",   "count" => 0),
                "PARALLEL"               => array("type" => "PARALLEL",  "translationUid" => "ID_BPMN_GATEWAY_NAME_PARALLEL",  "count" => 0),
                "PARALLEL-BY-EVALUATION" => array("type" => "INCLUSIVE", "translationUid" => "ID_BPMN_GATEWAY_NAME_INCLUSIVE", "count" => 0)
            );

            $arrayGatewayInfoR = array(
                "EXCLUSIVE" => "EVALUATE",
                "COMPLEX"   => "SELECT",
                "PARALLEL"  => "PARALLEL",
                "INCLUSIVE" => "PARALLEL-BY-EVALUATION"
            );

            $arrayBpmnData["gateway"] = array(); //Gateway

            foreach ($arrayWorkflowData["routes"] as $value) {
                $arrayRoute = $value;

                $arrayTask = $arrayTaskData[$arrayRoute["TAS_UID"]];

                $activityUid = $arrayTask["TAS_UID"];
                $activityBouX = (int)($arrayTask["TAS_POSX"]);
                $activityBouY = (int)($arrayTask["TAS_POSY"]);
                $activityBouWidth  = (int)($arrayTask["TAS_WIDTH"]);
                $activityBouHeight = (int)($arrayTask["TAS_HEIGHT"]);

                $flagFlow = false;
                $strFlowParams = "";
                $flagEventEnd = false;
                $strEventEndParams = "";

                switch ($arrayRoute["ROU_TYPE"]) {
                    case "EVALUATE":
                    case "SELECT":
                    case "PARALLEL":
                    case "PARALLEL-BY-EVALUATION":
                        if (!isset($arrayGatewayDivergingData[$activityUid])) {
                            $arrayGatewayInfo[$arrayRoute["ROU_TYPE"]]["count"] = $arrayGatewayInfo[$arrayRoute["ROU_TYPE"]]["count"] + 1;

                            list($arrayGateway, $arrayFlow) = $this->generateBpmnDataGateway(
                                $projectUid,
                                $bpmnProcessUid,
                                "bpmnActivity",
                                $activityUid,
                                $activityBouX,
                                $activityBouY,
                                $activityBouWidth,
                                $activityBouHeight,
                                $arrayGatewayInfo[$arrayRoute["ROU_TYPE"]]["type"],
                                //\G::LoadTranslation($arrayGatewayInfo[$arrayRoute["ROU_TYPE"]]["translationUid"], array($arrayGatewayInfo[$arrayRoute["ROU_TYPE"]]["count"])),
                                "",
                                "DIVERGING"
                            );

                            //Gateway
                            $arrayBpmnData["gateway"][] = $arrayGateway;

                            //Flow
                            $arrayBpmnData["flow"][] = $arrayFlow;

                            //Gateway DIVERGING
                            $arrayGatewayDivergingData[$activityUid] = array(
                                $arrayGateway["gat_uid"],
                                $arrayGateway["gat_type"],
                                $arrayGateway["bou_x"],
                                $arrayGateway["bou_y"],
                                $arrayGateway["bou_width"],
                                $arrayGateway["bou_height"],
                            );
                        }

                        $gatewayUid  = $arrayGatewayDivergingData[$activityUid][0];
                        $gatewayType = $arrayGatewayDivergingData[$activityUid][1];
                        $gatewayBouX = $arrayGatewayDivergingData[$activityUid][2];
                        $gatewayBouY = $arrayGatewayDivergingData[$activityUid][3];
                        $gatewayBouWidth  = $arrayGatewayDivergingData[$activityUid][4];
                        $gatewayBouHeight = $arrayGatewayDivergingData[$activityUid][5];

                        if ($arrayRoute["ROU_NEXT_TASK"] != "-1") {
                            $flagFlow = true;

                            $arrayTask = $arrayTaskData[$arrayRoute["ROU_NEXT_TASK"]];

                            $strFlowParams = "
                                \"bpmnGateway\",
                                \"$gatewayUid\",
                                $gatewayBouX,
                                $gatewayBouY,
                                $gatewayBouWidth,
                                $gatewayBouHeight,
                                \"bpmnActivity\",
                                \"" . $arrayTask["TAS_UID"] . "\",
                                " . ((int)($arrayTask["TAS_POSX"])) . ",
                                " . ((int)($arrayTask["TAS_POSY"])) . ",
                                " . ((int)($arrayTask["TAS_WIDTH"])) . ",
                                " . ((int)($arrayTask["TAS_HEIGHT"])) . "
                            ";

                            //Gateway DIVERGING - Next Activity
                            $arrayGatewayDivergingNextActivityData[$arrayTask["TAS_UID"]] = array(
                                $gatewayUid,
                                $gatewayType,
                                $gatewayBouX,
                                $gatewayBouY,
                                $gatewayBouWidth,
                                $gatewayBouHeight
                            );
                        } else {
                            $flagEventEnd = true;

                            $objectBpmnType = "bpmnGateway";
                            $objectUid = $gatewayUid;
                            $objectBouX = $gatewayBouX;
                            $objectBouY = $gatewayBouY;
                            $objectBouWidth = $gatewayBouWidth;
                            $objectBouHeight = $gatewayBouHeight;
                        }
                        break;
                    case "SEQUENTIAL":
                        if ($arrayRoute["ROU_NEXT_TASK"] != "-1") {
                            $flagFlow = true;

                            $arrayTask = $arrayTaskData[$arrayRoute["ROU_NEXT_TASK"]];

                            $strFlowParams = "
                                \"bpmnActivity\",
                                \"$activityUid\",
                                $activityBouX,
                                $activityBouY,
                                $activityBouWidth,
                                $activityBouHeight,
                                \"bpmnActivity\",
                                \"" . $arrayTask["TAS_UID"] . "\",
                                " . ((int)($arrayTask["TAS_POSX"])) . ",
                                " . ((int)($arrayTask["TAS_POSY"])) . ",
                                " . ((int)($arrayTask["TAS_WIDTH"])) . ",
                                " . ((int)($arrayTask["TAS_HEIGHT"])) . "
                            ";
                        } else {
                            $flagEventEnd = true;

                            $objectBpmnType = "bpmnActivity";
                            $objectUid = $activityUid;
                            $objectBouX = $activityBouX;
                            $objectBouY = $activityBouY;
                            $objectBouWidth = $activityBouWidth;
                            $objectBouHeight = $activityBouHeight;
                        }
                        break;
                    case "SEC-JOIN":
                        $arrayWorkflowDataRouteSecJoin[] = $arrayRoute;
                        break;
                }

                if ($flagFlow) {
                    eval("\$arrayFlow = \$this->generateBpmnDataFlow(\"$projectUid\", \"$bpmnProcessUid\", $strFlowParams);");

                    //Flow
                    $arrayBpmnData["flow"][] = $arrayFlow;
                }

                if ($flagEventEnd) {
                    $eventEndCount = $eventEndCount + 1;

                    list($arrayEvent, $arrayFlow) = $this->generateBpmnDataEvent(
                        $projectUid,
                        $bpmnProcessUid,
                        $objectBpmnType,
                        $objectUid,
                        $objectBouX,
                        $objectBouY,
                        $objectBouWidth,
                        $objectBouHeight,
                        "END",
                        $eventEndCount
                    );

                    //Event - END
                    $arrayBpmnData["event"][] = $arrayEvent;

                    //Flow
                    $arrayBpmnData["flow"][] = $arrayFlow;
                }
            }

            //ROU_TYPE = SEC-JOIN
            $arrayGatewayConvergingData = array();

            foreach ($arrayWorkflowDataRouteSecJoin as $value) {
                $arrayRoute = $value;

                $arrayTask = $arrayTaskData[$arrayRoute["TAS_UID"]];

                $activityUid = $arrayTask["TAS_UID"];
                $activityBouX = (int)($arrayTask["TAS_POSX"]);
                $activityBouY = (int)($arrayTask["TAS_POSY"]);
                $activityBouWidth  = (int)($arrayTask["TAS_WIDTH"]);
                $activityBouHeight = (int)($arrayTask["TAS_HEIGHT"]);

                $arrayTask = $arrayTaskData[$arrayRoute["ROU_NEXT_TASK"]];

                $nextActivityUid = $arrayTask["TAS_UID"];
                $nextActivityBouX = (int)($arrayTask["TAS_POSX"]);
                $nextActivityBouY = (int)($arrayTask["TAS_POSY"]);
                $nextActivityBouWidth  = (int)($arrayTask["TAS_WIDTH"]);
                $nextActivityBouHeight = (int)($arrayTask["TAS_HEIGHT"]);

                if (!isset($arrayGatewayConvergingData[$nextActivityUid])) {
                    $gatewayParentType = $arrayGatewayDivergingNextActivityData[$activityUid][1];

                    $arrayGatewayInfo[$arrayGatewayInfoR[$gatewayParentType]]["count"] = $arrayGatewayInfo[$arrayGatewayInfoR[$gatewayParentType]]["count"] + 1;

                    list($arrayGateway, $arrayFlow) = $this->generateBpmnDataGateway(
                        $projectUid,
                        $bpmnProcessUid,
                        "bpmnActivity",
                        $nextActivityUid,
                        $nextActivityBouX,
                        $nextActivityBouY,
                        $nextActivityBouWidth,
                        $nextActivityBouHeight,
                        $arrayGatewayInfo[$arrayGatewayInfoR[$gatewayParentType]]["type"],
                        //\G::LoadTranslation($arrayGatewayInfo[$arrayGatewayInfoR[$gatewayParentType]]["translationUid"], array($arrayGatewayInfo[$arrayGatewayInfoR[$gatewayParentType]]["count"])),
                        "",
                        "CONVERGING"
                    );

                    //Gateway
                    $arrayBpmnData["gateway"][] = $arrayGateway;

                    //Flow
                    $arrayBpmnData["flow"][] = $arrayFlow;

                    //Gateway CONVERGING
                    $arrayGatewayConvergingData[$nextActivityUid] = array(
                        $arrayGateway["gat_uid"],
                        $arrayGateway["gat_type"],
                        $arrayGateway["bou_x"],
                        $arrayGateway["bou_y"],
                        $arrayGateway["bou_width"],
                        $arrayGateway["bou_height"],
                    );
                }

                $gatewayUid  = $arrayGatewayConvergingData[$nextActivityUid][0];
                $gatewayType = $arrayGatewayConvergingData[$nextActivityUid][1];
                $gatewayBouX = $arrayGatewayConvergingData[$nextActivityUid][2];
                $gatewayBouY = $arrayGatewayConvergingData[$nextActivityUid][3];
                $gatewayBouWidth  = $arrayGatewayConvergingData[$nextActivityUid][4];
                $gatewayBouHeight = $arrayGatewayConvergingData[$nextActivityUid][5];

                $arrayFlow = $this->generateBpmnDataFlow(
                    $projectUid,
                    $bpmnProcessUid,
                    "bpmnActivity",
                    $activityUid,
                    $activityBouX,
                    $activityBouY,
                    $activityBouWidth,
                    $activityBouHeight,
                    "bpmnGateway",
                    $gatewayUid,
                    $gatewayBouX,
                    $gatewayBouY,
                    $gatewayBouWidth,
                    $gatewayBouHeight
                );

                //Flow
                $arrayBpmnData["flow"][] = $arrayFlow;
            }

            $arrayBpmnData["artifact"] = array(); //Artifact

            foreach ($arrayWorkflowData["lanes"] as $value) {
                $arrayLane = $value;

                //Artifact
                $artifactUid = \ProcessMaker\Util\Common::generateUID();
                $artifactBouX = (int)($arrayLane["SWI_X"]);
                $artifactBouY = (int)($arrayLane["SWI_Y"]);

                $artifactType = ($arrayLane["SWI_TYPE"] == "TEXT")? "TEXT_ANNOTATION" : (($artifactBouX == 0)? "HORIZONTAL_LINE" : "VERTICAL_LINE");
                $artifactName = ($artifactType == "TEXT_ANNOTATION")? $arrayLane["SWI_TEXT"] : "";
                $artifactBouX = ($artifactType == "TEXT_ANNOTATION")? $artifactBouX : (($artifactType == "HORIZONTAL_LINE")? -6666 : $artifactBouX);
                $artifactBouY = ($artifactType == "TEXT_ANNOTATION")? $artifactBouY : (($artifactType == "HORIZONTAL_LINE")? $artifactBouY : -6666);
                $artifactBouWidth  = ($artifactType == "TEXT_ANNOTATION")? 100 : 0;
                $artifactBouHeight = ($artifactType == "TEXT_ANNOTATION")? 30 : 0;

                $arrayBpmnData["artifact"][] = array(
                    "art_uid"    => $artifactUid,
                    "prj_uid"    => $projectUid,
                    "pro_uid"    => $bpmnProcessUid,
                    "art_type"   => $artifactType,
                    "art_name"   => $artifactName,
                    "bou_x"      => $artifactBouX,
                    "bou_y"      => $artifactBouY,
                    "bou_width"  => $artifactBouWidth,
                    "bou_height" => $artifactBouHeight
                );
            }

            //Set data
            $arrayData["tables"]["bpmn"] = $arrayBpmnData;
            $arrayData["tables"]["workflow"] = $arrayWorkflowData;

            $arrayData["files"]["bpmn"] = array();
            $arrayData["files"]["workflow"] = array_change_key_case($arrayWorkflowFile, CASE_LOWER);

            //Return
            return $arrayData;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function generateBpmn($processUid, $fieldNameForException)
    {
        try {
            //Verify data
            $obj = \ProcessPeer::retrieveByPK($processUid);

            if (is_null($obj)) {
                throw new \Exception(\G::LoadTranslation("ID_PROCESS_DOES_NOT_EXIST", array($fieldNameForException, $processUid)));
            }

            //Verify data
            $criteria = new \Criteria("workflow");

            $criteria->addSelectColumn(\BpmnProjectPeer::PRJ_UID);
            $criteria->add(\BpmnProjectPeer::PRJ_UID, $processUid, \Criteria::EQUAL);

            $rsCriteria = \BpmnProjectPeer::doSelectRS($criteria);

            if ($rsCriteria->next()) {
                throw new \Exception(\G::LoadTranslation("ID_PROJECT_IS_BPMN", array($fieldNameForException, $processUid)));
            }

            //Get data
            $arrayBpmnData = $this->generateBpmnData($processUid);

            $processTitle = $arrayBpmnData["tables"]["workflow"]["process"][0]["PRO_TITLE"] . " - New version - " . date("M d, H:i:s");

            $arrayBpmnData["tables"]["bpmn"]["project"][0]["prj_name"] = $processTitle;
            $arrayBpmnData["tables"]["bpmn"]["diagram"][0]["dia_name"] = $processTitle;
            $arrayBpmnData["tables"]["bpmn"]["process"][0]["pro_name"] = $processTitle;

            $arrayBpmnData["tables"]["workflow"]["process"][0]["PRO_PARENT"] = $processUid;
            $arrayBpmnData["tables"]["workflow"]["process"][0]["PRO_TITLE"]  = $processTitle;

            //Generate
            $importer = new \ProcessMaker\Importer\XmlImporter();
            $importer->setImportData($arrayBpmnData);

            $projectUid = $importer->import(\ProcessMaker\Importer\XmlImporter::IMPORT_OPTION_WORKFLOW_TO_BPMN_GENERATE);

            //Return
            return $projectUid;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

