<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Importer;

use Carbon\Carbon;
use Exception;
use G;
use Illuminate\Support\Facades\Cache;
use PmLicenseManager;
use ProcessMaker\Importer\XmlImporter;
use ProcessMaker\Model\Groupwf;
use ProcessMaker\Model\User;
use Tests\TestCase;

class XmlImporterTest extends TestCase
{
    private $user;

    /**
     * Set up unit tests.
     */
    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Groupwf::truncate();

        $cached = ["jXsSi94bkRUcVZyRStNVExlTXhEclVadGRRcG9xbjNvTWVFQUF3cklKQVBiVT0=" => 1];
        Cache::put(PmLicenseManager::CACHE_KEY . '.' . config("system.workspace"), $cached, Carbon::now()->addDay(1));
    }

    /**
     * Test the import new option and the import new group option.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     * @covers \ProcessMaker\Importer\XmlImporter::importBpmnTables()
     */
    public function it_should_matter_with_import_option_create_new_and_group_import_option_create_new()
    {
        $filename = PATH_TRUNK . "tests/resources/p1normal-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false);
        $this->assertNotNull($result);
    }

    /**
     * Test the import new without changing and the import merge group option.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_keep_without_changing_and_create_new_and_group_import_option_merge_preexistent()
    {
        factory(\ProcessMaker\Model\Groupwf::class)->create([
            'GRP_TITLE' => 'group1'
        ]);
        factory(\ProcessMaker\Model\Groupwf::class)->create([
            'GRP_TITLE' => 'group2'
        ]);
        $regenerateUids = false;
        $filename = PATH_TRUNK . "tests/resources/p1normal-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_MERGE_PREEXISTENT, true);
        $this->assertNotNull($result);
    }

    /**
     * Test the import overwrite option and the import rename group option.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_overwrite_and_group_import_option_rename()
    {
        factory(\ProcessMaker\Model\Groupwf::class)->create([
            'GRP_TITLE' => 'group1'
        ]);
        factory(\ProcessMaker\Model\Groupwf::class)->create([
            'GRP_TITLE' => 'group2'
        ]);
        $filename = PATH_TRUNK . "tests/resources/p1normal-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_OVERWRITE, XmlImporter::GROUP_IMPORT_OPTION_RENAME, false);
        $this->assertNotNull($result);
    }

    /**
     * Test the import new option and the import new group option with objects imports.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     * @covers \ProcessMaker\BusinessModel\Migrator\GranularImporter::structureBpmnData()
     */
    public function it_should_matter_with_import_option_create_new_and_group_import_option_create_new_and_objects_import()
    {
        $filename = PATH_TRUNK . "tests/resources/p2custom-1.pmx2";

        $objectsToImportFilename = PATH_TRUNK . "tests/resources/p2custom-1-ObjectsToImport.json";
        $json = file_get_contents($objectsToImportFilename);
        $objectsToImport = json_decode($json);

        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false, $objectsToImport);
        $this->assertNotNull($result);
    }

    /**
     * Test the import without changing option and the import new group option with objects import.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_keep_without_changing_and_create_new_and_group_import_option_create_new()
    {
        $regenerateUids = false;
        $filename = PATH_TRUNK . "tests/resources/p2custom-1.pmx2";

        $objectsToImportFilename = PATH_TRUNK . "tests/resources/p2custom-1-ObjectsToImport.json";
        $json = file_get_contents($objectsToImportFilename);
        $objectsToImport = json_decode($json);

        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_KEEP_WITHOUT_CHANGING_AND_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, true, $objectsToImport);
        $this->assertNotNull($result);
    }

    /**
     * Test the import overwrite option and the import new group option with objects import.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_overwrite_and_group_import_option_create_new()
    {
        $filename = PATH_TRUNK . "tests/resources/p2custom-1.pmx2";

        $objectsToImportFilename = PATH_TRUNK . "tests/resources/p2custom-1-ObjectsToImport.json";
        $json = file_get_contents($objectsToImportFilename);
        $objectsToImport = json_decode($json);

        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_OVERWRITE, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false, $objectsToImport);
        $this->assertNotNull($result);
    }

    /**
     * Test the import disable option and the import new group option with objects import.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_disable_and_create_new_and_group_import_option_create_new()
    {
        $regenerateUids = false;
        $filename = PATH_TRUNK . "tests/resources/p2custom-1.pmx2";

        $objectsToImportFilename = PATH_TRUNK . "tests/resources/p2custom-1-ObjectsToImport.json";
        $json = file_get_contents($objectsToImportFilename);
        $objectsToImport = json_decode($json);

        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_DISABLE_AND_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, true, $objectsToImport);
        $this->assertNotNull($result);
    }

    /**
     * Test the import new option and the import new group option with exception.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_create_new_and_group_import_option_create_new_with_exception()
    {
        $filename = PATH_TRUNK . "tests/resources/p1normal-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);

        $this->expectException(Exception::class);

        $result = $importer->import(XmlImporter::IMPORT_OPTION_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false);
        $this->assertNotNull($result);
    }

    /**
     * Test the import overwrite option and the import new group option with exist groups.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_overwrite_and_group_import_option_create_new_with_groups()
    {
        factory(\ProcessMaker\Model\Groupwf::class)->create([
            'GRP_TITLE' => 'group1'
        ]);
        factory(\ProcessMaker\Model\Groupwf::class)->create([
            'GRP_TITLE' => 'group2'
        ]);
        $filename = PATH_TRUNK . "tests/resources/p1normal-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);

        $this->expectException(Exception::class);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_OVERWRITE, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false);
        $this->assertNotNull($result);
    }

    /**
     * Test the import new option and the import new group option with generated uid from js such as null.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     */
    public function it_should_matter_with_import_option_create_new_and_group_import_option_create_new_try_exception()
    {
        $filename = PATH_TRUNK . "tests/resources/p1normalWithException-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);

        $this->expectException(Exception::class);
        $result = $importer->import(XmlImporter::IMPORT_OPTION_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW);
        $this->assertNotNull($result);
    }

    /**
     * Test the import new option and the import new group option with repeated title.
     * @test
     * @covers \ProcessMaker\Importer\XmlImporter::import()
     * @covers \ProcessMaker\Importer\XmlImporter::updateProcessInformation()
     */
    public function it_should_matter_with_import_option_create_new_and_group_import_option_create_new_try_rename_title()
    {
        factory(\ProcessMaker\Model\Process::class)->create([
            'PRO_TITLE' => 'p1normalWithoutTitle'
        ]);

        $filename = PATH_TRUNK . "tests/resources/p1normalWithoutTitle-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);

        $result = $importer->import(XmlImporter::IMPORT_OPTION_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false);
        $this->assertNotNull($result);

        factory(\ProcessMaker\Model\Process::class)->create([
            'PRO_TITLE' => 'p1normalWithoutTitle2'
        ]);

        $filename = PATH_TRUNK . "tests/resources/p1normalWithoutTitle2-1.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);

        $result = $importer->import(XmlImporter::IMPORT_OPTION_OVERWRITE, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false);
        $this->assertNotNull($result);
    }
}
