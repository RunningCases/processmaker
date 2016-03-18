<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/17/16
 * Time: 4:21 PM
 */

namespace ProcessMaker\BusinessModel\Migrator;

use Symfony\Component\Config\Definition\Exception\Exception;

class FilesMigrator implements Importable
{
    protected $processes;

    /**
     * FilesMigrator constructor.
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
            $this->processes->createFilesManager($data[0]['PRO_UID'],$data);
        } catch (\Exception $e) {
           Logger::log($e);
        }
    }

    public function afterImport($data)
    {
        // TODO: Implement afterImport() method.
    }
}