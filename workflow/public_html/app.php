<?php
/*
 * ProcessMaker Web Application Bootstrap
 */
try {
    $rootDir = realpath(__DIR__ . "/../../") . DIRECTORY_SEPARATOR;

    if (! is_dir($rootDir . 'vendor')) {
        if (file_exists($rootDir . 'composer.phar')) {
            throw new Exception(
                "ERROR: Vendors are missing!" . PHP_EOL .
                "Please execute the following command to install vendors:" .PHP_EOL.PHP_EOL.
                "$>php composer.phar install"
            );
        } else {
            throw new Exception(
                "ERROR: Vendors are missing!" . PHP_EOL .
                "Please execute the following commands to prepare/install vendors:" .PHP_EOL.PHP_EOL.
                "$>curl -sS https://getcomposer.org/installer | php" . PHP_EOL .
                "$>php composer.phar install"
            );
        }
    }

    if (! file_exists($rootDir . 'vendor' . DIRECTORY_SEPARATOR . "autoload.php")) {
        throw new Exception(
            "ERROR: Problems with Vendors!" . PHP_EOL .
            "Please execute the following command to repair vendors:" .PHP_EOL.PHP_EOL.
            "$>php composer.phar update"
        );
    }

    /** @var Composer\Autoload\ClassLoader $loader */
    $loader = include $rootDir . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
    $loader->add("", $rootDir . 'src/');
    $loader->add("", $rootDir . 'workflow/engine/src/');
    $loader->add("", $rootDir . 'workflow/engine/classes/model/');

    $app = new ProcessMaker\WebApplication();

    $app->setRootDir($rootDir);
    $app->setRequestUri($_SERVER['REQUEST_URI']);
    $stat = $app->route();

    switch ($stat)
    {
        case ProcessMaker\WebApplication::RUNNING_WORKFLOW:
            include "sysGeneric.php";
            break;
        case ProcessMaker\WebApplication::RUNNING_API:
            $app->run(ProcessMaker\WebApplication::SERVICE_API);
            break;
    }

} catch (Exception $e) {
    die($e->getMessage());
}
