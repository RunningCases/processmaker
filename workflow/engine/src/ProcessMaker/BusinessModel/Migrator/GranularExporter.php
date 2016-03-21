<?php
/**
 * ProcessMaker.
 */

namespace ProcessMaker\BusinessModel\Migrator;

use ProcessMaker\Project;

class GranularExporter
{

    protected $factory;
    protected $publisher;
    protected $generator;
    protected $data;
    protected $prjuid;
    /**
     * GranularExporter constructor.
     */
    public function __construct($prj_uid)
    {
        $this->prjuid = $prj_uid;
        $this->factory = new MigratorFactory();
        $this->generator = new PMXGenerator();
        $this->publisher = new PMXPublisher();
    }

    public function export($objectList)
    {
        $this->beforeExport();
        $exportObject = new ExportObjects();
        $objectList = $exportObject->mapObjectList($objectList);
        foreach ($objectList as $data) {
            $migrator = $this->factory->create($data);
            $migratorData = $migrator->export($this->prjuid);
            $this->mergeData($migratorData);
        }
        return $this->publish();
    }

    protected function beforeExport()
    {
        $bpmnProject = Project\Bpmn::load($this->prjuid);
        $projectData = $bpmnProject->getProject();
        $getProjectName = $this->publisher->truncateName($projectData['PRJ_NAME'], false);
        $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
        $version = \ProcessMaker\Util\Common::getLastVersion($outputDir . $getProjectName . "-*.pmx") + 1;
        $outputFilename = $outputDir . sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");

        $data = array(
            'bpmn-definition' => [],
            'workflow-definition' => [],
            'workflow-files' => []
        );

        $data["filename"] = $outputFilename;
        $data["version"] = "3.1";
        $data["container"] = "ProcessMaker-Project";
        $data["metadata"] = array(
            "vendor_version" => \System::getVersion(),
            "vendor_version_code" => "Michelangelo",
            "export_timestamp" => date("U"),
            "export_datetime" => date("Y-m-d\TH:i:sP"),
            "export_server_addr" => isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"].":".$_SERVER["SERVER_PORT"] : "Unknown",
            "export_server_os" => PHP_OS ,
            "export_server_php_version" => PHP_VERSION_ID,
        );
        $data["metadata"]["workspace"] = defined("SYS_SYS") ? SYS_SYS : "Unknown";
        $data["metadata"]["name"] = $projectData['PRJ_NAME'];
        $data["metadata"]["uid"] = $projectData['PRJ_UID'];
        $data["metadata"]["export_version"] = $version;
        $this->data = $data;
    }

    protected function mergeData($migratorData)
    {
        $this->data = array_merge_recursive($this->data, $migratorData);
    }

    public function publish()
    {
        return $this->publisher->publish(
            $this->data['filename'],
            $this->generator->generate(
                $this->data
            )
        );
    }
}