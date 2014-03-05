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
        $bpmnStruct["BOUND"] = \BpmnBound::getAll($this->prjUid);
        $bpmnStruct["DATA"] = array();
        $bpmnStruct["DIAGRAM"] = \BpmnDiagram::getAll($this->prjUid);
        $bpmnStruct["DOCUMENTATION"] = array();
        $bpmnStruct["BPMN_EVENT"] = \BpmnEvent::getAll($this->prjUid);
        $bpmnStruct["EXTENSION"] = array();
        $bpmnStruct["FLOW"] = \BpmnFlow::getAll($this->prjUid, null, null, "", CASE_UPPER, false);
        $bpmnStruct["BPMN_GATEWAY"] = \BpmnGateway::getAll($this->prjUid);
        $bpmnStruct["LANE"] = array();
        $bpmnStruct["LANESET"] = array();
        $bpmnStruct["PARTICIPANT"] = array();
        $bpmnStruct["PROCESS"] = \BpmnProcess::getAll($this->prjUid);
        $bpmnStruct["PROJECT"] = array(\BpmnProjectPeer::retrieveByPK($this->prjUid)->toArray());

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
                $data["WORKFLOW_FILES"]["DYNAFORMS"][] = array(
                    "filename" => $dynaform['DYN_FILENAME'] . '.html',
                    "filepath" => $dynaform['DYN_FILENAME'] . '.html',
                    "file_content" => file_get_contents($htmlFile)
                );
            }
        }

        // getting templates files
        $workspaceTargetDirs = array("TEMPLATES" => "mailTemplates", "PUBLIC" => "public");
        $workspaceDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP;

        foreach ($workspaceTargetDirs as $target => $workspaceTargetDir) {
            $templatesDir = $workspaceDir . $workspaceTargetDir . PATH_SEP . $this->prjUid;
            $templatesFiles = \G::rglob("*", 0, $templatesDir);

            foreach ($templatesFiles as $templatesFile) {
                if (is_dir($templatesFile)) continue;

                $data["WORKFLOW_FILES"][$target][] = array(
                    "filename" => basename($templatesFile),
                    "filepath" => str_replace($templatesDir, "", $templatesFile),
                    "file_content" => file_get_contents($templatesFile)
                );
            }
        }

        return $data;
    }

    public function getSystemInfo()
    {
        return array(
            "vendor" => "ProcessMaker",
            "codename" => "Michelangelo",
            "version" => \System::getVersion(),
            "workspace" => defined("SYS_SYS") ? SYS_SYS : "Unknown",
        );
    }
}