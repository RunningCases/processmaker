<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = "APPLICATION";
    // Our custom timestamp columns
    const CREATED_AT = 'APP_CREATE_DATE';
    const UPDATED_AT = 'APP_UPDATE_DATE';

    public function delegations()
    {
        return $this->hasMany(Delegation::class, 'APP_UID', 'APP_UID');
    }

    public function parent()
    {
        return $this->hasOne(Application::class, 'APP_PARENT', 'APP_UID');
    }

    public function currentUser()
    {
        return $this->hasOne(User::class, 'APP_CUR_USER', 'USR_UID');
    }

}
