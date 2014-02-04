<?php
namespace ProcessMaker\Project;

use \Criteria;
use \ResultSet;

use \Process;
use \Tasks;
use \Task;
use \Route;
use \RoutePeer;


use ProcessMaker\Project\ProjectHandler;
use ProcessMaker\Util\Hash;

class WorkflowProject //extends ProjectHandler
{
    protected $process;
    protected $proUid;

    protected $properties = array();

    protected $tasks = array();
    protected $routes = array();


    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->setProperties($data);
            $this->create();
        }
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getProperties()
    {
        $process = new Process();
        return $process->load($this->proUid);
    }

    public function getUid()
    {
        return $this->proUid;
    }

    public function create()
    {
        try {
            // setting defaults
            $this->properties['PRO_UID'] = array_key_exists('PRO_UID', $this->properties)
                ? $this->properties['PRO_UID'] : Hash::generateUID();

            $this->properties['USR_UID'] = array_key_exists('PRO_CREATE_USER', $this->properties)
                ? $this->properties['PRO_CREATE_USER'] : null;

            $this->properties['PRO_CATEGORY'] = array_key_exists('PRO_CATEGORY', $this->properties)
                ? $this->properties['PRO_CATEGORY'] : '';


            // Create project
            $process = new Process();
            $this->proUid = $process->create($this->properties, false);

        } catch (Exception $e) {
            //throw new \RuntimeException($e);
            echo $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            die;
        }
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public static function load($prjUid)
    {
        $process = new Process();
        return $process->load($prjUid);
    }

    /*
     * Projects elements handlers
     */

    public function addTask($taskData)
    {
        // Setting defaults
        $taskData['TAS_UID'] = array_key_exists('TAS_UID', $taskData) ? $taskData['TAS_UID'] : Hash::generateUID();
        $taskData['PRO_UID'] = $this->proUid;

        $task = new Task();

        return $task->create($taskData, false);
    }

    public function updateTask($tasUid, $taskData)
    {
        $task = new Task();
        $taskData['TAS_UID'] = $tasUid;

        return $task->update($taskData);
    }

    public function removeTask($tasUid)
    {
        $task = new Task();
        $task->remove($tasUid);
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
            $oTasks = new Tasks();

            $oTasks->deleteAllRoutesOfTask($this->proUid, $fromTasUid);
            //$oTasks->deleteAllGatewayOfTask($this->proUid, $fromTasUid);
        }
        return $this->saveNewPattern($this->proUid, $fromTasUid, $toTasUid, $type, $delete);
    }

    public function updateRoute($rouUid, $routeData)
    {
        $route = new Route();
        $routeData['ROU_UID'] = $rouUid;
        $route->update($routeData);
    }

    public function removeRoute($rouUid)
    {
        $route = new Route();

        return $route->remove($rouUid);
    }

    public function getRoute($rouUid)
    {
        $route = new Route();

        return $route->load($rouUid);
    }

    /////////////////////////////////
    /*
     * Migrated from class.processMap.php class
     */
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



}