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
                "title" => "ID_TASK_SCHEDULER_UNPAUSE",
                "enable" => "0",
                "service" => "unpause",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "0 */1 * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_UNPAUSE_DESC"                     
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CALCULATE_ELAPSED",
                "enable" => "0",
                "service" => "calculate",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "0:30",
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "0 */1 * * 0,1,2,3,4,5,6",
                "description" => 'ID_TASK_SCHEDULER_CALCULATE_ELAPSED_DESC'    
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_UNASSIGNED",
                "enable" => "0",
                "service" => "unassigned-case",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "0 */1 * * 0,1,2,3,4,5,6",
                "description" => 'ID_TASK_SCHEDULER_UNASSIGNED_DESC'               
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CLEAN_SELF",
                "enable" => "0",
                "service" => "clean-self-service-tables",
                "category" => "case_actions",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "0:30",
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "0 */1 * * 0,1,2,3,4,5,6",
                "description" => 'ID_TASK_SCHEDULER_CLEAN_SELF_DESC'     
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CASE_EMAILS",
                "enable" => "1",
                "service" => "emails",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/5 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_CASE_EMAILS_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_ACTION_EMAIL",
                "enable" => "1",
                "service" => "",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/actionsByEmailEmailResponse.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/5 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_ACTION_EMAIL_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_MESSAGE_EVENTS",
                "enable" => "1",
                "service" => "",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/messageeventcron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/5 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_MESSAGE_EVENTS_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_SEND_NOT",
                "enable" => "1",
                "service" => "",
                "category" => "emails_notifications",
                "file" => "workflow/engine/bin/sendnotificationscron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/5 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_SEND_NOT_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_REPORT_USERS",
                "enable" => "0",
                "service" => "report_by_user",
                "category" => "reporting",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/10 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_REPORT_USERS_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_REPORT_PROCESS",
                "enable" => "0",
                "service" => "report_by_process",
                "category" => "reporting",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/10 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_REPORT_PROCESS_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_CALCULATE_APP",
                "enable" => "0",
                "service" => "calculateapp",
                "category" => "reporting",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => null,
                "endingTime" => null,
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "*/10 * * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_CALCULATE_APP_DESC"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_LDAP",
                "enable" => "0",
                "service" => "",
                "category" => "processmaker_sync",
                "file" => "workflow/engine/bin/ldapcron.php",
                "startingTime" => "0:00",
                "endingTime" => "0:30",
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "0 */1 * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_LDAP"   
            ),
            array(
                "title" => "ID_TASK_SCHEDULER_PM_PLUGINS",
                "enable" => "0",
                "service" => "plugins",
                "category" => "plugins",
                "file" => "workflow/engine/bin/cron.php",
                "startingTime" => "0:00",
                "endingTime" => "0:30",
                "everyOn" => "1",
                "interval" => "week",
                "expression" => "0 */1 * * 0,1,2,3,4,5,6",
                "description" => "ID_TASK_SCHEDULER_PM_PLUGINS_DESC"   
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
            //$task->timezone = $arraySystemConfiguration['time_zone'];
            $task->enable = $services[$i]["enable"];
            $task->everyOn = $services[$i]["everyOn"];
            $task->interval = $services[$i]["interval"];
            $task->save();   
        }
    }
}