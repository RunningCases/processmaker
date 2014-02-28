<?php
namespace ProcessMaker\Exporter;

class XmlExporter extends Exporter
{
    protected $dom;
    protected $rootNode;

    public function __construct($prjUid)
    {
        parent::__construct($prjUid);

        /* @var $this->dom DomDocument */
        $this->dom = new \DOMDocument("1.0", "utf-8"); //\DOMImplementation::createDocument(null, 'project');
        $this->dom->formatOutput = true;

        $this->rootNode = $this->dom->createElement("PROJECT");
        $this->rootNode->setAttribute("version", "1.0");
        $this->dom->appendChild($this->rootNode);


    }

    public function build()
    {
        $data = $this->buildData();

        // metadata set up
        $metadata = $data["METADATA"];
        $metadataNode = $this->dom->createElement("METADATA");

        foreach ($metadata as $key => $value) {
            $metaNode = $this->dom->createElement("META");
            $metaNode->setAttribute("key", $key);
            $metaNode->setAttribute("value", $value);
            $metadataNode->appendChild($metaNode);
        }

        $this->rootNode->appendChild($metadataNode);
        // end setting metadata

        // bpmn struct data set up
        $dbData = array("BPMN_DATA" => $data["BPMN_DATA"], "WORKFLOW_DATA" => $data["WORKFLOW_DATA"]);
        //file_put_contents("/home/erik/out.log", print_r($dbData, true)); die;
        foreach ($dbData as $sectionName => $sectionData) {
            $dataNode = $this->dom->createElement($sectionName);

            foreach ($sectionData as $elementName => $elementData) {
                $elementNode = $this->dom->createElement(strtoupper($elementName));

                foreach ($elementData as $recordData) {
                    $recordNode = $this->dom->createElement("ROW");

                    foreach ($recordData as $key => $value) {
                        $columnNode = $this->dom->createElement(strtoupper($key));

                        if (is_array($value)) {print_r($value); die;}
                        if (preg_match('/^[\w\s]+$/', $value, $match) || empty($value)) {
                            $textNode = $this->dom->createTextNode($value);
                        } else {
                            $textNode = $this->dom->createCDATASection($value);
                        }

                        $columnNode->appendChild($textNode);
                        $recordNode->appendChild($columnNode);
                    }

                    $elementNode->appendChild($recordNode);
                }

                $dataNode->appendChild($elementNode);
            }

            $this->rootNode->appendChild($dataNode);
        }

        $workflowFilesNode = $this->dom->createElement("WORKFLOW_FILES");

        // workflow dynaforms files
        foreach ($data["WORKFLOW_FILES"] as $elementName => $elementData) {
            foreach ($elementData as $fileData) {
                $fileNode = $this->dom->createElement("FILE");
                $fileNode->setAttribute("target", strtolower($elementName));

                $filenameNode = $this->dom->createElement("FILE_NAME");
                $filenameNode->appendChild($this->dom->createCDATASection($fileData["filename"]));
                $fileNode->appendChild($filenameNode);

                $filepathNode = $this->dom->createElement("FILE_PATH");
                $filepathNode->appendChild($this->dom->createCDATASection($fileData["filepath"]));
                $fileNode->appendChild($filepathNode);

                $fileContentNode = $this->dom->createElement("FILE_CONTENT");
                $fileContentNode->appendChild($this->dom->createCDATASection(base64_encode($fileData["file_content"])));
                $fileNode->appendChild($fileContentNode);

                $workflowFilesNode->appendChild($fileNode);
            }
        }

        $this->rootNode->appendChild($workflowFilesNode);
    }

    public function save($outputFile)
    {
        file_put_contents($outputFile, $this->export());
    }

    public function export()
    {
        return $this->dom->saveXml();
    }
}