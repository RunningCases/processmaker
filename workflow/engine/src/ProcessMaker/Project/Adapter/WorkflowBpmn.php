<?php
namespace ProcessMaker\Project\Adapter;

use ProcessMaker\Project;
use ProcessMaker\Util\Common;

/**
 * Class WorkflowBpmn
 *
 * @package ProcessMaker\Project\Adapter
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class WorkflowBpmn extends Project\Workflow
{
    /**
     * @var \ProcessMaker\Project\Bpmn
     */
    protected $bp;

    /**
     * OVERRIDES
     */

    public static function load($prjUid)
    {
        $parent = parent::load($prjUid);

        $me = new self();

        $me->process = $parent->process;
        $me->proUid = $parent->proUid;
        $me->bp = Project\Bpmn::load($prjUid);

        return $me;
    }

    public function create($data)
    {
        try {
            parent::create($data);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf("Can't create Workflow Project." . PHP_EOL . $e->getMessage()));
        }

        try {
            $bpData = array();
            $bpData["PRJ_UID"] = $this->getUid();

            if (array_key_exists("PRO_TITLE", $data)) {
                $bpData["PRJ_NAME"] = $data["PRO_TITLE"];
            }
            if (array_key_exists("PRO_DESCRIPTION", $data)) {
                $bpData["PRJ_DESCRIPTION"] = $data["PRO_DESCRIPTION"];
            }
            if (array_key_exists("PRO_CREATE_USER", $data)) {
                $bpData["PRJ_AUTHOR"] = $data["PRO_CREATE_USER"];
            } elseif (array_key_exists("USR_UID", $data)) {
                $bpData["PRJ_AUTHOR"] = $data["USR_UID"];
            }

            $bp = new Project\Bpmn();
            $bp->create($bpData);

            // At this time we will add a default diagram and process
            $bp->addDiagram();
            $bp->addProcess();

        } catch (\Exception $e) {
            $prjUid = $this->getUid();
            $this->remove();

            throw new \RuntimeException(sprintf(
                "Can't create Project with prj_uid: %s, workflow creation fails." . PHP_EOL . $e->getMessage()
                , $prjUid
            ));
        }
    }

    public static function getList($start = null, $limit = null, $filter = "", $changeCaseTo = CASE_UPPER)
    {
        return parent::getList($start, $limit, $filter, $changeCaseTo);
    }

    public function remove()
    {
        parent::remove();
        $this->bp->remove();
    }
}