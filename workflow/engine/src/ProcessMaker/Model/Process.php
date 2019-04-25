<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Process
 * @package ProcessMaker\Model
 *
 * Represents a business process object in the system.
 */
class Process extends Model
{
    // Set our table name
    protected $table = 'PROCESS';
    // We do have a created at, but we don't store an updated at
    const CREATED_AT = 'PRO_CREATE_DATE';
    const UPDATED_AT = null;
    
    /**
     * Retrieve all applications that belong to this process
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'PRO_ID', 'PRO_ID');

    }
}