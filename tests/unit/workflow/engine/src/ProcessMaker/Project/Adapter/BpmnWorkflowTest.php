<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Project\Adapter;

use Exception;
use Faker\Factory;
use G;
use ProcessMaker\Model\BpmnProject;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\User;
use ProcessMaker\Project\Adapter\BpmnWorkflow;
use ProcessMaker\Importer\XmlImporter;
use Tests\TestCase;

class BpmnWorkflowTest extends TestCase
{
    private $user;

    /**
     * Set up testing.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->truncateNonInitialModels();
        $this->user = User::factory()->create();
    }

    /**
     * Creation of a bpmn project.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::create()
     */
    public function it_should_create_bpmn_project()
    {
        $faker = Factory::create();
        $data = [
            'PRJ_UID' => G::generateUniqueID(),
            'PRJ_AUTHOR' => G::generateUniqueID(),
            'PRJ_NAME' => $faker->title,
            'PRJ_DESCRIPTION' => $faker->text,
            'PRJ_TYPE' => $faker->name,
            'PRJ_CATEGORY' => $faker->word,
            'PRO_ID' => $faker->randomDigit,
            'PRO_STATUS' => 'ACTIVE'
        ];
        $bpmnWorkflow = new BpmnWorkflow();
        $bpmnWorkflow->create($data);

        $bpmnProject = BpmnProject::where('PRJ_UID', '=', $data['PRJ_UID'])
                ->get();

        $this->assertNotNull($bpmnProject);
    }

    /**
     * We get an exception when the data is incorrect.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::create()
     */
    public function it_should_create_bpmn_project_with_incorrect_data()
    {
        $faker = Factory::create();
        $data = [
            'PRJ_UID' => []
        ];
        $bpmnWorkflow = new BpmnWorkflow();

        $this->expectException(Exception::class);
        $bpmnWorkflow->create($data);
    }

    /**
     * An exception is obtained if we try to enter an existing title.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::create()
     */
    public function it_should_create_bpmn_project_with_duplicate_title()
    {
        $faker = Factory::create();
        $title = $faker->title;
        \ProcessMaker\Model\Process::factory()->create([
            'PRO_TITLE' => $title
        ]);

        $data = [
            'PRJ_UID' => G::generateUniqueID(),
            'PRJ_AUTHOR' => G::generateUniqueID(),
            'PRJ_NAME' => $title,
            'PRJ_DESCRIPTION' => $faker->text,
            'PRJ_TYPE' => $faker->name,
            'PRJ_CATEGORY' => $faker->word,
            'PRO_ID' => $faker->randomDigit,
            'PRO_STATUS' => 'ACTIVE'
        ];
        $bpmnWorkflow = new BpmnWorkflow();

        $this->expectException(Exception::class);
        $bpmnWorkflow->create($data);
    }

    /**
     * Create a project from a data structure.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct()
     */
    public function it_should_create_from_structure()
    {
        $faker = Factory::create();

        $projectDataFilename = PATH_TRUNK . "tests/resources/projectData.json";
        $json = file_get_contents($projectDataFilename);
        $projectData = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $projectData['prj_uid'] = G::generateUniqueID();
        $projectData["process"]["pro_id"] = $faker->randomDigit;

        $bpmnWorkflow = new BpmnWorkflow();
        $result = $bpmnWorkflow->createFromStruct($projectData, true, null);
        $result = json_encode($result);
        $this->assertStringContainsString($projectData['prj_uid'], $result);
    }

    /**
     * Get an exception if there is an invalid name in the data structure.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct()
     */
    public function it_should_create_from_structure_invalid_name()
    {
        $faker = Factory::create();

        $projectDataFilename = PATH_TRUNK . "tests/resources/projectData.json";
        $json = file_get_contents($projectDataFilename);
        $projectData = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $projectData['prj_uid'] = G::generateUniqueID();
        $projectData["process"]["pro_id"] = $faker->randomDigit;

        $bpmnWorkflow = new BpmnWorkflow();

        $projectData['prj_name'] = '';

        $this->expectException(Exception::class);
        $bpmnWorkflow->createFromStruct($projectData, true, null);
    }

    /**
     * Get an exception if there is a duplicate name.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct()
     */
    public function it_should_create_from_structure_with_duplicate_name()
    {
        $faker = Factory::create();

        $projectDataFilename = PATH_TRUNK . "tests/resources/projectData.json";
        $json = file_get_contents($projectDataFilename);
        $projectData = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $projectData['prj_uid'] = G::generateUniqueID();
        $projectData["process"]["pro_id"] = $faker->randomDigit;

        $bpmnWorkflow = new BpmnWorkflow();

        \ProcessMaker\Model\BpmnProject::factory()->create([
            'PRJ_NAME' => $projectData['prj_name']
        ]);

        \ProcessMaker\Model\Process::factory()->create([
            'PRO_TITLE' => $projectData['prj_name']
        ]);

        $this->expectException(Exception::class);
        $bpmnWorkflow->createFromStruct($projectData, true, null);
    }

    /**
     * We get an exception if the type field does not exist in the activity.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct()
     */
    public function it_should_create_from_structure_invalid_activity_type()
    {
        $faker = Factory::create();

        $projectDataFilename = PATH_TRUNK . "tests/resources/projectData.json";
        $json = file_get_contents($projectDataFilename);
        $projectData = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $projectData['prj_uid'] = G::generateUniqueID();
        $projectData["process"]["pro_id"] = $faker->randomDigit;

        $bpmnWorkflow = new BpmnWorkflow();

        $projectData['prj_name'] = $faker->name;
        unset($projectData['diagrams']['0']['activities']['0']['act_type']);

        $this->expectException(Exception::class);
        $bpmnWorkflow->createFromStruct($projectData, true, null);
    }

    /**
     * We get an exception if the type field does not exist in the event.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct()
     */
    public function it_should_create_from_structure_invalid_event_type()
    {
        $faker = Factory::create();

        $projectDataFilename = PATH_TRUNK . "tests/resources/projectData.json";
        $json = file_get_contents($projectDataFilename);
        $projectData = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $projectData['prj_uid'] = G::generateUniqueID();
        $projectData["process"]["pro_id"] = $faker->randomDigit;

        $bpmnWorkflow = new BpmnWorkflow();

        $projectData['prj_name'] = $faker->name;
        unset($projectData['diagrams']['0']['events']['0']['evn_type']);

        $this->expectException(Exception::class);
        $bpmnWorkflow->createFromStruct($projectData, true, null);
    }

    /**
     * We get an exception if the marker field does not exist in the event.
     * @test
     * @covers \ProcessMaker\Project\Adapter\BpmnWorkflow::createFromStruct()
     */
    public function it_should_create_from_structure_invalid_event_marker()
    {
        $faker = Factory::create();

        $projectDataFilename = PATH_TRUNK . "tests/resources/projectData.json";
        $json = file_get_contents($projectDataFilename);
        $projectData = json_decode($json, JSON_OBJECT_AS_ARRAY);
        $projectData['prj_uid'] = G::generateUniqueID();
        $projectData["process"]["pro_id"] = $faker->randomDigit;

        $bpmnWorkflow = new BpmnWorkflow();

        $projectData['prj_name'] = $faker->name;
        unset($projectData['diagrams']['0']['events']['0']['evn_marker']);

        $this->expectException(Exception::class);
        $bpmnWorkflow->createFromStruct($projectData, true, null);
    }
}
