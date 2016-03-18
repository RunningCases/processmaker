<?php
/**
 *
 *
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;
use Symfony\Component\Config\Definition\Exception\Exception;

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
        } catch (Exception $e) {
            Logger::log($e);
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

    public function export($prj_uid)
    {
        $oProcess = new \Process();
        $oData = new \StdClass();
        $oData->dynaforms = $oProcess->getDynaformRows($prj_uid);
        return $oData;
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}