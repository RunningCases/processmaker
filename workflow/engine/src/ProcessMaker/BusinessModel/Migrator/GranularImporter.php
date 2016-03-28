<?php
/**
 * Description of Granular Importer
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;

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
        $exportObjects = new ExportObjects();
        //create structure
        foreach ($aGranular as $key => $rowObject) {
            array_push($listObjectGranular, array("name" => strtoupper($exportObjects->getObjectName
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
                $objectList['PROCESSDEFINITION'] = isset($data['tables']['bpmn']) ? $this->structureBpmnData
                ($data['tables']['bpmn']) : '';
                break;
            case 'ASSIGNMENTRULES':
                $objectList['ASSIGNMENTRULES'] = isset($data['tables']['workflow']['tasks']) ?
                    $data['tables']['workflow']['tasks'] : '';
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
                $objectList['PERMISSIONS'] = isset($data['tables']['workflow']['objectPermissions']) ?
                    $data['tables']['workflow']['objectPermissions'] : '';
                break;
            case 'SUPERVISORS':
                $objectList['SUPERVISORS'] = isset($data['tables']['workflow']['processUser']) ?
                    $data['tables']['workflow']['processUser'] : '';
                break;
            case 'SUPERVISORSOBJECTS':
                $objectList['SUPERVISORSOBJECTS'] = isset($data['tables']['workflow']['stepSupervisor']) ?
                    $data['tables']['workflow']['stepSupervisor'] : '';
                break;
            case 'REPORTTABLES':
                $objectList['REPORTTABLES'] = isset($data['tables']['workflow']['reportTables']) ?
                    $data['tables']['workflow']['reportTables'] : '';
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
     * @return array
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
        } catch (ExportException $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * @param $objectList
     * @param bool|false $generateUid
     * @return array|bool
     */
    public function validateImportData($objectList, $generateUid = false)
    {
        try {
            if($generateUid){
                foreach ($objectList as $rowObject) {
                    if($rowObject['name'] === 'PROCESSDEFINITION' && $rowObject['value'] == "replace"){
                        return true;
                    }
                }
                return false;
            }
            return true;
        } catch (ExportException $e) {
            return array(
                    'success' => false,
                    'message' => $e->getMessage()
            );
        }
    }
}