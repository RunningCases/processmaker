<?php

namespace App\Console\Commands;

use Illuminate\Queue\Console\WorkCommand as BaseWorkCommand;
use Illuminate\Queue\Worker;
use Maveriks\WebApplication;

class WorkCommand extends BaseWorkCommand
{

    /**
     * Create a new queue work command.
     *
     * @param \Illuminate\Queue\Worker $worker
     *
     * @return void
     */
    public function __construct(Worker $worker)
    {
        $this->signature .= '
            {--workspace=workflow : ProcessMaker Indicates the workspace to be processed.}
            {--processmakerPath=./ : ProcessMaker path.}
            ';

        $this->description .= ' (ProcessMaker has extended this command)';

        parent::__construct($worker);
    }

    /**
     * Run the worker instance.
     *
     * @param  string $connection
     * @param  string $queue
     */
    protected function runWorker($connection, $queue)
    {
        $workspace = $this->option('workspace');

        if (!empty($workspace)) {
            $webApplication = new WebApplication();
            $webApplication->setRootDir($this->option('processmakerPath'));
            $webApplication->loadEnvironment($workspace);
        }
        parent::runWorker($connection, $queue);
    }

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        $options = parent::gatherWorkerOptions();
        return $options;
    }
}
