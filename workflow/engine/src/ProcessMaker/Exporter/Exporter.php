<?php
namespace ProcessMaker\Exporter;

use ProcessMaker\Project;

abstract class Exporter
{
    protected $prjUid;

    /**
     * @var \ProcessMaker\Project\Adapter\BpmnWorkflow
     */
    protected $bpmnProject;

    public function __construct($prjUid)
    {
        $this->prjUid = $prjUid;

        $this->bpmnProject = Project\Bpmn::load($prjUid);
    }

    public function buildData()
    {
        $data = array();
        $project = $this->bpmnProject->getProject();
        $data["METADATA"] = $this->getSystemInfo();
        $data["METADATA"]["project_name"] = $project["PRJ_NAME"];


        $bpmnStruct["ACTIVITY"] = \BpmnActivity::getAll($this->prjUid);
        //$data["BPMN_STRUCTURE"]["BPMN_BOUND"] = $this->bwap->getBounds();
//        $data["bpmn_data"] = $this->bwap->getProject();
//        $data["bpmn_diagram"] = $this->bwap->getProject();
//        $data["bpmn_documentation"] = $this->bwap->getProject();
        $bpmnStruct["BPMN_EVENT"] = \BpmnEvent::getAll($this->prjUid);
//        $data["bpmn_extension"] = $this->bwap->getProject();
//        $data["bpmn_flow"] = $this->bwap->getProject();
        $bpmnStruct["BPMN_GATEWAY"] = \BpmnGateway::getAll($this->prjUid);
//        $data["bpmn_lane"] = $this->bwap->getProject();
//        $data["bpmn_laneset"] = $this->bwap->getProject();
//        $data["bpmn_participant"] = $this->bwap->getProject();
//        $data["bpmn_process"] = $this->bwap->getProject();
//        $data["bpmn_project"] = $this->bwap->getProject();


        \G::LoadClass( 'processes' );
        $oProcess = new \Processes();
        $workflowData = (array) $oProcess->getWorkflowData($this->prjUid);
        $workflowData["process"] = array($workflowData["process"]);
        $workflowData["processCategory"] = empty($workflowData["processCategory"]) ? array() : $workflowData["processCategory"];


        $data["BPMN_DATA"] = $bpmnStruct;
        $data["WORKFLOW_DATA"] = $workflowData;
        $data["WORKFLOW_FILES"] = array();

        // getting dynaforms
        $dynaforms = array();

        foreach ($workflowData["dynaforms"] as $dynaform) {
            $dynFile = PATH_DYNAFORM . $dynaform['DYN_FILENAME'] . '.xml';
            $dynaforms[] = array(
                "filename" => $dynaform['DYN_TITLE'],
                "filepath" => $dynaform['DYN_FILENAME'] . '.xml',
                "file_content" => file_get_contents($dynFile)
            );

            $htmlFile = PATH_DYNAFORM . $dynaform['DYN_FILENAME'] . '.html';

            if (file_exists($htmlFile)) {
                $dynaforms[] = array(
                    "filename" => $dynaform['DYN_FILENAME'] . '.html',
                    "filepath" => $dynaform['DYN_FILENAME'] . '.html',
                    "file_content" => file_get_contents($htmlFile)
                );
            }
        }

        // getting templates files
        $templates = array();
        $workspaceDir = PATH_DATA . 'sites' . PATH_SEP . SYS_SYS . PATH_SEP;
        $templatesDir = $workspaceDir . 'mailTemplates' . PATH_SEP . $this->prjUid;
        $templatesFiles = \G::rglob("*", 0, $templatesDir);

        foreach ($templatesFiles as $templatesFile) {
            if (is_dir($templatesFile)) continue;

            $templates[] = array(
                "filename" => basename($templatesFile),
                "filepath" => str_replace($templatesDir, "", $templatesFile),
                "file_content" => file_get_contents($templatesFile)
            );
        }

        $data["WORKFLOW_FILES"]["DYNAFORMS"] = $dynaforms;
        $data["WORKFLOW_FILES"]["TEMPLATES"] = $templates;
        $data["WORKFLOW_FILES"]["PUBLIC"] = array();

        return $data;
    }

    public function getSystemInfo()
    {
        //$sysInfo = \System::getSysInfo();
        //print_r($sysInfo); die;

        return array(
            "vendor" => "ProcessMaker",
            "codename" => "Michelangelo",
            "version" => \System::getVersion(),
            "workspace" => defined("SYS_SYS") ? SYS_SYS : "Unknown",
        );
    }
}