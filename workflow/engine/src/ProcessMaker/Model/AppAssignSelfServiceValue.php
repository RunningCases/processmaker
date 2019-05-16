<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class AppAssignSelfServiceValue extends Model
{
    protected $table = 'APP_ASSIGN_SELF_SERVICE_VALUE';
    protected $primaryKey = 'ID';
    // We do not have create/update timestamps for this table
    public $timestamps = false;
}

