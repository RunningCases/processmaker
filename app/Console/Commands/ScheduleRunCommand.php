<?php
namespace App\Console\Commands;

use Maveriks\WebApplication;
use \Illuminate\Support\Carbon;
use Illuminate\Console\Scheduling\ScheduleRunCommand as BaseCommand;
use Illuminate\Support\Facades\Log;
use ProcessMaker\Model\TaskScheduler;
class ScheduleRunCommand extends BaseCommand
{

    use AddParametersTrait;
     /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
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
        TaskScheduler::all()->each(function($p) use ($that){
            if($p->isDue()){
                Log::info("Si se ejecuta" . $p->expression);
            }
            if($p->enable == '1'){
                $that->schedule->exec($p->body)->cron($p->expression)->between($p->startingTime, $p->endingTime);
            }           
        });
        parent::handle();
    }
}



