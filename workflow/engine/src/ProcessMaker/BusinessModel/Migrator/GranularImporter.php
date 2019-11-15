<?php
/**
 * Description of Granular Importer
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\BusinessModel\Migrator\ProcessDefinitionMigrator;
use ProcessMaker\Importer\XmlImporter;
use ProcessMaker\Project\Adapter;
use ProcessMaker\Project\Workflow;


class GranularImporter
{

    protected $factory;
    protected $data;
    protected $regeneratedUids;
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
            array_push(
                $listObjectGranular,
                array(
                    "name" => strtoupper(
                        $this->exportObjects->getObjectName($rowObject->id)
                    ),
                    "data" => [],
                    "value" => $rowObject->action
                )
            );
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
                    ($data['tables']['bpmn']) : [];
                $objectList['PROCESSDEFINITION']['workflow'] = isset($data['tables']['workflow']) ? 
                        $data['tables']['workflow'] : [];
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
                $prjUID = isset($data['tables']['workflow']['process']['PRO_UID'])
                    ?$data['tables']['workflow']['process']['PRO_UID']
                    :$data['tables']['workflow']['process'][0]['PRO_UID'];
                $objectList[$nameObject] = [];
                $objectList[$nameObject]['metadata'] = [
                    'PRJ_UID' => $prjUID,
                    'REGENERATED_UIDS' => $this->regeneratedUids
                ];
                foreach ($data['tables']['plugins'] as $pluginKey => $pluginTable) {
                    $key = explode(".", $pluginKey);
                    if ($key[0]===strtolower($nameObject)) {
                        $objectList[$nameObject][$key[1]] = $pluginTable;
                    }
                }
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
        $diagram["activities"] = (isset($tables["activity"])) ? $tables["activity"] : [];
        $diagram["artifacts"] = (isset($tables["artifact"])) ? $tables["artifact"] : [];
        $diagram["events"] = (isset($tables["event"])) ? $tables["event"] : [];
        $diagram["flows"] = (isset($tables["flow"])) ? $tables["flow"] : [];
        $diagram["gateways"] = (isset($tables["gateway"])) ? $tables["gateway"] : [];
        $diagram["data"] = (isset($tables["data"])) ? $tables["data"] : [];
        $diagram["participants"] = (isset($tables["participant"])) ? $tables["participant"] : [];
        $diagram["laneset"] = (isset($tables["laneset"])) ? $tables["laneset"] : [];
        $diagram["lanes"] = (isset($tables["lane"])) ? $tables["lane"] : [];
        $project["diagrams"] = array($diagram);
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
            if (\PMLicensedFeatures::getSingleton()->verifyfeature
            ("jXsSi94bkRUcVZyRStNVExlTXhEclVadGRRcG9xbjNvTWVFQUF3cklKQVBiVT0=")
            ) {
                $objectList = $this->reorderImportOrder($objectList);
                foreach ($objectList as $data) {
                    $objClass = $this->factory->create($data['name']);
                    if (is_object($objClass)) {
                        $dataImport = $data['data'][$data['name']];
                        $replace = ($data['value'] == 'replace') ? true : false;
                        $objClass->beforeImport($dataImport);
                        $migratorData = $objClass->import($dataImport, $replace);
                        $objClass->afterImport($dataImport);
                    }
                }
            } else {
                $exception = new ImportException();
                $exception->setNameException(\G::LoadTranslation('ID_NO_LICENSE_SELECTIVEIMPORTEXPORT_ENABLED'));
                throw($exception);
            }

        } catch (\Exception $e) {
            if (get_class($e) === 'ProcessMaker\BusinessModel\Migrator\ImportException') {
                throw $e;
            } else {
                $exception = new ImportException('Please review your current process definition
                for missing elements, it\'s recommended that a new process should be exported
                with all the elements.');
                throw $exception;
            }
        }
    }

    /**
     * @param $objectList
     * @param $option
     * @return bool
     * @throws \Exception
     */
    public function validateImportData($objectList, $option)
    {
        try {
            if (XmlImporter::IMPORT_OPTION_OVERWRITE !== $option) {
                $nativeElements = array(
                    'PROCESSDEFINITION',
                    'ASSIGNMENTRULES',
                    'VARIABLES',
                    'DYNAFORMS',
                    'INPUTDOCUMENTS',
                    'OUTPUTDOCUMENTS',
                    'TRIGGERS',
                    'REPORTTABLES',
                    'TEMPLATES',
                    'FILES',
                    'DBCONNECTION',
                    'PERMISSIONS',
                    'SUPERVISORS',
                    'SUPERVISORSOBJECTS'
                );
                foreach ($nativeElements as $element) {
                    $found = false;
                    foreach($objectList as $object) {
                        if ($element == $object->id) {
                            $found = true;
                        }
                    }
                    if (!$found) {
                        $exception = new ImportException();
                        $exception->setNameException(\G::LoadTranslation('ID_PROCESS_DEFINITION_INCOMPLETE'));
                        throw($exception);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * It's very important to import the elements in the right order, if not
     * some strange behavior will occur during import and export.
     * @param $objectList
     */
    public function reorderImportOrder($objectList)
    {
        $arrangeList = array(
            0 => 12,
            1 => 13,
            2 => 0,
            3 => 1,
            4 => 2,
            5 => 3,
            6 => 4,
            7 => 5,
            8 => 6,
            9 => 7,
            10 => 8,
            11 => 9,
            12 => 10,
            13 => 11
        );
        $orderedList = array();
        foreach ($arrangeList as $objectOrder => $executionOrder) {
            if (!empty($objectList[$objectOrder])) {
                $orderedList[$executionOrder] = $objectList[$objectOrder];
            }
        }

        for ($j=count($arrangeList); $j<count($objectList); $j++) {
            $orderedList[$j] = $objectList[$j];
        }
        ksort($orderedList);
        return $orderedList;
    }

    /**
     * Regenerate All UIDs of process and Import
     * 
     * @param array $data
     * @param bool $generateUid
     * @return array
     * @throws \Exception
     */
    public function regenerateAllUids($data, $generateUid = true)
    {
        try {
            $newData = [];
            $arrayBpmnTables = $data['tables']['bpmn'];
            $arrayWorkflowTables = $data['tables']['workflow'];
            $arrayWorkflowFiles = $data['files']['workflow'];
            $arrayBpmnTablesFormat = $this->structureBpmnData($arrayBpmnTables);
            $arrayBpmnTablesFormat['prj_type'] = $arrayWorkflowTables['process']['PRO_TYPE'];
            $arrayBpmnTablesFormat['pro_status'] = $arrayWorkflowTables['process']['PRO_STATUS'];
            $result = $this->bpmn->createFromStruct($arrayBpmnTablesFormat, $generateUid);
            $projectUidOld = $arrayBpmnTables['project'][0]['prj_uid'];
            $projectUid = ($generateUid) ? $result[0]['new_uid'] : $result;
            if ($generateUid) {
                $result[0]['object'] = 'project';
                $result[0]['old_uid'] = $projectUidOld;
                $result[0]['new_uid'] = $projectUid;

                $workflow = new Workflow();

                list($arrayWorkflowTables, $arrayWorkflowFiles) = $workflow->updateDataUidByArrayUid($arrayWorkflowTables, $arrayWorkflowFiles, $result);
            }
            $newData['tables']['workflow'] = $arrayWorkflowTables;
            $newData['tables']['plugins'] = isset($data['tables']['plugins']) ? $data['tables']['plugins'] : [];
            $newData['files']['workflow'] = $arrayWorkflowFiles;

            //Update Process Definition after import
            //@todo We need check and improve the "Granular Importer", some methods and classes probably can be simplified.
            $definition = new ProcessDefinitionMigrator();
            $definition->afterImport($newData['tables']);

            $this->regeneratedUids = $result;

            return [
                'data' => $newData,
                'new_uid' => $projectUid
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }
}