<?php
/**
 * Description of Granular Importer
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Project\Adapter;

class GranularImporter
{

    protected $factory;
    protected $data;
    /**
     * GranularImporter constructor.
     */
    public function __construct()
    {
        $this->factory = new MigratorFactory();
        $this->bpmn = new Adapter\BpmnWorkflow();
        $this->exportObjects = new ExportObjects();
    }

    /**
     * @param $data
     * @param $aGranular
     * @return array
     * @throws \Exception
     */
    public function loadObjectsListSelected($data, $aGranular)
    {
        $listObjectGranular = array();
        $this->exportObjects = new ExportObjects();
        //create structure
        foreach ($aGranular as $key => $rowObject) {
            array_push($listObjectGranular, array("name" => strtoupper($this->exportObjects->getObjectName
            ($rowObject->id)), "data" => "", "value" => $rowObject->action));
        }
        //add data
        foreach ($listObjectGranular as $key => $rowObject) {
            $listObjectGranular[$key]['data'] = $this->addObjectData($listObjectGranular[$key]['name'], $data);
        }
        return $listObjectGranular;
    }

    /**
     * @param $nameObject
     * @param $data
     * @return array
     */
    public function addObjectData($nameObject, $data)
    {
        $objectList = array();
        switch ($nameObject) {
            case 'PROCESSDEFINITION':
                $objectList['PROCESSDEFINITION']['bpmn'] = isset($data['tables']['bpmn']) ? $this->structureBpmnData
                ($data['tables']['bpmn']) : '';
                $objectList['PROCESSDEFINITION']['workflow'] = isset($data['tables']['workflow']) ?
                    $data['tables']['workflow'] : '';
                break;
            case 'ASSIGNMENTRULES':
                $objectList['ASSIGNMENTRULES']['tasks'] = isset($data['tables']['workflow']['tasks']) ?
                    $data['tables']['workflow']['tasks'] : [];
                $objectList['ASSIGNMENTRULES']['taskusers'] = isset($data['tables']['workflow']['taskusers']) ?
                    $data['tables']['workflow']['taskusers'] : [];
                $objectList['ASSIGNMENTRULES']['groupwfs'] = isset($data['tables']['workflow']['groupwfs']) ?
                    $data['tables']['workflow']['groupwfs'] : [];
                break;
            case 'VARIABLES':
                $objectList['VARIABLES'] = isset($data['tables']['workflow']['processVariables']) ?
                    $data['tables']['workflow']['processVariables'] : '';
                break;
            case 'DYNAFORMS':
                $objectList['DYNAFORMS'] = isset($data['tables']['workflow']['dynaforms']) ?
                    $data['tables']['workflow']['dynaforms'] : '';
                break;
            case 'INPUTDOCUMENTS':
                $objectList['INPUTDOCUMENTS'] = isset($data['tables']['workflow']['inputs']) ?
                    $data['tables']['workflow']['inputs'] : '';
                break;
            case 'OUTPUTDOCUMENTS':
                $objectList['OUTPUTDOCUMENTS'] = isset($data['tables']['workflow']['outputs']) ?
                    $data['tables']['workflow']['outputs'] : '';
                break;
            case 'TRIGGERS':
                $objectList['TRIGGERS'] = isset($data['tables']['workflow']['triggers']) ?
                    $data['tables']['workflow']['triggers'] : '';
                break;
            case 'TEMPLATES':
                $objectList['TEMPLATES']['TABLE'] = isset($data['tables']['workflow']['filesManager']) ?
                    $data['tables']['workflow']['filesManager'] : '';
                $objectList['TEMPLATES']['PATH'] = isset($data['files']['workflow']) ? $data['files']['workflow'] : '';
                break;
            case 'FILES':
                $objectList['FILES']['TABLE'] = isset($data['tables']['workflow']['filesManager']) ?
                    $data['tables']['workflow']['filesManager'] : '';
                $objectList['FILES']['PATH'] = isset($data['files']['workflow']) ? $data['files']['workflow'] : '';
                break;
            case 'DBCONNECTION':
            case 'DBCONNECTIONS':
                $objectList['DBCONNECTION'] = isset($data['tables']['workflow']['dbconnections']) ?
                    $data['tables']['workflow']['dbconnections'] : '';
                break;
            case 'PERMISSIONS':
                $objectList['PERMISSIONS']['objectPermissions'] = isset($data['tables']['workflow']['objectPermissions']) ?
                    $data['tables']['workflow']['objectPermissions'] : '';
                $objectList['PERMISSIONS']['groupwfs'] = isset($data['tables']['workflow']['groupwfs']) ?
                    $data['tables']['workflow']['groupwfs'] : '';
                break;
            case 'SUPERVISORS':
                $objectList['SUPERVISORS']['processUser'] = isset($data['tables']['workflow']['processUser']) ?
                    $data['tables']['workflow']['processUser'] : '';
                $objectList['SUPERVISORS']['groupwfs'] = isset($data['tables']['workflow']['groupwfs']) ?
                    $data['tables']['workflow']['groupwfs'] : '';
                break;
            case 'SUPERVISORSOBJECTS':
                $objectList['SUPERVISORSOBJECTS'] = isset($data['tables']['workflow']['stepSupervisor']) ?
                    $data['tables']['workflow']['stepSupervisor'] : '';
                break;
            case 'REPORTTABLES':
                $objectList['REPORTTABLES']['reportTablesDefinition'] = isset($data['tables']['workflow']['reportTablesDefinition']) ?
                    $data['tables']['workflow']['reportTablesDefinition'] : [];
                $objectList['REPORTTABLES']['reportTablesFields'] = isset($data['tables']['workflow']['reportTablesFields']) ?
                    $data['tables']['workflow']['reportTablesFields'] : [];
                break;
            default:
                break;
        }
        return $objectList;
    }

    /**
     * Update the structure from File
     */
    public function structureBpmnData(array $tables)
    {
        $project = $tables["project"][0];
        $diagram = $tables["diagram"][0];
        $diagram["activities"] = (isset($tables["activity"])) ? $tables["activity"] : array();
        $diagram["artifacts"] = (isset($tables["artifact"])) ? $tables["artifact"] : array();
        $diagram["events"] = (isset($tables["event"])) ? $tables["event"] : array();
        $diagram["flows"] = (isset($tables["flow"])) ? $tables["flow"] : array();
        $diagram["gateways"] = (isset($tables["gateway"])) ? $tables["gateway"] : array();
        $diagram["data"] = (isset($tables["data"])) ? $tables["data"] : array();
        $diagram["participants"] = (isset($tables["participant"])) ? $tables["participant"] : array();
        $diagram["laneset"] = (isset($tables["laneset"])) ? $tables["laneset"] : array();
        $diagram["lanes"] = (isset($tables["lane"])) ? $tables["lane"] : array();
        $project["diagrams"] = array($diagram);
        $project["prj_author"] = isset($this->data["usr_uid"]) ? $this->data["usr_uid"] : "00000000000000000000000000000001";
        $project["process"] = $tables["process"][0];
        return $project;
    }

    /**
     * @param $objectList
     * @throws \Exception
     */
    public function import($objectList)
    {
        try {
            foreach ($objectList as $data) {
                $objClass = $this->factory->create($data['name']);
                if (is_object($objClass)) {
                    $dataImport = $data['data'][$data['name']];
                    $replace = ($data['value'] == 'replace') ? true : false;
                    $migratorData = $objClass->import($dataImport, $replace);
                }
            }
        } catch (\Exception $e) {
            $exception = new ImportException('Please review your current process definition
                for missing elements, it\'s recommended that a new process should be exported
                with all the elements.');
            throw $exception;
        }
    }

    /**
     * @param $objectList
     * @param bool $generateUid
     * @return bool
     * @throws \Exception
     */
    public function validateImportData($objectList, $generateUid = false)
    {
        try {

            if ($generateUid) {
                if (count($objectList) !== count($this->exportObjects->getObjectsList())) {
                    $exception = new ImportException();
                    $exception->setNameException(\G::LoadTranslation('ID_PROCESS_DEFINITION_INCOMPLETE'));
                    throw($exception);
                }
            }
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $objectList
     * @param array $data
     * @return data
     * @throws \Exception
     */
    public function regenerateAllUids($data, $generateUid = true)
    {
        try {
            $newData = array();
            $arrayBpmnTables     = $data["tables"]["bpmn"];
            $arrayWorkflowTables = $data["tables"]["workflow"];
            $arrayWorkflowFiles  = $data["files"]["workflow"];
            $result = $this->bpmn->createFromStruct($this->structureBpmnData($arrayBpmnTables), $generateUid);
            $projectUidOld = $arrayBpmnTables["project"][0]["prj_uid"];
            $projectUid = ($generateUid)? $result[0]["new_uid"] : $result;
            if ($generateUid) {
                $result[0]["object"]  = "project";
                $result[0]["old_uid"] = $projectUidOld;
                $result[0]["new_uid"] = $projectUid;

                $workflow = new \ProcessMaker\Project\Workflow();

                list($arrayWorkflowTables, $arrayWorkflowFiles) = $workflow->updateDataUidByArrayUid($arrayWorkflowTables, $arrayWorkflowFiles, $result);
            }
            $newData['tables']['workflow'] = $arrayWorkflowTables;
            $newData['files']['workflow']  = $arrayWorkflowFiles;

            return array(
            'data'    => $newData,
            'new_uid' => $projectUid);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}