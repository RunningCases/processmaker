<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use Faker\Factory;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use ProcessMaker\Importer\XmlImporter;
use ProcessMaker\Services\Api\Project;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * Set the process owner with invalid value, the import test covers most of the code.
     * @test
     * @covers \ProcessMaker\Services\Api\Project::doSaveAs()
     * @covers \ProcessMaker\Importer\XmlImporter::saveAs()
     */
    public function it_should_set_the_process_owner_with_invalid_value()
    {
        $filename = PATH_TRUNK . "/tests/resources/p1normal-2.pmx";
        $importer = new XmlImporter();
        $importer->setData("usr_uid", $this->user->USR_UID);
        $importer->setSourceFile($filename);
        $proUid = $importer->import(XmlImporter::IMPORT_OPTION_CREATE_NEW, XmlImporter::GROUP_IMPORT_OPTION_CREATE_NEW, false);

        $faker = $faker = Factory::create();
        $project = new Project();
        $project->setUserId($this->user->USR_ID);
        $result = $project->doSaveAs($proUid, $faker->title);

        $this->assertNotEmpty($result);
    }
}
