<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/18/16
 * Time: 10:30 AM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use Symfony\Component\Config\Definition\Exception\Exception;

class AssignmentRulesMigrator implements Importable
{
    protected $processes;

    /**
     * AssignmentRulesMigrator constructor.
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
            $this->processes->createTaskRows($data);
        } catch (\Exception $e) {
           Logger::log($e);
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }

}