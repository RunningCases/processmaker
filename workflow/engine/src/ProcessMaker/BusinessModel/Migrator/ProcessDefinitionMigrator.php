<?php

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Project\Adapter;
use ProcessMaker\BusinessModel;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProcessDefinitionMigrator implements Importable, Exportable
{
    protected $bpmn;
    protected $processes;
    protected $className;

    /**
     * ProcessDefinitionMigrator constructor.
     */
    public function __construct()
    {
        $this->bpmn = new Adapter\BpmnWorkflow();
        $this->processes = new \Processes();
        $this->className = 'ProcessDefinition';
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    /**
     * @param $data
     * @param $replace
     * @throws ImportException
     */
    public function import($data, $replace)
    {
        try {
            //Bpmn elements
            $pjrUid = $this->bpmn->createFromStruct($data['bpmn'], false, $data);
            //Import workflow elements
        } catch (\Exception $e) {
            $exception = new ImportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

    /**
     * @param $data
     * @throws ImportException
     */
    public function afterImport($data)
    {
        try {
            //Workflow elements
            $this->processes->updateProcessRow($data['workflow']['process']);
            $workflowTaks = array();
            $dummyTaskTypes = BusinessModel\Task::getDummyTypes();
            foreach ($data['workflow']['tasks'] as $key => $value) {
                $arrayTaskData = $value;
                if (!in_array($arrayTaskData["TAS_TYPE"], $dummyTaskTypes)) {
                    $workflowTaks[] = $arrayTaskData;
                }
            }
            $this->processes->createTaskRows($workflowTaks);
            $this->processes->createLaneRows($data['workflow']['lanes']);
            $this->processes->createGatewayRows($data['workflow']['gateways']);
            $this->processes->createStepRows($data['workflow']['steps']);
            $this->processes->createStepTriggerRows($data['workflow']['steptriggers']);
            $this->processes->createTaskUserRows($data['workflow']['taskusers']);
            $this->processes->createSubProcessRows($data['workflow']['subProcess']);
            $this->processes->createCaseTrackerRows($data['workflow']['caseTracker']);
            $this->processes->createCaseTrackerObjectRows($data['workflow']['caseTrackerObject']);
            $this->processes->createStageRows($data['workflow']['stage']);
            $this->processes->createFieldCondition($data['workflow']['fieldCondition'], $data['workflow']['dynaforms']);
            $this->processes->createEventRows($data['workflow']['event']);
            $this->processes->createCaseSchedulerRows($data['workflow']['caseScheduler']);
            $this->processes->createProcessCategoryRow($data['workflow']['processCategory']);
            $this->processes->createTaskExtraPropertiesRows($data['workflow']['taskExtraProperties']);
            $this->processes->createWebEntry($data['workflow']['process']['PRO_UID'], $data['workflow']['process']['PRO_CREATE_USER'], $data['workflow']['webEntry']);
            $this->processes->createWebEntryEvent($data['workflow']['process']['PRO_UID'], $data['workflow']['process']['PRO_CREATE_USER'], $data['workflow']['webEntryEvent']);
            $this->processes->createMessageType($data['workflow']['messageType']);
            $this->processes->createMessageTypeVariable($data['workflow']['messageTypeVariable']);
            $this->processes->createMessageEventDefinition($data['workflow']['process']['PRO_UID'], $data['workflow']['messageEventDefinition']);
            $this->processes->createScriptTask($data['workflow']['process']['PRO_UID'], $data['workflow']['scriptTask']);
            $this->processes->createTimerEvent($data['workflow']['process']['PRO_UID'], $data['workflow']['timerEvent']);
            $this->processes->createEmailEvent($data['workflow']['process']['PRO_UID'], $data['workflow']['emailEvent']);
            $this->processes->createActionsByEmail($data['workflow']['process']['PRO_UID'], $data['workflow']['abeConfiguration']);

        } catch (\Exception $e) {
            $exception = new ImportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }
    }

    public function beforeExport()
    {
        // TODO: Implement beforeExport() method.
    }

    /**
     * @param $prj_uid
     * @return array
     * @throws ExportException
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

            $oData = new \StdClass();
            $oData->tasks = $this->processes->getTaskRows($prj_uid);
            $oData->taskusers = $this->processes->getTaskUserRows($oData->tasks);
            $oData->routes = $this->processes->getRouteRows($prj_uid);
            $oData->lanes = $this->processes->getLaneRows($prj_uid);
            $oData->gateways = $this->processes->getGatewayRows($prj_uid);
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
            $oData->steps = $this->processes->getStepRows($prj_uid);
            $oData->steptriggers = $this->processes->getStepTriggerRows($oData->tasks);
            $oData->scriptTask = $this->processes->getScriptTasks($prj_uid);
            $oData->timerEvent = $this->processes->getTimerEvents($prj_uid);
            $oData->emailEvent = $this->processes->getEmailEvent($prj_uid);
            $oData->abeConfiguration = $this->processes->getActionsByEmail($prj_uid);
            $oData->processUser = $this->processes->getProcessUser($prj_uid);
            $oData->process["PRO_TYPE_PROCESS"] = "PUBLIC";

            $result = array(
                'bpmn-definition' => (array)$bpmnStruct,
                'workflow-definition' => (array)$oData
            );
            return $result;

        } catch (\Exception $e) {
            $exception = new ExportException($e->getMessage());
            $exception->setNameException($this->className);
            throw($exception);
        }

    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}