<?php
namespace ProcessMaker\BusinessModel;

use ProcessMaker\Model\TaskScheduler;
use \G;
use ProcessMaker\Plugins\Interfaces\StepDetail;
use ProcessMaker\Plugins\PluginRegistry;
use \ProcessMaker\Util;
use Illuminate\Support\Facades\Log;
use ProcessMaker\Core\System;

class TaskSchedulerBM
{
    /**
     * Execute the records with Laravel Task Scheduler
     */
    public static function executeScheduler($that){
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
    }

    /**
     * Return the records in Schedule Table by category
     */
    public static function getSchedule($category){
        $tasks = TaskScheduler::all();
        $count =  $tasks->count();
        if($count == 0){          
            TaskSchedulerBM::generateInitialData();
            $tasks = TaskScheduler::all();
        }       
        if(is_null($category)){
            return $tasks;
        }else{
            return TaskScheduler::where('category', $category)->get();
        }
    }
    /**
     * Save the record Schedule in Schedule Table
     */
    public static function saveSchedule(array $request_data){
        $task = TaskScheduler::find($request_data['id']);
        if(isset($request_data['enable'])){           
            $task->enable =  $request_data['enable'];
        }

        if(isset($request_data['expression'])){
            $task->expression = $request_data['expression'];
            $task->startingTime =  $request_data['startingTime'];
            $task->endingTime =  $request_data['endingTime'];
            $task->timezone =  $request_data['timezone'];
            $task->everyOn =  $request_data['everyOn'];
            $task->interval =  $request_data['interval'];
            
        }            
        $task->save();
        return  array();
    }

    public static function generateInitialData(){
        $arraySystemConfiguration = System::getSystemConfiguration('', '', config("system.workspace"));
        $toSave = array();
        $services = array(
            array(
                "title" => "ProcessMaker Events",
                "service" => "events",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * * * *",
                "description" => "Unpauses any case whose pause time has expired"             
            ),
            array(
                "title" => "ProcessMaker Scheduler",
                "enable" => "1",
                "service" => "scheduler",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "description" => "Unpauses any case whose pause time has expired"
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_UNPAUSE",
                "enable" => "0",
                "service" => "unpause",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* */1 * * *",
                "description" => "ID_TASK_SCHEDULER_UNPAUSE_DESC"                     
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CASE_EMAILS",
                "enable" => "1",
                "service" => "emails",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "*/5 * * * *",
                "description" => "ID_TASK_SCHEDULER_CASE_EMAILS_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_PM_PLUGINS",
                "enable" => "0",
                "service" => "plugins",
                "category" => "plugins",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "description" => "ID_TASK_SCHEDULER_PM_PLUGINS_DESC"     
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CALCULATE_ELAPSED",
                "service" => "calculate",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "description" => 'ID_TASK_SCHEDULER_CALCULATE_ELAPSED_DESC'    
            ),
            array(
                "title" => "Calculate App data",
                "service" => "calculateapp",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "description" => 'Calculates the elapsed time "according to the configured calendar" of all open tasks in active cases'            
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_UNASSIGNED",
                "service" => "unassigned-case",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* */1 * * *",
                "description" => 'ID_TASK_SCHEDULER_UNASSIGNED_DESC'               
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CLEAN_SELF",
                "service" => "clean-self-service-tables",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "description" => 'ID_TASK_SCHEDULER_CLEAN_SELF_DESC'     
            ),
            array(
                "title" => "Report by Users",
                "enable" => "0",
                "service" => "report_by_user",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* */1 * * *",
                "description" => "Report by Users"  
                     
            ),
            array(
                "title" => "Report by process",
                "enable" => "0",
                "service" => "report_by_process",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* */1 * * *",
                "description" => "Report by process"      
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_MESSAGE_EVENTS",
                "enable" => "1",
                "service" => "",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/messageeventcron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "*/5 * * * *",
                "description" => "ID_TASK_SCHEDULER_MESSAGE_EVENTS_DESC"    
            ),
            array(
                "title" => "ProcessMaker timer event cron",
                "enable" => "0",
                "service" => "",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/timereventcron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "description" => "ProcessMaker timer event cron"    
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_LDAP",
                "enable" => "0",
                "service" => "",
                "category" => "processmaker_sync",
                "file" => "workflow/engine/bin/ldapcron.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "* * */1 * *",
                "service" => "",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/actionsByEmailEmailResponse.php",
                "startingTime" => "0:00",
                "endingTime" => "23:59",
                "expression" => "*/5 * * * *",
                "description" => "ID_TASK_SCHEDULER_ACTION_EMAIL_DESC"    
            )
        );
     
        for($i = 0; $i < count($services); ++$i) {           
            $task = new TaskScheduler;
            $task->title = $services[$i]["title"];
            $task->category = $services[$i]["category"];
            $task->description = $services[$i]["description"];
            $task->startingTime = $services[$i]["startingTime"];
            $task->endingTime = $services[$i]["endingTime"];            
            $task->body =  'su -s /bin/sh -c "php '. PATH_TRUNK . $services[$i]["file"] . " " . $services[$i]["service"] . ' +w' . config("system.workspace") . ' +force"';
            $task->expression = $services[$i]["expression"];
            $task->type = "shell";
            $task->system = 1;
            $task->timezone = $arraySystemConfiguration['time_zone'];
            $task->enable = $services[$i]["enable"];
            $task->startingTime = "0:00";
            $task->endingTime = "23:59";
            $task->save();   
        }
    }
}