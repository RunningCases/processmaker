<?php
use Faker\Generator as Faker;
use ProcessMaker\BusinessModel\Cases as BmCases;

$factory->define(\ProcessMaker\Model\Application::class, function(Faker $faker) {

    $process = \ProcessMaker\Model\Process::all()->random();
    return [
        'APP_UID' => G::generateUniqueID(),
        'APP_TITLE' => G::generateUniqueID(),
        'APP_NUMBER' => $faker->unique()->numberBetween(1000),
        'APP_STATUS' => 'TO_DO',
        'APP_STATUS_ID' => 1,
        'PRO_UID' => $process->PRO_UID,
        'APP_PARALLEL' => 'N',
        'APP_INIT_USER' => \ProcessMaker\Model\User::all()->random()->USR_UID,
        'APP_CUR_USER' => \ProcessMaker\Model\User::all()->random()->USR_UID,
        'APP_PIN' => G::generateUniqueID(),
        'APP_CREATE_DATE' => $faker->dateTime(),
        'APP_UPDATE_DATE' => $faker->dateTime(),
        'APP_INIT_DATE' => $faker->dateTime(),
        'APP_DATA' => serialize(['APP_NUMBER' => 12])
    ];
});