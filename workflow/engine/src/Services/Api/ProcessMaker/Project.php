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

            if (! is_null($diagram)) {
                $diagram = array_change_key_case($diagram, CASE_LOWER);
                $diagram["activities"] = $bwp->getActivities(array("changeCaseTo" => CASE_LOWER));
                $diagram["events"] = $bwp->getEvents();
                $diagram["gateways"] = $bwp->getGateways();
                $diagram["flows"] = $bwp->getFlows();
                $diagram["artifacts"] = $bwp->getArtifacts();
                $diagram["laneset"] = $bwp->getLanesets();
                $diagram["lanes"] = $bwp->getLanes();

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
                $diagram["activities"][$i] = $activityData = array_change_key_case($activityData, CASE_UPPER);

                // activity exists ?
                if ($activity = $bwp->getActivity($activityData["ACT_UID"])) {
                    // then update activity
                    $bwp->updateActivity($activityData["ACT_UID"], $activityData);

                    $whiteList[] = $activityData["ACT_UID"];
                } else {
                    // if not exists then create it
                    $oldActUid = $activityData["ACT_UID"];
                    $activityData["ACT_UID"] = Hash::generateUID();
                    $diagram["activities"][$i]["ACT_UID"] = $activityData["ACT_UID"];

                    $bwp->addActivity($activityData);

                    $result[] = array("object" => "activity", "new_uid" => $activityData["ACT_UID"], "old_uid" => $oldActUid);
                    $whiteList[] = $activityData["ACT_UID"];
                }
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
                $diagram["activities"][$i] = $gatewayData = array_change_key_case($gatewayData, CASE_UPPER);

                // gateway exists ?
                if ($gateway = $bwp->getGateway($gatewayData["GAT_UID"])) {
                    // then update activity
                    $bwp->updateGateway($gatewayData["GAT_UID"], $gatewayData);

                    $whiteList[] = $gatewayData["GAT_UID"];
                } else {
                    // if not exists then create it
                    $oldActUid = $gatewayData["GAT_UID"];
                    $gatewayData["GAT_UID"] = Hash::generateUID();
                    $diagram["activities"][$i]["ACT_UID"] = $gatewayData["GAT_UID"];

                    $bwp->addGateway($gatewayData);

                    $result[] = array("object" => "gateway", "new_uid" => $gatewayData["GAT_UID"], "old_uid" => $oldActUid);
                    $whiteList[] = $gatewayData["GAT_UID"];
                }
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
             * Diagram's Flows Handling
             */


            $whiteList = array();
            foreach ($diagram["flows"] as $i => $flowData) {
                //TODO, for test, assuming that all flows are new
                $diagram["flows"][$i] = $flowData = array_change_key_case($flowData, CASE_UPPER);
                $oldFloUid = $diagram["flows"][$i]["FLO_UID"];
                $diagram["flows"][$i]["FLO_UID"] = Hash::generateUID();
                Logger::log($flowData["FLO_ELEMENT_ORIGIN"], $result);
                $diagram["flows"][$i]["FLO_ELEMENT_ORIGIN"] = self::mapUid($flowData["FLO_ELEMENT_ORIGIN"], $result);
                $diagram["flows"][$i]["FLO_ELEMENT_DEST"] = self::mapUid($flowData["FLO_ELEMENT_DEST"], $result);

                $whiteList[] = $diagram["flows"][$i]["FLO_UID"];
                $result[] = array("object" => "flow", "new_uid" => $diagram["flows"][$i]["FLO_UID"], "old_uid" => $oldFloUid);
            }
            foreach ($diagram["flows"] as $flowData) {

                // flow exists ?
                if ($flow = $bwp->getFlow($flowData["FLO_UID"])) {
                    // then update activity
                    //$bwp->updateFlow($activityData["FLO_UID"], $flowData);

                    //$whiteList[] = $activityData["FLO_UID"];
                } else {
                    // if not exists then create it


                    //$bwp->addFlow($flowData);
                    $bwp->addFlow($flowData, $diagram["flows"], $diagram["gateways"], $diagram["events"]);
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

        return null;
    }
}

