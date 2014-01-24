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

        $processMap = new \processMap();

        if ($type != "SEQUENTIAL" && $type != "SEC-JOIN" && $type != "DISCRIMINATOR") {
            if ($processMap->getNumberOfRoutes($processUid, $taskUid, $nextTaskUid, $type) > 0) {
                //die();
                throw (new \Exception());
            }

            //unset($aRow);
        }

        if ($delete || $type == "SEQUENTIAL" || $type == "SEC-JOIN" || $type == "DISCRIMINATOR") {
            //\G::LoadClass("tasks");

            $tasks = new \Tasks();

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
            throw (new \Exception("Process data do not exist"));
        }

        //Process
        $process = new \Process();

        $arrayProcessData = $arrayDefineProcessData["process"];

        unset($arrayProcessData["tasks"]);
        unset($arrayProcessData["routes"]);

        switch ($option) {
            case "CREATE":
                if (!isset($arrayProcessData["USR_UID"]) || trim($arrayProcessData["USR_UID"]) == "") {
                    throw (new \Exception("User data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_TITLE"]) || trim($arrayProcessData["PRO_TITLE"]) == "") {
                    throw (new \Exception("Process title data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_DESCRIPTION"])) {
                    throw (new \Exception("Process description data do not exist"));
                }

                if (!isset($arrayProcessData["PRO_CATEGORY"])) {
                    throw (new \Exception("Process category data do not exist"));
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
            throw (new \Exception(\G::LoadTranslation("ID_PROCESSTITLE_ALREADY_EXISTS", SYS_LANG, array("PRO_TITLE" => $arrayProcessData["PRO_TITLE"]))));
        }

        $arrayProcessData["PRO_DYNAFORMS"] = array ();
        $arrayProcessData["PRO_DYNAFORMS"]["PROCESS"] = (isset($arrayProcessData["PRO_SUMMARY_DYNAFORM"]))? $arrayProcessData["PRO_SUMMARY_DYNAFORM"] : "";

        unset($arrayProcessData["PRO_SUMMARY_DYNAFORM"]);

        switch ($option) {
            case "CREATE":
                $processUid = $process->create($arrayProcessData, false);

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
            $calendar = new \Calendar();
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
                            "TAS_UID"   => $arrayData["TAS_UID"],
                            "PRO_UID"   => $arrayData["PRO_UID"],
                            "TAS_TITLE" => $arrayData["TAS_TITLE"],
                            "TAS_DESCRIPTION" => $arrayData["TAS_DESCRIPTION"],
                            "TAS_POSX"  => $arrayData["TAS_POSX"],
                            "TAS_POSY"  => $arrayData["TAS_POSY"],
                            "TAS_START" => $arrayData["TAS_START"]
                        );

                        $task = new \Task();

                        $taskUid = $task->create($arrayDataAux, false);

                        $uidAux = $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID"];
                        $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID"] = $taskUid;
                        $arrayDefineProcessData["process"]["tasks"][$index]["TAS_UID_OLD"] = $uidAux;

                        //Update task properties
                        $task2 = new \BusinessModel\Task();

                        $arrayResult = $task2->updateProperties($taskUid, $processUid, $arrayData);

                        //Update array routes
                        if (isset($arrayDefineProcessData["process"]["routes"]) && count($arrayDefineProcessData["process"]["routes"]) > 0) {
                            $arrayDefineProcessData["process"]["routes"] = $this->routeUpdateTaskUidInArray($arrayDefineProcessData["process"]["routes"], $taskUid, $t["TAS_UID"]);
                        }
                        break;
                    case "UPDATE":
                        //Update task
                        $task = new \Task();

                        $result = $task->update($arrayData);

                        //Update task properties
                        $task2 = new \BusinessModel\Task();

                        $arrayResult = $task2->updateProperties($arrayData["TAS_UID"], $processUid, $arrayData);
                        break;
                    case "DELETE":
                        $tasks = new \Tasks();

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

        $process = new \Process();

        $memcache = &\PMmemcached::getSingleton(SYS_SYS);

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
                    $memcache->set($memkey, $proData, \PMmemcached::ONE_HOUR);
                    $memcacheUsed = "no";
                }
            } else {
                $memkey = "processList-allProcesses-" . $start . "-" . $limit;
                $memkeyTotal = $memkey . "-total";
                $memcacheUsed = "yes";

                if (($proData = $memcache->get($memkey)) === false || ($totalCount = $memcache->get($memkeyTotal)) === false) {
                    $proData = $process->getAllProcesses($start, $limit);
                    $totalCount = $process->getAllProcessesCount();
                    $memcache->set($memkey, $proData, \PMmemcached::ONE_HOUR);
                    $memcache->set($memkeyTotal, $totalCount, \PMmemcached::ONE_HOUR);
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
        $process = new \Process();

        $arrayProcessData = $process->load($processUid);

        $arrayDefineProcessData["process"] = array(
            "PRO_UID"   => $processUid,
            "PRO_TITLE" => $arrayProcessData["PRO_TITLE"],
            "PRO_DESCRIPTION" => $arrayProcessData["PRO_DESCRIPTION"],
            "PRO_CATEGORY"    => $arrayProcessData["PRO_CATEGORY"]
        );

        //Load data
        $processMap = new \processMap();

        $arrayData = (array)(\Bootstrap::json_decode($processMap->load($processUid)));

        //Tasks & Routes
        $arrayDefineProcessData["process"]["tasks"]  = array();
        $arrayDefineProcessData["process"]["routes"] = array();

        if (isset($arrayData["task"]) && count($arrayData["task"]) > 0) {
            foreach ($arrayData["task"] as $indext => $valuet) {
                $t = (array)($valuet);

                $taskUid = $t["uid"];

                //Load task data
                $task = new \Task();

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
                        $criteria = new \Criteria("workflow");

                        $criteria->addSelectColumn(\RoutePeer::ROU_UID);
                        $criteria->add(\RoutePeer::PRO_UID, $processUid, \Criteria::EQUAL);
                        $criteria->add(\RoutePeer::TAS_UID, $taskUid, \Criteria::EQUAL);
                        $criteria->add(\RoutePeer::ROU_NEXT_TASK, $r["task"], \Criteria::EQUAL);

                        $rsCriteria = \RoutePeer::doSelectRS($criteria);
                        $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

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

    DEPRECATED
    public function deleteProcess($processUid, $checkCases = true)
    {
        if ($checkCases) {
            $process = new \Process();

            $arrayCases = $process->getCasesCountInAllProcesses($processUid);

            $sum = 0;

            if (isset($arrayCases[$processUid]) && count($arrayCases[$processUid]) > 0) {
                foreach ($arrayCases[$processUid] as $value) {
                    $sum = $sum + $value;
                }
            }

            if ($sum > 0) {
                throw (new \Exception("You can't delete the process, because it has $sum cases"));
            }
        }

        $processMap = new \processMap();

        return $processMap->deleteProcess($processUid);

    }*/

    public function deleteProcess($sProcessUID)
    {
        try {
            G::LoadClass('case');
            G::LoadClass('reportTables');
            //Instance all classes necesaries
            $oProcess = new Process();
            $oDynaform = new Dynaform();
            $oInputDocument = new InputDocument();
            $oOutputDocument = new OutputDocument();
            $oTrigger = new Triggers();
            $oRoute = new Route();
            $oGateway = new Gateway();
            $oEvent = new Event();
            $oSwimlaneElement = new SwimlanesElements();
            $oConfiguration = new Configuration();
            $oDbSource = new DbSource();
            $oReportTable = new ReportTables();
            $oCaseTracker = new CaseTracker();
            $oCaseTrackerObject = new CaseTrackerObject();
            //Delete the applications of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ApplicationPeer::PRO_UID, $sProcessUID);
            $oDataset = ApplicationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $oCase = new Cases();
            while ($aRow = $oDataset->getRow()) {
                $oCase->removeCase($aRow['APP_UID']);
                $oDataset->next();
            }
            //Delete the tasks of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(TaskPeer::PRO_UID, $sProcessUID);
            $oDataset = TaskPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $this->deleteTask($aRow['TAS_UID']);
                $oDataset->next();
            }
            //Delete the dynaforms of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(DynaformPeer::PRO_UID, $sProcessUID);
            $oDataset = DynaformPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oDynaform->remove($aRow['DYN_UID']);
                $oDataset->next();
            }
            //Delete the input documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(InputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = InputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oInputDocument->remove($aRow['INP_DOC_UID']);
                $oDataset->next();
            }
            //Delete the output documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(OutputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = OutputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oOutputDocument->remove($aRow['OUT_DOC_UID']);
                $oDataset->next();
            }

            //Delete the triggers of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(TriggersPeer::PRO_UID, $sProcessUID);
            $oDataset = TriggersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oTrigger->remove($aRow['TRI_UID']);
                $oDataset->next();
            }

            //Delete the routes of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
            $oDataset = RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oRoute->remove($aRow['ROU_UID']);
                $oDataset->next();
            }

            //Delete the gateways of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(GatewayPeer::PRO_UID, $sProcessUID);
            $oDataset = GatewayPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oGateway->remove($aRow['GAT_UID']);
                $oDataset->next();
            }

            //Delete the Event of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(EventPeer::PRO_UID, $sProcessUID);
            $oDataset = EventPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oEvent->remove($aRow['EVN_UID']);
                $oDataset->next();
            }

            //Delete the swimlanes elements of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(SwimlanesElementsPeer::PRO_UID, $sProcessUID);
            $oDataset = SwimlanesElementsPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oSwimlaneElement->remove($aRow['SWI_UID']);
                $oDataset->next();
            }
            //Delete the configurations of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ConfigurationPeer::PRO_UID, $sProcessUID);
            $oDataset = ConfigurationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oConfiguration->remove($aRow['CFG_UID'], $aRow['OBJ_UID'], $aRow['PRO_UID'], $aRow['USR_UID'], $aRow['APP_UID']);
                $oDataset->next();
            }
            //Delete the DB sources of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(DbSourcePeer::PRO_UID, $sProcessUID);
            $oDataset = DbSourcePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {

                /**
                 * note added by gustavo cruz gustavo-at-colosa-dot-com 27-01-2010
                 * in order to solve the bug 0004389, we use the validation function Exists
                 * inside the remove function in order to verify if the DbSource record
                 * exists in the Database, however there is a strange behavior within the
                 * propel engine, when the first record is erased somehow the "_deleted"
                 * attribute of the next row is set to true, so when propel tries to erase
                 * it, obviously it can't and trows an error. With the "Exist" function
                 * we ensure that if there is the record in the database, the _delete attribute must be false.
                 *
                 * note added by gustavo cruz gustavo-at-colosa-dot-com 28-01-2010
                 * I have just identified the source of the issue, when is created a $oDbSource DbSource object
                 * it's used whenever a record is erased or removed in the db, however the problem
                 * it's that the same object is used every time, and the delete method invoked
                 * sets the _deleted attribute to true when its called, of course as we use
                 * the same object, the first time works fine but trowns an error with the
                 * next record, cos it's the same object and the delete method checks if the _deleted
                 * attribute it's true or false, the attrib _deleted is setted to true the
                 * first time and later is never changed, the issue seems to be part of
                 * every remove function in the model classes, not only DbSource
                 * i recommend that a more general solution must be achieved to resolve
                 * this issue in every model class, to prevent future problems.
                 */
                $oDbSource->remove($aRow['DBS_UID'], $sProcessUID);
                $oDataset->next();
            }
            //Delete the supervisors
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ProcessUserPeer::PRO_UID, $sProcessUID);
            ProcessUserPeer::doDelete($oCriteria);
            //Delete the object permissions
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ObjectPermissionPeer::PRO_UID, $sProcessUID);
            ObjectPermissionPeer::doDelete($oCriteria);
            //Delete the step supervisors
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(StepSupervisorPeer::PRO_UID, $sProcessUID);
            StepSupervisorPeer::doDelete($oCriteria);
            //Delete the report tables
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(ReportTablePeer::PRO_UID, $sProcessUID);
            $oDataset = ReportTablePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oReportTable->deleteReportTable($aRow['REP_TAB_UID']);
                $oDataset->next();
            }
            //Delete case tracker configuration
            $oCaseTracker->remove($sProcessUID);
            //Delete case tracker objects
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(CaseTrackerObjectPeer::PRO_UID, $sProcessUID);
            ProcessUserPeer::doDelete($oCriteria);
            //Delete the process
            try {
                $oProcess->remove($sProcessUID);
            } catch (Exception $oError) {
                throw ($oError);
            }
            return true;
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    /**
     * Get all DynaForms of a Process
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all DynaForms of a Process
     */
    public function getDynaForms($processUid)
    {
        try {
            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $arrayDynaForm = array();

            $dynaForm = new \BusinessModel\DynaForm();

            $criteria = $dynaForm->getDynaFormCriteria();

            $criteria->add(\DynaformPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("DYN_TITLE");

            $rsCriteria = \DynaformPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayDynaForm[] = $dynaForm->getDynaFormDataFromRecord($row);
            }

            return $arrayDynaForm;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all InputDocuments of a Process
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all InputDocuments of a Process
     */
    public function getInputDocuments($processUid)
    {
        try {
            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $arrayInputDocument = array();

            $inputdoc = new \BusinessModel\InputDocument();

            $criteria = $inputdoc->getInputDocumentCriteria();

            $criteria->add(\InputDocumentPeer::PRO_UID, $processUid, \Criteria::EQUAL);
            $criteria->addAscendingOrderByColumn("INP_DOC_TITLE");

            $rsCriteria = \InputDocumentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();

                $arrayInputDocument[] = $inputdoc->getInputDocumentDataFromRecord($row);
            }

            return $arrayInputDocument;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all Web Entries of a Process
     *
     * @param string $processUid Unique id of Process
     *
     * return array Return an array with all Web Entries of a Process
     */
    public function getWebEntries($processUid)
    {
        try {
            $arrayWebEntry = array();

            //Verify data
            $process = new \Process();

            if (!$process->exists($processUid)) {
                throw (new \Exception(str_replace(array("{0}", "{1}"), array($processUid, "PROCESS"), "The UID \"{0}\" doesn't exist in table {1}")));
            }

            //Get data
            $webEntry = new \BusinessModel\WebEntry();

            $arrayWebEntryData = $webEntry->getData($processUid);

            foreach ($arrayWebEntryData as $index => $value) {
                $row = $value;

                $arrayWebEntry[] = $webEntry->getWebEntryDataFromRecord($row);
            }

            //Return
            return $arrayWebEntry;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

