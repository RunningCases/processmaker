<?php

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

    /**
     * @param $objectList
     * @return array|string
     * @throws \Exception
     */
    public function export($objectList)
    {
        try {
            $exportObject = new ExportObjects();
            $objectList = $exportObject->mapObjectList($objectList);
            $this->beforeExport($objectList);
            foreach ($objectList as $data) {
                $migrator = $this->factory->create($data);
                $migratorData = $migrator->export($this->prjuid);
                $this->mergeData($migratorData);
            }
            return $this->publish();
        } catch (ExportException $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    protected function beforeExport()
    {
        $objectList = func_get_args()[0];
        $bpmnProject = Project\Bpmn::load($this->prjuid);
        $projectData = $bpmnProject->getProject();
        $getProjectName = $this->publisher->truncateName($projectData['PRJ_NAME'], false);
        $outputDir = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "files" . PATH_SEP . "output" . PATH_SEP;
        $version = \ProcessMaker\Util\Common::getLastVersion($outputDir . $getProjectName . "-*.pmx") + 1;
        $outputFilename = $outputDir . sprintf("%s-%s.%s", str_replace(" ", "_", $getProjectName), $version, "pmx");

        $bpnmDefinition = array(
                        'ACTIVITY'      => [],
                        'ARTIFACT'      => [],
                        'BOUND'         => [],
                        'DATA'          => [],
                        'DIAGRAM'       => [],
                        'DOCUMENTATION' => [],
                        'EXTENSION'     => [],
                        'FLOW'          => [],
                        'GATEWAY'       => [],
                        'LANE'          => [],
                        'LANESET'       => [],
                        'PARTICIPANT'   => [],
                        'PROCESS'       => [],
                        'PROJECT'       => array(\BpmnProjectPeer::retrieveByPK($this->prjuid)->toArray())
        );
        $workflowDefinition = array(
                        'process'                => array(\Processes::getProcessRow($this->prjuid, false)),
                        'tasks'                  => [],
                        'routes'                 => [],
                        'lanes'                  => [],
                        'gateways'               => [],
                        'inputs'                 => [],
                        'outputs'                => [],
                        'dynaforms'              => [],
                        'steps'                  => [],
                        'triggers'               => [],
                        'taskusers'              => [],
                        'groupwfs'               => [],
                        'steptriggers'           => [],
                        'dbconnections'          => [],
                        'reportTables'           => [],
                        'reportTablesVars'       => [],
                        'stepSupervisor'         => [],
                        'objectPermissions'      => [],
                        'subProcess'             => [],
                        'caseTracker'            => [],
                        'caseTrackerObject'      => [],
                        'stage'                  => [],
                        'fieldCondition'         => [],
                        'event'                  => [],
                        'caseScheduler'          => [],
                        'processCategory'        => [],
                        'taskExtraProperties'    => [],
                        'processUser'            => [],
                        'processVariables'       => [],
                        'webEntry'               => [],
                        'webEntryEvent'          => [],
                        'messageType'            => [],
                        'messageTypeVariable'    => [],
                        'messageEventDefinition' => [],
                        'scriptTask'             => [],
                        'timerEvent'             => [],
                        'emailEvent'             => []
        );
        $data = array(
            'bpmn-definition' => $bpnmDefinition,
            'workflow-definition' => $workflowDefinition,
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
        $data["metadata"]["export_objects"] = implode('|', $objectList);
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