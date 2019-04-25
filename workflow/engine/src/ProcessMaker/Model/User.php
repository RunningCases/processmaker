<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "USERS";

    /**
     * Returns the delegations this user has (all of them)
     */
    public function delegations()
    {
        return $this->hasMany(Delegation::class, 'USR_ID', 'USR_ID');

    }
}
