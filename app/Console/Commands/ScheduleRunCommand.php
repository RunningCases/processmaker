<?php

namespace App\Console\Commands;

use Maveriks\WebApplication;
use \Illuminate\Support\Carbon;
use Illuminate\Console\Scheduling\ScheduleRunCommand as BaseCommand;
use Illuminate\Support\Facades\Log;
use ProcessMaker\BusinessModel\TaskSchedulerBM;
use ProcessMaker\Model\TaskScheduler;

class ScheduleRunCommand extends BaseCommand
{

    use AddParametersTrait;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function __construct(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        $this->startedAt = Carbon::now();
        $this->signature = "schedule:run";
        $this->signature .= '
        {--workspace=workflow : ProcessMaker Indicates the workspace to be processed.}
        {--processmakerPath=./ : ProcessMaker path.}
        ';
        $this->description .= ' (ProcessMaker has extended this command)';
        parent::__construct($schedule);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $that = $this;
        $workspace = $this->option('workspace');
        if (!empty($workspace)) {
            $webApplication = new WebApplication();
            $webApplication->setRootDir($this->option('processmakerPath'));
            $webApplication->loadEnvironment($workspace, false);
        }
        TaskScheduler::all()->each(function ($p) use ($that) {
            $starting = isset($p->startingTime) ? $p->startingTime : "0:00";
            $ending = isset($p->startingTime) ? $p->endingTime : "23:59";
            $timezone = isset($p->timezone) && $p->timezone != ""? $p->timezone: date_default_timezone_get();
            $that->schedule->exec($p->body)->cron($p->expression)->between($starting, $ending)->timezone($timezone)->when(function () use ($p) {
                $now = Carbon::now();
                $result = false;
                $datework = Carbon::createFromFormat('Y-m-d H:i:s', $p->last_update);
                if (isset($p->everyOn)) {
                    switch ($p->interval) {
                        case "day":
                            $interval = $now->diffInDays($datework);
                            $result = ($interval !== 0 && ($interval % intval($p->everyOn)) == 0);
                            break;
                        case "week":
                            $interval = $now->diffInDays($datework);
                            $result = ($interval !== 0 && $interval % (intval($p->everyOn) * 7) == 0);
                            break;
                        case "month":
                            $interval = $now->diffInMonths($datework);
                            $result = ($interval !== 0 && $interval % intval($p->everyOn) == 0);
                            break;
                        case "year":
                            $interval = $now->diffInYears($datework);
                            $result = ($interval !== 0 && $interval % intval($p->everyOn) == 0);
                            break;
                    }
                    return $result;
                }
                return true;
            });
        });
        parent::handle();
    }
}



