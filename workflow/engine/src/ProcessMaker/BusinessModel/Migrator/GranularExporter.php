<?php
/**
 * ProcessMaker.
 */

namespace ProcessMaker\BusinessModel\Migrator;

class GranularExporter
{
    protected $factory;
    protected $publisher;
    protected $data;
    /**
     * GranularExporter constructor.
     */
    public function __construct()
    {
        $this->factory = new MigratorFactory();
        $this->publisher = new PMXGenerator();
    }

    public function export($objectList)
    {
        foreach ($objectList as $key => $data) {
            $migrator = $this->factory->create($key);
            $migratorData = $migrator->export($data);
            $this->prepareData($migratorData);
        }
        return $this->publish();

    }

    protected function beforeExport()
    {
        $data = array();
        $data["version"] = "3.0";
        $data["container"] = "ProcessMaker-Project";
        $data["metadata"] = $this->getMetadata();
        $data["metadata"]["workspace"] = defined("SYS_SYS") ? SYS_SYS : "Unknown";
        $data["metadata"]["name"] = $this->getProjectName();
        $data["metadata"]["uid"] = $this->getProjectUid();
        $this->data = $data;
    }

    protected function addData($migratorData)
    {



        //$this->data = $data;
    }

    public function publish()
    {
        return $this->generator->generate(
            $this->data
        );
    }
}