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

    public function structureBpmnData(array $tables){
        $project = $tables["project"][0];
        $diagram = $tables["diagram"][0];
        $diagram["activities"] = $tables["activity"];
        $diagram["artifacts"] = (isset($tables["artifact"]))? $tables["artifact"] : array();
        $diagram["events"] = $tables["event"];
        $diagram["flows"] = $tables["flow"];
        $diagram["gateways"] = $tables["gateway"];
        $diagram["data"] = (isset($tables["data"]))? $tables["data"] : array();
        $diagram["participants"] = (isset($tables["participant"]))? $tables["participant"] : array();
        $diagram["laneset"] = (isset($tables["laneset"]))? $tables["laneset"] : array();
        $diagram["lanes"] = (isset($tables["lane"]))? $tables["lane"] : array();
        $project["diagrams"] = array($diagram);
        $project["prj_author"] = isset($this->data["usr_uid"])? $this->data["usr_uid"]: "00000000000000000000000000000001";
        $project["process"] = $tables["process"][0];
        return $project;
    }

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