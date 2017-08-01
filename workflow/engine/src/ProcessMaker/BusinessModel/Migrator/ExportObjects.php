<?php
namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Plugins\PluginRegistry;

class ExportObjects
{
    /**
     * @var array
     */
    protected $objectsList = array(
        'PROCESSDEFINITION' => 'Process Definition',
        'ASSIGNMENTRULES' => 'Assignment Rules',
        'VARIABLES' => 'Variables',
        'DYNAFORMS' => 'Dynaforms',
        'INPUTDOCUMENTS' => 'Input Documents',
        'OUTPUTDOCUMENTS' => 'Output Documents',
        'TRIGGERS' => 'Triggers',
        'REPORTTABLES' => 'Report Tables',
        'TEMPLATES' => 'Templates',
        'FILES' => 'Files',
        'DBCONNECTION' => 'DB Connection',
        'PERMISSIONS' => 'Permissions',
        'SUPERVISORS' => 'Supervisors',
        'SUPERVISORSOBJECTS' => 'Supervisors Objects'
    );

    /**
     * ExportObjects constructor.
     */
    public function __construct()
    {
        $this->objectsList = array_merge($this->objectsList, $this->processMigrablePlugins());
    }


    /**
     * @return array
     */
    public function getObjectsList()
    {
        return $this->objectsList;
    }

    /**
     * @param array $objectsList
     */
    public function setObjectsList($objectsList)
    {
        $this->objectsList = $objectsList;
    }

    /**
     * @param string $objectsEnable
     * @return mixed|string
     * @throws \Exception
     */
    public function objectList($objectsEnable = '')
    {
        $grid = [];
        try {
            $aObjectsEnable = explode('|', $objectsEnable);
            foreach ($this->objectsList as $key => $val) {
                $grid[] = array(
                    'OBJECT_ID' => strtoupper(str_replace(' ', '',$val)),
                    'OBJECT_NAME' => $val,
                    'OBJECT_ACTION' => 1,
                    'OBJECT_ENABLE' => in_array(strtoupper(str_replace(' ', '',$val)), $aObjectsEnable)
                );
            }
            $r = new \stdclass();
            $r->data = $grid;

            return \G::json_encode($r);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function processMigrablePlugins()
    {
        $plugins = array();
        $registry = PluginRegistry::loadSingleton();
        $data = $registry->getPluginsData();
        foreach ($data as $detail) {
            $detail = (array)$detail;
            if (isset($detail['bIsMigrable']) && $detail['bIsMigrable']) {
                $plugins[strtoupper($detail['sNamespace'])] = $detail['sNamespace'];
            }
        }
        return $plugins;
    }

    /**
     * @param $idObject
     * @return mixed
     * @throws \Exception
     */
    public function getObjectName($idObject)
    {
        try {
            return (str_replace(' ', '', $this->objectsList[$idObject]));

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $objects
     * @return array
     * @throws \Exception
     */
    public function mapObjectList($objects)
    {
        try {
            $mapObjectList = array();
            foreach ($objects as $objectId) {
                array_push($mapObjectList, strtoupper(str_replace(' ', '', $this->objectsList[$objectId])));
            }
            return $mapObjectList;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $objects
     * @return array
     * @throws \Exception
     */
    public function getIdObjectList($objects)
    {
        try {
            $idObjectList = array();
            foreach ($this->objectsList as $key => $val) {
                foreach ($objects as $row) {
                    if(strtoupper(str_replace(' ', '', $this->objectsList[$key])) === $row){
                        array_push($idObjectList, $row);
                    }
                }
            }
            return $idObjectList;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

