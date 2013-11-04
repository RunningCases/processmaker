<?php
namespace BusinessModel;

class Process
{
    /**
     * Create Route
     *
     * @param string $processUid
     * @param string $taskUid
     * @param string $nextTaskUid
     * @param string $type
     * @param bool   $delete
     *
     * return string Return UID of new Route
     *
     * @access public
     */
    public function defineRoute($processUid, $taskUid, $nextTaskUid, $type, $delete = false)
    {
        //Copy of processmaker/workflow/engine/methods/processes/processes_Ajax.php //case 'saveNewPattern':

        $processMap = new processMap();

        if ($type != "SEQUENTIAL" && $type != "SEC-JOIN" && $type != "DISCRIMINATOR") {
            if ($processMap->getNumberOfRoutes($processUid, $taskUid, $nextTaskUid, $type) > 0) {
                //die();
                throw (new Exception());
            }

            //unset($aRow);
        }

        if ($delete || $type == "SEQUENTIAL" || $type == "SEC-JOIN" || $type == "DISCRIMINATOR") {
            //G::LoadClass("tasks");

            $tasks = new Tasks();

            $tasks->deleteAllRoutesOfTask($processUid, $taskUid);
            $tasks->deleteAllGatewayOfTask($processUid, $taskUid);
        }

        return $processMap->saveNewPattern($processUid, $taskUid, $nextTaskUid, $type, $delete);
    }

    /**
     * Create/Update Process
     *
     * @param string $option
     * @param array  $arrayDefineProcessData
     *
     * return array  Return data array with new UID for each element
     *
     * @access public
     */
    public function defineProcess($option, $arrayDefineProcessData)
    {
        if (!isset($arrayDefineProcessData["process"]) || count($arrayDefineProcessData["process"]) == 0) {
            throw (new Exception("Process data do not exist"));
        }

        //Process
        $process = new Process();

        $arrayProcessData = $arrayDefineProcessData["process"];

        unset($arrayProcessData["tasks"]);
        unset($arrayProcessData["routes"]);

        switch ($option) {
            case "CREATE":
                if (!isset($arrayProcessData["USR_UID"]) || trim($arrayProcessData["USR_UID"]) == "") {
                    throw (new Exception("User data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_TITLE"]) || trim($arrayProcessData["PRO_TITLE"]) == "") {
                    throw (new Exception("Process title data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_DESCRIPTION"])) {
                    throw (new Exception("Process description data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_CATEGORY"])) {
                    throw (new Exception("Process category data do not exist"));
                }
                break;
            case "UPDATE":
                break;
        }

        if (isset($arrayProcessData["PRO_TITLE"])) {
            $arrayProcessData["PRO_TITLE"] = trim($arrayProcessData["PRO_TITLE"]);
        }

        if (isset($arrayProcessData["PRO_DESCRIPTION"])) {
            $arrayProcessData["PRO_DESCRIPTION"] = trim($arrayProcessData["PRO_DESCRIPTION"]);
        }

        if (isset($arrayProcessData["PRO_TITLE"]) && $process->existsByProTitle($arrayProcessData["PRO_TITLE"])) {
            throw (new Exception(G::LoadTranslation("ID_PROCESSTITLE_ALREADY_EXISTS", SYS_LANG, array("PRO_TITLE" => $arrayProcessData["PRO_TITLE"]))));
        }

        $arrayProcessData["PRO_DYNAFORMS"] = array ();
        $arrayProcessData["PRO_DYNAFORMS"]["PROCESS"] = (isset($arrayProcessData["PRO_SUMMARY_DYNAFORM"]))? $arrayProcessData["PRO_SUMMARY_DYNAFORM"] : "";

        unset($arrayProcessData["PRO_SUMMARY_DYNAFORM"]);

        switch ($option) {
            case "CREATE":
                $processUid = $process->create($arrayProcessData);

                //Call plugins
                //$arrayData = array(
                //    "PRO_UID"      => $processUid,
                //    "PRO_TEMPLATE" => (isset($arrayProcessData["PRO_TEMPLATE"]) && $arrayProcessData["PRO_TEMPLATE"] != "")? $arrayProcessData["PRO_TEMPLATE"] : "",
                //    "PROCESSMAP"   => $this //?
                //);
                //
                //$oPluginRegistry = &PMPluginRegistry::getSingleton();
                //$oPluginRegistry->executeTriggers(PM_NEW_PROCESS_SAVE, $arrayData);
                break;
            case "UPDATE":
                $result = $process->update($arrayProcessData);

                $processUid = $arrayProcessData["PRO_UID"];
                break;
        }

        //Process - Save Calendar ID for this process
        if (isset($arrayProcessData["PRO_CALENDAR"]) && $arrayProcessData["PRO_CALENDAR"] != "") {
            $calendar = new Calendar();
            $calendar->assignCalendarTo($processUid, $arrayProcessData["PRO_CALENDAR"], "PROCESS");
        }

        $uidAux = $arrayDefineProcessData["process"]["PRO_UID"];
        $arrayDefineProcessData["process"]["PRO_UID"] = $processUid;
        $arrayDefineProcessData["process"]["PRO_UID_OLD"] = $uidAux;

        //Tasks
        if (isset($arrayDefineProcessData["process"]["tasks"]) && count($arrayDefineProcessData["process"]["tasks"]) > 0) {
            $arrayTaskData = $arrayDefineProcessData["process"]["tasks"];

            foreach ($arrayTaskData as $index => $value) {
                $t = $value;
                $t["PRO_UID"] = $processUid;

                $arrayData = $t;

                $action = $arrayData["_action"];

                unset($arrayData["_action"]);

                switch ($action) {
                    case "CREATE":
                        //Create task
                        $arrayDataAux = array(
                            //"TAS_UID"   => $arrayData["TAS_UID"],
                            "PRO_UID"   => $arrayData["PRO_UID"],
                            "TAS_TITLE" => $arrayData["TAS_TITLE"],
                            "TAS_DESCRIPTION" => $arrayData["TAS_DESCRIPTION"],
                            "TAS_POSX"  => $arrayData["TAS_POSX"],
                            "TAS_POSY"  => $arrayData["TAS_POSY"],
                            "TAS_START" => $arrayData["TAS_START"]
                        );

                        $task = new Task();

                        $taskUid = $task->create($arrayDataAux);

                        $uidAux = $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID"];
                        $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID"] = $taskUid;
                        $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID_OLD"] = $uidAux;

                        //Update task properties
                        $task2 = new Task();

                        $arrayResult = $task2->updateProperties($taskUid, $processUid, $arrayData);

                        //Update array routes
                        if (isset($arrayDefineProcessData["process"]["routes"]) && count($arrayDefineProcessData["process"]["routes"]) > 0) {
                            $arrayDefineProcessData["process"]["routes"] = $this->routeUpdateTaskUidInArray($arrayDefineProcessData["process"]["routes"], $taskUid, $t["TAS_UID"]);
                        }
                        break;
                    case "UPDATE":
                        //Update task
                        $task = new Task();

                        $result = $task->update($arrayData);

                        //Update task properties
                        $task2 = new Task();

                        $arrayResult = $task2->updateProperties($arrayData["TAS_UID"], $processUid, $arrayData);
                        break;
                    case "DELETE":
                        $tasks = new Tasks();

                        $tasks->deleteTask($arrayData["TAS_UID"]);
                        break;
                }
            }
        }

        //Routes
        if (isset($arrayDefineProcessData["process"]["routes"]) && count($arrayDefineProcessData["process"]["routes"]) > 0) {
            $arrayRouteData = $arrayDefineProcessData["process"]["routes"];

            foreach ($arrayRouteData as $index => $value) {
                $r = $value;

                $routeUid = $this->defineRoute( //***** New method
                    $processUid,
                    $r["TAS_UID"],
                    $r["ROU_NEXT_TASK"],
                    $r["ROU_TYPE"],
                    false
                );

                $uidAux = $arrayDefineProcessData["process"]["routes"][$index]["ROU_UID"];
                $arrayDefineProcessData["process"]["routes"][$index]["ROU_UID"] = $routeUid;
                $arrayDefineProcessData["process"]["routes"][$index]["ROU_UID_OLD"] = $uidAux;
            }
        }

        return $arrayDefineProcessData;
    }

    /**
     * Update UID in array
     *
     * @param array  $arrayData
     * @param string $taskUid
     * @param string $taskUidOld
     *
     * return array  Return data array with UID updated
     *
     * @access public
     */
    public function routeUpdateTaskUidInArray($arrayData, $taskUid, $taskUidOld)
    {
        foreach ($arrayData as $index => $value) {
            $r = $value;

            if ($r["TAS_UID"] == $taskUidOld) {
                $arrayData[$index]["TAS_UID"] = $taskUid;
            }

            if ($r["ROU_NEXT_TASK"] == $taskUidOld) {
                $arrayData[$index]["ROU_NEXT_TASK"] = $taskUid;
            }
        }

        return $arrayData;
    }

    /**
     * Create Process
     *
     * @param string $userUid
     * @param array  $arrayDefineProcessData
     *
     * return array  Return data array with new UID for each element
     *
     * @access public
     */
    public function createProcess($userUid, $arrayDefineProcessData)
    {
        $arrayDefineProcessData["process"]["USR_UID"] = $userUid;

        return $this->defineProcess("CREATE", $arrayDefineProcessData);
    }

    /**
     * Load all Process
     *
     * @param array $arrayFilterData
     * @param int   $start
     * @param int   $limit
     *
     * return array Return data array with the Process
     *
     * @access public
     */
    public function loadAllProcess($arrayFilterData = array(), $start = 0, $limit = 25)
    {
        //Copy of processmaker/workflow/engine/methods/processes/processesList.php

        $process = new Process();

        $memcache = &PMmemcached::getSingleton(SYS_SYS);

        $memkey = "no memcache";
        $memcacheUsed = "not used";
        $totalCount = 0;

        if (isset($arrayFilterData["category"]) && $arrayFilterData["category"] !== "<reset>") {
            if (isset($arrayFilterData["processName"])) {
                $proData = $process->getAllProcesses($start, $limit, $arrayFilterData["category"], $arrayFilterData["processName"]);
            } else {
                $proData = $process->getAllProcesses($start, $limit, $arrayFilterData["category"]);
            }
        } else {
            if (isset($arrayFilterData["processName"])) {
                $memkey = "processList-" . $start . "-" . $limit . "-" . $arrayFilterData["processName"];
                $memcacheUsed = "yes";

                if (($proData = $memcache->get($memkey)) === false) {
                    $proData = $process->getAllProcesses($start, $limit, null, $arrayFilterData["processName"]);
                    $memcache->set($memkey, $proData, PMmemcached::ONE_HOUR);
                    $memcacheUsed = "no";
                }
            } else {
                $memkey = "processList-allProcesses-" . $start . "-" . $limit;
                $memkeyTotal = $memkey . "-total";
                $memcacheUsed = "yes";

                if (($proData = $memcache->get($memkey)) === false || ($totalCount = $memcache->get($memkeyTotal)) === false) {
                    $proData = $process->getAllProcesses($start, $limit);
                    $totalCount = $process->getAllProcessesCount();
                    $memcache->set($memkey, $proData, PMmemcached::ONE_HOUR);
                    $memcache->set($memkeyTotal, $totalCount, PMmemcached::ONE_HOUR);
                    $memcacheUsed = "no";
                }
            }
        }

        $arrayData = array(
            "memkey"     => $memkey,
            "memcache"   => $memcacheUsed,
            "data"       => $proData,
            "totalCount" => $totalCount
        );

        return $arrayData;
    }

    /**
     * Load data of the Process
     *
     * @param string $processUid
     *
     * return array  Return data array with data of the Process (attributes of the process, tasks and routes)
     *
     * @access public
     */
    public function loadProcess($processUid)
    {
        $arrayDefineProcessData = array();

        //Process
        $process = new Process();

        $arrayProcessData = $process->load($processUid);

        $arrayDefineProcessData["process"] = array(
            "PRO_UID"   => $processUid,
            "PRO_TITLE" => $arrayProcessData["PRO_TITLE"],
            "PRO_DESCRIPTION" => $arrayProcessData["PRO_DESCRIPTION"],
            "PRO_CATEGORY"    => $arrayProcessData["PRO_CATEGORY"]
        );

        //Load data
        $processMap = new processMap();

        $arrayData = (array)(Bootstrap::json_decode($processMap->load($processUid)));

        //Tasks & Routes
        $arrayDefineProcessData["process"]["tasks"]  = array();
        $arrayDefineProcessData["process"]["routes"] = array();

        if (isset($arrayData["task"]) && count($arrayData["task"]) > 0) {
            foreach ($arrayData["task"] as $indext => $valuet) {
                $t = (array)($valuet);

                $taskUid = $t["uid"];

                //Load task data
                $task = new Task();

                $arrayTaskData = $task->load($taskUid);

                //Set task
                $arrayDefineProcessData["process"]["tasks"][] = array(
                    "TAS_UID"   => $taskUid,
                    "TAS_TITLE" => $arrayTaskData["TAS_TITLE"],
                    "TAS_DESCRIPTION" => $arrayTaskData["TAS_DESCRIPTION"],
                    "TAS_POSX"  => $arrayTaskData["TAS_POSX"],
                    "TAS_POSY"  => $arrayTaskData["TAS_POSY"],
                    "TAS_START" => $arrayTaskData["TAS_START"]
                );

                //Routes
                if (isset($t["derivation"])) {
                    $t["derivation"] = (array)($t["derivation"]);

                    $type = "";

                    switch ($t["derivation"]["type"]) {
                        case 0:
                            $type = "SEQUENTIAL";
                            break;
                        case 1:
                            $type = "SELECT";
                            break;
                        case 2:
                            $type = "EVALUATE";
                            break;
                        case 3:
                            $type = "PARALLEL";
                            break;
                        case 4:
                            $type = "PARALLEL-BY-EVALUATION";
                            break;
                        case 5:
                            $type = "SEC-JOIN";
                            break;
                        case 8:
                            $type = "DISCRIMINATOR";
                            break;
                    }

                    foreach ($t["derivation"]["to"] as $indexr => $valuer) {
                        $r = (array)($valuer);

                        //Criteria
                        $criteria = new Criteria("workflow");

                        $criteria->addSelectColumn(RoutePeer::ROU_UID);
                        $criteria->add(RoutePeer::PRO_UID, $processUid, Criteria::EQUAL);
                        $criteria->add(RoutePeer::TAS_UID, $taskUid, Criteria::EQUAL);
                        $criteria->add(RoutePeer::ROU_NEXT_TASK, $r["task"], Criteria::EQUAL);

                        $rsCriteria = RoutePeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                        $rsCriteria->next();

                        $row = $rsCriteria->getRow();

                        $routeUid = $row["ROU_UID"];

                        //Set route
                        $arrayDefineProcessData["process"]["routes"][] = array(
                            "ROU_UID" => $routeUid,
                            "TAS_UID" => $taskUid,
                            "ROU_NEXT_TASK" => $r["task"],
                            "ROU_TYPE" => $type
                        );
                    }
                }
            }
        }

        return $arrayDefineProcessData;
    }

    /**
     * Update Process
     *
     * @param string $processUid
     * @param string $userUid
     * @param array  $arrayDefineProcessData
     *
     * return array
     *
     * @access public
     */
    public function updateProcess($processUid, $userUid, $arrayDefineProcessData)
    {
        $arrayDefineProcessData["process"]["PRO_UID"] = $processUid;
        $arrayDefineProcessData["process"]["USR_UID"] = $userUid;

        return $this->defineProcess("UPDATE", $arrayDefineProcessData);
    }

    /**
     * Delete Process
     *
     * @param string $processUid
     * @param bool   $checkCases
     *
     * return bool   Return true, if is succesfully
     *
     * @access public
     */
    public function deleteProcess($processUid, $checkCases = true)
    {
        if ($checkCases) {
            $process = new Process();

            $arrayCases = $process->getCasesCountInAllProcesses($processUid);
            $sum = 0;

            foreach ($arrayCases[$processUid] as $value) {
                $sum = $sum + $value;
            }

            if ($sum > 0) {
                throw (new Exception("You can't delete the process, because it has $sum cases"));
            }
        }

        $processMap = new processMap();

        return $processMap->deleteProcess($processUid);
    }
}

