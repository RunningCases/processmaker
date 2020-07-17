<?php

namespace App\Console\Commands;

use Illuminate\Queue\Console\WorkCommand as BaseWorkCommand;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Worker;

class WorkCommand extends BaseWorkCommand
{

    use AddParametersTrait;

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
     * Listen for the queue events in order to update the console output.
     *
     * @return void
     */
    public function listenForEvents(): void
    {
        $this->laravel['events']->listen(JobProcessing::class, function ($event) {
            $this->loadAdditionalClassesAtRuntime();
        });
        parent::listenForEvents();
    }

    /**
     * This call the 'pluginClassLoader.json' file, is required by artisan for dynamically 
     * access to plugin classes.
     */
    private function loadAdditionalClassesAtRuntime(): void
    {
        if (!defined('PATH_PLUGINS')) {
            return;
        }
        $content = scandir(PATH_PLUGINS, SCANDIR_SORT_ASCENDING);
        foreach ($content as $value) {
            if (in_array($value, ['.', '..'])) {
                continue;
            }
            $path = PATH_PLUGINS . $value;
            if (!is_dir($path)) {
                continue;
            }
            //this file is required by artisan for dynamically access to class
            $classloader = $path . PATH_SEP . 'pluginClassLoader.json';
            if (!file_exists($classloader)) {
                continue;
            }
            $object = json_decode(file_get_contents($classloader), false);
            if (empty($object)) {
                continue;
            }
            if (!property_exists($object, 'classes') && !is_array($object->classes)) {
                continue;
            }
            foreach ($object->classes as $classpath) {
                require_once $path . PATH_SEP . $classpath;
            }
        }
    }
}
