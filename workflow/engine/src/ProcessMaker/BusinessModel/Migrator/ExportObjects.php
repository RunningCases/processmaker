<?php
namespace ProcessMaker\BusinessModel\Migrator;

class ExportObjects
{
    /**
     * @var array
     */
    protected $objectsList = array(
        'Process Definition / Diagram',
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
     * @param string $pro_uid
     * @return mixed|string
     * @throws Exception
     * @throws \Exception
     */
    public function objectList($pro_uid = '')
    {
        try {
            foreach ($this->objectsList as $key => $val) {
                $key++;
                $grid[] = array('OBJECT_ID' => $key, 'OBJECT_NAME' => $val, 'OBJECT_ACTION' => 0);
            }

            $r = new \stdclass();
            $r->data = $grid;

            return \G::json_encode($r);
        } catch (Exception $e) {
            throw $e;
        }
    }
}

