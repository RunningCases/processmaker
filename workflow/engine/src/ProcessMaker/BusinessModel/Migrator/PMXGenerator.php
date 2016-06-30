<?php


namespace ProcessMaker\BusinessModel\Migrator;

/**
 * Class PMXGenerator
 * This class generates the a PMX class based on the data passed on the generate method.
 *
 * @package ProcessMaker\BusinessModel\Migrator
 */

class PMXGenerator
{

    /**
     * @var \DOMElement
     */
    protected $rootNode;
    /**
     * @var \DOMDocument
     */
    protected $domDocument;

    /**
     * PMXPublisher constructor.
     * @param $domDocument
     */
    public function __construct()
    {
        $this->domDocument = new \DOMDocument("1.0", "utf-8");
        $this->domDocument->formatOutput = true;
    }

    /**
     * Generates a PMX xml string based on the $data passed along.
     *
     * @param $data
     * @return string
     */
    public function generate($data)
    {
        $rootNode = $this->domDocument->createElement($data['container']);
        $rootNode->setAttribute("version", $data['version']);
        $this->domDocument->appendChild($rootNode);

        $metadata = $data["metadata"];
        $metadataNode = $this->domDocument->createElement("metadata");

        foreach ($metadata as $key => $value) {
            $metaNode = $this->domDocument->createElement("meta");
            $metaNode->setAttribute("key", $key);
            $metaNode->appendChild($this->getTextNode($value));
            $metadataNode->appendChild($metaNode);
        }

        $rootNode->appendChild($metadataNode);

        $dbData = array(
            "BPMN" => $data["bpmn-definition"],
            "workflow" => $data["workflow-definition"],
            "plugins" => $data["plugin-data"]
        );
        foreach ($dbData as $sectionName => $sectionData) {
            $dataNode = $this->domDocument->createElement("definition");
            $dataNode->setAttribute("class", $sectionName);

            foreach ($sectionData as $elementName => $elementData) {
                $elementNode = $this->domDocument->createElement("table");
                $elementNode->setAttribute("name", $elementName);

                foreach ($elementData as $recordData) {
                    $recordNode = $this->domDocument->createElement("record");
                    if(is_array($recordData)){
                        $recordData = array_change_key_case($recordData, CASE_LOWER);

                        foreach ($recordData as $key => $value) {
                            if (is_object($value) || is_array($value)) {
                                $value = serialize($value);
                            }
                            $columnNode = $this->domDocument->createElement($key);
                            $columnNode->appendChild($this->getTextNode($value));
                            $recordNode->appendChild($columnNode);
                        }

                        $elementNode->appendChild($recordNode);
                    }
                }

                $dataNode->appendChild($elementNode);
            }

            $rootNode->appendChild($dataNode);
        }

        $workflowFilesNode = $this->domDocument->createElement("workflow-files");

        foreach ($data["workflow-files"] as $elementName => $elementData) {
            foreach ($elementData as $fileData) {
                $fileNode = $this->domDocument->createElement("file");
                $fileNode->setAttribute("target", strtolower($elementName));

                $filenameNode = $this->domDocument->createElement("file_name");
                $filenameNode->appendChild($this->getTextNode($fileData["filename"]));
                $fileNode->appendChild($filenameNode);

                $filepathNode = $this->domDocument->createElement("file_path");
                $filepathNode->appendChild($this->domDocument->createCDATASection($fileData["filepath"]));
                $fileNode->appendChild($filepathNode);

                $fileContentNode = $this->domDocument->createElement("file_content");
                $fileContentNode->appendChild($this->domDocument->createCDATASection(base64_encode($fileData["file_content"])));
                $fileNode->appendChild($fileContentNode);

                $workflowFilesNode->appendChild($fileNode);
            }
        }
        $rootNode->appendChild($workflowFilesNode);
        return $this->domDocument->saveXML($rootNode);
    }

    /**
     * @param $value
     * @return mixed
     */
    private function getTextNode($value)
    {
        if (empty($value) || preg_match('/^[\w\s\.\-]+$/', $value, $match)) {
            return $this->domDocument->createTextNode($value);
        } else {
            return $this->domDocument->createCDATASection($value);
        }
    }
}