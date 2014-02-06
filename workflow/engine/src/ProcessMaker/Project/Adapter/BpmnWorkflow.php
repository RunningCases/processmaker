<?php
namespace ProcessMaker\Project\Adapter;

use ProcessMaker\Project;
use ProcessMaker\Util\Hash;

/**
 * Class BpmnWorkflow
 * @package ProcessMaker\Project\Adapter
 */
class BpmnWorkflow extends Project\Bpmn
{
    /**
     * @var \ProcessMaker\Project\Workflow
     */
    protected $wp;

    public function __construct()
    {
        $this->wp = new Project\Workflow();
    }


    /**
     * OVERRIDES
     */

    public static function load($prjUid)
    {
        $parent = parent::load($prjUid);

        $me = new self();

        $me->project = $parent->project;
        $me->prjUid = $parent->project->getPrjUid();
        $me->wp = Project\Workflow::load($me->prjUid);

        return $me;
    }

    public function create($data)
    {
        try {
            parent::create($data);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Can't create Bpmn Project." . PHP_EOL . $e->getMessage()));
        }

        try {
            $wpData = array();
            $wpData["PRO_UID"] = $this->getUid();

            if (array_key_exists("PRJ_NAME", $data)) {
                $wpData["PRO_TITLE"] = $data["PRJ_NAME"];
            }
            if (array_key_exists("PRJ_DESCRIPTION", $data)) {
                $wpData["PRO_DESCRIPTION"] = $data["PRJ_DESCRIPTION"];
            }
            if (array_key_exists("PRJ_AUTHOR", $data)) {
                $wpData["PRO_CREATE_USER"] = $data["PRJ_AUTHOR"];
            }

            $wp = new Project\Workflow();
            $wp->create($wpData);

        } catch (\Exception $e) {
            $prjUid = $this->getUid();
            $this->remove();

            throw new \RuntimeException(sprintf(
                "Can't create Bpmn Project with prj_uid: %s, workflow creation fails." . PHP_EOL . $e->getMessage()
                , $prjUid
            ));
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        $bpmnProjects = parent::getList($start, $limit, $filter, $changeCaseTo);
        $workflowProjects = Project\Workflow::getList($start, $limit, "", "");

        $workflowProjectsUids = array();

        foreach ($workflowProjects as $workflowProject) {
            $workflowProjectsUids[] = $workflowProject["PRO_UID"];
        }

        $prjUidKey = $changeCaseTo == CASE_UPPER ? "PRJ_UID" : "prj_uid";
        $list = array();

        foreach ($bpmnProjects as $bpmnProject) {
            if (in_array($bpmnProject[$prjUidKey], $workflowProjectsUids)) {
                $list[] = $bpmnProject;
            }
        }

        return $list;
    }

    public function addActivity($data)
    {
        parent::addActivity($data);

        $taskData = array();
        $taskData["TAS_UID"] = $data["ACT_UID"];

        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_TITLE"] = $data["ACT_NAME"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSX"] = $data["BOU_X"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSY"] = $data["BOU_Y"];
        }

        $this->wp->addTask($taskData);
    }

    public function updateActivity($actUid, $data)
    {
        parent::updateActivity($actUid, $data);

        $taskData = array();

        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_TITLE"] = $data["ACT_NAME"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSX"] = $data["BOU_X"];
        }
        if (array_key_exists("ACT_NAME", $data)) {
            $taskData["TAS_POSY"] = $data["BOU_Y"];
        }

        $this->wp->updateTask($actUid, $taskData);
    }

    public function removeActivity($actUid)
    {
        parent::removeActivity($actUid);
        $this->wp->removeTask($actUid);
    }
}