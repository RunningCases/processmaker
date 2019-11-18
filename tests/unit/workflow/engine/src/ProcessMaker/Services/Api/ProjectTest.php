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
        /**
         * To perform the test this requires a valid installation and its respective license.
         * 
         * In the file "workflow/engine/classes/WorkspaceTools.php", 
         * these lines need the db.php file.
         * 
         * public function __construct($workspaceName)
         * {
         *     $this->name = $workspaceName;
         *     $this->path = PATH_DB . $this->name;
         *     $this->dbPath = $this->path . '/db.php';
         *     if ($this->workspaceExists()) {
         *         $this->getDBInfo();
         *     }
         *     $this->setListContentMigrateTable();
         * }
         */
        $this->markTestIncomplete("To perform the test this requires a valid installation and its respective license.");
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
        $filename = PATH_TRUNK . "/tests/resources/p1normal-1.pmx";
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
