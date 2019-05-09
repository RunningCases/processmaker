<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class Groupwf extends Model
{
    protected $table = 'GROUPWF';
    protected $primaryKey = 'GRP_ID';
    // We do not have create/update timestamps for this table
    public $timestamps = false;
}

