<?php
namespace ProcessMaker\Project\Adapter;

use \Process;
use \Task;

use ProcessMaker\Project\ProjectHandler;

class WorkflowProject extends ProjectHandler
{
    protected $proUid;

    public function create($data)
    {
        try {
            // setting defaults
            $data['TASKS'] = array_key_exists('TASKS', $data) ? $data['TASKS'] : array();
            $data['ROUTES'] = array_key_exists('ROUTES', $data) ? $data['ROUTES'] : array();

            // Create project
            $process = new Process();
            $this->proUid = $process->create($data, false);

            // Create project's tasks
            foreach ($data['TASKS'] as $taskData) {
                $this->addTask($taskData);
            }

            // Create project's routes
            foreach ($data['ROUTES'] as $route) {

            }

        } catch (Exception $e) {
            //throw new \RuntimeException($e);
            echo $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            die;
        }
    }

    public function update($prjUid, $data)
    {
        // TODO: Implement update() method.
    }

    public function delete($prjUid)
    {
        // TODO: Implement delete() method.
    }

    public function load($prjUid)
    {
        // TODO: Implement load() method.
    }

    /*
     * Projects elements handlers
     */

    public function addTask($taskData)
    {
        $taskData['PRO_UID'] = $this->proUid;

        $task = new Task();
        $task->create($taskData, false);
    }

    public function updateTask($taskData)
    {
        # code...
    }

    public function removeTask($taskData)
    {
        # code...
    }

    public function addRoute($value='')
    {
        # code...
    }

    public function removeRoute($value='')
    {
        # code...
    }
}