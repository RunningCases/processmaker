<?php
/**
 * Model factory for a groups
 */
use Faker\Generator as Faker;

$factory->define(\ProcessMaker\Model\Groupwf::class, function(Faker $faker) {
    return [
        'GRP_UID' => G::generateUniqueID(),
        //'GRP_ID' The incremental fields of the tables must not be specified in the creation list.
        'GRP_TITLE' => $faker->sentence(2),
        'GRP_STATUS' => 'ACTIVE',
        'GRP_LDAP_DN' => '',
        'GRP_UX' => 'NORMAL',
    ];
});

