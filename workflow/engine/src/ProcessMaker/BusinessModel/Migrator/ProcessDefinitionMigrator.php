<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/18/16
 * Time: 10:28 AM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Project\Adapter;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProcessDefinitionMigrator implements Importable, Exportable
{
    protected $bpmn;
    protected $processes;

    /**
     * ProcessDefinitionMigrator constructor.
     */
    public function __construct()
    {
        $this->bpmn = new Adapter\BpmnWorkflow();
        $this->processes = new \Processes();
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data, $replace)
    {
        try {
            if ($replace) {
                $this->bpmn->createFromStruct($data, false);
            } else {
                $this->bpmn->updateFromStruct($data['PRJ_UID'], $data, false);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }

    public function beforeExport()
    {
        // TODO: Implement beforeExport() method.
    }

    /**
     * @param $prj_uid
     * @return array
     */
    public function export($prj_uid)
    {
        try {
            $bpmnStruct["ACTIVITY"] = \BpmnActivity::getAll($prj_uid);
            $bpmnStruct["ARTIFACT"] = \BpmnArtifact::getAll($prj_uid);
            $bpmnStruct["BOUND"] = \BpmnBound::getAll($prj_uid);
            $bpmnStruct["DATA"] = \BpmnData::getAll($prj_uid);
            $bpmnStruct["DIAGRAM"] = \BpmnDiagram::getAll($prj_uid);
            $bpmnStruct["DOCUMENTATION"] = array();
            $bpmnStruct["EVENT"] = \BpmnEvent::getAll($prj_uid);
            $bpmnStruct["EXTENSION"] = array();
            $bpmnStruct["FLOW"] = \BpmnFlow::getAll($prj_uid, null, null, "", CASE_UPPER, false);
            $bpmnStruct["GATEWAY"] = \BpmnGateway::getAll($prj_uid);
            $bpmnStruct["LANE"] = \BpmnLane::getAll($prj_uid);
            $bpmnStruct["LANESET"] = \BpmnLaneset::getAll($prj_uid);
            $bpmnStruct["PARTICIPANT"] = \BpmnParticipant::getAll($prj_uid);
            $bpmnStruct["PROCESS"] = \BpmnProcess::getAll($prj_uid);
            $bpmnStruct["PROJECT"] = array(\BpmnProjectPeer::retrieveByPK($prj_uid)->toArray());

            $oData = new \StdClass();
            $oData->process = $this->processes->getProcessRow($prj_uid, false);
            $oData->tasks = $this->processes->getTaskRows($prj_uid);

            $oDataTask = new \StdClass();
            $oDataTask->taskusers = $this->processes->getTaskUserRows($oData->tasks);

            $oData->routes = $this->processes->getRouteRows($prj_uid);
            $oData->lanes = $this->processes->getLaneRows($prj_uid);
            $oData->gateways = $this->processes->getGatewayRows($prj_uid);
            $oData->steps = $this->processes->getStepRows($prj_uid);
            $oData->groupwfs = $this->processes->getGroupwfRows($oDataTask->taskusers);
            $oData->steptriggers = $this->processes->getStepTriggerRows($oData->tasks);
            $oData->reportTablesVars = $this->processes->getReportTablesVarsRows($prj_uid);
            $oData->subProcess = $this->processes->getSubProcessRow($prj_uid);
            $oData->caseTracker = $this->processes->getCaseTrackerRow($prj_uid);
            $oData->caseTrackerObject = $this->processes->getCaseTrackerObjectRow($prj_uid);
            $oData->stage = $this->processes->getStageRow($prj_uid);
            $oData->fieldCondition = $this->processes->getFieldCondition($prj_uid);
            $oData->event = $this->processes->getEventRow($prj_uid);
            $oData->caseScheduler = $this->processes->getCaseSchedulerRow($prj_uid);
            $oData->processCategory = $this->processes->getProcessCategoryRow($prj_uid);
            $oData->taskExtraProperties = $this->processes->getTaskExtraPropertiesRows($prj_uid);
            $oData->webEntry = $this->processes->getWebEntries($prj_uid);
            $oData->webEntryEvent = $this->processes->getWebEntryEvents($prj_uid);
            $oData->messageType = $this->processes->getMessageTypes($prj_uid);
            $oData->messageTypeVariable = $this->processes->getMessageTypeVariables($prj_uid);
            $oData->messageEventDefinition = $this->processes->getMessageEventDefinitions($prj_uid);
            $oData->scriptTask = $this->processes->getScriptTasks($prj_uid);
            $oData->timerEvent = $this->processes->getTimerEvents($prj_uid);
            $oData->emailEvent = $this->processes->getEmailEvent($prj_uid);
            $oData->abeConfiguration = $this->processes->getActionsByEmail($prj_uid);
            $oData->groupwfs = $this->processes->groupwfsMerge($oData->groupwfs, $oData->processUser, "USR_UID");
            $oData->process["PRO_TYPE_PROCESS"] = "PUBLIC";

            $result = array(
                'bpmn-definition' => $bpmnStruct,
                'workflow-definition' => (array)$oData
            );
            return $result;

        } catch (\Exception $e) {
            \Logger::log($e->getMessage());
            throwException(new ExportException($e->getMessage()));
        }

    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}