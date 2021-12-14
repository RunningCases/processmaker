<?php
/**
 * Model factory for a group user relation
 */
use Faker\Generator as Faker;

$factory->define(\ProcessMaker\Model\GroupUser::class, function(Faker $faker) {
    return [
        'GRP_UID' => G::generateUniqueID(),
        'GRP_ID' => $faker->unique()->numberBetween(1, 2000),
        'USR_UID' => G::generateUniqueID()
    ];
});

// Create columns from a table with the foreign keys
$factory->state(\ProcessMaker\Model\GroupUser::class, 'foreign_keys', function (Faker $faker) {
    // Create values in the foreign key relations
    $user = factory(\ProcessMaker\Model\User::class)->create();
    $group = factory(\ProcessMaker\Model\Groupwf::class)->create();
    return [
        'GRP_UID' => $group->GRP_UID,
        'GRP_ID' => $group->GRP_ID,
        'USR_UID' => $user->USR_UID,
    ];
});