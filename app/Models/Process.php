<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    /**
     * Retrieve all applications that belong to this process
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'PRO_ID', 'PRO_ID');

    }
}
