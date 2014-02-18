<?php
namespace Services\Api\ProcessMaker;

use Luracast\Restler\RestException;
use ProcessMaker\Services\Api;
use ProcessMaker\Adapter\Bpmn\Model as BpmnModel;
use ProcessMaker\Util\Hash;
use ProcessMaker\Util\Logger;

/**
 * Class Project
 *
 * @package Services\Api\ProcessMaker
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 *
 * @protected
 */
class Project extends Api
{
    public function index()
    {
        try {
            $start = null;
            $limit = null;
            $filter = "";

            $projects = \ProcessMaker\Project\Adapter\BpmnWorkflow::getList($start, $limit, $filter, CASE_LOWER);

            return $projects;

        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function get($prjUid)
    {
        try {
            //return \ProcessMaker\Adapter\Bpmn\Model::loadProject($prjUid);

            $bwp = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($prjUid);

            $project = array_change_key_case($bwp->getProject(), CASE_LOWER);
            $diagram = $bwp->getDiagram();
            $process = $bwp->getProcess();
            $diagram["pro_uid"] = $process["PRO_UID"];

            $configList = array("changeCaseTo" => CASE_LOWER);

            if (! is_null($diagram)) {
                $diagram = array_change_key_case($diagram, CASE_LOWER);
                $diagram["activities"] = $bwp->getActivities($configList);
                $diagram["events"] = $bwp->getEvents($configList);
                $diagram["gateways"] = $bwp->getGateways($configList);
                $diagram["flows"] = $bwp->getFlows($configList);
                $diagram["artifacts"] = $bwp->getArtifacts($configList);
                $diagram["laneset"] = $bwp->getLanesets($configList);
                $diagram["lanes"] = $bwp->getLanes($configList);

                $project["diagrams"][] = $diagram;
            }

            return $project;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @status 201
     */
    public function post($request_data)
    {
        try {
// NEED REFACTOR
//            $config = array();
//            $config['project'] = array('replace_uids' => true);
//
//            $bpmnModel = new BpmnModel();
//            $result = $bpmnModel->createProject($request_data, $config['project']['replace_uids']);
//
//            if (array_key_exists('prj_uid', $result)) {
//                $prjUid = $result['prj_uid'];
//            } else {
//                $prjUid = $result[0]['new_uid'];
//            }
//
//            $wfProcess = Workflow::loadFromBpmnProject($prjUid);
//
//            $process = new \BusinessModel\Process();
//            $userUid = $this->getUserId();
//            $data = array('process' => $wfProcess);
//
//            $process->createProcess($userUid, $data);

//            return $result;
        } catch (\Exception $e) {
            // TODO in case that $process->createProcess($userUid, $data); fails maybe the BPMN project was created successfully
            //      so, we need remove it or change the creation order.

            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function put($prjUid, $request_data)
    {
        try {
            $projectData = $request_data;
            $prjUid = $projectData["prj_uid"];
            $diagram = isset($request_data["diagrams"]) && isset($request_data["diagrams"][0]) ? $request_data["diagrams"][0] : array();

            $bwp = \ProcessMaker\Project\Adapter\BpmnWorkflow::load($prjUid);

            $result = array();

            /*
             * Diagram's Activities Handling
             */
            $whiteList = array();
            foreach ($diagram["activities"] as $i => $activityData) {
                $activityData = array_change_key_case($activityData, CASE_UPPER);
                unset($activityData["_EXTENDED"]);

                // activity exists ?
                if ($bwp->activityExists($activityData["ACT_UID"])) {
                    // then update activity
                    $bwp->updateActivity($activityData["ACT_UID"], $activityData);
                } else {
                    // if not exists then create it
                    $oldActUid = $activityData["ACT_UID"];
                    $activityData["ACT_UID"] = Hash::generateUID();

                    $bwp->addActivity($activityData);

                    $result[] = array("object" => "activity", "new_uid" => $activityData["ACT_UID"], "old_uid" => $oldActUid);
                }

                $diagram["activities"][$i] = $activityData;
                $whiteList[] = $activityData["ACT_UID"];
            }

            $activities = $bwp->getActivities();

            // looking for removed elements
            foreach ($activities as $activityData) {
                if (! in_array($activityData["ACT_UID"], $whiteList)) {
                    // If it is not in the white list so, then remove them
                    $bwp->removeActivity($activityData["ACT_UID"]);
                }
            }


            /*
             * Diagram's Gateways Handling
             */
            $whiteList = array();
            foreach ($diagram["gateways"] as $i => $gatewayData) {
                $gatewayData = array_change_key_case($gatewayData, CASE_UPPER);

                // gateway exists ?
                if ($gateway = $bwp->getGateway($gatewayData["GAT_UID"])) {
                    // then update activity
                    $bwp->updateGateway($gatewayData["GAT_UID"], $gatewayData);
                } else {
                    // if not exists then create it
                    $oldActUid = $gatewayData["GAT_UID"];
                    $gatewayData["GAT_UID"] = Hash::generateUID();

                    $bwp->addGateway($gatewayData);

                    $result[] = array("object" => "gateway", "new_uid" => $gatewayData["GAT_UID"], "old_uid" => $oldActUid);
                }

                $diagram["gateways"][$i] = $gatewayData;
                $whiteList[] = $gatewayData["GAT_UID"];
            }

            $gateways = $bwp->getGateways();

            // looking for removed elements
            foreach ($gateways as $gatewayData) {
                if (! in_array($gatewayData["GAT_UID"], $whiteList)) {
                    // If it is not in the white list so, then remove them
                    $bwp->removeGateway($gatewayData["GAT_UID"]);
                }
            }

            /*
             * Diagram's Events Handling
             */
            $whiteList = array();
            foreach ($diagram["events"] as $i => $eventData) {
                $eventData = array_change_key_case($eventData, CASE_UPPER);

                // gateway exists ?
                if ($event = $bwp->getEvent($eventData["EVN_UID"])) {
                    // then update activity
                    $bwp->updateEvent($eventData["EVN_UID"], $eventData);
                } else {
                    // if not exists then create it
                    $oldActUid = $eventData["EVN_UID"];
                    $eventData["EVN_UID"] = Hash::generateUID();

                    $bwp->addEvent($eventData);

                    $result[] = array("object" => "event", "new_uid" => $eventData["EVN_UID"], "old_uid" => $oldActUid);
                }

                $diagram["events"][$i] = $eventData;
                $whiteList[] = $eventData["EVN_UID"];
            }

            $events = $bwp->getEvents();

            // looking for removed elements
            foreach ($events as $eventData) {
                if (! in_array($eventData["EVN_UID"], $whiteList)) {
                    // If it is not in the white list so, then remove them
                    $bwp->removeEvent($eventData["EVN_UID"]);
                }
            }


            /*
             * Diagram's Flows Handling
             */
            $whiteList = array();

            foreach ($diagram["flows"] as $i => $flowData) {
                $flowData = array_change_key_case($flowData, CASE_UPPER);

                // if it is a new flow record
                if (! \BpmnFlow::exists($flowData["FLO_UID"])) {
                    $oldFloUid = $flowData["FLO_UID"];
                    $flowData["FLO_UID"] = Hash::generateUID();

                    $mappedUid = self::mapUid($flowData["FLO_ELEMENT_ORIGIN"], $result);
                    if ($mappedUid !== false) {
                        $flowData["FLO_ELEMENT_ORIGIN"] = $mappedUid;
                    }

                    $mappedUid = self::mapUid($flowData["FLO_ELEMENT_DEST"], $result);
                    if ($mappedUid !== false) {
                        $flowData["FLO_ELEMENT_DEST"] = $mappedUid;
                    }

                    $result[] = array("object" => "flow", "new_uid" => $flowData["FLO_UID"], "old_uid" => $oldFloUid);
                }

                $diagram["flows"][$i] = $flowData;
                $whiteList[] = $flowData["FLO_UID"];
            }

            foreach ($diagram["flows"] as $flowData) {
                // flow exists ?
                if ($bwp->flowExists($flowData["FLO_UID"])) {
                    // then update activity
                    $bwp->updateFlow($flowData["FLO_UID"], $flowData);
                } else {
                    // if not exists then create it
                    $bwp->addFlow($flowData, $diagram["flows"], $diagram["gateways"], $diagram["events"]);
                }
            }

            $flows = $bwp->getFlows();

            // looking for removed elements
            foreach ($flows as $flowData) {
                if (! in_array($flowData["FLO_UID"], $whiteList)) {
                    // If it is not in the white list so, then remove them
                    $bwp->removeFlow($flowData["FLO_UID"]);
                }
            }


            return $result;
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    public function delete($prjUid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->deleteProcess($prjUid);

            BpmnModel::deleteProject($prjUid);
        } catch (\Exception $e) {
            throw new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage());
        }
    }

    /**
     * @url GET /:prj_uid/process
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetProcess($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getProcess($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url PUT /:prj_uid/process
     *
     * @param string $prj_uid      {@min 32}{@max 32}
     * @param array  $request_data
     */
    public function doPutProcess($prj_uid, $request_data)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $arrayData = $process->update($prj_uid, $request_data);
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/dynaforms
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetDynaForms($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getDynaForms($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/input-documents
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetInputDocuments($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getInputDocuments($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/web-entries
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetWebEntries($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getWebEntries($prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    protected static function mapUid($oldUid, $list)
    {
        foreach ($list as $item) {
            if ($item["old_uid"] == $oldUid) {
                return $item["new_uid"];
            }
        }

        return false;
    }

    /**
     * @url GET /:prj_uid/variables
     *
     * @param string $prj_uid {@min 32}{@max 32}
     */
    public function doGetVariables($prj_uid)
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = $process->getVariables("ALL", $prj_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }

    /**
     * @url GET /:prj_uid/grid/variables
     * @url GET /:prj_uid/grid/:grid_uid/variables
     *
     * @param string $prj_uid  {@min 32}{@max 32}
     */
    public function doGetGridVariables($prj_uid, $grid_uid = "")
    {
        try {
            $process = new \BusinessModel\Process();
            $process->setFormatFieldNameInUppercase(false);
            $process->setArrayFieldNameForException(array("processUid" => "prj_uid"));

            $response = ($grid_uid == "")? $process->getVariables("GRID", $prj_uid) : $process->getVariables("GRIDVARS", $prj_uid, $grid_uid);

            return $response;
        } catch (\Exception $e) {
            throw (new RestException(Api::STAT_APP_EXCEPTION, $e->getMessage()));
        }
    }
}

