<?php
/**
 *
 *
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;
use Symfony\Component\Config\Definition\Exception\Exception;

class DynaformsMigrator implements Importable
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

}