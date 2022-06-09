<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Project;

use Exception;
use Faker\Factory;
use G;
use ProcessMaker\Model\Dynaform;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\WebEntry;
use ProcessMaker\Project\Workflow;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    private $workflow;
    private $directories;
    private $files;
    private $faker;

    /**
     * This method sets the values before starting any test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->workflow = new Workflow();
        $this->directories = [];
        $this->files = [];
        $this->faker = Factory::create();
    }

    /**
     * This method is executed after each test.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->files as $value) {
            unlink($value);
        }
        foreach ($this->directories as $value) {
            rmdir($value);
        }
    }

    /**
     * This test ensures that the getData method returns the correct data.
     * @test
     * @covers \ProcessMaker\Project\Workflow::getData()
     */
    public function it_should_return_the_data_when_the_project_id_is_valid()
    {
        $process = factory(Process::class)->create();
        $dynaforms = factory(Dynaform::class, 5)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        factory(WebEntry::class, 5)->create([
            'PRO_UID' => $process->PRO_UID
        ]);

        //xmlForms
        if (!is_dir(PATH_DYNAFORM)) {
            mkdir(PATH_DYNAFORM);
        }
        $directory = PATH_DYNAFORM . $process->PRO_UID . "/";
        $this->directories[] = $directory;
        mkdir($directory);
        foreach ($dynaforms as $dynaform) {
            Dynaform::where('PRO_UID', $process->PRO_UID)
                    ->where('DYN_UID', $dynaform->DYN_UID)
                    ->update(['DYN_FILENAME' => $process->PRO_UID . '/' . $dynaform->DYN_UID]);

            $dynUid = $dynaform->DYN_UID;
            $data = '';
            $filename = $directory . $dynUid . ".xml";
            $this->files[] = $filename;
            file_put_contents($filename, $data);

            $filename = $directory . $dynUid . ".html";
            $this->files[] = $filename;
            file_put_contents($filename, $data);
        }

        //template
        if (!is_dir(PATH_DATA_MAILTEMPLATES)) {
            mkdir(PATH_DATA_MAILTEMPLATES);
        }
        $directory = PATH_DATA_MAILTEMPLATES . $process->PRO_UID;
        $this->directories[] = $directory;
        mkdir($directory);

        $filename = $directory . "/test.html";
        $this->files[] = $filename;
        file_put_contents($filename, '');

        //public files
        if (!is_dir(PATH_DATA_PUBLIC)) {
            mkdir(PATH_DATA_PUBLIC);
        }
        $directory = PATH_DATA_PUBLIC . $process->PRO_UID;
        $this->directories[] = $directory;
        mkdir($directory);

        $filename = $directory . "/wsClient.php";
        $this->files[] = $filename;
        file_put_contents($filename, '');

        $actual = $this->workflow->getData($process->PRO_UID);

        $this->assertCount(2, $actual);
        $this->assertArrayHasKey('process', $actual[0]);
        $this->assertArrayHasKey('DYNAFORMS', $actual[1]);
    }

    /**
     * This test should throw an exception when the parameter is not correct.
     * @test
     * @covers \ProcessMaker\Project\Workflow::getData()
     */
    public function it_should_throw_exception_when_get_data_is_failed()
    {
        $proUid = $this->faker->regexify("/[a-zA-Z]{32}/");

        $this->expectException(Exception::class);
        $actual = $this->workflow->getData($proUid);
    }

    /**
     * Test if the target xml dynaform was created correctly
     * 
     * @test
     * @covers \ProcessMaker\Project\Workflow::createDataFileByArrayFile()
     */
    public function it_review_creation_of_xml_dynaforms()
    {
        $dyna1 = [
            'file_name' => $this->faker->sentence(2),
            'file_path' => '7256532885f4e6876cc8a50043688438\\3953439805f4e689265e2b8072489041.xml',
            'file_content' => '<?xml version="1.0" encoding="UTF-8"?><dynaForm type="xmlform" name="7256532885f4e6876cc8a50043688438/3953439805f4e689265e2b8072489041" width="500" enabletemplate="0" mode="" nextstepsave="prompt"></dynaForm>',
        ];
        $formatFiles = [];
        $formatFiles['dynaforms'][] = $dyna1;
        $this->workflow->createDataFileByArrayFile($formatFiles);
        $this->assertTrue(file_exists(PATH_DYNAFORM . '7256532885f4e6876cc8a50043688438/3953439805f4e689265e2b8072489041.xml'));
        // Remove the xml created
        G::rm_dir(PATH_DYNAFORM . '7256532885f4e6876cc8a50043688438/3953439805f4e689265e2b8072489041.xml');
    }
}
