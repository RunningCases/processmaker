<?php
/**
 * Created by PhpStorm.
 * User: gustav
 * Date: 3/18/16
 * Time: 10:14 AM
 */

namespace ProcessMaker\BusinessModel\Migrator;


class GranularExporter
{

    protected $factory;
    protected $data;
    /**
     * GranularExporter constructor.
     */
    public function __construct()
    {
        $this->factory = new MigratorFactory();
    }

    public function export($objectList)
    {
        foreach ($objectList as $key => $data) {
            $migrator = $this->factory->create($key);
            $migratorData = $migrator->export($data);
            $this->prepareData($migratorData);
        }
        return $this->data;
    }

    protected function prepareData($migratorData)
    {
        $this->data = $this->data . $migratorData;
    }
}