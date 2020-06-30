<?php

use Faker\Generator as Faker;

$factory->define(\ProcessMaker\Model\AppDocument::class, function (Faker $faker) {
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
    return [
        'APP_DOC_UID' => G::generateUniqueID(),
        'APP_DOC_FILENAME' => $faker->name . '.' . $faker->fileExtension,
        'APP_DOC_TITLE' => $faker->title,
        'APP_DOC_COMMENT' => '',
        'DOC_VERSION' => 1,
        'APP_UID' => $application->APP_UID,
        'DEL_INDEX' => 1,
        'DOC_UID' => -1,
        'DOC_ID' => 0,
        'USR_UID' => $user->USR_UID,
        'APP_DOC_TYPE' => 'ATTACHED',
        'APP_DOC_CREATE_DATE' => $faker->dateTime(),
        'APP_DOC_INDEX' => 1,
        'FOLDER_UID' => '',
        'APP_DOC_PLUGIN' => '',
        'APP_DOC_TAGS' => null,
        'APP_DOC_STATUS' => 'ACTIVE',
        'APP_DOC_STATUS_DATE' => '',
        'APP_DOC_FIELDNAME' => '',
        'APP_DOC_DRIVE_DOWNLOAD' => 'a:0:{}',
        'SYNC_WITH_DRIVE' => 'UNSYNCHRONIZED',
        'SYNC_PERMISSIONS' => null
    ];
});
