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
     * Load Objects List Selected
     */
    public function loadObjectsListSelected($data, $aGranular) {
        $objectList = array();
        if(in_array('PROCESSDEFINITION', $aGranular)){
            $objectList['PROCESSDEFINITION'] = $this->structureBpmnData($data['tables']['bpmn']);
        }
        if(in_array('ASSIGNMENTRULES', $aGranular)){
            $objectList['ASSIGNMENTRULES'] = $data['tables']['workflow']['tasks'];
        }
        if(in_array('VARIABLES', $aGranular)){
            $objectList['VARIABLES'] = $data['tables']['workflow']['processVariables'];
        }
        if(in_array('DYNAFORMS', $aGranular)){
            $objectList['DYNAFORMS'] = $data['tables']['workflow']['dynaforms'];
        }
        if(in_array('INPUTDOCUMENTS', $aGranular)){
            $objectList['INPUTDOCUMENTS'] = $data['tables']['workflow']['inputs'];
        }
        if(in_array('OUTPUTDOCUMENTS', $aGranular)){
            $objectList['OUTPUTDOCUMENTS'] = $data['tables']['workflow']['outputs'];
        }
        if(in_array('TRIGGERS', $aGranular)){
            $objectList['TRIGGERS'] = $data['tables']['workflow']['triggers'];
        }
        if(in_array('TEMPLATES', $aGranular)){
            $objectList['TEMPLATES']['TABLE'] = $data['tables']['workflow']['filesManager'];
            $objectList['TEMPLATES']['PATH']  = $data['files']['workflow'];
        }
        if(in_array('FILES', $aGranular)){
            $objectList['FILES']['TABLE'] = $data['tables']['workflow']['filesManager'];
            $objectList['FILES']['PATH']  = $data['files']['workflow'];
        }
        return $objectList;
    }

    /**
     * Update the structure from File
     */
    public function structureBpmnData(array $tables){
        $project = $tables["project"][0];
        $diagram = $tables["diagram"][0];
        $diagram["activities"] = (isset($tables["activity"])) ? $tables["activity"] : array();
        $diagram["artifacts"]  = (isset($tables["artifact"])) ? $tables["artifact"] : array();
        $diagram["events"]     = (isset($tables["event"])) ? $tables["event"] : array();
        $diagram["flows"]      = (isset($tables["flow"])) ? $tables["flow"] : array();
        $diagram["gateways"]   = (isset($tables["gateway"])) ? $tables["gateway"] : array();
        $diagram["data"]       = (isset($tables["data"]))? $tables["data"] : array();
        $diagram["participants"] = (isset($tables["participant"]))? $tables["participant"] : array();
        $diagram["laneset"]      = (isset($tables["laneset"]))? $tables["laneset"] : array();
        $diagram["lanes"]        = (isset($tables["lane"]))? $tables["lane"] : array();
        $project["diagrams"]     = array($diagram);
        $project["prj_author"]   = isset($this->data["usr_uid"])? $this->data["usr_uid"]: "00000000000000000000000000000001";
        $project["process"]      = $tables["process"][0];
        return $project;
    }

    /**
     * import
     */
    public function import($objectList)
    {
        foreach ($objectList as $key => $data) {
            $objClass = $this->factory->create($key);
            if(is_object($objClass)) {
                $migratorData = $objClass->import($data);
            }
        }
    }
}