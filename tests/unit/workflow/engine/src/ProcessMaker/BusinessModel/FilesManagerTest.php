<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use Faker\Factory;
use G;
use ProcessMaker\BusinessModel\FilesManager;
use ProcessMaker\Model\EmailEvent as EmailEventModel;
use ProcessMaker\Model\Process as ProcessModel;
use ProcessMaker\Model\ProcessFiles as ProcessFilesModel;
use ProcessMaker\Model\User as UserModel;
use Tests\TestCase;

class FilesManagerTest extends TestCase
{
    private $faker;
    private $directories;

    /**
     * Set up method.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->directories = [];
    }

    /**
     * Tear down method.
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->directories = array_reverse($this->directories);
        foreach ($this->directories as $value) {
            rmdir($value);
        }
    }

    /**
     * This test verifies if a file is missing.
     * @test
     * @covers \ProcessMaker\BusinessModel\FilesManager::deleteProcessFilesManager()
     */
    public function it_should_deleted_public_files_when_not_exist()
    {
        $processFiles = factory(ProcessFilesModel::class)->create();

        $emailEvent = factory(EmailEventModel::class)->create([
            'PRF_UID' => $processFiles->PRF_UID
        ]);

        $filesManager = new FilesManager();

        $this->expectException(Exception::class);
        $filesManager->deleteProcessFilesManager($emailEvent->PRJ_UID, $processFiles->PRF_UID, true);
    }

    /**
     * This represents the windows and linux separators.
     */
    public function directorySeparator()
    {
        return [
            ["linux", "/"],
            ["windows", "\\"]
        ];
    }

    /**
     * This test verifies the deletion of a template.
     * @test
     * @covers \ProcessMaker\BusinessModel\FilesManager::deleteProcessFilesManager()
     * @dataProvider directorySeparator
     */
    public function it_should_deleted_a_template_file($type, $separator)
    {
        $user = factory(UserModel::class)->create([
            'USR_UID' => G::generateUniqueID()
        ]);

        $process = factory(ProcessModel::class)->create([
            'PRO_UID' => G::generateUniqueID()
        ]);

        //create a template file
        $directory = PATH_DATA_SITE;
        if (!is_dir($directory)) {
            mkdir($directory);
            $this->directories[] = $directory;
        }
        $directory = PATH_DATA_PUBLIC;
        if (!is_dir($directory)) {
            mkdir($directory);
            $this->directories[] = $directory;
        }
        $directory = PATH_DATA_PUBLIC . $process->PRO_UID;
        if (!is_dir($directory)) {
            mkdir($directory);
            $this->directories[] = $directory;
        }
        $fileName = "template1.html";
        $path = $directory . "/" . $fileName;
        file_put_contents($path, $this->faker->randomHtml());

        $processFiles = factory(ProcessFilesModel::class)->create([
            'PRF_UID' => G::generateUniqueID(),
            'PRO_UID' => $process->PRO_UID,
            'USR_UID' => $user->USR_UID,
            'PRF_PATH' => $separator . $fileName
        ]);

        $filesManager = new FilesManager();
        $filesManager->deleteProcessFilesManager($process->PRO_UID, $processFiles->PRF_UID);

        //assert empty registry
        $expectedEmptyObject = ProcessFilesModel::where('PRF_UID', '=', $processFiles->PRF_UID)->first();
        $this->assertTrue(empty($expectedEmptyObject));

        //assert empty file
        $this->assertTrue(!file_exists($path));
    }

    /**
     * This test verifies the deletion of a public file.
     * @test
     * @covers \ProcessMaker\BusinessModel\FilesManager::deleteProcessFilesManager()
     * @dataProvider directorySeparator
     */
    public function it_should_deleted_a_public_file($type, $separator)
    {
        $user = factory(UserModel::class)->create([
            'USR_UID' => G::generateUniqueID()
        ]);

        $process = factory(ProcessModel::class)->create([
            'PRO_UID' => G::generateUniqueID()
        ]);

        //create a temporal file
        $directory = PATH_DATA_SITE;
        if (!is_dir($directory)) {
            mkdir($directory);
            $this->directories[] = $directory;
        }
        $directory = PATH_DATA_MAILTEMPLATES;
        if (!is_dir($directory)) {
            mkdir($directory);
            $this->directories[] = $directory;
        }
        $directory = PATH_DATA_MAILTEMPLATES . $process->PRO_UID;
        if (!is_dir($directory)) {
            mkdir($directory);
            $this->directories[] = $directory;
        }
        $fileName = "temporal.html";
        $path = $directory . "/" . $fileName;
        file_put_contents($path, $this->faker->randomHtml());

        $processFiles = factory(ProcessFilesModel::class)->create([
            'PRF_UID' => G::generateUniqueID(),
            'PRO_UID' => $process->PRO_UID,
            'USR_UID' => $user->USR_UID,
            'PRF_PATH' => $separator . $fileName
        ]);

        $filesManager = new FilesManager();
        $filesManager->deleteProcessFilesManager($process->PRO_UID, $processFiles->PRF_UID);

        //assert empty registry
        $expectedEmptyObject = ProcessFilesModel::where('PRF_UID', '=', $processFiles->PRF_UID)->first();
        $this->assertTrue(empty($expectedEmptyObject));

        //assert empty file
        $this->assertTrue(!file_exists($path));
    }

    /**
     * This test verifies the removal of a template that is being used by an 
     * intermediate email event.
     * @test
     * @covers \ProcessMaker\BusinessModel\FilesManager::deleteProcessFilesManager()
     */
    public function it_should_deleted_public_files_with_event_relation()
    {
        $user = factory(UserModel::class)->create([
            'USR_UID' => G::generateUniqueID()
        ]);

        $process = factory(ProcessModel::class)->create([
            'PRO_UID' => G::generateUniqueID()
        ]);

        $processFiles = factory(ProcessFilesModel::class)->create([
            'PRF_UID' => G::generateUniqueID(),
            'PRO_UID' => $process->PRO_UID,
            'USR_UID' => $user->USR_UID,
            'PRF_PATH' => '/'
        ]);

        $emailEvent = factory(EmailEventModel::class)->create([
            'PRF_UID' => $processFiles->PRF_UID
        ]);

        $filesManager = new FilesManager();

        $this->expectException(Exception::class);
        $filesManager->deleteProcessFilesManager($process->PRO_UID, $processFiles->PRF_UID, true);
    }
}
