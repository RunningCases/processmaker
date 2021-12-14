<?php

use Faker\Generator as Faker;

$factory->define(\ProcessMaker\Model\ProcessUser::class, function(Faker $faker) {
    return [
        'PU_UID' => G::generateUniqueID(),
        'PRO_UID' => G::generateUniqueID(),
        'USR_UID' => G::generateUniqueID(),
        'PU_TYPE' => 'SUPERVISOR'
    ];
});

// Create a process with the foreign keys
$factory->state(\ProcessMaker\Model\ProcessUser::class, 'foreign_keys', function (Faker $faker) {
    // Create user
    $user = factory(\ProcessMaker\Model\User::class)->create();
    $process = factory(\ProcessMaker\Model\Process::class)->create();

    return [
        'PU_UID' => G::generateUniqueID(),
        'PRO_UID' => $process->PRO_UID,
        'USR_UID' => $user->USR_UID,
        'PU_TYPE' => 'SUPERVISOR'
    ];
});