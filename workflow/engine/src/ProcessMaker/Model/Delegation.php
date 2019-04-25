<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class Delegation extends Model
{
    protected $table = "APP_DELEGATION";

    // We don't have our standard timestamp columns
    protected $timestamps = false;

    /**
     * Returns the application this delegation belongs to
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'APP_UID', 'APP_UID');
    }

    /**
     * Returns the user this delegation belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID', 'USR_ID');
    }

    /**
     * Return the process task this belongs to
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'TAS_ID', 'TAS_ID');
    }

    /**
     * Return the process this delegation belongs to
     */
    public function process()
    {
        return $this->belongsTo(Process::class, 'PRO_ID', 'PRO_ID');
    }
}
