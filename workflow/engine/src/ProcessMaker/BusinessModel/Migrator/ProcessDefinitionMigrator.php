<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/18/16
 * Time: 10:28 AM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Project\Adapter;
use Symfony\Component\Config\Definition\Exception\Exception;

class ProcessDefinitionMigrator implements Importable, Exportable
{
    protected $bpmn;

    /**
     * DynaformsMigrator constructor.
     */
    public function __construct()
    {
        $this->bpmn = new Adapter\BpmnWorkflow();
    }

    public function beforeImport($data)
    {
        // TODO: Implement beforeImport() method.
    }

    public function import($data)
    {
        try {
            $this->bpmn->createFromStruct($data);
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

    }

    public function afterExport()
    {
        // TODO: Implement afterExport() method.
    }

}