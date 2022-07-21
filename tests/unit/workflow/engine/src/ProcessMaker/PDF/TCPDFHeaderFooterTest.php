<?php

namespace ProcessMaker\PDF;

use stdClass;
use Tests\TestCase;

/**
 * @covers ProcessMaker\PDF\TCPDFHeaderFooter
 * @test
 */
class TCPDFHeaderFooterTest extends TestCase
{
    /**
     * TCPDFHeaderFooter object.
     * @var TCPDFHeaderFooter
     */
    protected $object;

    /**
     * setUp method.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new TCPDFHeaderFooter('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->object->SetCreator(PDF_CREATOR);
        $this->object->SetAuthor('admin');
        $this->object->SetTitle('test');
        $this->object->SetSubject('test.pdf');
        $this->object->SetCompression(true);
        $this->setHeaderData();
        $this->setFooterData();
    }

    /**
     * tearDown method.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Setting data for header configuration.
     */
    private function setHeaderData()
    {
        $header = new stdClass();
        $header->logo = PATH_TRUNK . "/vendor/tecnickcom/tcpdf/examples/images/logo_example.jpg";
        $header->logoWidth = 10;
        $header->logoPositionX = 50;
        $header->logoPositionY = 0;
        $header->title = "Test1 Test1";
        $header->titleFontSize = 60;
        $header->titleFontPositionX = 10;
        $header->titleFontPositionY = 0;
        $header->pageNumber = true;
        $header->pageNumberTitle = "Pages";
        $header->pageNumberTotal = true;
        $header->pageNumberPositionX = 10;
        $header->pageNumberPositionY = 0;

        $struct = $this->object->getHeaderStruct();
        $struct->setLogo($header->logo);
        $struct->setLogoWidth($header->logoWidth);
        $struct->setLogoPositionX($header->logoPositionX);
        $struct->setLogoPositionY($header->logoPositionY);

        $struct->setTitle($header->title);
        $struct->setTitleFontSize($header->titleFontSize);
        $struct->setTitleFontPositionX($header->titleFontPositionX);
        $struct->setTitleFontPositionY($header->titleFontPositionY);

        $struct->setPageNumber($header->pageNumber);
        $struct->setPageNumberTitle($header->pageNumberTitle);
        $struct->setPageNumberTotal($header->pageNumberTotal);
        $struct->setPageNumberPositionX($header->pageNumberPositionX);
        $struct->setPageNumberPositionY($header->pageNumberPositionY);
    }

    /**
     * Setting data for footer configuration.
     */
    private function setFooterData()
    {
        $footer = new stdClass();
        $footer->logo = PATH_TRUNK . "/vendor/tecnickcom/tcpdf/examples/images/logo_example.jpg";
        $footer->logoWidth = 15;
        $footer->logoPositionX = 10;
        $footer->logoPositionY = 0;
        $footer->title = "Hola mundo como estas";
        $footer->titleFontSize = 20;
        $footer->titleFontPositionX = 0;
        $footer->titleFontPositionY = 5;
        $footer->pageNumber = true;
        $footer->pageNumberTitle = "Pages";
        $footer->pageNumberTotal = true;
        $footer->pageNumberPositionX = 40;
        $footer->pageNumberPositionY = 5;

        $struct = $this->object->getFooterStruct();
        $struct->setLogo($footer->logo);
        $struct->setLogoWidth($footer->logoWidth);
        $struct->setLogoPositionX($footer->logoPositionX);
        $struct->setLogoPositionY($footer->logoPositionY);

        $struct->setTitle($footer->title);
        $struct->setTitleFontSize($footer->titleFontSize);
        $struct->setTitleFontPositionX($footer->titleFontPositionX);
        $struct->setTitleFontPositionY($footer->titleFontPositionY);

        $struct->setPageNumber($footer->pageNumber);
        $struct->setPageNumberTitle($footer->pageNumberTitle);
        $struct->setPageNumberTotal($footer->pageNumberTotal);
        $struct->setPageNumberPositionX($footer->pageNumberPositionX);
        $struct->setPageNumberPositionY($footer->pageNumberPositionY);
    }

    /**
     * This test the getHeaderStruct() method.
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::getHeaderStruct()
     * @test
     */
    public function it_should_test_the_getHeaderStruct()
    {
        $result = $this->object->getHeaderStruct();
        $this->assertNotNull($result);
        $this->assertEquals(HeaderStruct::class, get_class($result));
    }

    /**
     * This test the getFooterStruct() method.
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::getFooterStruct()
     * @test
     */
    public function it_should_test_the_getFooterStruct()
    {
        $result = $this->object->getFooterStruct();
        $this->assertNotNull($result);
        $this->assertEquals(FooterStruct::class, get_class($result));
    }

    /**
     * This test the Header() method override.
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::Header()
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::buildHeaderLogo()
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::buildHeaderTitle()
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::buildHeaderPageNumber()
     * @test
     */
    public function it_should_test_the_Header()
    {
        $this->object->AddPage();
        $result = $this->object->Header();
        $this->assertEmpty($result);
    }

    /**
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::Footer()
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::buildFooterLogo()
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::buildFooterTitle()
     * @covers ProcessMaker\PDF\TCPDFHeaderFooter::buildFooterPageNumber()
     * @test
     */
    public function it_should_test_the_Footer()
    {
        $this->object->AddPage();
        $result = $this->object->Footer();
        $this->assertEmpty($result);
    }

}
