<?php
namespace ProcessMaker\Adapter\Bpmn;

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

use \ProcessMaker\Util\Hash;
use \BasePeer;


/**
 * Class Model
 *
 * @package ProcessMaker\Adapter\Bpmn
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Model
{
    public function createProject($data)
    {
        $data = array_change_key_case($data, CASE_UPPER);
        $uids = array();
        $oldPrjUid = $data['PRJ_UID'];
        $diagrams = $data['DIAGRAMS'];
        $mapId = array();

        unset($data['PRJ_UID']);

        /*
         * 1. Create a project record
         * 2. Create a default diagram record
         * 3. Create a default process record
         */

        $project = new Project();
        $project->fromArray($data, BasePeer::TYPE_FIELDNAME);
        $project->setPrjUid(Hash::generateUID());
        $project->setPrjCreateDate(date("Y-m-d H:i:s"));
        $project->save();
        $prjUid  = $project->getPrjUid();
        $prjName = $project->getPrjName();
        $uids[] = array('old_uid' => $oldPrjUid, 'new_uid' => $prjUid, 'object' => 'project');
        $mapId['project'][$oldPrjUid] = $prjUid;

        // By now, is thought create only one diagram for each project (1:1)
        $diagramData = (array) $diagrams[0];
        $oldDiaUid = $diagramData['dia_uid'];

        $diagram = new Diagram();
        $diagram->setDiaUid(Hash::generateUID());
        $diagram->setPrjUid($prjUid);
        $diagram->setDiaName($prjName);
        $diagram->save();
        $diaUid = $diagram->getDiaUid();
        $uids[] = array('old_uid' => $oldDiaUid, 'new_uid' => $diaUid, 'object' => 'diagram');
        $mapId['diagram'][$oldDiaUid] = $diaUid;

        $process = new Process();
        $process->setProUid(Hash::generateUID());
        $process->setPrjUid($prjUid);
        $process->setDiaUid($diaUid);
        $process->setProName($prjName);
        $process->save();
        $proUid = $process->getProUid();

        $uids = array_merge($uids, $this->createDiagram($prjUid, $proUid, $diaUid, $diagramData));

        return $uids;
    }

    private function createDiagram($prjUid, $proUid, $diaUid, $diagramData)
    {
        $uids = array();
        $mapId = array();

        /*
         * 1. ensure that all related data of objects are defined, if not we define them as empty
         * 2. create all related objects
         */

        $lanesets = array_key_exists('laneset', $diagramData) ? $diagramData['laneset'] : array();
        $lanes = array_key_exists('lane', $diagramData) ? $diagramData['lanes'] : array();
        $activities = array_key_exists('activities', $diagramData) ? $diagramData['activities'] : array();
        $events = array_key_exists('events', $diagramData) ? $diagramData['events'] : array();
        $gateways = array_key_exists('gateways', $diagramData) ? $diagramData['gateways'] : array();
        $flows = array_key_exists('flows', $diagramData) ? $diagramData['flows'] : array();
        $artifacts = array_key_exists('artifacts', $diagramData) ? $diagramData['artifacts'] : array();

        foreach($lanesets as $lanesetData) {
            $lanesetData = array_change_key_case((array) $lanesetData, CASE_UPPER);

            $laneset = new Laneset();
            $laneset->fromArray($lanesetData, BasePeer::TYPE_FIELDNAME);
            $laneset->setLnsUid(Hash::generateUID());
            $laneset->setPrjUid($prjUid);
            $laneset->setProUid($proUid);
            $laneset->save();
            $lnsUid = $laneset->getLnsUid();
            $oldLnsUid = $lanesetData['LNS_UID'];

            $uids[] = array('old_uid' => $oldLnsUid, 'new_uid' => $lnsUid, 'object' => 'laneset');
            $mapId['laneset'][$oldLnsUid] = $lnsUid;
        }

        foreach($lanes as $laneData) {
            $laneData = array_change_key_case((array) $laneData, CASE_UPPER);
            $oldLanUid = $laneData['LNS_UID'];

            $lane = new Lane();
            $lane->fromArray($laneData, BasePeer::TYPE_FIELDNAME);
            $lane->setLanUid(Hash::generateUID());
            $lane->setPrjUid($prjUid);
            $lane->setLnsUid($mapId['laneset'][$oldLanUid]);
            $lane->save();
            $lanUid = $lane->getLanUid();

            $uids[] = array('old_uid' => $oldLanUid, 'new_uid' => $lanUid, 'object' => 'lane');
            $mapId['lane'][$oldLanUid] = $lanUid;
        }

        /*
         * 1. crate project related object
         * 2. crate bound record for each object created previously
         */

        foreach($activities as $activityData) {
            $activityData = array_change_key_case((array) $activityData, CASE_UPPER);

            $activity = new Activity();
            $activity->fromArray($activityData, BasePeer::TYPE_FIELDNAME);
            $activity->setActUid(Hash::generateUID());
            $activity->setPrjUid($prjUid);
            $activity->setProUid($proUid);
            $activity->save();

            $actUid = $activity->getActUid();
            $oldActUid = $activityData['ACT_UID'];
            $uids[] = array('old_uid' => $oldActUid, 'new_uid' => $actUid, 'object' => 'activity');
            $mapId['activity'][$oldActUid] = $actUid;

            $bound = new Bound();
            $bound->fromArray($activityData, BasePeer::TYPE_FIELDNAME);
            $bound->setBouUid(Hash::generateUID());
            $bound->setPrjUid($prjUid);
            $bound->setDiaUid($diaUid);
            $bound->setElementUid($activity->getActUid());
            $bound->setBouElementType(str_replace('Bpmn', 'bpmn', get_class($activity)));
            $bound->setBouElement('pm_canvas');
            $bound->setBouContainer('bpmnDiagram');
            $bound->save();
        }

        foreach($events as $eventData) {
            $eventData = array_change_key_case((array) $eventData, CASE_UPPER);

            $event = new Event();
            $event->fromArray($eventData, BasePeer::TYPE_FIELDNAME);
            $event->setEvnUid(Hash::generateUID());
            $event->setPrjUid($prjUid);
            $event->setProUid($proUid);
            $event->save();

            $evnUid = $event->getEvnUid();
            $oldEvnUid = $eventData['EVN_UID'];
            $uids[] = array('old_uid' => $oldEvnUid, 'new_uid' => $evnUid, 'object' => 'event');
            $mapId['event'][$oldEvnUid] = $evnUid;

            $bound = new Bound();
            $bound->fromArray($eventData, BasePeer::TYPE_FIELDNAME);
            $bound->setBouUid(Hash::generateUID());
            $bound->setPrjUid($prjUid);
            $bound->setDiaUid($diaUid);
            $bound->setElementUid($event->getEvnUid());
            $bound->setBouElementType(str_replace('Bpmn', 'bpmn', get_class($activity)));
            $bound->setBouElement('pm_canvas');
            $bound->setBouContainer('bpmnDiagram');
            $bound->save();
        }

        foreach($gateways as $gatewayData) {
            $gatewayData = array_change_key_case((array) $gatewayData, CASE_UPPER);

            // fix data
            if ($gatewayData['GAT_DIRECTION'] === null) {
                unset($gatewayData['GAT_DIRECTION']);
            }

            $gateway = new Gateway();
            $gateway->fromArray($gatewayData, BasePeer::TYPE_FIELDNAME);
            $gateway->setGatUid(Hash::generateUID());
            $gateway->setPrjUid($prjUid);
            $gateway->setProUid($proUid);
            $gateway->save();

            $gatUid = $gateway->getGatUid();
            $oldGatUid = $gatewayData['GAT_UID'];
            $uids[] = array('old_uid' => $oldGatUid, 'new_uid' => $gatUid, 'object' => 'gateway');
            $mapId['gateway'][$oldGatUid] = $gatUid;

            $bound = new Bound();
            $bound->fromArray($gatewayData, BasePeer::TYPE_FIELDNAME);
            $bound->setBouUid(Hash::generateUID());
            $bound->setPrjUid($prjUid);
            $bound->setDiaUid($diaUid);
            $bound->setElementUid($gateway->getGatUid());
            $bound->setBouElementType(str_replace('Bpmn', 'bpmn', get_class($activity)));
            $bound->setBouElement('pm_canvas');
            $bound->setBouContainer('bpmnDiagram');
            $bound->save();
        }

        foreach($flows as $flowData) {
            $flowData = array_change_key_case((array) $flowData, CASE_UPPER);

            $floState = json_encode($flowData['FLO_STATE']);
            unset($flowData['FLO_STATE']);

            $flow = new Flow();
            $flow->fromArray($flowData, BasePeer::TYPE_FIELDNAME);
            $flow->setFloUid(Hash::generateUID());
            $flow->setPrjUid($prjUid);
            $flow->setDiaUid($diaUid);
            $flow->setFloState($floState);

            switch ($flow->getFloElementOriginType()) {
                case 'bpmnEvent':
                    $flow->setFloElementOrigin($mapId['event'][$flowData['FLO_ELEMENT_ORIGIN']]);
                    break;
                case 'bpmnGateway':
                    $flow->setFloElementOrigin($mapId['gateway'][$flowData['FLO_ELEMENT_ORIGIN']]);
                    break;
                case 'bpmnActivity':
                    $flow->setFloElementOrigin($mapId['activity'][$flowData['FLO_ELEMENT_ORIGIN']]);
                    break;
            }

            switch ($flow->getFloElementDestType()) {
                case 'bpmnEvent':
                    $flow->setFloElementDest($mapId['event'][$flowData['FLO_ELEMENT_DEST']]);
                    break;
                case 'bpmnGateway':
                    $flow->setFloElementDest($mapId['gateway'][$flowData['FLO_ELEMENT_DEST']]);
                    break;
                case 'bpmnActivity':
                    $flow->setFloElementDest($mapId['activity'][$flowData['FLO_ELEMENT_DEST']]);
                    break;
            }

            $flow->save();

            $floUid = $flow->getFloUid();
            $oldFloUid = $flowData['FLO_UID'];
            $uids[] = array('old_uid' => $oldFloUid, 'new_uid' => $floUid, 'object' => 'flow');
        }

        foreach($artifacts as $artifactData) {
            $artifactData = array_change_key_case((array) $artifactData, CASE_UPPER);

            $artifact = new Artifact();
            $artifact->fromArray($artifactData, BasePeer::TYPE_FIELDNAME);
            $artifact->setArtUid(Hash::generateUID());
            $artifact->setPrjUid($prjUid);
            $artifact->setProUid($proUid);
            $artifact->save();

            $artUid = $artifact->getFloUid();
            $oldArtUid = $artifactData['ART_UID'];
            $uids[] = array('old_uid' => $oldArtUid, 'new_uid' => $artUid, 'object' => 'artifact');
        }

        return $uids;
    }

    public static function loadProject($prjUid)
    {
        /*
         * 1. load object of project
         * 2. load object of process
         * 3. load object of diagram
         * 4. load collection of lanesets
         * 5. load collection of lanes
         * 6. load collection of activities
         * 7. load collection of events
         * 8. load collection of gateways
         * 9. load collection of flows
         * 10. load collection of artifacts
         * 11. compose project data structure
         */

        $project = self::getBpmnObjectBy('Project', ProjectPeer::PRJ_UID, $prjUid, true);
        $process = self::getBpmnObjectBy('Process', ProcessPeer::PRJ_UID, $prjUid, true);
        $diagram = self::getBpmnObjectBy('Diagram', DiagramPeer::DIA_UID, $process['dia_uid'], true);
        $lanesets = self::getBpmnCollectionBy('Laneset', LanesetPeer::PRJ_UID, $prjUid, true);
        $lanes = self::getBpmnCollectionBy('Lane', LanePeer::PRJ_UID, $prjUid, true);
        $activities = self::getBpmnCollectionBy('Activity', ActivityPeer::PRJ_UID, $prjUid, true);
        $events = self::getBpmnCollectionBy('Event', EventPeer::PRJ_UID, $prjUid, true);
        $gateways = self::getBpmnCollectionBy('Gateway', GatewayPeer::PRJ_UID, $prjUid, true);
        $flows = self::getBpmnCollectionBy('Flow', FlowPeer::PRJ_UID, $prjUid, true);
        $artifacts = self::getBpmnCollectionBy('Artifact', ArtifactPeer::PRJ_UID, $prjUid, true);

        // getting activity bound data
        foreach ($activities as $i => $activity) {
            $activities[$i] = array_merge(
                $activities[$i],
                self::getBpmnObjectBy('Bound', BoundPeer::ELEMENT_UID, $activity['act_uid'], true)
            );
        }

        // getting event bound data
        foreach ($events as $i => $event) {
            $events[$i] = array_merge(
                $events[$i],
                self::getBpmnObjectBy('Bound', BoundPeer::ELEMENT_UID, $event['evn_uid'], true)
            );
        }

        // getting gateway bound data
        foreach ($gateways as $i => $gateway) {
            $gateways[$i] = array_merge(
                $gateways[$i],
                self::getBpmnObjectBy('Bound', BoundPeer::ELEMENT_UID, $gateway['gat_uid'], true)
            );
        }

        $project = array_change_key_case($project);
        $project['diagrams'] = array($diagram);
        $project['diagrams'][0]['lanesets'] = $lanesets;
        $project['diagrams'][0]['lanes'] = $lanes;
        $project['diagrams'][0]['activities'] = $activities;
        $project['diagrams'][0]['events'] = $events;
        $project['diagrams'][0]['gateways'] = $gateways;
        $project['diagrams'][0]['flows'] = $flows;
        $project['diagrams'][0]['artifacts'] = $artifacts;

        return $project;
    }

    public static function loadProjects()
    {
        $projectsList = self::getAllBpmnCollectionFrom('Project', true);
        $projects = array();

        foreach ($projectsList as $project) {
            $projects[] = self::loadProject($project['prj_uid']);
        }

        return $projects;
    }

    public static function updateProject($prjUid, $projectUpdated)
    {
        $project = ProjectPeer::retrieveByPK($prjUid);
        $project->setPrjName($projectUpdated['prj_name']);
        $project->setPrjUpdateDate(date("Y-m-d H:i:s"));
        $project->save();

        $diagramData = $projectUpdated['diagrams'][0];

        $diagram = DiagramPeer::retrieveByPK($diagramData['dia_uid']);
        $diagram->setDiaName($diagramData['dia_name']);

        if (!empty($diagramData['dia_is_closable'])) {
            $diagram->setDiaIsClosable($diagramData['dia_is_closable']);
        }

        $diagram->save();

        $processData = self::getBpmnObjectBy('Process', ProcessPeer::PRJ_UID, $prjUid);

        $process = ProcessPeer::retrieveByPK($processData['pro_uid']);
        $process->setProName($process->getProName());
        $process->save();

        $savedProject = self::loadProject($prjUid);
        $diff = self::getDiffFromProjects($savedProject, $projectUpdated);

        self::updateDiagram($diff);
    }

    public static function updateDiagram($diff)
    {
        return false;

        // Creating new objects
        foreach ($diff['new'] as $element => $items) {
            foreach ($items as $data) {
                switch ($element) {
                    case 'activities':
                        $data = array_change_key_case((array) $data, CASE_UPPER);
                        $activity = new Activity();
                        $activity->create($data);
                        break;
                }
            }
        }

        //$activity = ActivityPeer::retrieveByPK($item);
    }

    public static function getDiffFromProjects($savedProject, $updatedProject)
    {
        // preparing target project
        $diagramElements = array(
            'act_uid' => 'activities',
            'evn_uid' => 'events',
            'flo_uid' => 'flows',
            'art_uid' => 'artifacts',
            'lns_uid' => 'laneset',
            'lan_uid' => 'lanes'
        );

        // Getting Differences
        $newRecords = array();
        $deletedRecords = array();
        $updatedRecords = array();

        // Get new records
        foreach ($diagramElements as $key => $element) {
            $arrayDiff = self::arrayDiff(
                $savedProject['diagrams'][0][$element],
                $updatedProject['diagrams'][0][$element],
                $key
            );

            if (! empty($arrayDiff)) {
                $newRecords[$element] = $arrayDiff;
            }
        }

        // Get deleted records
        foreach ($diagramElements as $key => $element) {
            $arrayDiff = self::arrayDiff(
                $updatedProject['diagrams'][0][$element],
                $savedProject['diagrams'][0][$element],
                $key
            );

            if (! empty($arrayDiff)) {
                $deletedRecords[$element] = $arrayDiff;
            }
        }

        // Get updated records
        $checksum = array();
        foreach ($diagramElements as $key => $element) {
            $checksum[$element] = self::getArrayChecksum($savedProject['diagrams'][0][$element], $key);
        }

        foreach ($diagramElements as $key => $element) {
            foreach ($updatedProject['diagrams'][0][$element] as $item) {
                if (in_array($item[$key], $newRecords[$element]) || in_array($item[$key], $deletedRecords[$element])) {
                    // skip new or deleted records
                    continue;
                }

                if (self::getChecksum($item) !== $checksum[$element][$item[$key]]) {
                    $updatedRecords[$element][] = $item;
                }
            }
        }

        $diff = array(
            'new' => $newRecords,
            'deleted' => $deletedRecords,
            'updated' => $updatedRecords
        );

        return $diff;
    }

    public static function getRelatedFlows($actUid)
    {
        $flows = array(
            'origin' => self::getBpmnCollectionBy('Flow', FlowPeer::FLO_ELEMENT_ORIGIN, $actUid, true),
            'dest' => self::getBpmnCollectionBy('Flow', FlowPeer::FLO_ELEMENT_DEST, $actUid, true)
        );

        return $flows;
    }


    /*** Private Functions ***/

    public static function getAllBpmnCollectionFrom($class, $changeCase = false)
    {
        $data = array();

        $c = new \Criteria('workflow');
        //$c->add($field, $value);

        $classPeer = 'Bpmn' . $class . 'Peer';
        $rs = $classPeer::doSelectRS($c);

        $rs->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        while ($rs->next()) {
            $data[] = $changeCase ? array_change_key_case($rs->getRow(), CASE_LOWER) : $rs->getRow();
        }

        return $data;
    }

    public static function select($select, $from, $where = '', $changeCase = false)
    {
        $data = array();

        $c = new \Criteria('workflow');
        if ($select != '*') {
            if (is_array($select)) {
                foreach ($select as $column) {
                    $c->addSelectColumn($column);
                }
            } else {
                $c->addSelectColumn($field);
            }
        }

        if (! empty($where)) {
            foreach ($where as $column => $value) {
                $c->add($column, $value);
            }
        }

        $classPeer = 'Bpmn' . $from . 'Peer';
        $rs = $classPeer::doSelectRS($c);

        $rs->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        while ($rs->next()) {
            $data[] = $changeCase ? array_change_key_case($rs->getRow(), CASE_LOWER) : $rs->getRow();
        }

        return $data;
    }

    public static function getBpmnCollectionBy($class, $field, $value, $changeCase = false)
    {
        $data = array();

        $c = new \Criteria('workflow');
        $c->add($field, $value);

        $classPeer = 'Bpmn' . $class . 'Peer';
        $rs = $classPeer::doSelectRS($c);

        $rs->setFetchmode(\ResultSet::FETCHMODE_ASSOC);

        while ($rs->next()) {
            $data[] = $changeCase ? array_change_key_case($rs->getRow(), CASE_LOWER) : $rs->getRow();
        }

        return $data;
    }

    public static function getBpmnObjectBy($class, $field, $value, $changeCase = false)
    {
        $record = self::getBpmnCollectionBy($class, $field, $value, $changeCase);

        return empty($record) ? null : $record[0];
    }

    private static function arrayDiff($list, $targetList, $key)
    {
        $uid = array();
        $diff = array();

        foreach ($list as $item) {
            if (array_key_exists($key, $item)) {
                $uid[] = $item[$key];
            }
        }

        foreach ($targetList as $item) {
            if (! in_array($item[$key], $uid)) {
                $diff[] = $item[$key];
            }
        }

        return $diff;
    }

    private static function getArrayChecksum($list, $key = null)
    {
        $checksum = array();

        foreach ($list as $k => $item) {
            if (empty($key)) {
                $checksum[$k] = self::getChecksum($item);
            } else {
                $checksum[$item[$key]] = self::getChecksum($item);
            }
        }

        return $checksum;
    }

    private static function getChecksum($data)
    {
        if (! is_string($data)) {
            $data = var_export($data, true);
        }

        return sha1($data);
    }
}

