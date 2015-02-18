<?php

ini_set("max_execution_time", 0);

if (isset($_FILES["PROCESS_FILENAME"]) &&
        pathinfo($_FILES["PROCESS_FILENAME"]["name"], PATHINFO_EXTENSION) == "bpmn"
) {
    try {
        $createMode = $_REQUEST["createMode"];
        $name = pathinfo($_FILES["PROCESS_FILENAME"]["name"], PATHINFO_FILENAME);
        $data = array(
            "type" => "bpmnProject",
            "PRO_TITLE" => $name,
            "PRO_DESCRIPTION" => "",
            "PRO_CATEGORY" => "",
            "PRO_CREATE_USER" => $_SESSION['USER_LOGGED']
        );
        $stringBpmn = base64_encode(file_get_contents($_FILES["PROCESS_FILENAME"]["tmp_name"]));
        if ($createMode === "overwrite") {
            $process = Process::getByProTitle($data["PRO_TITLE"]);
            if ($process !== null) {
                $oProcess = new Process();
                $oProcess->remove($process["PRO_UID"]);
            }
        }
        if ($createMode === "rename") {
            $data["PRO_TITLE"] = Process::getNextTitle($data["PRO_TITLE"]);
        }
        $project = new \ProcessMaker\Project\Adapter\WorkflowBpmn($data);
        $result = array(
            "success" => true,
            "catchMessage" => "",
            "prj_uid" => $project->getUid(),
            "stringBpmn" => $stringBpmn,
            "createMode" => $createMode
        );
    } catch (Exception $e) {
        $result = array(
            "success" => "confirm",
            "catchMessage" => $e->getMessage(),
            "createMode" => $createMode
        );
    }
    echo G::json_encode($result);
    exit(0);
} else {
    $result = array(
        "success" => "error",
        "catchMessage" => G::LoadTranslation("ID_FILE_UPLOAD_INCORRECT_EXTENSION")
    );
    echo G::json_encode($result);
    exit(0);
}
