<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Application extends Model
{
    protected $table = "APPLICATION";
    protected $primaryKey = 'APP_NUMBER';
    public $incrementing = false;
    // No timestamps
    public $timestamps = false;
    // Status id
    const STATUS_DRAFT = 1;
    const STATUS_TODO = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    // Status name and status id
    public static $app_status_values = ['DRAFT' => 1, 'TO_DO' => 2, 'COMPLETED' => 3, 'CANCELLED' => 4];

    public function delegations()
    {
        return $this->hasMany(Delegation::class, 'APP_UID', 'APP_UID');
    }

    public function currentUser()
    {
        return $this->belongsTo(User::class, 'APP_CUR_USER', 'USR_UID');
    }

    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'APP_INIT_USER', 'USR_UID');
    }

    /**
     * Scope for query to get the positive cases
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePositivesCases($query)
    {
        $result = $query->where('APP_NUMBER', '>', 0);
        return $result;
    }

    /**
     * Scope for query to get the application by APP_UID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $appUid
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAppUid($query, $appUid)
    {
        $result = $query->where('APP_UID', '=', $appUid);
        return $result;
    }

    /**
     * Scope for query to get the application by status Id
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param integer $status
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatusId($query, int $status)
    {
        $result = $query->where('APP_STATUS_ID', '=', $status);
        return $result;
    }

    /**
     * Scope for query to get the applications by PRO_UID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $proUid
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProUid($query, $proUid)
    {
        $result = $query->where('PRO_UID', '=', $proUid);
        return $result;
    }

    /**
     * Get Applications by PRO_UID, ordered by APP_NUMBER.
     *
     * @param string $proUid
     *
     * @return object
     * @see ReportTables->populateTable()
     */
    public static function getByProUid($proUid)
    {
        $query = Application::query()
            ->select()
            ->proUid($proUid)
            ->positivesCases()
            ->orderBy('APP_NUMBER', 'ASC');
        return $query->get();
    }

    /**
     * Get information related to the created case
     *
     * @param string $appUid
     *
     * @return array|bool
     */
    public static function getCase($appUid)
    {
        $query = Application::query()->select(['APP_STATUS', 'APP_INIT_USER']);
        $query->appUid($appUid);
        $result = $query->get()->toArray();
        $firstElement = head($result);

        return $firstElement;
    }

    /**
     * Update properties
     *
     * @param string $appUid
     * @param array $fields
     *
     * @return array
    */
    public static function updateColumns($appUid, $fields)
    {
        $properties = [];
        $properties['APP_ROUTING_DATA'] = !empty($fields['APP_ROUTING_DATA']) ? serialize($fields['APP_ROUTING_DATA']) : serialize([]);

        // This column will to update only when the thread is related to the user
        if (!empty($fields['APP_CUR_USER'])) {
            $properties['APP_CUR_USER'] = $fields['APP_CUR_USER'];
        }
        Application::query()->appUid($appUid)->update($properties);

        return $properties;
    }

    /**
     * Get Applications by PRO_UID, ordered by APP_NUMBER.
     *
     * @param string $proUid
     * @param int $status
     *
     * @return object
     * @see ReportTables->populateTable()
     */
    public static function getCountByProUid(string $proUid, $status = 2)
    {
        $query = Application::query()
            ->select()
            ->proUid($proUid)
            ->statusId($status)
            ->positivesCases();

        return $query->get()->count();
    }
}
