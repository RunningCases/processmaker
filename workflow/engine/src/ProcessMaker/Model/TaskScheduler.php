<?php

namespace ProcessMaker\Model;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\DB;
use \Cron\CronExpression;
use \Illuminate\Support\Carbon;
use \Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Console\Scheduling\Schedule;
/**
 * Class TaskScheduler
 * @package ProcessMaker\Model
 *
 * Represents a dynaform object in the system.
 */
class TaskScheduler extends Model
{
    protected $table = 'SCHEDULER';
    public $timestamps = true;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
