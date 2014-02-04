<?php
namespace ProcessMaker\Project;

use \BpmnProject as Project;
use \BpmnProcess as Process;
use \BpmnDiagram as Diagram;
use \BpmnLaneset as Laneset;
use \BpmnLane as Lane;
use \BpmnActivity as Activity;
use \BpmnBound as Bound;
use \BpmnEvent as Event;
use \BpmnGateway as Gateway;
use \BpmnFlow as Flow;
use \BpmnArtifact as Artifact;

use \BpmnProjectPeer as ProjectPeer;
use \BpmnProcessPeer as ProcessPeer;
use \BpmnDiagramPeer as DiagramPeer;
use \BpmnLanesetPeer as LanesetPeer;
use \BpmnLanePeer as LanePeer;
use \BpmnActivityPeer as ActivityPeer;
use \BpmnBoundPeer as BoundPeer;
use \BpmnEventPeer as EventPeer;
use \BpmnGatewayPeer as GatewayPeer;
use \BpmnFlowPeer as FlowPeer;
use \BpmnArtifactPeer as ArtifactPeer;

use \BasePeer;

use ProcessMaker\Project\ProjectHandler;
use ProcessMaker\Util\Hash;

class BpmnProject //extends ProjectHandler
{
    protected static $diagramElements = array(
        'activities' => 'act_uid',
        'events'     => 'evn_uid',
        'flows'      => 'flo_uid',
        'artifacts'  => 'art_uid',
        'laneset'    => 'lns_uid',
        'lanes'      => 'lan_uid'
    );

    /**
     * @var \BpmnProject
     */
    protected $project;

    /**
     * @var \BpmnProcess
     */
    protected $process;

    /**
     * @var \BpmnDiagram
     */
    protected $diagram;

    /**
     * @var array of \BpmnActivities objects
     */
    protected $activities = array();

    /**
     * @var array of \BpmnEvents objects
     */
    protected $events = array();

    /**
     * @var array on \BpmnFlow objects
     */
    protected $flows = array();

    /**
     * @var array of \BpmnArtifact objects
     */
    protected $artifacts = array();

    /**
     * @var array of \BpmnLaneset objects
     */
    protected $laneset = array();

    /**
     * @var array of \BpmnLanes objects
     */
    protected $lanes = array();


    public function __construct($data = null)
    {
        if (! is_null($data)) {
            $this->create($data);
        }
    }

    public static function load($prjUid)
    {
        $me = new BpmnProject();
        $me->project = ProjectPeer::retrieveByPK($prjUid);

        return $me;
    }

    /**
     * @param array|null $data optional array attributes to create and initialize a BpmnProject
     */
    public function create($data)
    {
        // setting defaults
        $data['PRJ_UID'] = array_key_exists('PRJ_UID', $data) ? $data['PRJ_UID'] : Hash::generateUID();

        $this->project = new Project();
        $this->project->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->project->setPrjCreateDate(date("Y-m-d H:i:s"));
        $this->project->save();
    }

    public function addDiagram($data = array())
    {
        if (empty($this->project)) {
            throw new \Exception("Error: There is not an initialized project.");
        }

        // setting defaults
        $data['DIA_UID'] = array_key_exists('DIA_UID', $data) ? $data['DIA_UID'] : Hash::generateUID();
        $data['DIA_NAME'] = array_key_exists('DIA_NAME', $data) ? $data['DIA_NAME'] : $this->project->getPrjName();

        $this->diagram = new Diagram();
        $this->diagram->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->diagram->setPrjUid($this->project->getPrjUid());
        $this->diagram->save();
    }

    public function addProcess($data = array())
    {
        if (empty($this->diagram)) {
            throw new \Exception("Error: There is not an initialized diagram.");
        }

        // setting defaults
        $data['PRO_UID'] = array_key_exists('PRO_UID', $data) ? $data['PRO_UID'] : Hash::generateUID();;
        $data['PRO_NAME'] = array_key_exists('PRO_NAME', $data) ? $data['PRO_NAME'] : $this->diagram->getDiaName();

        $this->process = new Process();
        $this->process->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $this->process->setPrjUid($this->project->getPrjUid());
        $this->process->setDiaUid($this->getDiagram("object")->getDiaUid());
        $this->process->save();
    }

    public function addActivity($data)
    {
        if (empty($this->diagram)) {
            throw new \Exception("Error: There is not an initialized diagram.");
        }

        // setting defaults
        $data['ACT_UID'] = array_key_exists('ACT_UID', $data) ? $data['ACT_UID'] : Hash::generateUID();;

        $activity = new Activity();
        $activity->fromArray($data);
        $activity->setPrjUid($this->project->getPrjUid());
        $activity->setProUid($this->getProcess("object")->getProUid());
        $activity->save();

        $this->activities[$activity->getActUid()] = $activity;
    }

    public function getActivity($actUid)
    {
        if (empty($this->activities) || ! array_key_exists($actUid, $this->activities)) {
            $activity = ActivityPeer::retrieveByPK($actUid);

            if (! is_object($activity)) {
                return null;
            }

            $this->activities[$actUid] = $activity;
        }

        return $this->activities[$actUid];
    }

    public function getActivities()
    {
        if (empty($this->activities)) {
            $this->activities = Activity::getAll($this->project->getPrjUid(), null, null, '', 'object');
        }

        $activitiesList = array();

        foreach ($this->activities as $activity) {
            $activitiesList[] = $activity->toArray();
        }

        return $activitiesList;
    }

    public function addEvent($data)
    {
        // setting defaults
        $data['EVN_UID'] = array_key_exists('EVN_UID', $data) ? $data['EVN_UID'] : Hash::generateUID();;

        $event = new Event();
        $event->fromArray($data);
        $event->setPrjUid($this->project->getPrjUid());
        $event->setProUid($this->getProcess("object")->getProUid());
        $event->save();

        $this->events[$event->getEvnUid()] = $event;
    }

    public function getEvent($evnUid)
    {
        if (empty($this->events) || ! array_key_exists($evnUid, $this->activities)) {
            $event = EventPeer::retrieveByPK($evnUid);

            if (! is_object($event)) {
                return null;
            }

            $this->events[$evnUid] = $event;
        }

        return $this->events[$evnUid];
    }

    public function getEvents()
    {
        if (empty($this->events)) {
            $this->events = Activity::getAll($this->project->getPrjUid(), null, null, '', 'object');
        }

        $eventsList = array();

        foreach ($this->events as $event) {
            $eventsList[] = $event->toArray();
        }

        return $eventsList;
    }

    public function addGateway($data)
    {
        // setting defaults
        $data['GAT_UID'] = array_key_exists('GAT_UID', $data) ? $data['GAT_UID'] : Hash::generateUID();;

        $gateway = new Gateway();
        $gateway->fromArray($data);
        $gateway->setPrjUid($this->project->getPrjUid());
        $gateway->setProUid($this->getProcess("object")->getProUid());
        $gateway->save();

        $this->gateways[$gateway->getGatUid()] = $gateway;
    }

    public function getGateway($gatUid)
    {
        if (empty($this->gateways) || ! array_key_exists($gatUid, $this->gateways)) {
            $gateway = GatewayPeer::retrieveByPK($gatUid);

            if (! is_object($gateway)) {
                return null;
            }

            $this->gateways[$gatUid] = $gateway;
        }

        return $this->gateways[$gatUid];
    }

    public function getGateways()
    {
        if (empty($this->gateways)) {
            $this->gateways = Activity::getAll($this->project->getPrjUid(), null, null, '', 'object');
        }

        $gatewaysList = array();

        foreach ($this->gateways as $gateway) {
            $gatewaysList[] = $gateway->toArray();
        }

        return $gatewaysList;
    }

    public function addFlow($data)
    {
        // setting defaults
        $data['GAT_UID'] = array_key_exists('GAT_UID', $data) ? $data['GAT_UID'] : Hash::generateUID();;
        $data['FLO_STATE'] = json_encode($data['FLO_STATE']);

        $flow = new Flow();
        $flow->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $flow->setPrjUid($this->project->getPrjUid());
        $flow->setDiaUid($this->getDiagram("object")->getDiaUid());
        $flow->save();
    }

    public function getFlow($floUid)
    {
        if (empty($this->flows) || ! array_key_exists($floUid, $this->flows)) {
            $flow = GatewayPeer::retrieveByPK($floUid);

            if (! is_object($flow)) {
                return null;
            }

            $this->flows[$floUid] = $flow;
        }

        return $this->flows[$floUid];
    }

    public function getFlows()
    {
        if (empty($this->flows)) {
            $this->flows = Activity::getAll($this->project->getPrjUid(), null, null, '', 'object');
        }

        $flowsList = array();

        foreach ($this->flows as $flow) {
            $flowsList[] = $flow->toArray();
        }

        return $flowsList;
    }

    public function addArtifact($data)
    {
        // TODO: Implement update() method.
    }

    public function getArtifact($artUid)
    {
        // TODO: Implement update() method.
    }

    public function getArtifacts()
    {
        // TODO: Implement update() method.
    }

    public function addLane($data)
    {
        // TODO: Implement update() method.
    }

    public function getLane($lanUid)
    {
        // TODO: Implement update() method.
    }

    public function getLanes()
    {
        // TODO: Implement update() method.
    }

    public function addLaneset($data)
    {
        // TODO: Implement update() method.
    }

    public function getLaneset($lnsUid)
    {
        // TODO: Implement update() method.
    }

    public function getLanesets()
    {
        // TODO: Implement update() method.
    }



    // getters

    public function getUid()
    {
        if (empty($this->project)) {
            throw new \Exception("Error: There is not an initialized project.");
        }

        return $this->project->getPrjUid();
    }

    public function getProject($retType = "array")
    {
        return $retType == "array" ? $this->project->toArray() : $this->project;
    }

    public function getDiagram($retType = "array")
    {
        if (empty($this->diagram)) {
            $diagrams = Diagram::findAllByProUid($this->getUid());

            if (! empty($diagrams)) {
                //NOTICE for ProcessMaker we're just handling a "one to one" relationship between project and process
                $this->diagram = $diagrams[0];
            }
        }

        return $retType == "array" ? $this->diagram->toArray() : $this->diagram;
    }

    public function getProcess($retType = "array")
    {
        if (empty($this->process)) {
            $processes = Process::findAllByProUid($this->getUid());

            if (! empty($processes)) {
                //NOTICE for ProcessMaker we're just handling a "one to one" relationship between project and process
                $this->process = $processes[0];
            }
        }

        return $retType == "array" ? $this->process->toArray() : $this->process;
    }
}