<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use Faker\Factory;
use Luracast\Restler\RestException;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use ProcessMaker\Model\RbacUsers;
use ProcessMaker\Importer\XmlImporter;
use ProcessMaker\Services\Api\Project;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Set the process owner with invalid value, the import test covers most of the code.
     * @test
     * @covers \ProcessMaker\Services\Api\Project::doSaveAs()
     * @covers \ProcessMaker\Importer\XmlImporter::saveAs()
     */
    public function it_should_set_the_process_owner_with_invalid_value()
    {
        $filename = PATH_TRUNK . "tests/resources/p1normal-2.pmx";
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

    /**
     * Tests the doGetProcess method
     * 
     * @test
     * @covers \ProcessMaker\Services\Api\Project::doGetProcess()
     */
    public function it_should_test_the_do_get_process_method()
    {
        //Create user
        $user = User::factory()->create();
        RbacUsers::factory()->create([
            'USR_UID' => $user->USR_UID,
            'USR_USERNAME' => $user->USR_USERNAME,
            'USR_FIRSTNAME' => $user->USR_FIRSTNAME,
            'USR_LASTNAME' => $user->USR_LASTNAME
        ]);

        //Create process
        $process = Process::factory()->create([
            'PRO_CREATE_USER' => $user->USR_UID,
            'PRO_STATUS' => 'ACTIVE',
            'PRO_TYPE_PROCESS' => 'PRIVATE',
        ]);

        $project = new Project();
        $res = $project->doGetProcess($process->PRO_UID);

        //Asserts the response has the user information
        $this->assertArrayHasKey('pro_create_username', $res);
        $this->assertArrayHasKey('pro_create_firstname', $res);
        $this->assertArrayHasKey('pro_create_lastname', $res);
    }

    /**
     * Tests the doGetProcess with exception
     * 
     * @test
     * @covers \ProcessMaker\Services\Api\Project::doGetProcess()
     */
    public function it_should_test_the_do_get_process_method_with_exception()
    {
        $project = new Project();

        //This asserts the expected exception
        $this->expectExceptionMessage("**ID_PROJECT_DOES_NOT_EXIST**");
        $project->doGetProcess('');
    }
}
