<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:38 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;


class SupervisorsMigrator implements Importable, Exportable
{
    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data)
    {

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
        $oProcess = new \Processes();
        $oData = new \StdClass();
        $oData->stepSupervisor = $oProcess->getStepSupervisorRows($prj_uid);
        return $oData;
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}