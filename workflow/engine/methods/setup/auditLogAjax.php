<?php

use ProcessMaker\AuditLog\AuditLog;

$auditLog = new AuditLog();
$auditLog->setUserLogged($_SESSION["USER_LOGGED"]);

$response = [];

$option = (isset($_REQUEST["option"])) ? $_REQUEST["option"] : null;

switch ($option) {
    case "LST":
        $pageSize = $_REQUEST["pageSize"];
        $workspace = config("system.workspace");
        $action = $_REQUEST["action"];
        $description = $_REQUEST["description"];
        $dateFrom = $_REQUEST["dateFrom"];
        $dateTo = $_REQUEST["dateTo"];

        $arrayFilter = [
            "workspace" => $workspace,
            "action" => $action,
            "description" => $description,
            "dateFrom" => str_replace("T00:00:00", null, $dateFrom),
            "dateTo" => str_replace("T00:00:00", null, $dateTo)
        ];

        $limit = isset($_REQUEST["limit"]) ? $_REQUEST["limit"] : $pageSize;
        $start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;

        list ($count, $data) = $auditLog->getAuditLogData($arrayFilter, $limit, $start);
        $response = [
            "success" => true,
            "resultTotal" => $count,
            "resultRoot" => $data
        ];
        break;
    case "EMPTY":
        $status = 1;

        try {
            $file = PATH_DATA . "log" . PATH_SEP . "cron.log";

            if (file_exists($file)) {
                unlink($file);
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

echo G::json_encode($response);
