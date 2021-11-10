<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $table = 'TRANSLATION';
    protected $primaryKey = ['TRN_CATEGORY', 'TRN_ID', 'TRN_LANG'];
    public $incrementing = false;
    public $timestamps = false;

}
