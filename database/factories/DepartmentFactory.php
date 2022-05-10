<?php

use Faker\Generator as Faker;

$factory->define(\ProcessMaker\Model\Department::class, function (Faker $faker) {
    return [
        'DEP_UID' => G::generateUniqueID(),
        'DEP_TITLE' => $faker->sentence(2),
        'DEP_PARENT' => '',
        'DEP_MANAGER' => '',
        'DEP_LOCATION' => 0,
        'DEP_STATUS' => 'ACTIVE',
        'DEP_REF_CODE' => '',
        'DEP_LDAP_DN' => '',
    ];
});
