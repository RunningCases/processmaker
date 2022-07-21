<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\BusinessModel;

use G;
use ProcessMaker\BusinessModel\WebEntry as BmWebEntry;
use ProcessMaker\Model\WebEntry;
use Tests\TestCase;

/**
 * Class WebEntryTest
 *
 * @coversDefaultClass \ProcessMaker\BusinessModel\WebEntry
 */
class WebEntryTest extends TestCase
{
    /**
     * Test if exist a Web Entry that uses a Uid like filename
     *
     * @test
     *
     * @covers \ProcessMaker\BusinessModel\WebEntry::isWebEntry()
     */
    public function it_should_exist_web_entry_that_uses_uid_like_filename() {
        // Initializing variables
        $phpExtension = '.php';
        $postFileExtension = 'Post.php';
        $infoFileExtension = 'Info.php';
        $webEntryFilename = G::generateUniqueID();

        // Create a Web Entry
        $webEntry = WebEntry::factory()->create(['WE_DATA' => $webEntryFilename . $phpExtension]);

        // Post file is from a valid Web Entry?
        $isWebEntry = BmWebEntry::isWebEntry($webEntry->PRO_UID, $webEntryFilename . $postFileExtension);
        $this->assertTrue($isWebEntry);

        // Information file is from a valid Web Entry?
        $isWebEntry = BmWebEntry::isWebEntry($webEntry->PRO_UID, $webEntryFilename . $infoFileExtension);
        $this->assertTrue($isWebEntry);
    }

    /**
     * Test if exist a Web Entry that uses a custom name like filename
     *
     * @test
     *
     * @covers \ProcessMaker\BusinessModel\WebEntry::isWebEntry()
     */
    public function it_should_exist_web_entry_that_uses_custom_name_like_filename() {
        // Initializing variables
        $phpExtension = '.php';
        $postFileExtension = 'Post.php';
        $infoFileExtension = 'Info.php';
        $webEntryFilename = 'My_Custom_Form';

        // Create a Web Entry
        $webEntry = WebEntry::factory()->create(['WE_DATA' => $webEntryFilename . $phpExtension]);

        // Post file is from a valid Web Entry?
        $isWebEntry = BmWebEntry::isWebEntry($webEntry->PRO_UID, $webEntryFilename . $postFileExtension);
        $this->assertTrue($isWebEntry);

        // Information file is from a valid Web Entry?
        $isWebEntry = BmWebEntry::isWebEntry($webEntry->PRO_UID, $webEntryFilename . $infoFileExtension);
        $this->assertTrue($isWebEntry);
    }

    /**
     * Test if not exist a Web Entry
     *
     * @test
     *
     * @covers \ProcessMaker\BusinessModel\WebEntry::isWebEntry()
     */
    public function it_should_not_exist_web_entry() {
        // Initializing variables
        $processThatNotExists = G::generateUniqueID();
        $webEntryThatNotExists = G::generateUniqueID() . '.php';

        // File is from a valid Web Entry?
        $isWebEntry = BmWebEntry::isWebEntry($processThatNotExists, $webEntryThatNotExists);
        $this->assertFalse($isWebEntry);
    }

    /**
     * Test if is sent empty parameters to the method
     *
     * @test
     *
     * @covers \ProcessMaker\BusinessModel\WebEntry::isWebEntry()
     */
    public function it_should_return_false_with_empty_parameters() {
        // Initializing variables
        $emptyProcess = '';
        $emptyFilePath = '';

        // File is from a valid Web Entry?
        $isWebEntry = BmWebEntry::isWebEntry($emptyProcess, $emptyFilePath);
        $this->assertFalse($isWebEntry);
    }
}
