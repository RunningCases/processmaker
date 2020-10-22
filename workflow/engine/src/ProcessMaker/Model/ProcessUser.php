<?php

namespace ProcessMaker\Model;

use Illuminate\Database\Eloquent\Model;

class ProcessUser extends Model
{
    protected $table = 'PROCESS_USER';
    protected $primaryKey = 'PU_UID';
    // We do not have create/update timestamps for this table
    public $timestamps = false;

    /**
     * Scope process supervisor
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userUid
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessSupervisor($query, $userUid)
    {
        $query->where('USR_UID', $userUid);
        $query->where('PU_TYPE', 'SUPERVISOR');
        $query->joinProcess();

        return $query;
    }

    /**
     * Scope process group supervisor
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userUid
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessGroupSupervisor($query, $userUid)
    {
        $query->where('PU_TYPE', 'GROUP_SUPERVISOR');
        $query->leftJoin('GROUP_USER', function ($leftJoin) use ($userUid) {
            $leftJoin->on('PROCESS_USER.USR_UID', '=', 'GROUP_USER.GRP_UID')
                ->where('GROUP_USER.USR_UID', $userUid);
        });
        $query->joinProcess();

        return $query;
    }

    /**
     * Scope join with process table
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJoinProcess($query)
    {
        $query->leftJoin('PROCESS', function ($leftJoin) {
            $leftJoin->on('PROCESS.PRO_UID', '=', 'PROCESS_USER.PRO_UID');
        });

        return $query;
    }

    /**
     * It returns a list of processes ids as an array
     * 
     * @param array $processes
     * @return array
     */
    public static function getListOfProcessUid($processes)
    {
        $res = (array_map(function ($x) {
            if (array_key_exists('PRO_ID', $x)) {
                return $x['PRO_ID'];
            }
        }, $processes));

        return array_filter($res);
    }

    /**
     * It returns a list of processes of the supervisor
     * 
     * @param string $userUid
     * @return array
     */
    public static function getProcessesOfSupervisor($userUid)
    {
        $query1 = ProcessUser::query()->select(['PRO_ID']);
        $query1->processSupervisor($userUid);
        $processes = $query1->get()->values()->toArray();

        $query2 = ProcessUser::query()->select(['PRO_ID']);
        $query2->processGroupSupervisor($userUid);

        array_push($processes, $query2->get()->values()->toArray());

        $processes = ProcessUser::getListOfProcessUid($processes);

        return $processes;
    }
}