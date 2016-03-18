<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:31 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use Symfony\Component\Config\Definition\Exception\Exception;

class InputDocumentsMigrator implements Importable, Exportable
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
            $this->processes->createInputRows($data);
        } catch (\Exception $e) {
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
        $oData->inputs = $oProcess->getInputRows($prj_uid);
        return $oData;
    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}