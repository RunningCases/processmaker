<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 * @package ProcessMaker\Model
 */
class Department extends Model
{
    // Set our table name
    protected $table = 'DEPARTMENT';
    // We do not store timestamps
    public $timestamps = false;
}
