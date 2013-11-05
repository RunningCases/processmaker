<?php
namespace Services\Api\ProcessMaker;

class Process extends \ProcessMaker\Api
{
    public function index($proTitle = "", $proCategory = "", $start = 0, $limit = 25)
    {
        $response = array();

        try {
            $arrayFilterData = array();

            if ($proTitle != "") {
                $arrayFilterData["processName"] = $proTitle;
            }

            if ($proCategory != "") {
                $arrayFilterData["category"] = $proCategory;
            }

            $process = new \BusinessModel\Process();

            $data = $process->loadAllProcess($arrayFilterData, $start, $limit);

            //Response
            $response["success"] = true;
            $response["message"] = "Processes loaded successfully";
            $response["data"] = $data;
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    public function post($request_data = null)
    {
        define("SYS_LANG", $request_data["lang"]);

        $response = array();

        try {
            $userUid = "00000000000000000000000000000001"; //$this->getUserId()

            $process = new \BusinessModel\Process();

            $data = $process->createProcess($userUid, $request_data);

            //Response
            $response["success"] = true;
            $response["message"] = "Process saved successfully";
            $response["data"] = $data;
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    public function get($processUid)
    {
        $response = array();

        try {
            $process = new \BusinessModel\Process();

            $data = $process->loadProcess($processUid);

            //Response
            $response["success"] = true;
            $response["message"] = "Process load successfully";
            $response["data"] = $data;
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    public function put($processUid, $request_data = null)
    {
        $response = array();

        try {
            $userUid = "00000000000000000000000000000001";

            $process = new \BusinessModel\Process();

            $data = $process->updateProcess($processUid, $userUid, $request_data);

            //Response
            $response["success"] = true;
            $response["message"] = "Process updated successfully";
            $response["data"] = $data;
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    public function delete($processUid, $checkCases = 1)
    {
        $response = array();

        try {
            $process = new \BusinessModel\Process();

            $result = $process->deleteProcess($processUid, (($checkCases && $checkCases == 1)? true : false));

            //Response
            $response["success"] = true;
            $response["message"] = "Process was deleted successfully";
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }





    /**
     * @url GET /:processUid/activity/:activityUid
     */
    public function getActivity($activityUid, $processUid)
    {
        $response = array();

        try {
            $task1 = new \Task();
            $task2 = new \BusinessModel\Task();

            $arrayData = $task1->load($activityUid);

            $arrayData = array(
                //"tas_uid"   => $activityUid,
                "tas_title" => $arrayData["TAS_TITLE"],
                "tas_description" => $arrayData["TAS_DESCRIPTION"],
                "tas_posx"  => $arrayData["TAS_POSX"],
                "tas_posy"  => $arrayData["TAS_POSY"],
                "tas_start" => $arrayData["TAS_START"],
                "_extended" => array(
                    "properties" => $task2->getProperties($activityUid, true),
                    "steps" => array(
                        "steps"       => $task2->getSteps($activityUid, true),
                        "conditions"  => "...", //lo mismo que steps //$task->getSteps()
                        "triggers"    => $task2->getTriggers($activityUid, true),
                        "users"       => $task2->getUsers($activityUid, 1, true),
                        "users_adhoc" => $task2->getUsers($activityUid, 2, true)
                    )
                )
            );

            //Response
            $response["success"] = true;
            $response["message"] = "Properties loaded successfully";
            $response["data"]    = array("activity" => $arrayData);
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:processUid/activity/:activityUid/properties
     */
    public function getActivityProperties($activityUid, $processUid)
    {
        $response = array();

        try {
            $task1 = new \Task();

            $arrayData = $task1->load($activityUid);

            $arrayData = array(
                //"tas_uid"   => $activityUid,
                "tas_title" => $arrayData["TAS_TITLE"],
                "tas_description" => $arrayData["TAS_DESCRIPTION"],
                "tas_posx"  => $arrayData["TAS_POSX"],
                "tas_posy"  => $arrayData["TAS_POSY"],
                "tas_start" => $arrayData["TAS_START"]
            );

            //Response
            $response["success"] = true;
            $response["message"] = "Properties loaded successfully";
            $response["data"]    = array("activity" => $arrayData);
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:processUid/activity/:activityUid/extended
     */
    public function getActivityExtended($activityUid, $processUid)
    {
        $response = array();

        try {
            $task2 = new \BusinessModel\Task();

            $arrayData = array(
                "_extended" => array(
                    "properties" => $task2->getProperties($activityUid, true),
                    "steps" => array(
                        "steps"       => $task2->getSteps($activityUid, true),
                        "conditions"  => "...", //lo mismo que steps //$task->getSteps()
                        "triggers"    => $task2->getTriggers($activityUid, true),
                        "users"       => $task2->getUsers($activityUid, 1, true),
                        "users_adhoc" => $task2->getUsers($activityUid, 2, true)
                    )
                )
            );

            //Response
            $response["success"] = true;
            $response["message"] = "Extended loaded successfully";
            $response["data"]    = array("activity" => $arrayData);
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @url GET /:processUid/activity/:activityUid/steps/list
     */
    public function getActivityStepsList($activityUid, $processUid, $start = 0, $limit = 10)
    {
        $response = array();

        try {
            $task = new \BusinessModel\Task();

            $data = $task->getStepsList($activityUid, $processUid, false, $start, $limit);

            //Response
            $response["success"] = true;
            $response["message"] = "Steps loaded successfully";
            $response["data"]    = $data;
        } catch (Exception $e) {
            //Response
            $response["success"] = false;
            $response["message"] = $e->getMessage();
        }

        return $response;
    }
}

