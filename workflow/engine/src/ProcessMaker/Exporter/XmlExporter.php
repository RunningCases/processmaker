<?php
namespace ProcessMaker\Exporter;

/**
 * Class XmlExporter
 *
 * @package ProcessMaker\Exporter
 * @author Erik Amaru Ortiz <erik@coilosa.com>
 */
class XmlExporter extends Exporter
{
    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @var \DOMElement
     */
    protected $rootNode;

    /**
     * XmlExporter Constructor
     *
     * @param $prjUid
     *
     */
    public function __construct($prjUid)
    {
        parent::__construct($prjUid);

        $this->dom = new \DOMDocument("1.0", "utf-8");
        $this->dom->formatOutput = true;
    }

    /**
     * @inherits
     */
    public function build()
    {
        $this->rootNode = $this->dom->createElement($this->getContainerName());
        $this->rootNode->setAttribute("version", self::getVersion());
        $this->dom->appendChild($this->rootNode);

        $data = $this->buildData();

        // metadata set up
        $metadata = $data["Metadata"];
        $metadataNode = $this->dom->createElement("Metadata");

        foreach ($metadata as $key => $value) {
            $metaNode = $this->dom->createElement("meta:$key");
            //$metaNode->setAttribute("key", $key);
            //$metaNode->setAttribute("value", $value);
            $metaNode->appendChild($this->dom->createTextNode($value));
            $metadataNode->appendChild($metaNode);
        }

        $this->rootNode->appendChild($metadataNode);
        // end setting metadata

        // bpmn struct data set up
        $dbData = array("BPMN" => $data["BPMN-Definition"], "Workflow" => $data["Workflow-Definition"]);
        //file_put_contents("/home/erik/out.log", print_r($dbData, true)); die;
        foreach ($dbData as $sectionName => $sectionData) {
            $dataNode = $this->dom->createElement("Definition");
            $dataNode->setAttribute("class", $sectionName);

            foreach ($sectionData as $elementName => $elementData) {
                $elementNode = $this->dom->createElement("table");
                $elementNode->setAttribute("name", $elementName);

                foreach ($elementData as $recordData) {
                    $recordNode = $this->dom->createElement("record");
                    $recordData = array_change_key_case($recordData, CASE_LOWER);
                    //var_dump($recordData); die;


                    foreach ($recordData as $key => $value) {
                        $columnNode = $this->dom->createElement($key);

                        if (preg_match('/^[\w\s\.]+$/', $value, $match) || empty($value)) {
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

        $workflowFilesNode = $this->dom->createElement("Workflow-Files");

        // workflow dynaforms files
        foreach ($data["Workflow-Files"] as $elementName => $elementData) {
            foreach ($elementData as $fileData) {
                $fileNode = $this->dom->createElement("file");
                $fileNode->setAttribute("target", strtolower($elementName));

                $filenameNode = $this->dom->createElement("file_name");
                if (preg_match('/^[\w\s\.\-]+$/', $fileData["filename"], $match)) {
                    $filenameNode->appendChild($this->dom->createTextNode($fileData["filename"]));
                } else {
                    $filenameNode->appendChild($this->dom->createCDATASection($fileData["filename"]));
                }
                $fileNode->appendChild($filenameNode);

                $filepathNode = $this->dom->createElement("file_path");
                $filepathNode->appendChild($this->dom->createCDATASection($fileData["filepath"]));
                $fileNode->appendChild($filepathNode);

                $fileContentNode = $this->dom->createElement("file_content");
                $fileContentNode->appendChild($this->dom->createCDATASection(base64_encode($fileData["file_content"])));
                $fileNode->appendChild($fileContentNode);

                $workflowFilesNode->appendChild($fileNode);
            }
        }

        $this->rootNode->appendChild($workflowFilesNode);
    }

    /**
     * @inherits
     */
    public function saveExport($outputFile)
    {
        file_put_contents($outputFile, $this->export());
        chmod($outputFile, 0755);
    }

    /**
     * @inherits
     */
    public function export()
    {
        $this->build();
        return $this->dom->saveXml();
    }
}