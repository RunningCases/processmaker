<?php

namespace Tests\unit\workflow\engine\classes\model;

use Faker\Factory;
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
    var $faker = null;
    /**
     * OutputDocumentTest constructor.
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        // Faker instance
        $this->faker = Factory::create();

        // Check if the constant "K_PATH_FONTS" is defined, if is not defined we need to define
        if (!defined('K_PATH_FONTS')) {
            // Generate a new folder name
            $folderName = $this->faker->word;

            // Define the path of the fonts, "K_PATH_FONTS" is a constant used by "TCPDF" library
            define('K_PATH_FONTS', PATH_DATA . 'fonts' . PATH_SEP . $folderName . PATH_SEP);
        }

        // Parent constructor
        parent::__construct($name, $data, $dataName);
    }

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
    }

    /**
     * Test checkTcPdfFontsPath method
     *
     * @test
     * @covers \OutputDocument::checkTcPdfFontsPath()
     */
    public function it_should_test_check_tcpdf_fonts_path()
    {
        // Generate a new folder name
        $folderName = $this->faker->word;

        // Check if the TCPDF fonts path exists, if not exists should be created and initialized
        OutputDocument::checkTcPdfFontsPath($folderName);

        // Assertion
        $this->assertDirectoryExists(K_PATH_FONTS);
    }

    /**
     * Test loadTcPdfFontsList method
     *
     * @test
     * @covers \OutputDocument::loadTcPdfFontsList()
     */
    public function it_should_test_load_tcpdf_fonts_list()
    {
        // Get fonts
        $fonts = OutputDocument::loadTcPdfFontsList();

        // Assertion
        $this->assertTrue(is_array($fonts));
    }

    /**
     * Test saveTcPdfFontsList method
     *
     * @test
     * @covers \OutputDocument::saveTcPdfFontsList()
     */
    public function it_should_test_save_tcpdf_fonts_list()
    {
        // Get fonts stored originally
        $fontsOriginal = OutputDocument::loadTcPdfFontsList();

        // Set variables needed
        $fontFamily = $this->faker->word;
        $font = [
            'fileName' => "{$fontFamily}.ttf",
            'tcPdfFileName' => $fontFamily,
            'familyName' => $fontFamily,
            'inTinyMce' => true,
            'friendlyName' => $fontFamily,
            'properties' => ''
        ];
        $fontsToSave = $fontsOriginal;
        $fontsToSave[] = $font;

        // Check if TCPDF fonts paths exists
        if (!file_exists(K_PATH_FONTS)) {
            G::mk_dir(K_PATH_FONTS);
        }

        // Save fonts
        OutputDocument::saveTcPdfFontsList($fontsToSave);

        // Get fonts
        $fontsModified = OutputDocument::loadTcPdfFontsList();

        // Assertion
        $this->assertTrue(count($fontsModified) > count($fontsOriginal));
    }

    /**
     * Test addTcPdfFont method
     *
     * @test
     * @covers \OutputDocument::addTcPdfFont()
     */
    public function it_should_test_add_tcpdf_font()
    {
        // Set variables needed
        $fontFamily = $this->faker->word;
        $font = [
            'fileName' => "{$fontFamily}.ttf",
            'tcPdfFileName' => $fontFamily,
            'familyName' => $fontFamily,
            'inTinyMce' => true,
            'friendlyName' => $fontFamily,
            'properties' => ''
        ];

        // Check if TCPDF fonts paths exists
        if (!file_exists(K_PATH_FONTS)) {
            G::mk_dir(K_PATH_FONTS);
        }

        // Add new font
        OutputDocument::addTcPdfFont($font);

        // Get fonts
        $fonts = OutputDocument::loadTcPdfFontsList();

        // Assertion
        $this->assertArrayHasKey("{$fontFamily}.ttf", $fonts);
    }

    /**
     * Test existTcpdfFont method
     *
     * @test
     * @covers \OutputDocument::existTcpdfFont()
     */
    public function it_should_test_exist_tcpdf_font()
    {
        // Generate a fake family name
        $fontFamily = $this->faker->word;

        // Add a new font
        $font = [
            'fileName' => "{$fontFamily}.ttf",
            'tcPdfFileName' => $fontFamily,
            'familyName' => $fontFamily,
            'inTinyMce' => true,
            'friendlyName' => $fontFamily,
            'properties' => ''
        ];

        // Check if TCPDF fonts paths exists
        if (!file_exists(K_PATH_FONTS)) {
            G::mk_dir(K_PATH_FONTS);
        }

        // Add new font
        OutputDocument::addTcPdfFont($font);

        // Assertion
        $this->assertTrue(OutputDocument::existTcpdfFont("{$fontFamily}.ttf"));
    }

    /**
     * Test removeTcPdfFont method
     *
     * @test
     * @covers \OutputDocument::removeTcPdfFont()
     */
    public function it_should_test_remove_tcpdf_font()
    {
        // Generate a fake family name
        $fontFamily = $this->faker->word;

        // Add a new font
        $font = [
            'fileName' => "{$fontFamily}.ttf",
            'tcPdfFileName' => $fontFamily,
            'familyName' => $fontFamily,
            'inTinyMce' => true,
            'friendlyName' => $fontFamily,
            'properties' => ''
        ];

        // Check if TCPDF fonts paths exists
        if (!file_exists(K_PATH_FONTS)) {
            G::mk_dir(K_PATH_FONTS);
        }

        // Add new font
        OutputDocument::addTcPdfFont($font);

        // Remove font
        OutputDocument::removeTcPdfFont("{$fontFamily}.ttf");

        // Assertion
        $this->assertFalse(OutputDocument::existTcpdfFont("{$fontFamily}.ttf"));
    }

    /**
     * Test generateCssFile method
     *
     * @test
     * @covers \OutputDocument::generateCssFile()
     */
    public function it_should_test_generate_css_file()
    {
        // Set variables needed
        $fontFamily = $this->faker->word;
        $font = [
            'fileName' => "{$fontFamily}.ttf",
            'tcPdfFileName' => $fontFamily,
            'familyName' => $fontFamily,
            'inTinyMce' => true,
            'friendlyName' => $fontFamily,
            'properties' => ''
        ];
        $fontsToSave = ["{$fontFamily}.ttf" => $font];

        // Check if TCPDF fonts paths exists
        if (!file_exists(K_PATH_FONTS)) {
            G::mk_dir(K_PATH_FONTS);
        }

        // Save fonts
        OutputDocument::saveTcPdfFontsList($fontsToSave);

        // Re-generate CSS file
        OutputDocument::generateCssFile();

        // Assertion
        $cssContent = file_get_contents(K_PATH_FONTS . 'fonts.css');
        $this->assertTrue(strpos($cssContent, "{$fontFamily}.ttf") !== false);
    }
}