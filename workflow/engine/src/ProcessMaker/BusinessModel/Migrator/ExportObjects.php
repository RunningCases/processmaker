<?php
namespace ProcessMaker\BusinessModel\Migrator;

class ExportObjects
{
    /**
     * @var array
     */
    protected $objectsList = array(
        'Process Definition',
        'Assignment Rules',
        'Variables',
        'Dynaforms',
        'Input Documents',
        'Output Documents',
        'Triggers',
        'Report Tables',
        'Templates',
        'Files',
        'DB Connection',
        'Permissions',
        'Supervisors',
        'Supervisors Objects'
    );

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
                    'OBJECT_ID' => $key+1,
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

    /**
     * @param $idObject
     * @return mixed
     * @throws \Exception
     */
    public function getObjectName($idObject)
    {
        try {
            return (str_replace(' ', '', $this->objectsList[$idObject - 1]));

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
                array_push($mapObjectList, strtoupper(str_replace(' ', '', $this->objectsList[$objectId - 1])));
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
                $key++;
                foreach ($objects as $row) {
                    if(strtoupper(str_replace(' ', '', $this->objectsList[$key - 1])) === $row){
                        array_push($idObjectList, $key);
                    }
                }
            }
            return $idObjectList;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

