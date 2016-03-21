<?php
/**
 *
 *
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;

class DynaformsMigrator implements Importable, Exportable
{
    protected $processes;

    /**
     * DynaformsMigrator constructor.
     */
    public function __construct()
    {
        $this->processes = new \Processes();
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data)
    {
        try {
            $this->processes->createDynaformRows($data);
        } catch (\Exception $e) {
            \Logger::log($e);
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }

    public function beforeExport()
    {
        // TODO: Implement beforeExport() method.
    }

    /**
     * @param $prj_uid
     * @return array
     */
    public function export($prj_uid)
    {
        try {
            $oData = new \StdClass();
            $oData->dynaforms = $this->processes->getDynaformRows($prj_uid);
            $result = array(
                'workflow-definition' => (array)$oData
            );

            return $result;

        } catch (\Exception $e) {
            \Logger::log($e);
        }
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}