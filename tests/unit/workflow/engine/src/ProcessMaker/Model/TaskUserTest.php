<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Model;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use ProcessMaker\Model\Task;
use ProcessMaker\Model\TaskUser;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class TaskUserTest
 *
 * @coversDefaultClass \ProcessMaker\Model\TaskUser
 */
class TaskUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Create task assigment
     *
     * @param string $type
     * @param string $relation
     *
     * @return array
     */
    public function createAssigment($type = 'NORMAL', $relation = 'USER')
    {
        if ($type === 'NORMAL'){
            if ($relation === 'USER'){
                $assigment = TaskUser::factory()->normal_assigment_user()->create();
            } else {
                $assigment = TaskUser::factory()->normal_assigment_group()->create();
            }
        } else {
            if ($relation === 'USER'){
                $assigment = TaskUser::factory()->adhoc_assigment_user()->create();
            } else {
                $assigment = TaskUser::factory()->adhoc_assigment_group()->create();
            }
        }

        return $assigment;
    }
    /**
     * Test belongs to TAS_UID
     *
     * @covers \ProcessMaker\Model\TaskUser::task()
     * @test
     */
    public function it_has_a_task()
    {
        $assigment = TaskUser::factory()->create([
            'TAS_UID' => function () {
                return Task::factory()->create()->TAS_UID;
            }
        ]);
        $this->assertInstanceOf(Task::class, $assigment->task);
    }

    /**
     * Test belongs to USR_UID
     *
     * @covers \ProcessMaker\Model\TaskUser::user()
     * @test
     */
    public function it_has_a_user()
    {
        $assigment = TaskUser::factory()->create([
            'USR_UID' => function () {
                return User::factory()->create()->USR_UID;
            }
        ]);
        $this->assertInstanceOf(User::class, $assigment->user);
    }

    /**
     * Test the assigment in the task
     *
     * @covers \ProcessMaker\Model\TaskUser::scopeAssigment()
     * @covers \ProcessMaker\Model\TaskUser::getAssigment()
     * @test
     */
    public function it_has_an_assigment()
    {
        // Create factory
        $assigment = $this->createAssigment();
        // Create the TaskUser object
        $taskUser = new TaskUser();
        $response = $taskUser->getAssigment($assigment->TAS_UID, $assigment->USR_UID);
        $this->assertNotEmpty($response);
        // Create factory
        $assigment = $this->createAssigment('NORMAL', 'GROUP');
        // Create the TaskUser object
        $taskUser = new TaskUser();
        $response = $taskUser->getAssigment($assigment->TAS_UID, $assigment->USR_UID);
        $this->assertNotEmpty($response);
    }
}