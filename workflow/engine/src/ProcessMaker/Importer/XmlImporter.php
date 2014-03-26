<?php
namespace ProcessMaker\Importer;

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
                $tableName = strtoupper($tableNode->getAttribute("name"));
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
                        $columns[strtoupper($columnNode->nodeName)] = self::getNodeText($columnNode);;
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

                $fileContent = self::getNodeText($fileNode->getElementsByTagName("file_content")->item(0));
                $fileContent = base64_decode($fileContent);

                $wfFiles[$target][] = array(
                    "file_name" => self::getNodeText($fileNode->getElementsByTagName("file_name")->item(0)),
                    "file_path" => self::getNodeText($fileNode->getElementsByTagName("file_path")->item(0)),
                    "file_content" => $fileContent
                );
            }
        }

        print_r($tables);
        print_r($wfFiles);


        // load workflow definition
        // load workflow files
    }

    public function import()
    {
        $this->load();
    }

    private static function getNodeText($node)
    {
        if ($node->nodeType == XML_ELEMENT_NODE) {
            return $node->textContent;
        } else if ($node->nodeType == XML_TEXT_NODE || $node->nodeType == XML_CDATA_SECTION_NODE) {
            return (string) simplexml_import_dom($node->parentNode);
        }
    }
}