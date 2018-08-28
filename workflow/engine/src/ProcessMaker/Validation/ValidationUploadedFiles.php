<?php

namespace ProcessMaker\Validation;

use Bootstrap;
use G;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use ProcessMaker\Core\System;
use ProcessMaker\Services\OAuth2\Server;
use ProcessMaker\Validation\Validator;

class ValidationUploadedFiles
{
    /**
     * Single object instance to be used in the entire environment.
     * 
     * @var object 
     */
    private static $validationUploadedFiles = null;

    /**
     * List of evaluated items that have not passed the validation rules.
     * 
     * @var array 
     */
    private $fails = [];

    /**
     * Check if the loaded files comply with the validation rules, add here if you 
     * want more validation rules. 
     * Accept per argument an array or object that contains a "filename" and "path" values.
     * The rules are verified in the order in which they have been added.
     * 
     * @param array|object $file 
     * @return Validator
     */
    public function runRules($file)
    {
        $validator = new Validator();

        //rule: disable_php_upload_execution
        $validator->addRule()
                ->validate($file, function($file) {
                    $filesystem = new Filesystem();
                    $extension = $filesystem->extension($file->filename);

                    return Bootstrap::getDisablePhpUploadExecution() === 1 && $extension === 'php';
                })
                ->status(550)
                ->message(G::LoadTranslation('ID_THE_UPLOAD_OF_PHP_FILES_WAS_DISABLED'))
                ->log(function($rule) {
                    /**
                     * Levels supported by MonologProvider is:
                     * 100 "DEBUG"
                     * 200 "INFO"
                     * 250 "NOTICE"
                     * 300 "WARNING"
                     * 400 "ERROR"
                     * 500 "CRITICAL"
                     * 550 "ALERT"
                     * 600 "EMERGENCY"
                     */
                    Bootstrap::registerMonologPhpUploadExecution('phpUpload', 550, $rule->getMessage(), $rule->getData()->filename);
                });

        //rule: upload_attempts_limit_per_user
        $validator->addRule()
                ->validate($file, function($file) {
                    $systemConfiguration = System::getSystemConfiguration('', '', config("system.workspace"));
                    $filesWhiteList = explode(',', $systemConfiguration['upload_attempts_limit_per_user']);
                    $userId = Server::getUserId();
                    $key = config("system.workspace") . '/' . $userId;
                    $attemps = (int) trim($filesWhiteList[0]);
                    $minutes = (int) trim($filesWhiteList[1]);
                    $pastAttemps = Cache::remember($key, $minutes, function() {
                                return 1;
                            });
                    //We only increase when the file path exists, useful when pre-validation is done.
                    if (isset($file->path)) {
                        Cache::increment($key, 1);
                    }
                    if ($pastAttemps <= $attemps) {
                        return false;
                    }
                    return true;
                })
                ->status(429)
                ->message(G::LoadTranslation('ID_TOO_MANY_REQUESTS'))
                ->log(function($rule) {
                    /**
                     * Levels supported by MonologProvider is:
                     * 100 "DEBUG"
                     * 200 "INFO"
                     * 250 "NOTICE"
                     * 300 "WARNING"
                     * 400 "ERROR"
                     * 500 "CRITICAL"
                     * 550 "ALERT"
                     * 600 "EMERGENCY"
                     */
                    Bootstrap::registerMonologPhpUploadExecution('phpUpload', 250, $rule->getMessage(), $rule->getData()->filename);
                });

        return $validator->validate();
    }

    /**
     * File upload validation.
     * 
     * @return $this
     */
    public function runRulesToAllUploadedFiles()
    {
        $files = $_FILES;
        if (!is_array($files)) {
            return;
        }
        $this->fails = [];
        foreach ($files as $file) {
            $data = (object) $file;
            if (!is_array($data->name) || !is_array($data->tmp_name)) {
                $data->name = [$data->name];
                $data->tmp_name = [$data->tmp_name];
            }
            foreach ($data->name as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $validator = $this->runRules(['filename' => $value, 'path' => $data->tmp_name[$key]]);
                if ($validator->fails()) {
                    $this->fails[] = $validator;
                }
            }
        }
        return $this;
    }

    /**
     * Get the first error and call the argument function.
     * 
     * @param function $callback
     * @return $this
     */
    public function dispach($callback)
    {
        if (!empty($this->fails[0])) {
            if (!empty($callback) && is_callable($callback)) {
                $callback($this->fails[0], $this->fails);
            }
        }
        return $this;
    }

    /**
     * It obtains a single object to be used as a record of the whole environment.
     * 
     * @return object
     */
    public static function getValidationUploadedFiles()
    {
        if (self::$validationUploadedFiles === null) {
            self::$validationUploadedFiles = new ValidationUploadedFiles();
        }
        return self::$validationUploadedFiles;
    }
}
