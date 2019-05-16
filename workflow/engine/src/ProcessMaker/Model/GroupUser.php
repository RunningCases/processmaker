<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $table = 'GROUP_USER';
    // We do not have create/update timestamps for this table
    public $timestamps = false;
}

