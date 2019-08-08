<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class EmailServer extends Model
{
    protected $table = 'EMAIL_SERVER';
    protected $primaryKey = 'MESS_UID';
    public $timestamps = false;

}
