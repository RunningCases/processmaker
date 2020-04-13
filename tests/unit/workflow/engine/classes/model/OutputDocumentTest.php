<?php

namespace Tests\unit\workflow\engine\classes\model;

use G;
use OutputDocument;
use ProcessMaker\Model\OutputDocument as OutputDocumentModel;
use Tests\TestCase;

/**
 * Class OutputDocumentTest
 *
 * @coversDefaultClass \OutputDocument
 */
class OutputDocumentTest extends TestCase
{

    /**
     * Review the generate pdf using TCPDF
     * @test
     * @covers \OutputDocument::generateTcpdf()
     */
    public function it_should_generate_tcpdf()
    {
        // Create a register in the output document
        $output = factory(OutputDocumentModel::class)->create([
        	'OUT_DOC_TEMPLATE' => '<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<body>
<p>TEST OUTPUT DOCUMENT 内 </p>
<p><font size="2" color="blue" face="verdana" style="text-align:center">this is some font</font></p>
</body>
</html>',
        ]);

        // Prepare the font arialuni.ttf
        $arialBkp = PATH_TRUNK . 'tests' . PATH_SEP . 'resources' . PATH_SEP . 'fonts' . PATH_SEP;
        $fonts = PATH_DATA . 'fonts' . PATH_SEP;
        if (!file_exists($fonts)) {
            G::mk_dir($fonts);
        }
        G::recursive_copy($arialBkp, $fonts);
        // Define the path for generate the pdf
        $appUid = G::generateUniqueID();
        $pathOutput = PATH_DB . config('system.workspace') . PATH_SEP . 'files' . G::getPathFromUID($appUid) . PATH_SEP . 'outdocs' . PATH_SEP;
        G::mk_dir($pathOutput);
        // Define some parameters
        $fields = [];
        $fields['USR_USERNAME'] = G::generateUniqueID();
        // Define some atributes for the pdf
        $properties = [];
        $properties['margins'] = [];
        $properties['margins']['left'] = 15;
        $properties['margins']['right'] = 15;
        $properties['margins']['top'] = 15;
        $properties['margins']['bottom'] = 15;
        $properties['pdfSecurity'] = true;
        // Call output document
        $outputDocument = new OutputDocument();
        $outputDocument->generateTcpdf(
            $output->OUT_DOC_UID, 
            $fields, 
            $pathOutput, 
            $output->OUT_DOC_FILENAME,
            $output->OUT_DOC_TEMPLATE,
            false,
            $properties
        );
        $this->assertFileExists($pathOutput . $output->OUT_DOC_FILENAME . '.pdf');
        // Remove the shared folder
        G::rm_dir($fonts);
    }
}