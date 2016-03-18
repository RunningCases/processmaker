<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:29 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;


class TriggersMigrator implements Importable, Exportable
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
        $process = new \Processes();
        $oData = new \StdClass();
        $oDataTasks = $process->getTaskRows($prj_uid);
        $oData->steptriggers = $process->getStepTriggerRows($oDataTasks);
        return $oData;
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}