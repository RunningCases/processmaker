<?php

use Faker\Generator as Faker;

$factory->define(\ProcessMaker\Model\AppDelay::class, function (Faker $faker) {
    $actions = ['CANCEL', 'PAUSE', 'REASSIGN'];
    return [
        'APP_DELAY_UID' => G::generateUniqueID(),
        'PRO_UID' => G::generateUniqueID(),
        'APP_UID' => G::generateUniqueID(),
        'APP_NUMBER' => $faker->unique()->numberBetween(1000),
        'APP_THREAD_INDEX' => 1,
        'APP_DEL_INDEX' => $faker->unique()->numberBetween(10),
        'APP_TYPE' => $faker->randomElement($actions),
        'APP_STATUS' => 'TO_DO',
        'APP_NEXT_TASK' => 0,
        'APP_DELEGATION_USER' => G::generateUniqueID(),
        'APP_ENABLE_ACTION_USER' => G::generateUniqueID(),
        'APP_ENABLE_ACTION_DATE' => $faker->dateTime(),
        'APP_DISABLE_ACTION_USER' => G::generateUniqueID(),
        'APP_DISABLE_ACTION_DATE' => $faker->dateTime(),
        'APP_AUTOMATIC_DISABLED_DATE' => '',
        'APP_DELEGATION_USER_ID' => $faker->unique()->numberBetween(1000),
        'PRO_ID' => $faker->unique()->numberBetween(1000),
    ];
});

// Create a delegation with the foreign keys
$factory->state(\ProcessMaker\Model\AppDelay::class, 'paused_foreign_keys', function (Faker $faker) {
    // Create values in the foreign key relations
    $user = factory(\ProcessMaker\Model\User::class)->create();
    $process = factory(\ProcessMaker\Model\Process::class)->create();
    $task = factory(\ProcessMaker\Model\Task::class)->create([
        'PRO_UID' => $process->PRO_UID,
        'PRO_ID' => $process->PRO_ID
    ]);
    $application = factory(\ProcessMaker\Model\Application::class)->create([
        'PRO_UID' => $process->PRO_UID,
        'APP_INIT_USER' => $user->USR_UID,
        'APP_CUR_USER' => $user->USR_UID
    ]);
    $delegation1 = factory(\ProcessMaker\Model\Delegation::class)->create([
        'PRO_UID' => $process->PRO_UID,
        'PRO_ID' => $process->PRO_ID,
        'TAS_UID' => $task->TAS_UID,
        'TAS_ID' => $task->TAS_ID,
        'APP_NUMBER' => $application->APP_NUMBER,
        'APP_UID' => $application->APP_UID,
        'DEL_THREAD_STATUS' => 'CLOSED',
        'USR_UID' => $user->USR_UID,
        'USR_ID' => $user->USR_ID,
        'DEL_PREVIOUS' => 0,
        'DEL_INDEX' => 1
    ]);
    $delegation = factory(\ProcessMaker\Model\Delegation::class)->create([
        'PRO_UID' => $process->PRO_UID,
        'PRO_ID' => $process->PRO_ID,
        'TAS_UID' => $task->TAS_UID,
        'TAS_ID' => $task->TAS_ID,
        'APP_NUMBER' => $application->APP_NUMBER,
        'APP_UID' => $application->APP_UID,
        'DEL_THREAD_STATUS' => 'OPEN',
        'USR_UID' => $user->USR_UID,
        'USR_ID' => $user->USR_ID,
        'DEL_PREVIOUS' => $delegation1->DEL_INDEX,
        'DEL_INDEX' => $delegation1->DEL_INDEX++
    ]);

    // Return with default values
    return [
        'APP_DELAY_UID' => G::generateUniqueID(),
        'PRO_UID' => $process->PRO_UID,
        'PRO_ID' => $process->PRO_ID,
        'APP_UID' => $application->APP_UID,
        'APP_NUMBER' => $application->APP_NUMBER,
        'APP_DEL_INDEX' => $delegation->DEL_INDEX,
        'APP_TYPE' => 'PAUSE',
        'APP_STATUS' => $application->APP_STATUS,
        'APP_DELEGATION_USER' => $user->USR_UID,
        'APP_DELEGATION_USER_ID' => $user->USR_ID,
        'APP_ENABLE_ACTION_USER' => G::generateUniqueID(),
        'APP_ENABLE_ACTION_DATE' => $faker->dateTime(),
        'APP_DISABLE_ACTION_USER' => 0,
    ];
});
