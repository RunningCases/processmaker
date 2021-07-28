<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class AppDelay extends Model
{
    protected $table = 'APP_DELAY';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'APP_DELAY_UID',
        'PRO_UID',
        'PRO_ID',
        'APP_UID',
        'APP_NUMBER',
        'APP_THREAD_INDEX',
        'APP_DEL_INDEX',
        'APP_TYPE',
        'APP_STATUS',
        'APP_DELEGATION_USER',
        'APP_DELEGATION_USER_ID'.
        'APP_ENABLE_ACTION_USER',
        'APP_ENABLE_ACTION_DATE',
        'APP_DISABLE_ACTION_DATE',
    ];
}
