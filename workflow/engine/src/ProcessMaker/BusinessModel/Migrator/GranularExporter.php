<?php
/**
 * ProcessMaker.
 */

namespace ProcessMaker\BusinessModel\Migrator;
use \ProcessMaker\Exporter\XmlExporter;

class GranularExporter
{
    protected $factory;
    protected $publisher;
    protected $data;
    protected $prjuid;
    /**
     * GranularExporter constructor.
     */
    public function __construct($prj_uid)
    {
        $this->prjuid = $prj_uid;
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
        $exporter = new XmlExporter($this->prjuid);
        $getProjectName = $exporter->truncateName($exporter->getProjectName(), false);
        $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
        $version = \ProcessMaker\Util\Common::getLastVersion($outputDir . $getProjectName . "-*.pmx") + 1;
        $outputFilename = $outputDir . sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");
        $exporter->setMetadata("export_version", $version);

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