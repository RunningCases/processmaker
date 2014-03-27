<?php
namespace ProcessMaker\Importer;

use ProcessMaker\Project\Adapter;
use ProcessMaker\Util;

class XmlImporter extends Importer
{
    /**
     * @var \DOMDocument
     */
    protected $dom;
    protected $root;
    protected $version = "";

    public function __construct()
    {
        $this->dom = new \DOMDocument();
    }

    public function setSourceFile($filename)
    {
        $this->filename = $filename;
    }

    public function load()
    {
        $this->dom->load($this->filename);
        $this->root = $this->dom->documentElement;

        // validate version
        $this->version = $this->root->getAttribute("version");

        if (empty($this->version)) {
            throw new \Exception("ProcessMaker Project version is missing on file source.");
        }

        // read metadata section
        $metadata = $this->root->getElementsByTagName("metadata");

        if ($metadata->length != 1) {
            throw new \Exception("Invalid Document format, metadata section is missing or has multiple definition.");
        }

        $metadata = $metadata->item(0);

        // load project definition
        /** @var \DOMElement[]|\DomNodeList $definitions */
        $definitions = $this->root->getElementsByTagName("definition");

        if ($definitions->length == 0) {
            throw new \Exception("Definition section is missing.");
        } elseif ($definitions->length < 2) {
            throw new \Exception("Definition section is incomplete.");
        }

        $tables = array();

        foreach ($definitions as $definition) {
            $defClass = strtoupper($definition->getAttribute("class"));
            $tables[$defClass] = array();

            // getting tables def
            // first we need to know if the project already exists
            /** @var \DOMElement[] $tablesNodeList */
            $tablesNodeList = $definition->getElementsByTagName("table");

            foreach ($tablesNodeList as $tableNode) {
                $tableName = $tableNode->getAttribute("name"); //strtoupper($tableNode->getAttribute("name"));
                $tables[$defClass][$tableName] = array();
                /** @var \DOMElement[] $recordsNodeList */
                $recordsNodeList = $tableNode->getElementsByTagName("record");

                foreach ($recordsNodeList as $recordsNode) {
                    if (! $recordsNode->hasChildNodes()) {
                        continue;
                    }

                    $columns = array();

                    foreach ($recordsNode->childNodes as $columnNode) {
                        if ($columnNode->nodeName == "#text") continue;
                        //$columns[strtoupper($columnNode->nodeName)] = self::createTextNode($columnNode);;
                        $columnName = $defClass == "WORKFLOW" ? strtoupper($columnNode->nodeName) : $columnNode->nodeName;
                        $columns[$columnName] = self::createTextNode($columnNode);
                    }

                    $tables[$defClass][$tableName][] = $columns;
                }
            }
        }

        $wfFilesNodeList = $this->root->getElementsByTagName("workflow-files");
        $wfFiles = array();

        if ($wfFilesNodeList->length > 0) {
            $filesNodeList = $wfFilesNodeList->item(0)->getElementsByTagName("file");

            foreach ($filesNodeList as $fileNode) {
                $target = $fileNode->getAttribute("target");

                if (! isset($wfFiles[$target])) {
                    $wfFiles[$target] = array();
                }

                $fileContent = self::createTextNode($fileNode->getElementsByTagName("file_content")->item(0));
                $fileContent = base64_decode($fileContent);

                $wfFiles[$target][] = array(
                    "file_name" => self::createTextNode($fileNode->getElementsByTagName("file_name")->item(0)),
                    "file_path" => self::createTextNode($fileNode->getElementsByTagName("file_path")->item(0)),
                    "file_content" => $fileContent
                );
            }
        }

        //print_r($tables);
        //print_r($wfFiles);
        return array($tables, $wfFiles);
    }

    public function import($data = array())
    {
        list($tables, $files) = $this->load();

        // Build BPMN project struct
        $project = $tables["BPMN"]["PROJECT"][0];
        $diagram = $tables["BPMN"]["DIAGRAM"][0];
        $diagram["activities"] = $tables["BPMN"]["ACTIVITY"];
        $diagram["artifacts"] = array();
        $diagram["events"] = $tables["BPMN"]["EVENT"];
        $diagram["flows"] = $tables["BPMN"]["FLOW"];
        $diagram["gateways"] = $tables["BPMN"]["GATEWAY"];
        $diagram["lanes"] = array();
        $diagram["laneset"] = array();
        $project["diagrams"] = array($diagram);
        $project["prj_author"] = isset($data["usr_uid"])? $data["usr_uid"]: "00000000000000000000000000000001";
        $project["process"] = $tables["BPMN"]["PROCESS"][0];
        $result = Adapter\BpmnWorkflow::createFromStruct($project);

        $this->importWfTables($tables["WORKFLOW"]);
        $this->importWfFiles($files);

        return $result;
    }

    private static function createTextNode($node)
    {
        if ($node->nodeType == XML_ELEMENT_NODE) {
            return $node->textContent;
        } else if ($node->nodeType == XML_TEXT_NODE || $node->nodeType == XML_CDATA_SECTION_NODE) {
            return (string) simplexml_import_dom($node->parentNode);
        }
    }
    
    private static function importWfFiles(array $workflowFiles)
    {
        foreach ($workflowFiles as $target => $files) {
            switch ($target) {
                case "dynaforms": $basePath = PATH_DYNAFORM; break;
                case "public":
                    $basePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "public" . PATH_SEP;
                    break;
                case "templates":
                    $basePath = PATH_DATA . "sites" . PATH_SEP . SYS_SYS . PATH_SEP . "mailTemplates" . PATH_SEP;
                    break;
                default: $basePath = "";
            }

            if (empty($basePath)) continue;

            foreach ($files as $file) {
                $filename = $basePath . $file["file_path"];
                $path = dirname($filename);

                if (! is_dir($path)) {
                    Util\Common::mk_dir($path, 0775);
                }

                file_put_contents($filename, $file["file_content"]);
                chmod($filename, 0775);
            }
        }
    }

    public function importWfTables($tables)
    {
        $tables = (object) $tables;

        $processes = new \Processes();
        $processes->createProcessPropertiesFromData($tables);
    }
}