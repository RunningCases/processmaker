<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class AppDocument extends Model
{
    protected $table = "APP_DOCUMENT";
    protected $primaryKey = 'APP_DOC_UID';
    public $incrementing = false;
    public $timestamps = false;

}
