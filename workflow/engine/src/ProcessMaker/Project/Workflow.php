<?php
namespace ProcessMaker\Project;

use \Criteria;
use \ResultSet;

use \Process;
use \Tasks;
use \Task;
use \Route;
use \RoutePeer;

use ProcessMaker\Util\Hash;
use ProcessMaker\Exception;

/**
 * Class Workflow
 * 
 * @package ProcessMaker\Project
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Workflow extends Handler
{
    protected $process;
    protected $proUid;

    protected $tasks = array();
    protected $routes = array();


    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->create($data);
        }
    }

    public static function load($proUid)
    {
        $me = new self();

        try {
            $process = new Process();
            $processData = $process->load($proUid);
        } catch (\Exception $e) {
            throw new Exception\ProjectNotFound($me, $proUid);
        }

        $me->process = $processData;
        $me->proUid = $processData["PRO_UID"];

        return $me;
    }

    public function create($data)
    {
        try {
            self::log("===> Executing -> ".__METHOD__, "Create Process with data:", $data);

            // setting defaults
            $data['PRO_UID'] = array_key_exists('PRO_UID', $data) ? $data['PRO_UID'] : Hash::generateUID();
            $data['USR_UID'] = array_key_exists('PRO_CREATE_USER', $data) ? $data['PRO_CREATE_USER'] : null;
            $data['PRO_TITLE'] = array_key_exists('PRO_TITLE', $data) ? trim($data['PRO_TITLE']) : "";
            $data['PRO_CATEGORY'] = array_key_exists('PRO_CATEGORY', $data) ? $data['PRO_CATEGORY'] : "";

            //validate if process with specified name already exists
            if (Process::existsByProTitle($data["PRO_TITLE"])) {
                throw new Exception\ProjectAlreadyExists($this, $data["PRO_TITLE"]);
            }

            // Create project
            $process = new Process();
            $this->proUid = $process->create($data, false);

            // Call Plugins
            $pluginData['PRO_UID'] = $this->proUid;
            $pluginData['PRO_TEMPLATE'] = empty($data["PRO_TEMPLATE"]) ? "" : $data["PRO_TEMPLATE"];
            $pluginData['PROCESSMAP'] = null;

            $pluginRegistry = \PMPluginRegistry::getSingleton();
            $pluginRegistry->executeTriggers(PM_NEW_PROCESS_SAVE, $pluginData);

            // Save Calendar ID for this process
            if (! empty($data["PRO_CALENDAR"])) {
                //G::LoadClass( "calendar" );
                $calendar = new \Calendar();
                $calendar->assignCalendarTo($this->proUid, $data["PRO_CALENDAR"], 'PROCESS');
            }

            self::log("Create Process Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function remove()
    {
        try {
            self::log("===> Executing -> ".__METHOD__, "Remove Process with uid: {$this->proUid}");
            $this->deleteProcess($this->proUid);
            self::log("Remove Process Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        //return Project::getAll($start, $limit, $filter, $changeCaseTo);
        $process = new Process();
        $processes = $process->getAllProcesses( $start, $limit, "", "");
        //$processes = $process->getAll();

        return $processes;
    }

    public function getUid()
    {
        if (empty($this->proUid)) {
            throw new \RuntimeException("Error: There is not an initialized project.");
        }

        return $this->proUid;
    }

    public function getProcess()
    {
        if (empty($this->proUid)) {
            throw new \Exception("Error: There is not an initialized project.");
        }

        $process = new Process();

        return $process->load($this->proUid);
    }

    /*
     * Projects elements handlers
     */

    public function addTask($taskData)
    {
        // Setting defaults
        $taskData['TAS_UID'] = array_key_exists('TAS_UID', $taskData) ? $taskData['TAS_UID'] : Hash::generateUID();
        $taskData['PRO_UID'] = $this->proUid;

        try {
            self::log("===> Executing -> ".__METHOD__, "Add Task with data: ", $taskData);
            $task = new Task();
            $tasUid = $task->create($taskData, false);
            self::log("Add Task Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $tasUid;
    }

    public function updateTask($tasUid, $taskData)
    {
        try {
            self::log("===> Executing -> ".__METHOD__, "Update Task: $tasUid", "With data: ", $taskData);
            $task = new Task();
            $taskData['TAS_UID'] = $tasUid;
            $result = $task->update($taskData);
            self::log("Update Task Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }

        return $result;
    }

    public function removeTask($tasUid)
    {
        try {
            self::log("===> Executing -> ".__METHOD__, "Remove Task: $tasUid");
            $task = new Task();
            $task->remove($tasUid);
            self::log("Remove Task Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getTask($tasUid)
    {
        $task = new Task();
        return $task->load($tasUid);
    }


    public function getTasks()
    {
        if (empty($this->proUid)) {
            return null;
        }

        $tasks = new Tasks();

        return $tasks->getAllTasks($this->proUid);
    }

    public function setStartTask($tasUid)
    {
        $task = \TaskPeer::retrieveByPK($tasUid);
        $task->setTasStart("TRUE");
        $task->save();
    }

    public function setEndTask($tasUid)
    {
        $this->addSequentialRoute($tasUid, "-1", "SEQUENTIAL", true);
    }

    public function addSequentialRoute($fromTasUid, $toTasUid, $delete = null)
    {
        $this->addRoute($fromTasUid, $toTasUid, "SEQUENTIAL", $delete);
    }

    public function addSelectRoute($fromTasUid, array $toTasks, $delete = null)
    {
        foreach ($toTasks as $toTasUid) {
            $this->addRoute($fromTasUid, $toTasUid, "SELECT", $delete);
        }
    }

    public function addRoute($fromTasUid, $toTasUid, $type, $delete = null)
    {
        try {
            self::log("Add Route from task: $fromTasUid -> to task: $toTasUid ($type)");
            /*switch ($type) {
                    case 0:
                        $sType = 'SEQUENTIAL';
                        break;
                    case 1:
                        $sType = 'SELECT';
                        break;
                    case 2:
                        $sType = 'EVALUATE';
                        break;
                    case 3:
                        $sType = 'PARALLEL';
                        break;
                    case 4:
                        $sType = 'PARALLEL-BY-EVALUATION';
                        break;
                    case 5:
                        $sType = 'SEC-JOIN';
                        break;
                    case 8:
                        $sType = 'DISCRIMINATOR';
                        break;
                    default:
                        throw new \Exception("Invalid type code, given: $type, expected: integer [1...8]");
                }
            }*/

            $validTypes = array("SEQUENTIAL", "SELECT", "EVALUATE", "PARALLEL", "PARALLEL-BY-EVALUATION", "SEC-JOIN", "DISCRIMINATOR");

            if (! in_array($type, $validTypes)) {
                throw new \Exception("Invalid Route type, given: $type, expected: [".implode(",", $validTypes)."]");
            }

            //if ($type != 0 && $type != 5 && $type != 8) {
            if ($type != 'SEQUENTIAL' && $type != 'SEC-JOIN' && $type != 'DISCRIMINATOR') {
                if ($this->getNumberOfRoutes($this->proUid, $fromTasUid, $toTasUid, $type) > 0) {
                    // die(); ????
                    throw new \RuntimeException("Unexpected behaviour");
                }
                //unset($aRow);
            }
            //if ($delete || $type == 0 || $type == 5 || $type == 8) {
            if ($delete || $type == 'SEQUENTIAL' || $type == 'SEC-JOIN' || $type == 'DISCRIMINATOR') {
                //$oTasks = new Tasks();

                //$oTasks->deleteAllRoutesOfTask($this->proUid, $fromTasUid);
                //$oTasks->deleteAllGatewayOfTask($this->proUid, $fromTasUid);
            }

            $result = $this->saveNewPattern($this->proUid, $fromTasUid, $toTasUid, $type, $delete);
            self::log("Add Route Success! -> ", $result);

            return $result;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateRoute($rouUid, $routeData)
    {
        $routeData['ROU_UID'] = $rouUid;

        try {
            self::log("===> Executing -> ".__METHOD__, "Update Route: $rouUid with data:", $routeData);
            $route = new Route();
            $route->update($routeData);
            self::log("Update Route Success!");
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function removeRoute($rouUid)
    {
        try {
            self::log("===> Executing -> ".__METHOD__, "Remove Route: $rouUid");
            $route = new Route();
            $result = $route->remove($rouUid);
            self::log("Remove Route Success!");

            return $result;
        } catch (\Exception $e) {
            self::log("Exception: ", $e->getMessage(), "Trace: ", $e->getTraceAsString());
            throw $e;
        }
    }

    public function getRoute($rouUid)
    {
        $route = new Route();

        return $route->load($rouUid);
    }


    /****************************************************************************************************
     * Migrated Methods from class.processMap.php class                                                 *
     ****************************************************************************************************/

    private function getNumberOfRoutes($sProcessUID = '', $sTaskUID = '', $sNextTask = '', $sType = '')
    {
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn('COUNT(*) AS ROUTE_NUMBER');
            $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
            $oCriteria->add(RoutePeer::TAS_UID, $sTaskUID);
            $oCriteria->add(RoutePeer::ROU_NEXT_TASK, $sNextTask);
            $oCriteria->add(RoutePeer::ROU_TYPE, $sType);
            $oDataset = RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            return (int) $aRow['ROUTE_NUMBER'];
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    private function saveNewPattern($sProcessUID = '', $sTaskUID = '', $sNextTask = '', $sType = '', $sDelete = '')
    {
        try {
            $oCriteria = new Criteria('workflow');
            $oCriteria->addSelectColumn('COUNT(*) AS ROUTE_NUMBER');
            //$oCriteria->addSelectColumn('GAT_UID AS GATEWAY_UID');
            $oCriteria->add(RoutePeer::PRO_UID, $sProcessUID);
            $oCriteria->add(RoutePeer::TAS_UID, $sTaskUID);
            $oCriteria->add(RoutePeer::ROU_TYPE, $sType);

            $oDataset = RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $aRow = $oDataset->getRow();

            $aFields['PRO_UID'] = $sProcessUID;
            $aFields['TAS_UID'] = $sTaskUID;
            $aFields['ROU_NEXT_TASK'] = $sNextTask;
            $aFields['ROU_TYPE'] = $sType;
            $aFields['ROU_CASE'] = (int) $aRow['ROUTE_NUMBER'] + 1;

            //$sGatewayUID = $aRow['GATEWAY_UID'];

            //if ($sDelete && $sGatewayUID != '') {
            //    $oGateway = new Gateway();
            //   $oGateway->remove($sGatewayUID);
            //}
            //Getting Gateway UID after saving gateway
            //if($sType != 'SEQUENTIAL' && $sGatewayUID == '' && $sDelete == '1')

            /*??? Maybe this is deprecated
            if ($sType != 'SEQUENTIAL') {
                $oProcessMap = new processMap();
                $sGatewayUID = $this->saveNewGateway($sProcessUID, $sTaskUID, $sNextTask);
            }*/

            //$aFields['GAT_UID'] = (isset($sGatewayUID)) ? $sGatewayUID : '';

            $oRoute = new Route();

            return $oRoute->create($aFields);
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

    public function deleteProcess($sProcessUID)
    {
        try {
            //G::LoadClass('case');
            //G::LoadClass('reportTables');

            //Instance all classes necesaries
            $oProcess = new \Process();
            $oDynaform = new \Dynaform();
            $oInputDocument = new \InputDocument();
            $oOutputDocument = new \OutputDocument();
            $oTrigger = new \Triggers();
            $oRoute = new \Route();
            $oGateway = new \Gateway();
            $oEvent = new \Event();
            $oSwimlaneElement = new \SwimlanesElements();
            $oConfiguration = new \Configuration();
            $oDbSource = new \DbSource();
            $oReportTable = new \ReportTables();
            $oCaseTracker = new \CaseTracker();
            $oCaseTrackerObject = new \CaseTrackerObject();
            //Delete the applications of process
            $oCriteria = new \Criteria('workflow');
            $oCriteria->add(\ApplicationPeer::PRO_UID, $sProcessUID);
            $oDataset = \ApplicationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(\ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            $oCase = new \Cases();

            while ($aRow = $oDataset->getRow()) {
                $oCase->removeCase($aRow['APP_UID']);
                $oDataset->next();
            }

            //Delete the tasks of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\TaskPeer::PRO_UID, $sProcessUID);
            $oDataset = \TaskPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                //$this->deleteTask($aRow['TAS_UID']);
                $oTasks = new \Tasks();
                $oTasks->deleteTask($aRow['TAS_UID']);

                $oDataset->next();
            }
            //Delete the dynaforms of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\DynaformPeer::PRO_UID, $sProcessUID);
            $oDataset = \DynaformPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oDynaform->remove($aRow['DYN_UID']);
                $oDataset->next();
            }
            //Delete the input documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\InputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = \InputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oInputDocument->remove($aRow['INP_DOC_UID']);
                $oDataset->next();
            }
            //Delete the output documents of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\OutputDocumentPeer::PRO_UID, $sProcessUID);
            $oDataset = \OutputDocumentPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oOutputDocument->remove($aRow['OUT_DOC_UID']);
                $oDataset->next();
            }

            //Delete the triggers of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\TriggersPeer::PRO_UID, $sProcessUID);
            $oDataset = \TriggersPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oTrigger->remove($aRow['TRI_UID']);
                $oDataset->next();
            }

            //Delete the routes of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\RoutePeer::PRO_UID, $sProcessUID);
            $oDataset = \RoutePeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oRoute->remove($aRow['ROU_UID']);
                $oDataset->next();
            }

            //Delete the gateways of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\GatewayPeer::PRO_UID, $sProcessUID);
            $oDataset = \GatewayPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oGateway->remove($aRow['GAT_UID']);
                $oDataset->next();
            }

            //Delete the Event of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\EventPeer::PRO_UID, $sProcessUID);
            $oDataset = \EventPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oEvent->remove($aRow['EVN_UID']);
                $oDataset->next();
            }

            //Delete the swimlanes elements of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\SwimlanesElementsPeer::PRO_UID, $sProcessUID);
            $oDataset = \SwimlanesElementsPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oSwimlaneElement->remove($aRow['SWI_UID']);
                $oDataset->next();
            }
            //Delete the configurations of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ConfigurationPeer::PRO_UID, $sProcessUID);
            $oDataset = \ConfigurationPeer::doSelectRS($oCriteria);
            $oDataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $oDataset->next();
            while ($aRow = $oDataset->getRow()) {
                $oConfiguration->remove($aRow['CFG_UID'], $aRow['OBJ_UID'], $aRow['PRO_UID'], $aRow['USR_UID'], $aRow['APP_UID']);
                $oDataset->next();
            }
            //Delete the DB sources of process
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\DbSourcePeer::PRO_UID, $sProcessUID);
            $oDataset = \DbSourcePeer::doSelectRS($oCriteria);
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
            $oCriteria->add(\ProcessUserPeer::PRO_UID, $sProcessUID);
            \ProcessUserPeer::doDelete($oCriteria);
            //Delete the object permissions
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ObjectPermissionPeer::PRO_UID, $sProcessUID);
            \ObjectPermissionPeer::doDelete($oCriteria);
            //Delete the step supervisors
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\StepSupervisorPeer::PRO_UID, $sProcessUID);
            \StepSupervisorPeer::doDelete($oCriteria);
            //Delete the report tables
            $oCriteria = new Criteria('workflow');
            $oCriteria->add(\ReportTablePeer::PRO_UID, $sProcessUID);
            $oDataset = \ReportTablePeer::doSelectRS($oCriteria);
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
            $oCriteria->add(\CaseTrackerObjectPeer::PRO_UID, $sProcessUID);
            \ProcessUserPeer::doDelete($oCriteria);
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

}