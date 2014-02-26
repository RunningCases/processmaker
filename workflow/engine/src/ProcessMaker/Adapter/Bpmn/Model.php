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

use \ProcessMaker\Util\Logger;
use System;


/**
 * Class Model
 *
 * @package ProcessMaker\Adapter\Bpmn
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */
class Model
{
    private static $diagramElements = array(
        'act_uid' => 'activities',
        'evn_uid' => 'events',
        'flo_uid' => 'flows',
        'art_uid' => 'artifacts',
        'lns_uid' => 'laneset',
        'lan_uid' => 'lanes'
    );

    public function createProject($data, $replaceUids = false)
    {
        $data = array_change_key_case($data, CASE_UPPER);
        $uids = array();
        $diagrams = array_key_exists('DIAGRAMS', $data) && is_array($data['DIAGRAMS'])
            && count($data['DIAGRAMS']) > 0 ? $data['DIAGRAMS'] : null;
        $mapId = array();

        /*
         * 1. Create a project record
         * 2. Create a default diagram record
         * 3. Create a default process record
         */

        $project = new Project();
        $project->fromArray($data, BasePeer::TYPE_FIELDNAME);

        if (array_key_exists('PRJ_UID', $data)) {
            if ($replaceUids) {
                $oldPrjUid = $data['PRJ_UID'];
                $project->setPrjUid(Hash::generateUID());
            }
        } else {
            $project->setPrjUid(Hash::generateUID());
        }

        $project->setPrjCreateDate(date("Y-m-d H:i:s"));
        $project->save();
        $prjUid  = $project->getPrjUid();
        $prjName = $project->getPrjName();

        if ($replaceUids) {
            $uids[] = array('old_uid' => $oldPrjUid, 'new_uid' => $prjUid, 'object' => 'project');
        }

        /*if (! isset($diagrams)) {
            if ($replaceUids) {
                return $uids;
            } else {
                return self::loadProject($prjUid);
            }
        }*/

        $diagram = new Diagram();

        if (isset($diagrams) && array_key_exists('dia_uid', $diagrams[0])) {
            if ($replaceUids) {
                $oldDiaUid = $diagrams[0]['dia_uid'];
                $diagram->setDiaUid(Hash::generateUID());
            } else {
                $diagram->setDiaUid($diagrams[0]['dia_uid']);
            }
        } else {
            $diagram->setDiaUid(Hash::generateUID());
        }

        $diagram->setPrjUid($prjUid);
        $diagram->setDiaName($prjName);
        $diagram->save();
        $diaUid = $diagram->getDiaUid();

        if ($replaceUids) {
            $uids[] = array('old_uid' => (isset($oldDiaUid) ? $oldDiaUid : ''), 'new_uid' => $diaUid, 'object' => 'diagram');
        }

        $process = new Process();

        if (isset($diagrams) && array_key_exists('pro_uid', $diagrams[0])) {
            if ($replaceUids) {
                $oldProUid = $data['pro_uid'];
                $process->setProUid(Hash::generateUID());
            } else {
                $process->setProUid($diagrams[0]['pro_uid']);
            }
        } else {
            $process->setProUid(Hash::generateUID());
        }

        $process->setPrjUid($prjUid);
        $process->setDiaUid($diaUid);
        $process->setProName($prjName);
        $process->save();
        $proUid = $process->getProUid();

        if ($replaceUids) {
            $uids[] = array('old_uid' => (isset($oldProUid) ? $oldProUid : ''), 'new_uid' => $proUid, 'object' => 'project');
        }


        if (isset($diagrams)) {
            // By now, is thought create only one diagram for each project (1:1)
            $diagramData = (array) $diagrams[0];

            // there is not a defined diagram to save
            $diagramUids = $this->createDiagram($prjUid, $proUid, $diaUid, $diagramData, $replaceUids);

            if ($replaceUids) {
                $uids = array_merge($uids, $diagramUids);
            }
        }

        if ($replaceUids) {
            return $uids;
        } else {
            return self::loadProject($prjUid);
        }
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
        $lanes = array_key_exists('lanes', $diagramData) ? $diagramData['lanes'] : array();
        $activities = array_key_exists('activities', $diagramData) ? $diagramData['activities'] : array();
        $events = array_key_exists('events', $diagramData) ? $diagramData['events'] : array();
        $gateways = array_key_exists('gateways', $diagramData) ? $diagramData['gateways'] : array();
        $flows = array_key_exists('flows', $diagramData) ? $diagramData['flows'] : array();
        $artifacts = array_key_exists('artifacts', $diagramData) ? $diagramData['artifacts'] : array();

        foreach ($lanesets as $lanesetData) {
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

        foreach ($lanes as $laneData) {
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

        foreach ($activities as $activityData) {
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

        foreach ($events as $eventData) {
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

        foreach ($gateways as $gatewayData) {
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

        foreach ($flows as $flowData) {
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

        foreach ($artifacts as $artifactData) {
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

        if (empty($project)) {
            throw new \RuntimeException("Project with id: $prjUid, doesn't exist. ");
        }

        $process = self::getBpmnObjectBy('Process', ProcessPeer::PRJ_UID, $prjUid, true);
        $diagram = self::getBpmnObjectBy('Diagram', DiagramPeer::DIA_UID, $process['dia_uid'], true);

        $project = array_change_key_case($project);

        //if (! empty($diagram)) {
        $lanesets = self::getBpmnCollectionBy('Laneset', LanesetPeer::PRJ_UID, $prjUid, true);
        $lanes = self::getBpmnCollectionBy('Lane', LanePeer::PRJ_UID, $prjUid, true);

        //$activities = self::getBpmnCollectionBy('Activity', ActivityPeer::PRJ_UID, $prjUid, true);
        $activities = Activity::getAll($prjUid, null, null, null, 'object', CASE_LOWER);
        //$activities = Activity::getAll(array('prjUid' => $prjUid, 'changeCaseTo' => CASE_LOWER));
        //print_r($activities); die;

        $events = self::getBpmnCollectionBy('Event', EventPeer::PRJ_UID, $prjUid, true);
        $gateways = self::getBpmnCollectionBy('Gateway', GatewayPeer::PRJ_UID, $prjUid, true);
        $flows = self::getBpmnCollectionBy('Flow', FlowPeer::PRJ_UID, $prjUid, true);
        $artifacts = self::getBpmnCollectionBy('Artifact', ArtifactPeer::PRJ_UID, $prjUid, true);

        // getting activity bound data
        foreach ($activities as $i => $activity) {
            $bound = self::getBpmnObjectBy('Bound', BoundPeer::ELEMENT_UID, $activity['act_uid'], true);

            if (is_object($bound)) {
                $activities[$i] = array_merge($activities[$i], $bound);
            }
        }

        // getting event bound data
        foreach ($events as $i => $event) {
            $bound = self::getBpmnObjectBy('Bound', BoundPeer::ELEMENT_UID, $event['evn_uid'], true);

            if (is_object($bound)) {
                $events[$i] = array_merge($events[$i], $bound);
            }
        }

        // getting gateway bound data
        foreach ($gateways as $i => $gateway) {
            $bound = self::getBpmnObjectBy('Bound', BoundPeer::ELEMENT_UID, $gateway['gat_uid'], true);

            if (is_object($bound)) {
                $gateways[$i] = array_merge($gateways[$i], $bound);
            }
        }

        $project['diagrams'] = array($diagram);
        $project['diagrams'][0]['pro_uid'] = $process['pro_uid'];
        $project['diagrams'][0]['laneset'] = $lanesets;
        $project['diagrams'][0]['lanes'] = $lanes;
        $project['diagrams'][0]['activities'] = $activities;
        $project['diagrams'][0]['events'] = $events;
        $project['diagrams'][0]['gateways'] = $gateways;
        $project['diagrams'][0]['flows'] = $flows;
        $project['diagrams'][0]['artifacts'] = $artifacts;
        //}

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

        //print_r($project->toArray());

        $diagramData = $projectUpdated['diagrams'][0];

        //print_r($diagramData); die;

        $diagram = DiagramPeer::retrieveByPK($diagramData['dia_uid']);

        if (! is_object($diagram)) {
            throw new \RuntimeException("Project's diagram with id: {$diagramData['dia_uid']}, doesn't exist.");
        }

        if (array_key_exists('dia_name', $diagramData)) {
            $diagram->setDiaName($diagramData['dia_name']);
        }

        if (!empty($diagramData['dia_is_closable'])) {
            $diagram->setDiaIsClosable($diagramData['dia_is_closable']);
        }

        $diagram->save();

        $processData = self::getBpmnObjectBy('Process', ProcessPeer::PRJ_UID, $prjUid, true);

        //print_r($processData); die;

        $process = ProcessPeer::retrieveByPK($processData['pro_uid']);

        //print_r($process); die;

        $process->setProName($process->getProName());
        $process->save();

        $savedProject = self::loadProject($prjUid);
        $diff = self::getDiffFromProjects($savedProject, $projectUpdated);

        $result = self::updateDiagram($prjUid, $process->getProUid(), $diff);

        self::log("Method: ".__METHOD__, 'Returns: ', $result);

        return $result;
    }

    public static function updateDiagram($prjUid, $proUid, $diff)
    {
        self::log("Method: ".__METHOD__, 'Params: ', "\$prjUid: $prjUid", "\$proUid: $proUid", "\$diff:", $diff);

        //return false;
        $uids = array();

        /*
         * Updating Records
         */
        foreach ($diff['updated'] as $element => $items) {
            foreach ($items as $data) {
                $data = array_change_key_case((array) $data, CASE_UPPER);

                // the calls in switch sentence are setting and saving the related BpmnBound objects too,
                // because methods: save(), fromArray(), toArray() are beautifully extended

                switch ($element) {
                    case 'laneset':
                        break;
                    case 'lanes':
                        break;
                    case 'activities':
                        $activity = ActivityPeer::retrieveByPk($data['ACT_UID']);

                        // fixing data
                        //$data['ELEMENT_UID'] = $data['BOU_ELEMENT_UID'];
                        //unset($data['BOU_ELEMENT_UID']);

                        $activity->fromArray($data);
                        $activity->save();
                        break;
                    case 'events':
                        $event = EventPeer::retrieveByPk($data['EVN_UID']);
                        $event->fromArray($data);
                        $event->save();
                        break;
                    case 'gateways':
                        $gateway = GatewayPeer::retrieveByPk($data['GAT_UID']);
                        $gateway->fromArray($data);
                        $gateway->save();
                        break;
                    case 'flows':
                        break;
                    case 'artifacts':
                        break;
                }
            }
        }

        /*
         * Deleting Records
         */
        foreach ($diff['deleted'] as $element => $items) {
            foreach ($items as $uid) {
                $data = array_change_key_case((array) $data, CASE_UPPER);

                switch ($element) {
                    case 'laneset':
                        break;
                    case 'lanes':
                        break;
                    case 'activities':
                        $activity = ActivityPeer::retrieveByPK($uid);
                        $activity->delete();
                        break;
                    case 'events':
                        break;
                    case 'gateways':
                        break;
                    case 'flows':
                        break;
                    case 'artifacts':
                        break;
                }
            }
        }

        /*
         * Creating new records
         */
        foreach ($diff['new'] as $element => $items) {
            foreach ($items as $data) {
                $data = array_change_key_case((array) $data, CASE_UPPER);

                switch ($element) {
                    case 'laneset':
                        break;
                    case 'lanes':
                        break;
                    case 'activities':
                        $uidData = array('old_uid' => $data['ACT_UID'], 'object' => 'Activity');

                        $activity = new Activity();
                        $activity->fromArray($data, BasePeer::TYPE_FIELDNAME);
                        $activity->setActUid(Hash::generateUID());
                        $activity->setPrjUid($prjUid);
                        $activity->setProUid($proUid);
                        $activity->getBound()->setBouUid(Hash::generateUID());
                        $activity->save();

                        $uidData['new_uid'] = $activity->getActUid();
                        $uids[] = $uidData;
                        break;
                    case 'events':
                        break;
                    case 'gateways':
                        break;
                    case 'flows':
                        break;
                    case 'artifacts':
                        break;
                }
            }
        }

        return $uids;
    }

    public function deleteProject($prjUid)
    {
        $projectData = self::loadProject($prjUid);

        // TODO first at all, make validation, the project can only deleted if there are not any started case for it

        // Delete related objects
        $diagramData = $projectData['diagrams'][0];

        foreach ($diagramData['flows'] as $data) {
            $flow = FlowPeer::retrieveByPK($data['flo_uid']);
            $flow->delete();
        }
        foreach ($diagramData['artifacts'] as $data) {
            $artifact = ArtifactPeer::retrieveByPK($data['art_uid']);
            $artifact->delete();
        }
        foreach ($diagramData['lanes'] as $data) {
            $lane = LanePeer::retrieveByPK($data['lan_uid']);
            $lane->delete();
        }
        foreach ($diagramData['laneset'] as $data) {
            $laneset = LanesetPeer::retrieveByPK($data['lns_uid']);
            $laneset->delete();
        }
        foreach ($diagramData['laneset'] as $data) {
            $laneset = LanesetPeer::retrieveByPK($data['lns_uid']);
            $laneset->delete();
        }
        foreach ($diagramData['activities'] as $data) {
            $activity = ActivityPeer::retrieveByPK($data['act_uid']);
            $activity->delete();
        }
        foreach ($diagramData['events'] as $data) {
            $event = EventPeer::retrieveByPK($data['evn_uid']);
            $event->delete();
        }
        foreach ($diagramData['gateways'] as $data) {
            $gateway = GatewayPeer::retrieveByPK($data['gat_uid']);
            $gateway->delete();
        }

        $process = ProcessPeer::retrieveByPK($diagramData['pro_uid']);
        $process->delete();

        $diagram = DiagramPeer::retrieveByPK($diagramData['dia_uid']);
        $diagram->delete();

        $project = ProjectPeer::retrieveByPK($prjUid);
        $project->delete();

    }


    /*
     * Others functions
     */

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
        $newRecordsUids = array();
        $deletedRecords = array();
        $updatedRecords = array();

        // Get new records
        foreach ($diagramElements as $key => $element) {
            if (! array_key_exists($element, $updatedProject['diagrams'][0])) {
                continue;
            }

            /*print_r($savedProject['diagrams'][0][$element]);
            print_r($updatedProject['diagrams'][0][$element]);
            var_dump($key);*/

            $arrayDiff = self::arrayDiff(
                $savedProject['diagrams'][0][$element],
                $updatedProject['diagrams'][0][$element],
                $key
            );

            if (! empty($arrayDiff)) {
                $newRecordsUids[$element] = $arrayDiff;

                foreach ($updatedProject['diagrams'][0][$element] as $item) {
                    if (in_array($item[$key], $newRecordsUids[$element])) {
                        $newRecords[$element][] = $item;
                    }
                }
            }
        }

        // Get deleted records
        foreach ($diagramElements as $key => $element) {
            if (! array_key_exists($element, $updatedProject['diagrams'][0])) {
                continue;
            }

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
            if (! array_key_exists($element, $updatedProject['diagrams'][0])) {
                continue;
            }

            foreach ($updatedProject['diagrams'][0][$element] as $item) {
                if ((array_key_exists($element, $newRecordsUids) && in_array($item[$key], $newRecordsUids[$element])) ||
                    (array_key_exists($element, $deletedRecords) && in_array($item[$key], $deletedRecords[$element]))
                ) {
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
            $data = ksort(var_export($data, true));
        }

        return sha1($data);
    }

    protected static function log()
    {
        if (System::isDebugMode()) {

            $me = Logger::getInstance();
            $args = func_get_args();
            //array_unshift($args, 'Class '.__CLASS__.' ');

            call_user_func_array(array($me, 'setLog'), $args);
        }
    }
}

