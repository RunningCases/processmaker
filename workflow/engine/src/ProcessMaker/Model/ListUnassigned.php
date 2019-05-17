<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;
use ListUnassigned as PropelListUnassigned;

class ListUnassigned extends Model
{
    protected $table = "LIST_UNASSIGNED";
    // No timestamps
    public $timestamps = false;

    /**
     * Returns the application this belongs to
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'APP_UID', 'APP_UID');
    }

    /**
     * Return the process task this belongs to
     */
    public function task()
    {
        return $this->belongsTo(Task::class, 'TAS_ID', 'TAS_ID');
    }

    /**
     * Return the process this belongs to
     */
    public function process()
    {
        return $this->belongsTo(Process::class, 'PRO_ID', 'PRO_ID');
    }

    /**
     * Get count
     *
     * @param string $userUid
     * @param array $filters
     *
     * @return array
    */
    public static function doCount($userUid, $filters = [])
    {
        $list = new PropelListUnassigned();
        $result = $list->getCountList($userUid, $filters);

        return $result;
    }

    /**
     * Search data
     *
     * @param string $userUid
     * @param array $filters
     *
     * @return array
     */
    public static function loadList($userUid, $filters = [])
    {
        $list = new PropelListUnassigned();
        $result = $list->loadList($userUid, $filters);

        return $result;
    }
}

