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

    /**
     * ProcessDefinitionMigrator constructor.
     */
    public function __construct()
    {
        $this->bpmn = new Adapter\BpmnWorkflow();
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data)
    {
        try {
            return $this->bpmn->createFromStruct($data);;
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

    public function export($prj_uid)
    {
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

        $oProcess = new \Processes();
        $oData = new \StdClass();
        $oData->process = $oProcess->getProcessRow($prj_uid, false);
        $oData->tasks = $oProcess->getTaskRows($prj_uid);
        $oData->routes = $oProcess->getRouteRows($prj_uid);
        $oData->lanes = $oProcess->getLaneRows($prj_uid);
        $oData->gateways = $oProcess->getGatewayRows($prj_uid);
        $oData->steps = $oProcess->getStepRows($prj_uid);
        $oData->taskusers = $oProcess->getTaskUserRows($oData->tasks);
        $oData->groupwfs = $oProcess->getGroupwfRows($oData->taskusers);
        $oData->steptriggers = $oProcess->getStepTriggerRows($oData->tasks);
        $oData->reportTablesVars = $oProcess->getReportTablesVarsRows($prj_uid);
        $oData->objectPermissions = $oProcess->getObjectPermissionRows($prj_uid, $oData);
        $oData->subProcess = $oProcess->getSubProcessRow($prj_uid);
        $oData->caseTracker = $oProcess->getCaseTrackerRow($prj_uid);
        $oData->caseTrackerObject = $oProcess->getCaseTrackerObjectRow($prj_uid);
        $oData->stage = $oProcess->getStageRow($prj_uid);
        $oData->fieldCondition = $oProcess->getFieldCondition($prj_uid);
        $oData->event = $oProcess->getEventRow($prj_uid);
        $oData->caseScheduler = $oProcess->getCaseSchedulerRow($prj_uid);
        $oData->processCategory = $oProcess->getProcessCategoryRow($prj_uid);
        $oData->taskExtraProperties = $oProcess->getTaskExtraPropertiesRows($prj_uid);
        $oData->processUser = $oProcess->getProcessUser($prj_uid);
        $oData->processVariables = $oProcess->getProcessVariables($prj_uid);
        $oData->webEntry = $oProcess->getWebEntries($prj_uid);
        $oData->webEntryEvent = $oProcess->getWebEntryEvents($prj_uid);
        $oData->messageType = $oProcess->getMessageTypes($prj_uid);
        $oData->messageTypeVariable = $oProcess->getMessageTypeVariables($prj_uid);
        $oData->messageEventDefinition = $oProcess->getMessageEventDefinitions($prj_uid);
        $oData->scriptTask = $oProcess->getScriptTasks($prj_uid);
        $oData->timerEvent = $oProcess->getTimerEvents($prj_uid);
        $oData->emailEvent = $oProcess->getEmailEvent($prj_uid);
        $oData->filesManager = $oProcess->getFilesManager($prj_uid);
        $oData->abeConfiguration = $oProcess->getActionsByEmail($prj_uid);
        $oData->groupwfs = $oProcess->groupwfsMerge($oData->groupwfs, $oData->processUser, "USR_UID");
        $oData->process["PRO_TYPE_PROCESS"] = "PUBLIC";

        $result = array(
            'bpmn-definition' => $bpmnStruct,
            'workflow-definition' => (array)$oData
        );
        return $result;
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}