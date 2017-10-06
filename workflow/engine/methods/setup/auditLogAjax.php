<?php

function mktimeDate ($date)
{
    $arrayAux = getdate( strtotime( $date ) );
    $mktDate = mktime( $arrayAux["hours"], $arrayAux["minutes"], $arrayAux["seconds"], $arrayAux["mon"], $arrayAux["mday"], $arrayAux["year"] );
    return $mktDate;
}

function auditLogArraySet ($str, $filter)
{   
    $arrayAux = explode( "|", $str );
    $date = "";
    $workspace = "";
    $action = "";
    $ip = "";
    $user = "";
    $description = "";
    
    if (count( $arrayAux ) > 1) {
        $date = (isset( $arrayAux[0] )) ? trim( $arrayAux[0] ) : "";
        $workspace = (isset( $arrayAux[1] )) ? trim( $arrayAux[1] ) : "";
        $ip = (isset( $arrayAux[2] )) ? trim( $arrayAux[2] ) : "";
        $user = (isset( $arrayAux[4] )) ? trim( $arrayAux[4] ) : "";
        $action = (isset( $arrayAux[5] )) ? trim( $arrayAux[5] ) : "";
        $description = (isset( $arrayAux[6] )) ? trim( $arrayAux[6] ) : "";
    }

    $mktDate = (! empty( $date )) ? mktimeDate( $date ) : 0;
    
    //Filter
    $sw = 1;
    if ($workspace != $filter["workspace"]) {
        $sw = 0;
    }

    if ($filter["action"] != "ALL") {
        if ($action != $filter["action"]) {
            $sw = 0;
        }       
    }

    if ($filter["dateFrom"] && $mktDate > 0) {
        if (! (mktimeDate( $filter["dateFrom"] ) <= $mktDate)) {
            $sw = 0;
        }
    }

    if ($filter["dateTo"] && $mktDate > 0) {
        if (! ($mktDate <= mktimeDate( $filter["dateTo"] . " 23:59:59" ))) {
            $sw = 0;
        }
    }

    if ($filter["description"]) {
        $sw = 0;
        $string = $filter["description"];
        
        if ( (stristr($date, $string) !== false) || (stristr($ip, $string) !== false) || (stristr($user, $string) !== false) || (stristr($action, $string) !== false) || (stristr($description, $string) !== false) ) {
            $sw = 1;
        }
    }

    $arrayData = array ();
    $newAction = preg_replace('/([A-Z])/', '_$1', $action);
    $newAction = "ID".strtoupper($newAction);

    if ($sw == 1) {
        $arrayData = array ("DATE" => $date, "USER" => $user, "IP" =>$ip, "ACTION" => G::LoadTranslation($newAction), "DESCRIPTION" => $description);
    }

    return $arrayData;
}

function getAuditLogData ($filter, $r, $i)
{   
    $arrayData = array ();
    $strAux = null;
    $count = 0;

    $file = PATH_DATA . "log" . PATH_SEP . "audit.log";

    if (file_exists($file)) {
        $arrayFileData = file($file);
        
        for ($k = 0; $k < count($arrayFileData); $k++) {
            
            $strAux = $arrayFileData[$k];

            if ($strAux) {
                $arrayAux = auditLogArraySet($strAux, $filter);
                if (count($arrayAux) > 0) {
                    $count = $count + 1;

                    if ($count > $i && count($arrayData) < $r) {
                        $arrayData[] = $arrayAux;
                    }
                }
            }
        }
    }

    return array($count, $arrayData);
}

$option = (isset( $_REQUEST["option"] )) ? $_REQUEST["option"] : null;

$response = array ();

switch ($option) {
    case "LST":
        $pageSize = $_REQUEST["pageSize"];
        $workspace = config("sys_sys");
        $action = $_REQUEST["action"];
        $description = $_REQUEST["description"];
        $dateFrom = $_REQUEST["dateFrom"];
        $dateTo = $_REQUEST["dateTo"];

        $arrayFilter = array ("workspace" => $workspace, "action" => $action, "description" => $description,"dateFrom" => str_replace( "T00:00:00", null, $dateFrom ),"dateTo" => str_replace( "T00:00:00", null, $dateTo )
        );

        $limit = isset( $_REQUEST["limit"] ) ? $_REQUEST["limit"] : $pageSize;
        $start = isset( $_REQUEST["start"] ) ? $_REQUEST["start"] : 0;

        list ($count, $data) = getAuditLogData( $arrayFilter, $limit, $start );
        $response = array ("success" => true,"resultTotal" => $count,"resultRoot" => $data
        );
        break;
    case "EMPTY":
        $status = 1;

        try {
            $file = PATH_DATA . "log" . PATH_SEP . "cron.log";

            if (file_exists( $file )) {
                unlink( $file );
            }

            $response["status"] = "OK";
        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
            $status = 0;
        }

        if ($status == 0) {
            $response["status"] = "ERROR";
        }
        break;
}

echo G::json_encode( $response );